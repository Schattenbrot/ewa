<?php
/**
 * PHP Version X
 * @package pizzaservice
 * @author Aaron Machill
 * @author Markus Stuber
 * @Release 1.0
 */

require_once './Page.php';
require_once './Pizza.php';

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

    $sql = "SELECT angebot.PizzaName, angebot.Bilddatei, angebot.Preis FROM bestelltepizza left join angebot on bestelltepizza.fPizzaNummer = angebot.PizzaNummer";//bestelltepizza left join angebot on bestelltepizza.fPizzaNummber=angebot.PizzaNummer";

    $recordset = $this->_database->query($sql);
    if(!$recordset) {
      throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error());
    }

    while ($record = $recordset->fetch_assoc()) {
      $pizza = new Pizza($record['PizzaName'], $record['Bilddatei'], $record['Preis']);
      $this->pizza_list[] = $pizza;
    }
    $recordset->free();
    return $pizza_list;
  }

  protected function generateView() {
    $this->getViewData();
    $this->generatePageHeader('Bäcker');

    echo <<<EOT
    <form>
      <h3>Bestellung</h3>
      <p>Bestellt</p>
      <p>Ofen</p>
      <p>Fertig</p>
EOT;
      if(isset($this->pizza_list)) {
        $i = 0;
        foreach($this->pizza_list as $value) {
          $_pizza = $value;
          echo <<<EOT
          <p>{$_pizza->PizzaName}
            <input type="radio" name="radio_{$i}" value="" checked>
            <input type="radio" name="radio_{$i}" value="">
            <input type="radio" name="radio_{$i}" value="">
          </p>
EOT;
          $i++;
        }
      }
    echo('</form>');

    $this->generatePageFooter();
  }

  protected function processReceivedData() {
    parent::processReceivedData();
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
