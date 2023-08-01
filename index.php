<?php
// van de oorspronkelijke classes uit het voorbeeld van Github waarop dit reserveersysteem is gebaseerd, heb ik de class EReservationException vervangen door een andere oplossing wanneer reserveren niet mogelijk is (zie class BookManager)
require_once __DIR__ . "/classes/interface Reservable.php";
require_once __DIR__ . "/classes/abstract class Table.php";
require_once __DIR__ . "/classes/class TableForTwo.php";
require_once __DIR__ . "/classes/class TableForFour.php";
require_once __DIR__ . "/classes/class TableForSix.php";
require_once __DIR__ . "/classes/class Guest.php";
require_once __DIR__ . "/classes/class Reservation.php";
// require_once __DIR__ . "/classes/class EReservationException.php";//NIET MEER NODIG (VERVANGEN)
require_once __DIR__ . "/classes/class Bookmanager.php";

$Guest1 = new Guest("Guest", "ReservesForTwo", 002);
// echo $Guest1->__toString();
// echo "<br>";

$Guest2 = new Guest("Guest", "ReservesForFour", 004);
$Guest3 = new Guest("Guest", "ReservesForSix", 006);

//voorbeeld hieronder met voor het gemak 3 gasten en herhaling van 3 reserveringen met overlap op zelfde datum/tijd (als het goed is werkt het onder alle scenario's qua $reservation->numberOfGuests; reserveringen boven de 6 personen worden niet doorgezet, maar krijgen een melding - zie class Bookmanager)
//in dit voorbeeld heeft elke tafel maar 1 reservering op index 0 (doordat alle 20 reserveringen overlappen qua datum en tijd)
$Reservation1 = new Reservation("19-10-2023 17:30", $Guest1, 6); // op 6 gezet om te checken of SplitReservation over 3 tafels werkt (ook qua verwijderen SplitReservation): $Reservation12 is nu verdeeld over 3 tafels
$Reservation2 = new Reservation("19-10-2023 18:00", $Guest2, 4);
$Reservation3 = new Reservation("19-10-2023 17:00", $Guest3, 6);
$Reservation4 = new Reservation("19-10-2023 17:30", $Guest1, 2);
$Reservation5 = new Reservation("19-10-2023 18:00", $Guest2, 4);
$Reservation6 = new Reservation("19-10-2023 17:00", $Guest3, 6);
$Reservation7 = new Reservation("19-10-2023 17:30", $Guest1, 2);
$Reservation8 = new Reservation("19-10-2023 18:00", $Guest2, 4);
$Reservation9 = new Reservation("19-10-2023 17:00", $Guest3, 6);
$Reservation10 = new Reservation("19-10-2023 17:30", $Guest1, 2);
$Reservation11 = new Reservation("19-10-2023 18:00", $Guest2, 4);
$Reservation12 = new Reservation("19-10-2023 17:00", $Guest3, 6);
$Reservation13 = new Reservation("19-10-2023 17:30", $Guest1, 2);
$Reservation14 = new Reservation("19-10-2023 18:00", $Guest2, 4);
$Reservation15 = new Reservation("19-10-2023 17:00", $Guest3, 6);
$Reservation16 = new Reservation("19-10-2023 17:30", $Guest1, 2);
$Reservation17 = new Reservation("19-10-2023 18:00", $Guest2, 4);
$Reservation18 = new Reservation("19-10-2023 17:00", $Guest3, 6);
$Reservation19 = new Reservation("19-10-2023 17:30", $Guest1, 2);
$Reservation20 = new Reservation("19-10-2023 18:00", $Guest2, 4);

//LET OP VOLGORDE: array v. tafels met aflopende (!) PERSONS_COUNT (anders loopt de bookTable() functie in de class BookManager in de soep)
//bij toevoegen nieuwe tafel daarom rekening houden met volgorde (index) in array (zie uitleg hieronder)
$tables[1] = new TableForSix(1);
$tables[2] = new TableForSix(2);
$tables[3] = new TableForFour(3);
$tables[4] = new TableForFour(4);
$tables[5] = new TableForFour(5);
$tables[6] = new TableForFour(6);
$tables[7] = new TableForFour(7);
$tables[8] = new TableForFour(8);
$tables[9] = new TableForTwo(9);
$tables[10] = new TableForTwo(10);
$tables[11] = new TableForTwo(11);
$tables[12] = new TableForTwo(12);
$tables[13] = new TableForTwo(13);
$tables[14] = new TableForTwo(14);
$tables[15] = new TableForTwo(15);
$tables[16] = new TableForTwo(16);
$tables[17] = new TableForTwo(17);

BookManager::countReservedTables($Reservation1);
BookManager::bookTable($Reservation1);
echo "<br>";
// echo "<br>";
// echo $Reservation1->__toString();
// echo "<br>";
// echo "<br>";
BookManager::countReservedTables($Reservation2);
BookManager::bookTable($Reservation2);
echo "<br>";
BookManager::countReservedTables($Reservation3);
BookManager::bookTable($Reservation3);
echo "<br>";
BookManager::countReservedTables($Reservation4);
BookManager::bookTable($Reservation4);
echo "<br>";
BookManager::countReservedTables($Reservation5);
BookManager::bookTable($Reservation5);
echo "<br>";
BookManager::countReservedTables($Reservation6);
BookManager::bookTable($Reservation6);
echo "<br>";
BookManager::countReservedTables($Reservation7);
BookManager::bookTable($Reservation7);
echo "<br>";
BookManager::countReservedTables($Reservation8);
BookManager::bookTable($Reservation8);
echo "<br>";
BookManager::countReservedTables($Reservation9);
BookManager::bookTable($Reservation9);
echo "<br>";
BookManager::countReservedTables($Reservation10);
BookManager::bookTable($Reservation10);
echo "<br>";
BookManager::countReservedTables($Reservation11);
BookManager::bookTable($Reservation11);
echo "<br>";
BookManager::countReservedTables($Reservation12);
BookManager::bookTable($Reservation12);
echo "<br>";
BookManager::countReservedTables($Reservation13);
BookManager::bookTable($Reservation13);
echo "<br>";
BookManager::countReservedTables($Reservation14);
BookManager::bookTable($Reservation14);
echo "<br>";
BookManager::countReservedTables($Reservation15);
BookManager::bookTable($Reservation15);
echo "<br>";
BookManager::countReservedTables($Reservation16);
BookManager::bookTable($Reservation16);
echo "<br>";
BookManager::countReservedTables($Reservation17);
BookManager::bookTable($Reservation17);
echo "<br>";
BookManager::countReservedTables($Reservation18);
BookManager::bookTable($Reservation18);
echo "<br>";
BookManager::countReservedTables($Reservation19);
BookManager::bookTable($Reservation19);
echo "<br>";
BookManager::countReservedTables($Reservation20);
BookManager::bookTable($Reservation20);

echo "<pre>";
print_r($tables);
echo "</pre>";
echo "<br>";

echo "Reservations total: " . Reservation::$count . "<br>";//LET OP: alleen succesvolle reserveringen worden hier geteld (wanneer ze dus in een array met reserveringen van een tafel zijn opgenomen); SplitReservations worden 1 keer geteld (zie Class Reservation)

BookManager::cancelTable($Reservation12); //SplitReservation verdeeld over 3 tafels wordt verwijderd

echo "<pre>";
print_r($tables);
echo "</pre>";
echo "<br>";

echo "Reservations total: " . Reservation::$count;//LET OP: alleen succesvolle reserveringen worden hier geteld (wanneer ze dus in een array met reserveringen van een tafel zijn opgenomen); SplitReservations worden 1 keer geteld (zie Class Reservation)
