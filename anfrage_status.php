<?php
session_start();
$db = new SQLite3('objekte.db');

// Prüfen ob ID und Aktion übergeben wurden
if (!isset($_GET['id']) || !isset($_GET['aktion'])) {
  echo "<p style='color:red;'>❌ Ungültiger Aufruf.</p>";
  exit;
}

$anfrage_id = intval($_GET['id']);
$aktion = $_GET['aktion'];

// Nur erlaubte Aktionen
if (!in_array($aktion, ['annehmen', 'ablehnen'])) {
  echo "<p style='color:red;'>❌ Ungültige Aktion.</p>";
  exit;
}

// Anfrage laden
$res = $db->querySingle("SELECT * FROM anfragen WHERE id = $anfrage_id", true);
if (!$res) {
  echo "<p style='color:red;'>❌ Anfrage nicht gefunden.</p>";
  exit;
}

// Status ändern
$status = ($aktion === 'annehmen') ? 'angenommen' : 'abgelehnt';
$stmt = $db->prepare("UPDATE anfragen SET status = :status WHERE id = :id");
$stmt->bindValue(':status', $status, SQLITE3_TEXT);
$stmt->bindValue(':id', $anfrage_id, SQLITE3_INTEGER);
$stmt->execute();

// Wenn angenommen → Objekt automatisch reservieren
if ($aktion === 'annehmen') {
  $objekt_id = intval($res['objekt_id']);
  $db->exec("UPDATE objekte SET reserviert = 1 WHERE id = $objekt_id");
  echo "<p style='color:lime;'>✅ Anfrage angenommen & Objekt-ID $objekt_id reserviert.</p>";
} else {
  echo "<p style='color:orange;'>⛔ Anfrage abgelehnt.</p>";
}

echo "<br><a href='anfragen_verwalten.php' style='color:white;'>🔙 Zurück zu den Anfragen</a>";
?>
