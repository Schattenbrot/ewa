<?php

require_once './Pizza.php';

class Order {
	private $orderID;
	private $adresse;
	private $status;
	private $pizzaName = array();
	private $price;
	private $pizzaID;

	function __construct($orderID, $adresse, $pizzaName, $status, $price) {
		$this->orderID = $orderID;
		$this->adresse = $adresse;
		$this->pizzaName[] = $pizzaName;
		$this->status = $status;
		$this->price = $price;
	} 
	function getOrderID() {
		return $this->orderID;
	}

	function setPizzaID($id) {
		$this->pizzaID = $id;
  }
  function getPizzaID() {
    return $this->pizzaID;
  }

	function getAdresse() {
		return $this->adresse;
	}
	
	function getStatus() {
		if($this->status == "bestellt") {
			return 1;
		} else if ($this->status == "inZubereitung") {
			return 2;
		} else if ($this->status == "fertig") {
			return 3;
		} else if ($this->status == "inZustellung") {
			return 4;
		} else if ($this->status == "zugestellt") {
			return 5;
		} else {
			return -1;
		}
  }
	
	function addPizza($pizza) {
		$this->pizzaName[] = $pizza;
	}

  function getPizzaName() {
    return $this->pizzaName;
  }

  function getPrice() {
    return $this->price;
	}
	
	function addPrice($price) {
		$this->price += $price;
	}
}

?>