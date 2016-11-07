<?php

/* Last modified 08.07.2013
Last version of pg_connect need a string as parameter
T. Produit
*/

// connects to Postgres database and returns the PostgreSQL connection resource $dbh (database handle)
function db_connect() {
	$dbh = pg_connect("host=localhost port=5432 dbname=GenMon_CH user=geome_admin password=geome");
	return $dbh;
}

// disconnects from database
function db_disconnect($dbh) {
	pg_close($dbh);
}

?>
