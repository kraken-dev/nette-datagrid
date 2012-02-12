<?php

namespace DataGrid\Renderers\Column;

use Nette\Utils\Html,
	DataGrid\Columns;

/**
 * Description of ColumnRenderer
 *
 * @author rodney2
 */
class ActionColumnRenderer
extends ColumnRenderer
{
	/**
	 * @param Columns\IColumn $column
	 * @return Html
	 */
	public function generateHeaderCell(Columns\IColumn $column)
	{
		$cell = parent::generateHeaderCell($column);
		$cell->addClass('actions');
		return $cell;
	}

	/**
	 * @param Columns\IColumn $column
	 * @return Html
	 */
	public function generateFilterCell(Columns\IColumn $column)
	{
		$cell = $this->gridRenderer->getWrapper('row.filter cell container');

		// TODO: set on filters too?
		$cell->attrs = $column->getCellPrototype()->attrs;

		$value = (string)$this->gridRenderer->getSubmitControl();
		$cell->addClass('actions');
		$cell->setHtml($value);

		return $cell;
	}

	/**
	 * @param Columns\IColumn $column
	 * @param array $data
	 * @param string $primary
	 * @return Html
	 */
	public function generateContentCell(Columns\IColumn $column, $data, $primary)
	{
		$cell = $this->gridRenderer->getWrapper('row.content cell container');
		$cell->attrs = $column->getCellPrototype()->attrs;

		$value = '';
		foreach ($this->gridRenderer->getDataGrid()->getActions() as $action) {
			$value .= $this->generateAction($action, $data, $primary);
		}
		$cell->addClass('actions');

		$cell->setHtml((string) $value);
		return $cell;
	}

	/**
	 * @param \DataGrid\Action
	 * @param array $data
	 * @param string $primary
	 * @return Html
	 */
	protected function generateAction($action, $data, $primary)
	{
		if (!is_callable($action->ifDisableCallback) || !callback($action->ifDisableCallback)->invokeArgs(array($data))) {
			$html = $action->getHtml();
			$html->title($this->gridRenderer->getDataGrid()->translate($html->title));
			$action->generateLink(array($primary => $data[$primary]));
			$this->gridRenderer->onActionRender($html, $data);
			return $html->render() . ' ';
		} else {
			return Html::el('span')->setText($this->gridRenderer->getDataGrid()->translate($action->getHtml()->title))->render() . ' ';
		}
	}
}
