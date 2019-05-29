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

class Orderpage extends Page
{
  private $sessionId;
  
  protected function __construct()
  {
    parent::__construct();
    if (!isset($_SESSION['sessionId']))
    {
      $_SESSION['sessionId'] = md5(uniqid(mt_rand()));
    }
    $this->sessionId = $_SESSION['sessionId'];
  }

  protected function __destruct()
  {
    parent::__destruct();
  }

  protected function getViewData()
  {
    $pizza_list = array();
    
    $sql = "SELECT PizzaName, Bilddatei, Preis FROM angebot";

    $recordset = $this->_database->query($sql);
    if (!$recordset)
    {
      throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
    }

    while ($record = $recordset->fetch_assoc()) {
      $pizza = new Pizza($record['PizzaName'], $record['Bilddatei'], $record['Preis']);
      $this->pizza_list[] = $pizza;
    }

    $recordset->free();

    return $pizza_list;
  }

  protected function generateView()
  {
    $this->getViewData();
    $this->generatePageHeader('Bestellung');
    echo <<<EOT
    <nav>
      <ul>
        <li><a href="bestellung.php">Bestellung</a></li>
        <li class="current"><a href="baecker.php">Bäcker</a></li>
        <li><a href="fahrer.php">Fahrer</a></li>
        <li><a href="kunde.php">Kunde</a></li>
      </ul>
		</nav>
    <section>
      <h1>Bestellung</h1>
      <h2>Speisekarte</h2>
EOT;
      if(isset($this->pizza_list)) {
        foreach ($this->pizza_list as $value) {
          $_pizza = $value;
          
          $_pizza->PizzaName = htmlspecialchars($_pizza->PizzaName);
          $_pizza->Bilddatei = htmlspecialchars($_pizza->Bilddatei);
          $_pizza->Preis = htmlspecialchars($_pizza->Preis);

          echo <<<EOT
          <p>
            <img src="{$_pizza->Bilddatei}" alt="$_pizza->PizzaName">
            {$_pizza->PizzaName}
            {$_pizza->Preis}€
          </p>
EOT;
        }
      }
      echo <<<EOT
    </section>
    <section tabindex="1">
      <h1>Warenkorb</h1>
      <!--Dynamischer Teil -->
      <form action="bestellung.php" method="post">
        <!--Dropdownmenu -->
        <select multiple name="basket[]" id="myList" size="5" tabindex="2">
          <option value="1">Pizza Salami</option>
          <option value="2">Pizza Margherita</option>
          <option value="3">Pizza Hawaii</option>
          <option value="4">Pizza Marinara</option>
          <option value="5">Pizza Hühnchen</option>
          <option value="6">Pizza TEst</option>
        </select>
        <!--Maximalpreis -->
        <p>14.50€</p>			
        <p>
          <input type="text" name="adresse" size="20" value="" placeholder="Adresse Nr." tabindex="3"><br />
          <input type="button" name="delete all" value="Alle Löschen" tabindex="4">
          <input type="button" name="delete selected" value="Auswahl Löschen" tabindex="5">
          <input type="submit" name="order" value="Bestellen" tabindex="6">
        </p>
      </form>
    </section> 

EOT;

    $this->generatePageFooter();
  }

  protected function processReceivedData()
  {
    parent::processReceivedData();
    if(isset($_POST['adresse']) && isset($_POST['basket'])) {
      $date = new DateTime();
      $orderTime = $date->format('Y-m-d H:i:s');

      $_POST['adresse'] = $this->_database->real_escape_string($_POST['adresse']);
      $orderTime = $this->_database->real_escape_string($orderTime);

      $sqlpost = "INSERT INTO bestellung (Adresse, Bestellzeitpunkt) VALUES ('{$_POST['adresse']}', '{$orderTime}')";
      $recordset = $this->_database->query($sqlpost);

      $sqlGetOrderID = "SELECT BestellungID FROM bestellung WHERE Adresse='{$_POST['adresse']}' AND Bestellzeitpunkt='{$orderTime}'";
      $recordset = $this->_database->query($sqlGetOrderID);
      while ($record = $recordset->fetch_assoc()) {
        $this->orderID = $record['BestellungID'];
        $_SESSION['sessionId'] = $this->orderID;
      }
      foreach ($_POST['basket'] as $pizzaid) {
        $pizzaid = $this->_database->real_escape_string($pizzaid);
        $sqlInsertBestelltePizza = "INSERT INTO bestelltepizza (fBestellungID, fPizzaNummer) VALUES ('{$this->orderID}', '{$pizzaid}')";
        $recordset = $this->_database->query($sqlInsertBestelltePizza);
      }
      header('Location: kunde.php');
    }
  }

  public static function main()
  {
    try {
      $page = new Orderpage();
      $page->processReceivedData();
      $page->generateView();
    }
    catch (Exception $e) {
      header("Content-type: text/plain; charset=UTF-8");
      echo $e->getMessage();
    }
  }
}

Orderpage::main();

?>