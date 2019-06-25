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

    $sql = "SELECT angebot.PizzaName, bestelltepizza.status, bestelltepizza.PizzaID
      FROM bestelltepizza 
      JOIN angebot ON bestelltepizza.fPizzaNummer=angebot.PizzaNummer
      WHERE bestelltepizza.fBestellungID={$this->sessionId}
      ORDER BY bestelltepizza.fBestellungID ASC";
    
    $recordset = $this->_database->query($sql);
    if (!$recordset)
    {
      throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
    }

    while ($record = $recordset->fetch_assoc()) {
      $pizza = array($record['PizzaName'], $record['status'], $record['PizzaID']);
      $this->pizza_list[] = $pizza;
    }

    $recordset->free();

    return $pizza_list;
  }

  protected function generateView() {
    $this->getViewData();
    $this->generatePageHeader('Kunde');
/*
    echo <<<EOT
    <section id="menu">
      <nav>
        <ul>
          <li><a href="bestellung.php">Bestellung</a></li>
          <li><a href="baecker.php">Bäcker</a></li>
          <li><a href="fahrer.php">Fahrer</a></li>
          <li class="current"><a href="kunde.php">Kunde</a></li>
        </ul>
      </nav>
    </section>
    */
    echo <<<EOT
    <section id="sectionKunde1">
    <h1>Bestellung: {$this->sessionId}</h1>

    <script src='javascript/StatusUpdate.js'></script>
    <div id="div1">
    </div>
    <input type="button" name="redirect" value="Neue Bestellung" tabindex="5" onclick="location.href='bestellung.php';">
EOT;
    echo '</section>';
    $this->generatePageFooter();
  }

  protected function processReceivedData() {
    parent::processReceivedData();
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