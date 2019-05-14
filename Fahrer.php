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

class Driver extends Page {
	private $sessionId;
	
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
		$order_list = array();
		
		$sql = "SELECT bestellung.BestellungID, bestellung.Adresse, 
			bestellung.Bestellzeitpunkt, bestelltepizza.Status 
      FROM bestelltepizza JOIN bestellung 
      ON bestelltepizza.fBestellungID=bestellung.BestellungID";

		$recordset = $this->_database->query($sql);
		if(!$recordset) {
			throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
		}

		while ($record = $recordset->fetch_assoc()) {
			$order = new Order($record['BestellungID'], $record['Adresse'], $record['Bestellzeitpunkt'], $record['Status']);
			$this->order_list[] = $order;
		}

		$recordset->free();
		return $order_list;
	}

	protected function generateView() {
		$this->getViewData();
    $this->generatePageHeader('Fahrer');
    
		if(isset($this->order_list)) {
			foreach($this->order_list as $value) {
        $_order = $value;
        if ($_order->getStatus() != 'bestellt' && $_order->getStatus() != 'inZubereitung') {
          echo <<<EOT
          <p>
            Order: {$_order->getOrderID()}
            Preis: XXX€
            Adresse: {$_order->getAdresse()}
            <label for="statusList">Status: </label>
            <select name ="status" id="statusList" size="1">
EOT;
            if ($_order->getStatus() == 'fertig') {
              echo "<option value=" . 1 . " selected>fertig</option>";
              echo "<option value=" . 2 . ">unterwegs</option>";
              echo "<option value=" . 3 . ">geliefert</option>";
            }
            if ($_order->getStatus() == 'inZustellung') {
              echo "<option value=" . 1 . ">fertig</option>";
              echo "<option value=" . 2 . " selected>unterwegs</option>";
              echo "<option value=" . 3 . ">geliefert</option>";
            }
            if ($_order->getStatus() == 'zugestellt') {
              echo "<option value=" . 1 . ">fertig</option>";
              echo "<option value=" . 2 . ">unterwegs</option>";
              echo "<option value=" . 3 . " selected>geliefert</option>";
            }
            echo <<<EOT
            </select>
              Bestellung: Pizza1, Pizza2
            </p>
EOT;
        }
			}
		}
		$this->generatePageFooter();
	}

	protected function processReceivedData() {
		parent::processReceivedData();
	}

	public static function main() {
		try {
			$page = new Driver();
			$page->processReceivedData();
			$page->generateView();
		}
		catch (Exception $e) {
			header("Content-type: text/plain; charset=UTF-8");
			echo $e->getMessage();
		}
	}
}

Driver::main();

?>