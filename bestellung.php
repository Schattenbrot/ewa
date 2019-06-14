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
  private $PREIS;
  
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
    
    $sql = "SELECT PizzaNummer, PizzaName, Bilddatei, Preis FROM angebot";

    $recordset = $this->_database->query($sql);
    if (!$recordset)
    {
      throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
    }

    while ($record = $recordset->fetch_assoc()) {
      $pizza = new Pizza($record['PizzaName'], $record['Bilddatei'], $record['Preis']);
      $pizza->setPizzaID($record['PizzaNummer']);
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
          $_pizza->pizzaID = htmlspecialchars($_pizza->pizzaID);

          echo <<<EOT
          <p>
            <img src="{$_pizza->Bilddatei}" alt="$_pizza->PizzaName" onClick="addItem($_pizza->pizzaID,  '$_pizza->PizzaName', $_pizza->Preis)" />
            {$_pizza->pizzaID}
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
        </select>
        <!--Maximalpreis -->
        <h4 id="preis">0.00€</h4>
        <p>
          <input type="text" name="adresse" size="20" value="" placeholder="Adresse Nr." tabindex="3"><br />
          <input type="button" name="delete all" value="Alle Löschen" tabindex="4" onclick="deleteAll()">
          <input type="button" name="delete selected" value="Auswahl Löschen" tabindex="5" onclick="deleteSelected()">
          <input type="submit" name="order" value="Bestellen" tabindex="6" onclick="selectAll()">
        </p>
      </form>
    </section> 

EOT;
    $this->generatePageFooter();
  }

  protected function processReceivedData()
  {
    parent::processReceivedData();
    print_r($_POST['adresse']);
    print_r($_POST['basket']);
    if(isset($_POST['adresse']) && isset($_POST['basket']) && !empty($_POST['adresse'])) {
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