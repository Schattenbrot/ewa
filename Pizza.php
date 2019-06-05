<?php

class Pizza {
  //Variables
  var $pizzaID;
  var $PizzaName;
  var $Bilddatei;
  var $Preis;

  //constructor
  function __construct($PizzaName, $Bilddatei, $Preis) {
    $this->PizzaName = $PizzaName;
    $this->Bilddatei = $Bilddatei;
    $this->Preis = $Preis; 
  }

  function setPizzaID($ID) {
    $this->pizzaID = $ID;
  }
}

?>