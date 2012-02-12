<?php

namespace DataGrid\Renderers\Extended;

use Nette\Utils\Html,
	DataGrid\Columns\IColumn,
	DataGrid\Action;

/**
 * Description of ColumnRenderer
 *
 * @author rodney2
 */
class ActionColumnRenderer
extends \DataGrid\Renderers\Column\ActionColumnRenderer
{
	/**
	 * @param IColumn $column
	 * @return Html
	 */
	public function generateHeaderCell(IColumn $column)
	{
		$cell = parent::generateHeaderCell($column);

		if (!$this->gridRenderer->getDataGrid()->hasFilters()) {
			$actions = Html::el('span')->setHtml(
			$this->generateActions($column, null, null, Action::WITHOUT_KEY));
			$cell->setHtml($actions . $cell->getHtml());
		}

		return $cell;
	}

	/**
	 * @param IColumn $column
	 * @return Html
	 */
	public function generateFilterCell(IColumn $column)
	{
		$cell = $this->gridRenderer->getWrapper('row.filter cell container');

		// TODO: set on filters too?
		$cell->attrs = $column->getCellPrototype()->attrs;

		$submit = $this->gridRenderer->getSubmitControl();
		$submit->value = '';

		$value = (string) $submit;
		$value .= Html::el('span')->setHtml(
		$this->generateActions($column, null, null, Action::WITHOUT_KEY));

		$cell->addClass('actions');
		$cell->setHtml($value);

		return $cell;
	}

	/**
	 * @param IColumn $column
	 * @param array $data
	 * @para, string $primary
	 * @return Html
	 */
	public function generateContentCell(IColumn $column, $data, $primary)
	{
		$cell = $this->gridRenderer->getWrapper('row.content cell container');
		$cell->attrs = $column->getCellPrototype()->attrs;

		$value = $this->generateActions($column, $data, $primary, Action::WITH_KEY);
		$cell->addClass('actions');

		$cell->setHtml(Html::el('span')->setHtml((string) $value));
		return $cell;
	}

	/**
	 * @param IColumn $column
	 * @param array $data
	 * @param string $primary
	 * @param string $key
	 * @return string
	 */
	protected function generateActions(IColumn $column, $data, $primary, $key)
	{
		$value = '';
		foreach ($this->gridRenderer->getDataGrid()->getActions() as $action) {
			if ($action->getKey() == $key) {
				$value .= $this->generateAction($action, $data, $primary);
			}
		}
		return $value;
	}
}
