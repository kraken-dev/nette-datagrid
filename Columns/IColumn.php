<?php

namespace DataGrid\Columns;

/**
 * Defines method that must be implemented to allow a component act like a data grid column.
 *
 * @author     Roman Sklenář
 * @copyright  Copyright (c) 2009 Roman Sklenář (http://romansklenar.cz)
 * @license    New BSD License
 * @package    Nette\Extras\DataGrid
 */
interface IColumn
{
	/**
	 * Is column orderable?
	 * @return bool
	 */
	function isOrderable();

	/**
	 * Gets header link (order signal)
	 * @param string $dir
	 * @return string
	 */
	function getOrderLink($dir = NULL);

	/**
	 * Has column filter box?
	 * @return bool
	 */
	function hasFilter();

	/**
	 * Returns column's filter.
	 * @return \DataGrid\Filters\IColumnFilter|NULL
	 */
	function getFilter();

	/**
	 * Formats cell's content.
	 * @param mixed $value
	 * @return string
	 */
	function formatContent($value);

	/**
	 * Filters data source.
	 * @param mixed $value
	 */
	function applyFilter($value);

	/**
	 * Return a renderer.
	 * It might be a renderer ID which is then used to get the actual 
	 * renderer, or a renderer itself
	 * @return string|\DataGrid\Renderers\Column
	 */
	function getRenderer();
}
