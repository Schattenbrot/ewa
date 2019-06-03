<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class KundenStatus for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 5
 *
 * @category File
 * @package  Pizzaservice
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @license  http://www.h-da.de  none
 * @Release  1.2
 * @link     http://www.fbi.h-da.de
 */

// to do: change name 'KundenStatus' throughout this file
require_once './Page.php';

class KundenStatus extends Page
{
 private $sessionId;
 

  protected function __construct() {
    parent::__construct();
    if (!isset($_SESSION['sessionId'])) {
        $_SESSION['sessionId'] = md5(uniqid(mt_rand()));
    }
    $this->sessionId = $_SESSION['sessionId'];
  }
    


    protected function __destruct()
    {
        parent::__destruct();
    }


      protected function getViewData() {
    $pizza_list = array();

    $sql = "SELECT angebot.PizzaName, bestelltepizza.status FROM bestelltepizza 
      JOIN angebot ON bestelltepizza.fPizzaNummer=angebot.PizzaNummer
	  WHERE bestelltepizza.fBestellungID={$this->sessionId}
	  ORDER BY bestelltepizza.fBestellungID ASC";
    
	$db_query = $this->_database->query($sql);
	if(!$db_query){
		throw new Exception("Abfrage fehlgeschalgen: " . $this->_database->error);	
	}
   

    return $db_query;
    }


    protected function generateView()
    {
	 $bestellungen = $this->getViewData();
	 
	 $recordset_array = array();
	 
	 while($record = $bestellungen->fetch_assoc()){
		 array_push($recordset_array, $record);
	 }
	 
	 $SerializedData = json_encode($recordset_array);
	 
	 echo $SerializedData;
	 $bestellungen->free();  
	 }

  
    protected function processReceivedData()
    {
        parent::processReceivedData();
		
		header("Content-type: application/json; charset=UTF-8");
	}


    public static function main()
    {
        try {
            $page = new KundenStatus();
            $page->processReceivedData();
            $page->generateView();
        }
        catch (Exception $e) {
            header("Content-type: application/json; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page.
// That is input is processed and output is created.
KundenStatus::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
?>