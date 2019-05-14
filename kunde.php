<?php
/**
 * PHP Version X
 * @package pizzaservice
 * @author Aaron Machill
 * @author Markus Stuber
 * @Release 1.0
 */

require_once './Page.php';
require_once './Order.php';

class Customer extends Page {
  private $sessionId;
  private $orderID;

  protected function __construct() {
    parent::__construct();
    if (!isset($_SESSION['sessionId'])) {
        $_SESSION['sessionId'] = md5(uniqid(mt_rand()));
    }
    $this->sessionId = $_SESSION['sessionId'];
  }

  protected function __destruct() {
    parent::__destruct();
  }

  protected function getViewData() {
    $pizza_list = array();

    $sql = "SELECT angebot.PizzaName, bestelltepizza.status FROM bestelltepizza 
      JOIN angebot ON bestelltepizza.fPizzaNummer=angebot.PizzaNummer
      ORDER BY bestelltepizza.fBestellungID ASC";
    
    $recordset = $this->_database->query($sql);
    if (!$recordset)
    {
      throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
    }

    while ($record = $recordset->fetch_assoc()) {
      $pizza = array($record['PizzaName'], $record['status']);
      //$pizza = new Pizza($record['PizzaName'], $record['Bilddatei'], $record['Preis']);
      $this->pizza_list[] = $pizza;
    }

    $recordset->free();

    return $pizza_list;
  }

  protected function generateView() {
    $this->getViewData();
    $this->generatePageHeader('Kunde');
    echo <<<EOT
    <nav>
      <ul>
        <li><a href="bestellung.php">Bestellung</bestellung></a></li>
        <li><a href="baecker.php">Bäcker</a></li>
        <li><a href="fahrer.php">Fahrer</a></li>
        <li class="current"><a href="kunde.php">Kunde</a></li>
      </ul>
    </nav>

    <h2>Bestellung: {$this->orderID}</h2>
EOT;
    if (isset($this->pizza_list)) {
      foreach ($this->pizza_list as $_pizza) {
        echo <<<EOT
        <p>{$_pizza[0]}: {$_pizza[1]}</p>
EOT;
      }
    }

    $this->generatePageFooter();
  }

  protected function processReceivedData() {
    parent::processReceivedData();
    if(isset($_POST['adresse']) && isset($_POST['basket'])) {
      $date = new DateTime();
      $orderTime = $date->format('Y-m-d H:i:s');

      $sqlpost = "INSERT INTO bestellung (Adresse, Bestellzeitpunkt) VALUES ('{$_POST['adresse']}', '{$orderTime}')";
      $recordset = $this->_database->query($sqlpost);

      $sqlGetOrderID = "SELECT BestellungID FROM bestellung WHERE Adresse='{$_POST['adresse']}' AND Bestellzeitpunkt='{$orderTime}'";
      $recordset = $this->_database->query($sqlGetOrderID);
      while ($record = $recordset->fetch_assoc()) {
        $this->orderID = $record['BestellungID'];
      }
      foreach ($_POST['basket'] as $pizzaid) {
        //$pizzaid = $_POST['basket'][0];
        $sqlInsertBestelltePizza = "INSERT INTO bestelltepizza (fBestellungID, fPizzaNummer) VALUES ('{$this->orderID}', '{$pizzaid}')";
        $recordset = $this->_database->query($sqlInsertBestelltePizza);
      }
      header('Location: kunde.php');
    }

    //$page = 'kunde.php';
    //header('Location: ' . $page, true, 303);
  }

	public static function main() {
		try {
			$page = new Customer();
			$page->processReceivedData();
			$page->generateView();
		}
		catch (Exception $e) {
			header("Content-type: text/plain; charset=UTF-8");
			echo $e->getMessage();
		}
	}
}

Customer::main();
?>