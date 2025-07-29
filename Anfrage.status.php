<?php
$db = new SQLite3('objekte.db');

$db->exec("
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT,
  email TEXT UNIQUE,
  passwort TEXT
)
");

echo "<p style='color:lime;'>✅ Tabelle 'users' wurde erstellt oder existiert schon.</p>";
?>
