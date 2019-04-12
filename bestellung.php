<?php
  header ("Content-type: text/html");
  $title="Bestellformular";
?>
<!DOCTYPE html>
<html lang="de">

<?php
  echo <<<EOT
<!-- HEREDOC! Hier steht HTML-Code -->
<head>
  <meta charset="utf-8" />
    <title>$title</title>
</head>
EOT;
?>

<body>
  <section>
    <h1>Bestellung</h1>
    <h2>Speisekarte</h2>
    <p>
      <a href="pizza_margherita.jpg">
      <img src="tn_pizza_margherita.jpg" alt="Pizza Margherita" />
      </a><br />
        Margherita<br />
        4.00€
    </p>
    <p><img src="tn_pizza_salami.jpg" alt="Pizza Salami" /><br />
        Salami<br />
	4.50€
    </p>
    <p><img src="tn_pizza_salami.jpg" alt="Pizza Hawaii" /><br />
        Hawaii<br />
        5.50€
    </p>
  </section>
  <section>
    <h1>Warenkorb</h1>
    <!--Dynamischer Teil -->
    <form>
      <!--Dropdownmenu -->
      <select multiple name="Warenkorb" id="myList" size="5">
        <option value="1">Pizza Margherita</option>
        <option value="2">Pizza Salami</option>
        <option value="3">Pizza Hawaii</option>
      </select>
      <!--Maximalpreis -->
      <p>14.50€</p>			
      <p>
        <input type="text" name="adresse" size="20" placeholder="Adresse Nr."><br />
        <input type="button" name="delete all" value="Alle Löschen">
        <input type="button" name="delete selected" value="Auswahl Löschen">
        <input type="button" name="order" value="Bestellen">
      </p>
    </form>
  </section>
</body>
</html>
