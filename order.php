<?php

require_once './Pizza.php';

class Order {
	private $orderID;
	private $adresse;
	private $ordertime;
	private $pizza_list = array();

	//function __construct($adresse) {
	//	$this->adresse = $adresse;
	//}

	function __construct($orderID, $adresse, $ordertime) {
		$this->orderID = $orderID;
		$this->adresse = $adresse;
		$this->ordertime = $ordertime;
	} 
	function addPizza($pizza) {
		$pizza_list = $pizza;
	}
	function getOrderID() {
		return $this->orderID;
	}

	function getAdresse() {
		return $this->adresse;
	}

	function getPizzaList() {
    return $this->pizza_list;
  }
}

?>