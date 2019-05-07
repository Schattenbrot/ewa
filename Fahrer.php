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
		if (!isset($_SESSION[sessionId])) {
			$_SESSION['sessionId'] = md5(uniqid(mt_rand()));
		}
		$this->sessionId = $_SESSION['sessionId'];
	}

	protected function __destruct() {
		parent::__destruct();
	}

	protected function getViewData() {
		$order_list[] = array();

		$sql = "SELECT BestellungID, Adresse, Bestellzeitpunkt FROM bestellung";

		$recordset = $this->_database->query($sql);
		if(!recordset) {
			throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
		}

		while ($record = $recordset->fetch_assoc()) {
			$order = new Order($record['BestellungID'], $record['Adresse'], $record['Bestellzeitpunkt']);
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
				echo <<<EOT
				<p>
					Order: {$_order->getOrderID()}
					Preis: XXX€
					Adresse: {$_order->getAdresse()}
					<label for="statusList">Status: </label>
					<select name ="status" id="statusList" size="1">
						<option value="1">fertig</option>
						<option value="2">unterwegs</option>
						<option value="3">geliefert</option>
					</select>
					Bestellung: Pizza1, Pizza2
				</p>
EOT;
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