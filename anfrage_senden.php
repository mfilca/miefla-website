<?php
session_start();
$db = new SQLite3('objekte.db');

// Objekt-ID prüfen
if (!isset($_GET['id'])) {
  echo "<p style='color:red;'>❌ Kein Objekt ausgewählt.</p>";
  exit;
}

$objekt_id = intval($_GET['id']);

// Formular anzeigen
if ($_SERVER["REQUEST_METHOD"] != "POST") {
  echo "<h2 style='color:white;'>Anfrage stellen für Objekt-ID $objekt_id</h2>";
  echo "<form method='POST'>";
  echo "<label style='color:white;'>Dein Name:</label><br><input name='name' required><br><br>";
  echo "<label style='color:white;'>Nachricht (optional):</label><br><textarea name='nachricht'></textarea><br><br>";
  echo "<input type='submit' value='Anfrage senden'>";
  echo "</form>";
  exit;
}

// Anfrage verarbeiten
$name = htmlspecialchars($_POST['name']);
$nachricht = htmlspecialchars($_POST['nachricht']);
$zeit = date("Y-m-d H:i:s");

// In Datenbank speichern
$stmt = $db->prepare("INSERT INTO anfragen (objekt_id, name, nachricht, zeitstempel) VALUES (:objekt_id, :name, :nachricht, :zeit)");
$stmt->bindValue(':objekt_id', $objekt_id, SQLITE3_INTEGER);
$stmt->bindValue(':name', $name, SQLITE3_TEXT);
$stmt->bindValue(':nachricht', $nachricht, SQLITE3_TEXT);
$stmt->bindValue(':zeit', $zeit, SQLITE3_TEXT);
$stmt->execute();

echo "<h2 style='color:lime;'>✅ Anfrage erfolgreich gespeichert!</h2>";
echo "<p style='color:white;'>Angefragt von: <strong>$name</strong></p>";
echo "<p style='color:white;'>Nachricht: $nachricht</p>";
echo "<p style='color:white;'>Zeit: $zeit</p>";
echo "<a href='anzeigen.php' style='color:lightblue;'>Zurück zur Übersicht</a>";
?>
