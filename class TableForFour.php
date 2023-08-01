<?php
require_once "abstract class Table.php";
class TableForFour extends Table
{
	public static $count = 0;
	const PERSONS_COUNT = 4;
	function __construct($tableNumber)
	{
		parent::__construct($tableNumber, TableForFour::PERSONS_COUNT);

		TableForFour::$count++;
	}
}
