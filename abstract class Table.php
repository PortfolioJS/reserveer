<?php
require_once("interface Reservable.php");
abstract class Table implements Reservable
{
	protected $reservations = [];
	protected $personsCount; // i.e.: max. seats
	protected $tableNumber;

	public function __construct($tableNumber, $personsCount)
	{
		$this->personsCount = $personsCount;
		$this->tableNumber = $tableNumber;
	}

	/**
	 * @return integer
	 */
	public function getPersonsCount()
	{
		return $this->personsCount;
	}

	/**
	 * @return array
	 */
	public function getReservations() //wordt gebruikt in BookManager Class cancelTable() en cancelTableS() functie
	{
		return $this->reservations;
	}

	/**
	 * @return integer
	 */
	public function getTableNumber()
	{
		return $this->tableNumber;
	}

	public function __toString()
	{
		$resultString = "Table number: $this->tableNumber\n";
		foreach ($this->reservations as $reservation) {
			$resultString .= $reservation->__toString() . PHP_EOL;
		}
		return $resultString;
	}

	public function addReservation(Reservation $reservation)
	{
		// $this->checkForValidReservation($reservation); //zo zag deze functie er eerst uit in het voorbeeld van Github (met Exception-throw als reserveren niet mogelijk is op tijdstip - zie checkForValidReservation() hieronder)
		$this->reservations[] = $reservation; //$table->checkForValidReservation($reservation); staat nu (ook) in class BookManager::bookTable($reservation)
	}

	//onderstaande functie werd in het oorspronkelijke Github-voorbeeld nooit gebruikt (inmiddels wordt de functie in de Bookmanager Class aangeroepen in de functies cancelTable(Reservation $reservation) + cancelTableS($id))
	public function removeReservation(/*Reservation $reservation /* weggecomment omdat de $key nu 'global' vanuit de BookManager Class wordt gehaald*/)
	{
		// if (($key = array_search($reservation, $this->reservations)) !== false) {//dit stukje code verplaatst naar BookManager::cancelTable($reservation) + BookManager::cancelTableS($id) REDEN: complicaties bij cancelen van een 'SplitReservation' (zie in BookManager Class bij betreffende functies)
		global $key; //toegevoegd omdat bovenstaande stukje code (met de $key definitie) is verplaatst
		unset($this->reservations[$key]);
	}

	public function checkForValidReservation(Reservation $reservation)
	{
		global $count; // geinitialiseerd op 0 in class BookManager::bookTable($reservation) {}
		global $check;
		$check = False; // if ($check == True) {$table->addReservation($reservation);} eveneens in class BookManager::bookTable()

		foreach ($this->reservations as $existingReservation) {
			if (
				($reservation->getStartDate() >= $existingReservation->getStartDate() &&
					$reservation->getStartDate() <= $existingReservation->getEndDate())
			) {
				// throw new EReservationException($this->tableNumber, $reservation); // oorspronkelijke Exception-throw vervangen door:
				$count += 1; //als alle tafels bezet zijn is $count gelijk aan het aantal tafels in de array
				$check = True;
			} elseif (
				$reservation->getEndDate() >= $existingReservation->getStartDate() &&
				$reservation->getEndDate() <= $existingReservation->getEndDate()
			) {
				// throw new EReservationException($this->tableNumber, $reservation); // oorspronkelijke Exception-throw vervangen door:
				$count += 1; //als alle tafels bezet zijn is $count gelijk aan het aantal tafels in de array
				$check = True;
			}
		}
		global $numberCorresponds; //more or less...(reservering kan 1 persoon minder zijn dan tafel max. aan kan)
		global $toomuch;
		$numberCorresponds = True; //wordt op False gezet als de reservering groter is dan het maximum dat de tafel aan kan, of kleiner dan het maximum minus 1.
		$toomuch = False; //wordt op True gezet als de reservering groter is dan het maximum dat de tafel aan kan

		if ($reservation->getNumberOfGuests() > $this->getPersonsCount()) {
			// $count -= 1;//misschien nodig bij bijvoorbeeld 2x reserveren 2 persoonstafels voor 4 personen wanneer alle 4 persoonstafels zijn gereserveerd
			$numberCorresponds = False;
			$toomuch = True; //te veel gasten voor de tafel
		} else if ($reservation->getNumberOfGuests() < -1 + $this->getPersonsCount()) { //oftewel: als bijv. 2 personen een 4 persoonstafel reserveren, kan dat niet (zolang er nog 2 persoonstafels beschikbaar zijn, is althans de bedoeling); 3 personen kunnen wel een 4 persoonstafel reserveren (-1 + 4)
			$numberCorresponds = False;
			$toomuch = False; //te weinig gasten voor de tafel
		}
	}
}
