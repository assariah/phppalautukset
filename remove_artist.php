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

// Tarkista, onko artist_id-parametri asetettu
if (isset($_GET['artist_id'])) {
  $artist_id = $_GET['artist_id'];

  // Aloita transaktio
  $pdo->beginTransaction();

  try {
    // Poista invoice_items, jotka liittyvät kyseiseen artistiin
    $query = "DELETE FROM invoice_items
              WHERE track_id IN (
                SELECT tracks.track_id FROM tracks
                JOIN albums ON tracks.album_id = albums.album_id
                WHERE albums.artist_id = :artist_id
              )";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':artist_id', $artist_id);
    $statement->execute();

    // Poista tracks, jotka liittyvät kyseiseen artistiin
    $query = "DELETE FROM tracks
              WHERE album_id IN (
                SELECT album_id FROM albums WHERE artist_id = :artist_id
              )";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':artist_id', $artist_id);
    $statement->execute();

    // Poista albums, jotka liittyvät kyseiseen artistiin
    $query = "DELETE FROM albums WHERE artist_id = :artist_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':artist_id', $artist_id);
    $statement->execute();

    // Poista artist
    $query = "DELETE FROM artists WHERE artist_id = :artist_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':artist_id', $artist_id);
    $statement->execute();

    // Vahvista transaktio
    $pdo->commit();

    echo "Artisti ja siihen liittyvät tiedot poistettiin onnistuneesti.";
  } catch (PDOException $e) {
    // Peruuta transaktio virheen sattuessa
    $pdo->rollBack();
    echo "Virhe poistettaessa artistia: " . $e->getMessage();
  }
} else {
  echo "artist_id-parametri puuttuu.";
}
?>