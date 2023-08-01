# Reservation system (for a restaurant)

This reservation system for - in this case - a restaurant is based on the OOP architecture of a hotel room reservation system I found on Github: https://github.com/Risorm/OOP/blob/master/9.%20OOP-in-PHP-Homework/Problem%201.%20Hotel%20Reservation%20System

In order to make it work for a restaurant I changed it quite a bit. For one: I skipped one of the original classes, that for error handling (class EReservationException). The class BookManager in particular, where this error handling was implemented, has been overhauled completely and extended to quite a degree in order to make it work in different scenario's (depending on availability of tables of different size, to reserve tables for different numbers of guests; and where needed, reservations are split and more tables are reserved under one reservation id).

Besides, the abstract class Room (in the Github example) also needed to change considerably to become the abstract class Table: a lot of functionality could simply be deleted (contrary to an abstract room an abstract table has not much relevant extra’s); the checkForValidReservation() method needed addional checks to make sure the right reservation (number of guests) goes to the right table(s); some of the methods (for adding/removing reservations) have been split and code was moved to the class BookManager (these two main classes and their methods now communicate through extensive use of the global keyword). Some relevant code snippets I changed/moved have been commented so as to keep the original code for comparison.

The method for removing reservations, also in the abstract class Table - which was never implemented in the original Github example - now communicates with two (new) methods in the BookManager class to allow for reservations which are split into more reservations to be removed completely.

All in all this project is the result of an incremental approach: for conveniency reasons I chose to look for an example and build further on that OOP architecture. During the process it turned out to be a more complex project than imagined, because new considerations of the functionality of the system popped up. Of course, I could have chosen to keep the system simpler in the first place, why split reservations, say. Eventually, complexity had reached a threshold and I decided that reservations for more than 6 persons would get a simple message: phone us! (Maybe if I had started thinking about what functionality this system should have beforehand, this system would have turned out simpler.)

Note: the comments in the project are in dutch.

