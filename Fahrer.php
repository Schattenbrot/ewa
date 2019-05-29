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
			$orderID = 0;
			$countPrintedOrders = 0;
			$formid = 0;
			foreach($this->order_list as $value) {
        $_order = $value;
        $currOrderID = htmlspecialchars($_order->getOrderID());
        $currPrice = htmlspecialchars($_order->getPrice());
        $currAdresse = htmlspecialchars($_order->getAdresse());
				$currPizzaName = htmlspecialchars($_order->getPizzaName());
				$currStatus = htmlspecialchars($_order->getStatus());
				if ($currStatus < 3) {
					$orderID = $_order->getOrderID();
					continue;
				} else if ($currStatus >= 3 && $currStatus <= 4 && $_order->getOrderID() != $orderID) {
					$countPrintedOrders++;
					echo <<<EOT
					<form action="fahrer.php" method="post" id="{$formid}">
						<p>
							Order: {$currOrderID}
							Preis: {$currPrice}€
							Adresse: {$currAdresse}
							<input type="hidden" name="order" value="{$currOrderID}">
EOT;
							if ($currStatus == 3) {
								echo <<<EOT
								<input type="radio" name="status" value="fertig" checked>
								<input type="radio" name="status" value="inZustellung" onclick="document.forms['{$formid}'].submit();">
								<input type="radio" name="status" value="zugestellt" onclick="document.forms['{$formid}'].submit();">
EOT;
							}
							if ($currStatus == 4) {
								echo <<<EOT
								<input type="radio" name="status" value="fertig" onclick="document.forms['{$formid}'].submit();">
								<input type="radio" name="status" value="inZustellung" checked>
								<input type="radio" name="status" value="zugestellt" onclick="document.forms['{$formid}'].submit();">
EOT;
							}
							echo <<<EOT
							Bestellung: {$currPizzaName}
						</p>
					</form>
EOT;
					$formid++;
        }
			}
		}
		$this->generatePageFooter();
	}

	protected function processReceivedData() {
    parent::processReceivedData();
    if(isset($_POST['status']) &&
      isset($_POST['order']) && is_numeric($_POST['order'])) {
			$_POST['status'] = $this->_database->real_escape_string($_POST['status']);
			//$_POST['order'] = $this->_database->real_escape_string($_POST['order']);

      $sqlpost;
      if ($_POST['status'] == "zugestellt") {
        $sqlpost = "DELETE FROM bestellung WHERE BestellungID='{$_POST['order']}'";
      } else {
        $sqlpost = "UPDATE bestelltepizza SET Status='{$_POST['status']}'
          WHERE fBestellungID={$_POST['order']}";
      }
			$recordset = $this->_database->query($sqlpost);
			//header('Location: fahrer.php');
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