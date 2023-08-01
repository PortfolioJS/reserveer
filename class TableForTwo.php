<?php
require_once "abstract class Table.php";
class TableForTwo extends Table
{
	public static $count = 0;
	const PERSONS_COUNT = 2;
	function __construct($tableNumber)
	{
		parent::__construct($tableNumber, TableForTwo::PERSONS_COUNT);

		TableForTwo::$count++;
	}
}