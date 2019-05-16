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
			bestelltepizza.Status, angebot.PizzaName, angebot.Preis
      FROM bestelltepizza 
			LEFT JOIN bestellung ON bestelltepizza.fBestellungID=bestellung.BestellungID
			LEFT JOIN angebot ON bestelltepizza.fPizzaNummer=angebot.PizzaNummer
			ORDER BY bestelltepizza.fBestellungID ASC, bestelltepizza.Status ASC";

		$recordset = $this->_database->query($sql);
		if(!$recordset) {
			throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
		}

		while ($record = $recordset->fetch_assoc()) {
			$record['Adresse'] = htmlspecialchars($record['Adresse']);
			$order = new Order($record['BestellungID'], $record['Adresse'], $record['PizzaName'], $record['Status'], $record['Preis']);
			$this->order_list[] = $order;
		}

		$recordset->free();


		return $order_list;
	}

	protected function generateView() {
		$this->getViewData();
    $this->generatePageHeader('Fahrer');
    echo <<<EOT
    <nav>
      <ul>
        <li><a href="bestellung.php">Bestellung</a></li>
        <li class="current"><a href="baecker.php">Bäcker</a></li>
        <li><a href="fahrer.php">Fahrer</a></li>
        <li><a href="kunde.php">Kunde</a></li>
      </ul>
		</nav>
EOT;
		if(isset($this->order_list)) {
			$orderID;
			$countPrintedOrders = 0;
			$tabindex = 1;
			foreach($this->order_list as $value) {
				$_order = $value;
				if ($_order->getStatus() < 3) {
					$orderID = $_order->getOrderID();
					continue;
				} else if ($_order->getStatus() >= 3 && $_order->getOrderID() != $orderID) {
					$countPrintedOrders++;
					echo <<<EOT
					<form action="fahrer.php" method="post">
						<p>
							Order: {$_order->getOrderID()}
							Preis: {$_order->getPrice()}€
							Adresse: {$_order->getAdresse()}
							<label for="statusList_{$countPrintedOrders}">Status: </label>
							<select name ="status" id="statusList_{$countPrintedOrders}" size="1" tabindex="{$tabindex}">
EOT;
							$tabindex++;
							if ($_order->getStatus() == 3) {
								echo "<option value=" . 3 . " selected>fertig</option>";
								echo "<option value=" . 4 . ">unterwegs</option>";
								echo "<option value=" . 5 . ">geliefert</option>";
							}
							if ($_order->getStatus() == 4) {
								echo "<option value=" . 3 . ">fertig</option>";
								echo "<option value=" . 4 . " selected>unterwegs</option>";
								echo "<option value=" . 5 . ">geliefert</option>";
							}
							if ($_order->getStatus() == 5) {
								echo "<option value=" . 3 . ">fertig</option>";
								echo "<option value=" . 4 . ">unterwegs</option>";
								echo "<option value=" . 5 . " selected>geliefert</option>";
							}
							echo <<<EOT
							</select>
							Bestellung: {$_order->getPizzaName()}
							<input type="submit" name="order" value="{$_order->getOrderID()}" tabindex={$tabindex}>
						</p>
					</form>
EOT;
					$tabindex++;
        }
			}
		}
		$this->generatePageFooter();
	}

	protected function processReceivedData() {
		parent::processReceivedData();
		
		if(isset($_POST['status'])) {
			$sqlpost = "UPDATE bestelltepizza SET Status='{$_POST['status']}'
				WHERE fBestellungID='{$_POST['order']}'";
			$recordset = $this->_database->query($sqlpost);
			header('Location: fahrer.php');
		}
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