<?php
$db = new SQLite3('objekte.db');

// Tabelle für Reservierungen anlegen
$db->exec("
CREATE TABLE IF NOT EXISTS reservierungen (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    objekt_id INTEGER NOT NULL,
    vermieter TEXT NOT NULL,
    mieter TEXT NOT NULL,
    von DATE NOT NULL,
    bis DATE NOT NULL,
    status TEXT NOT NULL DEFAULT 'ausstehend',
    erstellt_am DATETIME DEFAULT CURRENT_TIMESTAMP
)
");

echo "<p style='color:lime;'>✅ Tabelle 'reservierungen' wurde erfolgreich erstellt oder existiert bereits.</p>";
?>
