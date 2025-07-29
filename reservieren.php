<?php
session_start();

if (!isset($_GET['id'])) {
  echo "<p style='color:red;'>❌ Kein Objekt ausgewählt.</p>";
  exit;
}

$objekt_id = intval($_GET['id']);
$db = new SQLite3('objekte.db');

// Objekt auf reserviert setzen
$stmt = $db->prepare("UPDATE objekte SET reserviert = 1 WHERE id = :id");
$stmt->bindValue(':id', $objekt_id, SQLITE3_INTEGER);
$result = $stmt->execute();

if ($result) {
  echo "<h2 style='color:lime;'>✅ Objekt-ID $objekt_id wurde erfolgreich als reserviert markiert.</h2>";
} else {
  echo "<p style='color:red;'>❌ Fehler beim Reservieren.</p>";
}
?>
<a href='anzeigen.php' style='color:white;'>Zurück zur Übersicht</a>
