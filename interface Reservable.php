<?php

interface Reservable
{
    function addReservation(Reservation $reservation);

    function removeReservation(); //geen parameter, zie comment Table::removeReservation()
}
