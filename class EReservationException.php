<?php
// class EReservationException extends LogicException //NIET MEER NODIG (bewaard 'for the record')
// { //deze Exception gebaseerd op het voorbeeld van Github heb ik uiteindelijk vervangen door een andere manier om aan te geven dat reserveren op bepaald tijdstip niet meer kan
// 	function __construct($tableNumber, Reservation $reservation)
// 	{
// 		$this->message = "Helaas. Tafel " . $tableNumber . " is al bezet op "
// 			. $reservation->getStartDate()->format("d-m-y") . " om "
// 			. $reservation->getStartDate()->format("H:i") . " uur.";
// 	}
// }