<?php
class Reservation
{
	protected $startDate;
	protected $endDate; //=$startDate/Time + 149 minuten (of zoiets, zie hieronder)
	protected $guest;
	protected $numberOfGuests;
	protected int $id;
	public static int $count = 0;
	protected $splitReservation = False;
	protected int $splitCount = 1; //DEFAULT (wanneer de reservering niet is opgesplitst over meerdere tafels/reserveringen en er dus geen $splitReservations array is - waar de $splitcount normaliter uitgehaald wordt, zie hieronder bij de constructor voor meer info over deze variabelen)

	/**
	 * @param string $startDate Data in format "d-m-y H:i".
	 * @param string $endDate Data in format "d-m-y H:i".
	 * @param Guest $guest
	 * @param int $numberOfGuests
	 */
	function __construct($startDate, Guest $guest, $numberOfGuests, ...$splitReservation) //de $splitReservation bool|array staat default op False (als-ie niet ingevuld is; EIGENLIJK IS HET DAN EEN LEGE ARRAY); in de BookManager Class wordt hij bij een opgesplitste reservering op True gezet (dan wordt het bij de bijbehorende reservering(en) een array met op index 0 de 1 (True), op index 1 de $id van de bijbehorende reservering: $reservation->getID() en op index 2 het aantal tafels waarover de reservering is verdeeld, zie hieronder)
	{ //LET OP: de $splitReservation bool staat bij de EERSTE reservering (voor de split) nog op False en wordt pas bij het aanmaken van een nieuw reserveringsobject door BookManager op True gezet (zie de BookManager Class)
		$this->startDate = new DateTime($startDate);
		$minutes = 149; //aangenomen dat een restaurantbezoek max. 2,5 uur duurt
		$this->endDate = (clone $this->startDate)->add(new DateInterval("PT{$minutes}M")); // use clone to avoid modification of $now object
		$this->guest = $guest;
		$this->numberOfGuests = $numberOfGuests;
		$this->splitReservation = $splitReservation;
		Reservation::$count++; //elke unieke reservering wordt geteld (in de BookManager Class wordt een reservering die niet kan worden doorgezet - omdat alle tafels op een specifiek tijdstip zijn bezet, of omdat de reservering voor meer dan 6 personen is en dus niet door dit systeempje wordt afgehandeld - weer afgeteld: Reservation::$count--;)
		if ($this->splitReservation == False) { //de eerste van de gesplitste reserveringen wordt pas NA het constructen van het reserveringsobject op True gezet (in de BookManager Class), DUS die wordt NIET afgeteld (zie hieronder: else {Reservation::$count--)
			$this->id = Reservation::$count;
		} else {
			Reservation::$count--; //je wilt gesplitste reserveringen niet nog eens tellen (behalve de eerste in de reeks, zie hierboven bij if)
			$this->id = $this->splitReservation[1]; //het $id wordt uit de bijbehorende array gehaald (waar het in de BookManager Class m.b.v. $reservation->getID() in was gezet, zie comment hierboven bij de parameters van de constructor)
			$this->splitCount = $this->splitReservation[2]; //het aantal tafels/reserveringen waarover de SplitReservation is verdeeld (staat op index 2 in de $splitReservation array)
		}
	}

	/**
	 * @param \Guest $guest
	 */
	public function setGuest($guest)
	{
		$this->guest = $guest;
	}

	/**
	 * @return \Guest
	 */
	public function getGuest()
	{
		return $this->guest;
	}

	/**
	 * @param \DateTime $endDate
	 */
	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
	}

	/**
	 * @return \DateTime
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @param \DateTime $startDate
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}

	/**
	 * @return \DateTime
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @param \int $numberOfGuests
	 */
	public function setNumberOfGuests($numberOfGuests)
	{
		$this->numberOfGuests = $numberOfGuests;
	}

	/**
	 * @param \int
	 */
	public function getNumberOfGuests()
	{
		return $this->numberOfGuests;
	}

	/**
	 * @param \int
	 */
	public function getID()
	{
		return $this->id;
	}

	/**
	 * @param \array|bool $splitReservation
	 */
	public function setSplitReservation($splitReservation)
	{
		$this->splitReservation = $splitReservation;
	}

	/**
	 * @param \array|bool
	 */
	public function getSplitReservation()
	{
		return $this->splitReservation;
	}

	/**
	 * @param \int $splitCount
	 */
	public function setSplitCount($splitCount)
	{
		$this->splitCount = $splitCount;
	}

	/**
	 * @param \int
	 */
	public function getSplitCount()
	{
		return $this->splitCount;
	}

	function __toString()
	{
		return "Reservering ID: " . $this->id . " Datum/tijd: " . $this->startDate->format("d-m-y") . " om " . $this->getStartDate()->format("H:i") . " uur.\n"
			. "Aantal gasten: " . $this->numberOfGuests . "<br>"
			// . "End Date: " . $this->endDate->format("d-m-y") . "\n" //End Date niet nodig bij reservering voor restaurant (hoeft althans niet te worden ingevuld)
			. "Reservering op naam: " . $this->guest->__toString();
	}
}
