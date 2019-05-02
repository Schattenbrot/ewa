<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset=utf-8>
    <title>Fahrer</title>
  </head>
  <body>
    <p>Bestellung 123</p>
    <p>Preis: 10.5€</p>
    <p>Adresse: Pfungstadt Eschollbrücken, Fichtenweg 3</p>
    <p>
      <label for="statusList">Status: </label>
      <select name ="status" id="statusList" size="1">
	<option value="1">fertig</option>
	<option value="2">unterwegs</option>
	<option value="3">geliefert</option>
      </select>
    </p>
    <p>Bestellung:  Pizza1, Pizza2</p>
    <p>Preis:	    14.50€</p>
  </body>
</html>


<?php
/**
 * PHP Version X
 * @package pizzaservice
 * @author Aaron Machill
 * @author Markus Stuber
 * @Release 1.0
 */

 require_once './Page.php';

class Driver extends Page {
		private $sessionId;
		
		protected function __construct() {

		}

}

?>