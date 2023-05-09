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
// Tarkista, onko playlist_id-parametri asetettu
if (isset($_GET['playlist_id'])) {
  $playlist_id = $_GET['playlist_id'];

  // Hae kappaleiden nimet ja säveltäjät kyseiselle soittolistalle
  $query = "SELECT tracks.name AS track_name, composers.name AS composer_name
            FROM playlist_track
            JOIN tracks ON playlist_track.track_id = tracks.track_id
            JOIN composers ON tracks.composer_id = composers.composer_id
            WHERE playlist_track.playlist_id = :playlist_id";
  $statement = $pdo->prepare($query);
  $statement->bindParam(':playlist_id', $playlist_id);
  $statement->execute();
  $result = $statement->fetchAll(PDO::FETCH_ASSOC);

  // Tulosta kappaleiden tiedot
  foreach ($result as $row) {
    echo "Kappale: " . $row['track_name'] . ", Säveltäjä: " . $row['composer_name'] . "<br>";
  }
} else {
  echo "playlist_id-parametri puuttuu.";
}
?>