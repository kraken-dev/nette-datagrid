<?php // vim: ts=4 sw=4 ai:
/**
 * @copyright (c) 2010, 2011 Lopo <lopo@lohini.net>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License Version 3
 * @license New BSD
 * @author Lopo <lopo@lohini.net>
 */
namespace DataGrid\DataSources\Doctrine\Utils;

use Doctrine\ORM\Query\AST;

/**
 * Distinct AST walker
 * used for getting distinct values of 1 column
 *
 * backported from Lohini {@link http://www.lohini.net}
 */
class DistinctASTWalker
extends \Doctrine\ORM\Query\TreeWalkerAdapter
{
	public function walkSelectStatement(AST\SelectStatement $ast)
	{
		$column=$this->_getQuery()->getHint('distinct');

		$ast->selectClause->isDistinct=TRUE;
		list($parentName, $distinct)=explode('.', $column);
		$pathExpression=new AST\PathExpression(
					AST\PathExpression::TYPE_STATE_FIELD | AST\PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION,
					$parentName,
					$distinct
					);
		$pathExpression->type=AST\PathExpression::TYPE_STATE_FIELD;
		$ast->selectClause->selectExpressions=array(
			new AST\SelectExpression(
				$pathExpression,
				NULL
				)
			);

		$ast->orderByClause=array(); //reset ORDER BY clause, it is not necessary
	}
}
