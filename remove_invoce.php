<?php
$ini =parse_ini_file("myconfig.ini");
$host = $ini["host"];
$dbname = $ini["dbname"];
$username = $ini["username"];
$password = $ini["password"];

// Ota yhteys tietokantaan
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
  die("VIRHE: " . $e->getMessage());
}

// Tarkista, onko invoice_item_id-parametri asetettu
if (isset($_GET['invoice_item_id'])) {
  $invoice_item_id = $_GET['invoice_item_id'];

  // Suorita poisto tietokannasta
  $query = "DELETE FROM invoice_items WHERE invoice_item_id = :invoice_item_id";
  $statement = $pdo->prepare($query);
  $statement->bindParam(':invoice_item_id', $invoice_item_id);
  $statement->execute();

  // Tarkista, montako riviä poistettiin
  $deletedRows = $statement->rowCount();

  if ($deletedRows > 0) {
    echo "Tietue poistettiin onnistuneesti.";
  } else {
    echo "Tietueen poistaminen epäonnistui.";
  }
} else {
  echo "invoice_item_id-parametri puuttuu.";
}
?>