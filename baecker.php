<?php
  header("Content-type: text/html");
$title = "Bäcker";
$reloadDelay = "5";
?>
<!DOCTYPE html>
<html lang="de">
<?php
  echo <<<EOT
  <head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="$reloadDelay">
    <title>$title</title>	
  </head>
EOT;
?>

<?php
echo <<<EOT
  <body>
      <form>
      <h3>Bestellung</h3>
      <p>Bestellt</p>
      <p>Ofen</p>
      <p>Fertig</p>
EOT;
      for($i = 0; $i < 5; $i++) {
echo <<<EOT
	<p> $i te Pizza 
      
	  <input type="radio" name="radio_{$i}" value="" checked>
	  <input type="radio" name="radio_{$i}" value="">
	  <input type="radio" name="radio_{$i}" value="">
	  </p>

EOT;
	}
echo <<<EOT
    </form>
  </body>
</html>
EOT;
?>
