<?php

namespace DataGrid\Renderers\Column;

use \DataGrid\Columns\IColumn;

/**
 * Defines method that must implement data grid rendered.
 *
 * @author     Dusan Jakub
 * @license    New BSD License
 * @package    Nette\Extras\DataGrid
 */
interface IColumnRenderer
{
	/**
	 * @param IColumn $column
	 * @return string
	 */
	public function generateHeaderCell(IColumn $column);

	/**
	 * @param IColumn $column
	 * @return string
	 */
	public function generateFilterCell(IColumn $column);

	/**
	 * @param IColumn $column
	 * @param array $data
	 * @param string $primary
	 * @return string
	 */
	public function generateContentCell(IColumn $column, $data, $primary);
}
