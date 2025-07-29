<?php
$db = new SQLite3('objekte.db');

$db->exec("
CREATE TABLE IF NOT EXISTS anfragen (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  objekt_id INTEGER,
  name TEXT,
  nachricht TEXT,
  status TEXT DEFAULT 'offen',
  zeitstempel TEXT
)
");

echo "<p style='color:lime;'>✅ Tabelle 'anfragen' wurde erstellt (oder existierte schon).</p>";
?>
