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

class Baker extends Page {
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
    $pizza_list[] = array();

		$sql = "SELECT bestellung.BestellungID, bestellung.Adresse,
			bestelltepizza.Status, bestelltepizza.PizzaID, angebot.PizzaName, angebot.Preis
      FROM bestelltepizza 
			LEFT JOIN bestellung ON bestelltepizza.fBestellungID=bestellung.BestellungID
			LEFT JOIN angebot ON bestelltepizza.fPizzaNummer=angebot.PizzaNummer
      ORDER BY bestelltepizza.fBestellungID ASC";

    $recordset = $this->_database->query($sql);
    if(!$recordset) {
      throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error());
    }

    while ($record = $recordset->fetch_assoc()) {
      $order = new Order($record['BestellungID'], $record['Adresse'], $record['PizzaName'], $record['Status'], $record['Preis']);
      $order->setPizzaID($record['PizzaID']);
      $this->pizza_list[] = $order;
    }
    $recordset->free();
    return $pizza_list;
  }

  protected function generateView() {
    $this->getViewData();
    $this->generatePageHeader('Bäcker');

    echo <<<EOT
    <nav>
      <ul>
        <li><a href="bestellung.php">Bestellung</a></li>
        <li class="current"><a href="baecker.php">Bäcker</a></li>
        <li><a href="fahrer.php">Fahrer</a></li>
        <li><a href="kunde.php">Kunde</a></li>
      </ul>
    </nav>

    <h3>Bestellung</h3>
    <p>Bestellt</p>
    <p>Ofen</p>
    <p>Fertig</p>
EOT;
    if(isset($this->pizza_list)) {
      $formid = 0;
      foreach($this->pizza_list as $value) {
        $_pizza = $value;
        if ($_pizza->getStatus() <= 3){
            echo <<<EOT
            <form action="baecker.php" method="post" id="{$formid}">
              <p>{$_pizza->getOrderID()}: {$_pizza->getPizzaName()}
              <input type="hidden" name="changedPizza" value="{$_pizza->getPizzaID()}">
EOT;
              if ($_pizza->getStatus() == 1) {
                echo <<<EOT
                <input type="radio" name="radio" value="bestellt" checked>
                <input type="radio" name="radio" value="inZubereitung" onclick="document.forms['{$formid}'].submit();">
                <input type="radio" name="radio" value="fertig" onclick="document.forms['{$formid}'].submit();">
EOT;
              }
              if ($_pizza->getStatus() == 2) {
                echo <<<EOT
                <input type="radio" name="radio" value="bestellt" onclick="document.forms['{$formid}'].submit();">
                <input type="radio" name="radio" value="inZubereitung" checked>
                <input type="radio" name="radio" value="fertig" onclick="document.forms['{$formid}'].submit();">
EOT;
              }
              if ($_pizza->getStatus() == 3) {
                echo <<<EOT
                <input type="radio" name="radio" value="bestellt" onclick="document.forms['{$formid}'].submit();">
                <input type="radio" name="radio" value="inZubereitung" onclick="document.forms['{$formid}'].submit();">
                <input type="radio" name="radio" value="fertig" checked>
EOT;
              }
              echo <<<EOT
              </p>
            </form>
EOT;
            $formid++;
          }
          $i++;
        }
      }
 
    $this->generatePageFooter();
  }

  protected function processReceivedData() {
    parent::processReceivedData();

    if(isset($_POST['radio'])) {
      $sqlpost = "UPDATE bestelltepizza SET Status='{$_POST['radio']}'
        WHERE PizzaID='{$_POST['changedPizza']}'";
      $recordset = $this->_database->query($sqlpost);
      header('Location: baecker.php');
    }
  }

  public static function main() {
		try {
			$page = new Baker();
			$page->processReceivedData();
			$page->generateView();
		}
		catch (Exception $e) {
			header("Content-type: text/plain; charset=UTF-8");
			echo $e->getMessage();
		}
  }
}

Baker::main();

?>
