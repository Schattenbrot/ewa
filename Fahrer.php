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

	protected function orderExists($order_list, $bestellungID) {
		if (empty($order_list)) {
			print_r("YEP EMPTY!");
		}
		foreach($order_list as $value) {
			if ($value->getOrderID() == $bestellungID) {
				return true;
			}
		}
		return false;
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

		$iterator = 0;
		while ($record = $recordset->fetch_assoc()) {
			if(empty($this->order_list)) {
				$order = new Order($record['BestellungID'], $record['Adresse'], $record['PizzaName'], $record['Status'], $record['Preis']);
				$this->order_list[] = $order;
				$iterator++;
			} else if ($this->orderExists($this->order_list, $record['BestellungID'])) {
				$this->order_list[$iterator-1]->addPizza($record['PizzaName']);
				$this->order_list[$iterator-1]->addPrice($record['Preis']);
			} else if (!$this->orderExists($this->order_list, $record['BestellungID'])) {
				$order = new Order($record['BestellungID'], $record['Adresse'], $record['PizzaName'], $record['Status'], $record['Preis']);
				$this->order_list[] = $order;
				$iterator++;
			}
		}

		$recordset->free();
		return $order_list;
	}

	protected function generateView() {
		$this->getViewData();
		$this->generatePageHeader('Fahrer');
		/*
		echo <<<EOT
		<section id="menu">
	    <nav>
				<ul>
					<li><a href="bestellung.php">Bestellung</a></li>
					<li><a href="baecker.php">Bäcker</a></li>
					<li class="current"><a href="fahrer.php">Fahrer</a></li>
					<li><a href="kunde.php">Kunde</a></li>
				</ul>
			</nav>
		</section>
*/
		echo <<<EOT
		<section id="fahrerMainSection">
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
				$currPizza = "";
				$firstElem = true;
				foreach($_order->getPizzaName() as $pizza) {
					$pizza = htmlspecialchars($pizza);
					if ($firstElem) {
						$currPizza = $currPizza . $pizza;
						$firstElem = false;
					} else {
						$currPizza = $currPizza . ", " . $pizza;
					}
				}
				$currStatus = htmlspecialchars($_order->getStatus());
				if ($currStatus < 3) {
					$orderID = $_order->getOrderID();
					continue;
				} else if ($currStatus >= 3 && $currStatus <= 4 && $_order->getOrderID() != $orderID) {
					$countPrintedOrders++;
					echo <<<EOT
					<div id="div1">
					<form action="fahrer.php" method="post" id="a{$formid}">
						<p id="fahrerP1">
							Adresse: {$currAdresse}
						</p>
						<p id="fahrerP3">
							Preis: {$currPrice}€
							<input type="hidden" name="order" value="{$currOrderID}">
						</p>
						<p id="fahrerP2">
							{$currPizza}
						</p>
EOT;
							if ($currStatus == 3) {
								echo <<<EOT
								<ul>
									<li>
										<label for="radio1">Fertig</label>
										<input type="radio" id="radio1" name="status" value="fertig" checked>
									</li>
									<li>
										<label for="radio2">In Zustellung</label>
										<input type="radio" id="radio2" name="status" value="inZustellung" onclick="document.forms['a{$formid}'].submit();">
									</li>
									<li>
										<label for="radio3">Zugestellt</label>
										<input type="radio" id="radio3" name="status" value="zugestellt" onclick="document.forms['a{$formid}'].submit();">
									</li>
								</ul>
EOT;
							}
							if ($currStatus == 4) {
								echo <<<EOT
								<ul>
									<li>
										<label for="radio1">Fertig</label>
										<input type="radio" id="radio1" name="status" value="fertig" onclick="document.forms['a{$formid}'].submit();">
									</li>
									<li>
										<label for="radio2">In Zustellung</label>
										<input type="radio" id="radio2" name="status" value="inZustellung" checked>
									</li>
									<li>
										<label for="radio3">Zugestellt</label>
										<input type="radio" id="radio3" name="status" value="zugestellt" onclick="document.forms['a{$formid}'].submit();">
									</li>
								</ul>
EOT;
							}
echo <<<EOT
					</form>
					</div>
EOT;
					$formid++;
        }
			}
		}
	
		echo '</section>';
		$this->generatePageFooter();
	}

	protected function processReceivedData() {
    parent::processReceivedData();
    if(isset($_POST['status']) &&
      isset($_POST['order']) && is_numeric($_POST['order'])) {
			$_POST['status'] = $this->_database->real_escape_string($_POST['status']);

      $sqlpost;
      if ($_POST['status'] == "zugestellt") {
        $sqlpost = "DELETE FROM bestellung WHERE BestellungID='{$_POST['order']}'";
      } else {
        $sqlpost = "UPDATE bestelltepizza SET Status='{$_POST['status']}'
          WHERE fBestellungID={$_POST['order']}";
      }
			$recordset = $this->_database->query($sqlpost);
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