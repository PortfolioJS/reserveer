<?php
class BookManager
{
	public static function countReservedTables(Reservation $reservation)
	{
		global $tables;
		global $check;
		global $countReservedTablesForSix;
		global $countReservedTablesForFour;
		global $countReservedTablesForTwo;
		$countReservedTablesForSix = 0;
		$countReservedTablesForFour = 0;
		$countReservedTablesForTwo = 0;

		foreach ($tables as $table) {

			$table->checkForValidReservation($reservation);
			if ($check == True && $table->getPersonsCount() == 6) {
				$countReservedTablesForSix += 1;
			} else if ($check == True && $table->getPersonsCount() == 4) {
				$countReservedTablesForFour += 1;
			} else if ($check == True && $table->getPersonsCount() == 2) {
				$countReservedTablesForTwo += 1;
			}
		}
		//onderstaande TEST echo's laten het aantal gereserveerde tafels per tijdstip van de reservering zien, voorafgaand aan het (eventueel) boeken van de tafel (kan dus veranderen per reservering, is vooral overzichtelijk om te testen of systeem naar behoren werkt op zelfde overlappende data/tijdstippen)
		echo /*"Date/time: " . $reservation->getStartDate()->format("d-m-y ") . $reservation->getStartDate()->format("H:i ") . */ "Table for Six (reserved/total): " . $countReservedTablesForSix . "/" . TableForSix::$count . "<br>";
		echo /*"Date/time: " . $reservation->getStartDate()->format("d-m-y ") . $reservation->getStartDate()->format("H:i ") . */ "Table for Four (reserved/total): " . $countReservedTablesForFour . "/" . TableForFour::$count . "<br>";
		echo /*"Date/time: " . $reservation->getStartDate()->format("d-m-y ") . $reservation->getStartDate()->format("H:i ") . */ "Table for Two (reserved/total): " . $countReservedTablesForTwo . "/" . TableForTwo::$count . "<br>";
	}


	public static function bookTable(Reservation $reservation)
	{
		global $tables;
		global $check;
		global $count;
		$count = 0;
		global $numberCorresponds;
		global $toomuch;
		$onetwo = 0; //TESTVARIABELE
		$numberCorresponds = True;
		global $countReservedTablesForSix;
		global $countReservedTablesForFour;
		global $countReservedTablesForTwo;

		foreach ($tables as $table) {
			$onetwo += 1; //TESTVARIABELE
			$table->checkForValidReservation($reservation); //bij 'invalid' reservation: $count +=1
			// als $count gelijk is aan het aantal tafels in de array zijn alle tafels gereserveerd op bepaald tijdstip
			if ($count == count($tables)) {
				Reservation::$count--; // als reserveren niet mogelijk is, wordt de reservering niet geteld (BELANGRIJK: omdat het aanmaken van de Reservation $id gekoppeld is aan de $count)
				echo "Helaas zijn al onze tafels gereserveerd op " . $reservation->getStartDate()->format("d-m-y") . " om " . $reservation->getStartDate()->format("H:i") . " uur.";
			} else if ($check == False && $numberCorresponds == True) {
				$table->addReservation($reservation);
				echo "Tafel " . $table->getTableNumber() . " voor " . $table->getPersonsCount() . " personen gereserveerd voor "
					. $reservation->getGuest()->getFullName()
					. " op " . $reservation->getStartDate()->format("d-m-y") . " om " . $reservation->getStartDate()->format("H:i") . " uur.";
				break;
			} else if ($check == False && $numberCorresponds == False && $toomuch == True) { //dit scenario doet zich bijv. voor wanneer alle tafels voor 6 zijn gereserveerd, maar er nog wel tafels voor 4 en/of 2 beschikbaar zijn.
				if ($reservation->getNumberOfGuests() > 6) {
					//LET OP: reserveringen boven de 6 personen worden niet doorgezet, maar krijgen onderstaande melding REDEN: padafkhankelijkheid/incrementalisme (voortbordurend op eerder gemaakte keuzes is de 'technical debt' wat te hoog opgelopen om dit reserveersysteempje eenvoudig aan te passen - het zou eigenlijk opnieuw doordacht moeten worden maar mijn 'budget' is op...)
					Reservation::$count--; // als reserveren niet mogelijk is, wordt de reservering niet geteld (BELANGRIJK: omdat het aanmaken van de Reservation $id gekoppeld is aan de $count)
					echo "Voor groepsreserveringen: neemt u alstublieft telefonisch contact met ons op voor de mogelijkheden.";
					break;
				} else if ($countReservedTablesForSix < TableForSix::$count && $countReservedTablesForTwo < -1 + TableForTwo::$count) { //implicatie: reservering van 4 of 3 (anders was de 6 persoonstafel in de array eerst wel gepakt), terwijl alle 4 persoonstafels al bezet zijn (want toomuch = True); in dat geval checken of er nog minimaal 2 x 2 persoonstafel vrij zijn

					$x = $reservation->getNumberOfGuests() - 2;
					$rest = $reservation->getNumberOfGuests() - $x; // LET OP: rest = 2 (zie hierboven, momenteel werkt onderstaande niet bij: 3 x 2 persoonstafel en ook niet bij: 2 x 4 persoonstafel; alleen combinaties van 4 + 2 of 2 + 2 persoonstafel zijn mogelijk).
					if ($x == $table->getPersonsCount() or $x == -1 + $table->getPersonsCount()) {
						$table->addReservation($reservation);
						$reservation->setSplitReservation(True);
						$reservation->setSplitCount(2); // de $splitCount van de eerste reservering (default is immers 1) wordt gelijkgezet aan die van de $SplitReservation (in de $splitReservation array: index 2, hieronder)
						echo "Tafel " . $table->getTableNumber() . " voor " . $table->getPersonsCount() . " personen gereserveerd voor "
							. $reservation->getGuest()->getFullName()
							. " op " . $reservation->getStartDate()->format("d-m-y") . " om " . $reservation->getStartDate()->format("H:i") . " uur. PLUS: <br>";
						//onderstaande reservering zou worden overschreven wanneer er nog een soortgelijke reservering voor dezelfde of andere datum/tijd zou binnenkomen (daarom deze reserveringsvariabele niet gebruiken in de array(s) met reserveringen van de tafels op de index.php, maar de bestaande reservering verdubbelen met aanpassing van het aantal personen - dus minus het aantal personen van de eerste tafel) IDEM voor de andere $SplitReservations
						$SplitReservation = new Reservation($reservation->getStartDate()->format("y-m-d H:i"), $reservation->getGuest(), $rest, True, $reservation->getID(), 2); //de laatste drie argumenten staan in de $splitReservation array

						BookManager::bookTable($SplitReservation);
						echo " (Wees gerust, we zetten de tafels heus speciaal voor jullie groepje van " . $reservation->getNumberOfGuests() . " bij elkaar...)";
						break;
					}
				} else if ( /*$countReservedTablesForSix == TableForSix::$count &&/*voorgaande wordt al geimpliceerd*/$countReservedTablesForFour < TableForFour::$count && $countReservedTablesForTwo < TableForTwo::$count) { //oftewel: als de 6 persoonstafels gereserveerd zijn, maar er nog wel voldoende tafels van 4 en 2 beschikbaar zijn: splits de reservering

					$x = $reservation->getNumberOfGuests() - 2;
					$rest = $reservation->getNumberOfGuests() - $x; // LET OP: rest = 2 (zie hierboven, momenteel werkt deze methode nog niet bij: 2 x 4 persoonstafel; alleen combinaties van 4 + 2 of 2 + 2 persoonstafel zijn hier mogelijk - en hieronder staat een combinatie van 3 x 2 persoonstafels, een reservation split in driëen).
					if ($x == $table->getPersonsCount() or $x == -1 + $table->getPersonsCount()) {
						$table->addReservation($reservation);
						$reservation->setSplitReservation(True);
						$reservation->setSplitCount(2); // de $splitCount van de eerste reservering (default is immers 1) wordt gelijkgezet aan die van de $SplitReservation (in de $splitReservation array: index 2, hieronder)
						echo "Tafel " . $table->getTableNumber() . " voor " . $table->getPersonsCount() . " personen gereserveerd voor "
							. $reservation->getGuest()->getFullName()
							. " op " . $reservation->getStartDate()->format("d-m-y") . " om " . $reservation->getStartDate()->format("H:i") . " uur. PLUS: <br>";
						$SplitReservation = new Reservation($reservation->getStartDate()->format("y-m-d H:i"), $reservation->getGuest(), $rest, True, $reservation->getID(), 2); //de laatste drie argumenten staan in de $splitReservation array
						BookManager::bookTable($SplitReservation);
						echo " (Wees gerust, we zetten de tafels heus speciaal voor jullie groepje van " . $reservation->getNumberOfGuests() . " bij elkaar...)";
						break;
					}
				} else if ( /*$countReservedTablesForSix == TableForSix::$count &&/*voorgaande wordt al geimpliceerd*/$countReservedTablesForTwo < -1 + TableForTwo::$count && $reservation->getNumberOfGuests() == 4 | $reservation->getNumberOfGuests() == 3) { //OOK reservering van 4 of 3, terwijl alle 4 persoonstafels al bezet zijn (want toomuch = True) MAAR: de 6 persoonstafels zijn nu allemaal gereserveerd; in dat geval nogmaals checken of er nog minimaal 2 x 2 persoonstafel vrij zijn

					$x = $reservation->getNumberOfGuests() - 2;
					$rest = $reservation->getNumberOfGuests() - $x; // LET OP: rest = 2 (zie hierboven, momenteel werkt onderstaande niet bij: 3 x 2 persoonstafel en ook niet bij: 2 x 4 persoonstafel; alleen combinaties van 4 + 2 of 2 + 2 persoonstafel zijn mogelijk).
					if ($x == $table->getPersonsCount() or $x == -1 + $table->getPersonsCount()) {
						$table->addReservation($reservation);
						$reservation->setSplitReservation(True);
						$reservation->setSplitCount(2); // de $splitCount van de eerste reservering (default is immers 1) wordt gelijkgezet aan die van de $SplitReservation (in de $splitReservation array: index 2, hieronder)
						echo "Tafel " . $table->getTableNumber() . " voor " . $table->getPersonsCount() . " personen gereserveerd voor "
							. $reservation->getGuest()->getFullName()
							. " op " . $reservation->getStartDate()->format("d-m-y") . " om " . $reservation->getStartDate()->format("H:i") . " uur. PLUS: <br>";
						$SplitReservation = new Reservation($reservation->getStartDate()->format("y-m-d H:i"), $reservation->getGuest(), $rest, True, $reservation->getID(), 2); //de laatste drie argumenten staan in de $splitReservation array

						BookManager::bookTable($SplitReservation);
						echo " (Wees gerust, we zetten de tafels heus speciaal voor jullie groepje van " . $reservation->getNumberOfGuests() . " bij elkaar...)";
						break;
					}
				} else if ( /*$countReservedTablesForFour == TableForFour::$count && /*voorgaande wordt al geimpliceerd*/$countReservedTablesForTwo < -2 + TableForTwo::$count && $reservation->getNumberOfGuests() == 6 | $reservation->getNumberOfGuests() == 5) { //oftewel: als de 6 en de 4 persoonstafels gereserveerd zijn, maar er nog wel voldoende tafels van 2 beschikbaar zijn: splits de reservering in drieën

					$x = $reservation->getNumberOfGuests() - 4; //oftewel: $x = 1 of 2
					$rest1 = $reservation->getNumberOfGuests() - $x - 2; // oftewel: $rest1 = 2 (zie bovenstaande comment bij LET OP)
					$rest2 = $reservation->getNumberOfGuests() - $x - 2; // oftewel: $rest2 = 2 (zie bovenstaande comment bij LET OP)
					if ($x == $table->getPersonsCount() or $x == -1 + $table->getPersonsCount()) { //oftewel: een 2 persoonstafel wordt gereserveerd
						$table->addReservation($reservation);
						$reservation->setSplitReservation(True);
						$reservation->setSplitCount(3); // de $splitCount van de eerste reservering (default is immers 1) wordt gelijkgezet aan die van de $SplitReservation (in de $splitReservation array: index 2, hieronder)
						echo "Tafel " . $table->getTableNumber() . " voor " . $table->getPersonsCount() . " personen gereserveerd voor "
							. $reservation->getGuest()->getFullName()
							. " op " . $reservation->getStartDate()->format("d-m-y") . " om " . $reservation->getStartDate()->format("H:i") . " uur. PLUS2: <br>";
						$SplitReservation1 = new Reservation($reservation->getStartDate()->format("y-m-d H:i"), $reservation->getGuest(), $rest1, True, $reservation->getID(), 3); //de laatste drie argumenten staan in de $splitReservation array
						BookManager::bookTable($SplitReservation1);
						echo " (Wees gerust, we zetten de tafels heus speciaal voor jullie groepje van " . $reservation->getNumberOfGuests() . " bij elkaar...)<br>";
						$SplitReservation2 = new Reservation($reservation->getStartDate()->format("y-m-d H:i"), $reservation->getGuest(), $rest2, True, $reservation->getID(), 3); //de laatste drie argumenten staan in de $splitReservation array
						BookManager::bookTable($SplitReservation2);
						echo " (Wees gerust, we zetten de tafels heus speciaal voor jullie groepje van " . $reservation->getNumberOfGuests() . " bij elkaar...)";
						break;
					}
				}

				echo "Helaas zijn er geen tafels voor " . $reservation->getNumberOfGuests() . " personen meer beschikbaar op dit tijdstip.";
				break;
			} else if ($check == False && $numberCorresponds == False && $toomuch == False) { //bijv. reservering voor 1/2 personen voor 4 of 6 persoonstafel of reservering voor 3/4 personen voor 6 persoonstafel
				if ($countReservedTablesForFour < TableForFour::$count && $countReservedTablesForTwo == TableForTwo::$count && $reservation->getNumberOfGuests() >= -3 + $table->getPersonsCount()) { //oftewel: als alle 2 persoonstafels gereserveerd zijn, pak dan een 4 persoonstafel (ook in geval 1 persoonsreservering)
					$table->addReservation($reservation);
					echo "Tafel " . $table->getTableNumber() . " voor " . $table->getPersonsCount() . " personen gereserveerd voor "
						. $reservation->getGuest()->getFullName()
						. " op " . $reservation->getStartDate()->format("d-m-y") . " om " . $reservation->getStartDate()->format("H:i") . " uur.";
					break;
				} else if ($countReservedTablesForSix < TableForSix::$count && $countReservedTablesForFour == TableForFour::$count && $countReservedTablesForTwo >= -1 + TableForTwo::$count) { //oftewel: als alle 4 en 2 persoonstafels (op evt 1 na) zijn gereserveerd, maar er zijn nog wel 6 persoonstafels vrij: reserveer in dit uiterste geval toch een 6 persoonstafel voor 3 of 4 personen. LET OP: deze ELSE IF kan op zichzelf GEEN tafel voor 6 personen reserveren bij een reservering voor 2 personen, maar WEL in combinatie met bovenstaande IF (als zowel alle 2 als 4 persoonstafels zijn gereserveerd: pak dan een 6 persoonstafel (ook in geval 1 persoonsreservering))
					$table->addReservation($reservation);
					echo "Tafel " . $table->getTableNumber() . " voor " . $table->getPersonsCount() . " personen gereserveerd voor "
						. $reservation->getGuest()->getFullName()
						. " op " . $reservation->getStartDate()->format("d-m-y") . " om " . $reservation->getStartDate()->format("H:i") . " uur.";
					break;
				} else {
					echo $onetwo . ". (TEST: Too few persons for this table.)<br>";
				}
			} else if ($check == True) {
				echo $onetwo . ". (TEST: Table reserved.)<br>";
			}
		}
		// zo zag deze class::functie (bookTable was bookRoom) er oorspronkelijk - d.w.z. het voorbeeld van Github - uit (met Exception-throw); dit was de enige functie in de class BookManager:
		// public static function bookRoom(Room $room, Reservation $reservation)
		// {
		// 	try {
		// 		$room->addReservation($reservation);
		// 		echo "\nRoom " . $room->getRoomNumber() . " successfully booked for "
		// 			. $reservation->getGuest()->getFullName()
		// 			. " from " . $reservation->getStartDate()->format("d-m-y")
		// 			. " to " . $reservation->getEndDate()->format("d-m-y");
		// 	} catch (EReservationException $ex) {
		// 		echo PHP_EOL . $ex->getMessage();
		// 	}
		// }
	}

	public static function cancelTable(Reservation $reservation)
	{
		global $tables;
		global $key; //wordt indien nodig (i.e. bij een SplitReservation) overschreven in de cancelTables($id) functie (zie hieronder)
		global $id;

		foreach ($tables as $table) {

			if (($key = array_search($reservation, $table->getReservations())) !== false && empty($reservation->getSplitReservation())) {
				$table->removeReservation();
				Reservation::$count--; //de reservering wordt afgeteld (BELANGRIJK: omdat het aanmaken van de Reservation $id gekoppeld is aan de $count)
				break; //break kan hier omdat er maar 1 reservering te verwijderen is
			} elseif (($key = array_search($reservation, $table->getReservations())) !== false && !empty($reservation->getSplitReservation())) {
				if ($reservation->getSplitReservation() === True) {
					$id = $reservation->getID(); //$id is bij SplitReservation nodig om ook de tweede (en evt. derde) reservering te verwijderen
					Bookmanager::cancelTableS($id); //hiermee worden alle SplitReservations verwijderd (zie functie hieronder)
					Reservation::$count--; //de SplitReservation wordt 1 keer afgeteld (BELANGRIJK: omdat het aanmaken van de Reservation $id gekoppeld is aan de $count)
					break; //want deze loop/functie heeft zijn taak gedaan (de SplitReservations worden nu op basis van hun $id verwijderd)
				}
			}
		}
	}

	public static function cancelTableS($id) //op basis van het $id van de reservering worden de SplitReservations eruitgehaald
	{
		global $tables;
		global $key; //wordt hieronder onderschreven
		global $id; //de $id van de reservering is in de cancelTable() vastgesteld (zie hierboven)
		global $count;
		$count = 0;
		$break = False;

		foreach ($tables as $table) {
			foreach ($table->getReservations() as $reservation) {
				if ($reservation->getID() === $id) {
					$key = array_search($reservation, $table->getReservations()); // de global $key (index) wordt overschreven (zodat het indexgetal in de lijst(en) met reserveringen (per tafel) in alle gevallen overeenstemt met de te verwijderen reservering)
					// echo "TESTKEY " . $key . "<br>";
					$table->removeReservation();
					$count += 1;
					if ($count === $reservation->getSplitCount()) { // wanneer alle SplitReservations van de betreffende reservering zijn verwijderd...
						$break = True;
						break; // ... de foreach $reservations loop van de tafel wordt gebreakt EN ...
					}
				}
			}
			if ($break === True) {
				break; // ... de foreach $tables loop wordt OOK gebreakt
			}
		}
	}
}
