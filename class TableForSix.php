<?php
require_once "abstract class Table.php";
class TableForSix extends Table
{
    public static $count = 0;
    const PERSONS_COUNT = 6;
    function __construct($tableNumber)
    {
        parent::__construct($tableNumber, TableForSix::PERSONS_COUNT);

        TableForSix::$count++;
    }
}