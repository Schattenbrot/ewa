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
    $order_list[] = array();

		$sql = "SELECT bestellung.BestellungID, bestellung.Adresse,
			bestelltepizza.Status, bestelltepizza.PizzaID, angebot.PizzaName, angebot.Preis
      FROM bestelltepizza 
			LEFT JOIN bestellung ON bestelltepizza.fBestellungID=bestellung.BestellungID
			LEFT JOIN angebot ON bestelltepizza.fPizzaNummer=angebot.PizzaNummer
      ORDER BY bestelltepizza.fBestellungID, bestelltepizza.Status ASC";

    $recordset = $this->_database->query($sql);
    if(!$recordset) {
      throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error());
    }

    while ($record = $recordset->fetch_assoc()) {
      $order = new Order($record['BestellungID'], $record['Adresse'], $record['PizzaName'], $record['Status'], $record['Preis']);
      $order->setPizzaID($record['PizzaID']);
      $this->order_list[] = $order;
    }
    $recordset->free();
    return $order_list;
  }

  protected function generateView() {
    $this->getViewData();
    $this->generatePageHeader('Bäcker');

    echo <<<EOT
		<section id="menu">
      <nav>
        <ul>
          <li><a href="bestellung.php">Bestellung</a></li>
          <li class="current"><a href="baecker.php">Bäcker</a></li>
          <li><a href="fahrer.php">Fahrer</a></li>
          <li><a href="kunde.php">Kunde</a></li>
        </ul>
      </nav>
    </section>
    
    <section id="bakerMainSection">
EOT;
    if(isset($this->order_list)) {
      $formid = 0;
      echo <<<EOT
      <table>
        <tr id="tr1">
          <td id="td1">Id und Pizza</td>
          <td>Bestellt</td>
          <td>Ofen</td>
          <td>Fertig</td>
        </tr>
EOT;
      foreach($this->order_list as $value) {
        $_pizza = $value;
        $OrderID = htmlspecialchars($_pizza->getOrderID());
        $PizzaName = htmlspecialchars($_pizza->getPizzaName()[0]);
        $PizzaID = htmlspecialchars($_pizza->getPizzaID());
        $Status = htmlspecialchars($_pizza->getStatus());
        if ($Status < 3){
            echo <<<EOT
            <tr>            
            <form action="baecker.php" method="post" id="a{$formid}">
              <td id="td2">{$OrderID}: {$PizzaName}</td>
              <input type="hidden" name="changedPizza" value="{$PizzaID}">
EOT;
              if ($Status == 1) {
                echo <<<EOT
                <td><input type="radio" name="radio" value="bestellt" checked></td>
                <td><input type="radio" name="radio" value="inZubereitung" onclick="document.forms['a{$formid}'].submit();"></td>
                <td><input type="radio" name="radio" value="fertig" onclick="document.forms['a{$formid}'].submit();"></td>
EOT;
              }
              if ($Status == 2) {
                echo <<<EOT
                <td><input type="radio" name="radio" value="bestellt" onclick="document.forms['a{$formid}'].submit();"></td>
                <td><input type="radio" name="radio" value="inZubereitung" checked></td>
                <td><input type="radio" name="radio" value="fertig" onclick="document.forms['a{$formid}'].submit();"></td>
EOT;
              }
              echo <<<EOT
              </tr>
            </form>
EOT;
            $formid++;
          }
        }
      echo '</table>';
      }
    echo '</section>';
    $this->generatePageFooter();
  }

  protected function processReceivedData() {
    parent::processReceivedData();

    if(isset($_POST['radio']) && isset($_POST['changedPizza']) && is_numeric($_POST['changedPizza'])) {
      $_POST['radio'] = $this->_database->real_escape_string($_POST['radio']);
      $_POST['changedPizza'] = $this->_database->real_escape_string($_POST['changedPizza']);
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
