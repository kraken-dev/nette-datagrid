<?php

namespace DataGrid\Columns;

use Nette\Utils;

/**
 * Representation of textual data grid column.
 *
 * @author     Roman Sklenář
 * @copyright  Copyright (c) 2009 Roman Sklenář (http://romansklenar.cz)
 * @license    New BSD License
 * @example    http://addons.nette.org/datagrid
 * @package    Nette\Extras\DataGrid
 */
class TextColumn
extends Column
{
	/**
	 * Formats cell's content.
	 * @param mixed $value
	 * @param \DibiRow|array $data
	 * @return string
	 */
	public function formatContent($value, $data = NULL)
	{
		$value = htmlSpecialChars($value);

		if (is_array($this->replacement) && !empty($this->replacement)) {
			if (in_array($value, array_keys($this->replacement))) {
				$value = $this->replacement[$value];
			}
		}

		foreach ($this->formatCallback as $callback) {
			if (is_callable($callback)) {
				$value = call_user_func($callback, $value, $data);
			}
		}

		// truncate
		if ($this->maxLength != 0) {
			if ($value instanceof Utils\Html) {
				$text = $value->getText();
				$text = Utils\Strings::truncate($text, $this->maxLength);
				$value->setText($text);
			} else {
				$value = Utils\Strings::truncate($value, $this->maxLength);
			}
		}

		return $value;
	}

	/**
	 * Filters data source.
	 * @param mixed
	 */
	public function applyFilter($value)
	{
		if (!$this->hasFilter()) {
			return;
		}

		$dataSource = $this->getDataGrid()->getDataSource();

		if (strpos($value, '*') !== FALSE) {
			$dataSource->filter($this->name, 'LIKE', $value); //asterisks are converted internally
		} elseif ($value === 'NULL' || $value === 'NOT NULL') {
			$dataSource->filter($this->name, "IS $value");
		} else {
			$dataSource->filter($this->name, 'LIKE', "*$value*");
		}
	}
}
