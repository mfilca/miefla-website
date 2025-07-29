<?php
$db = new SQLite3('objekte.db');

// Tabelle für Vermietungen anlegen
$db->exec("
CREATE TABLE IF NOT EXISTS vermietungen (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titel TEXT,
    kategorie TEXT,
    unterkategorie TEXT,
    beschreibung TEXT,
    preis REAL,
    kaution REAL,
    verfuegbar_von TEXT,
    verfuegbar_bis TEXT,
    region TEXT,
    bilder TEXT,
    zusatzoptionen TEXT
)
");

echo "<p style='color:lime;'>✅ Tabelle 'vermietungen' wurde erstellt oder existiert bereits.</p>";
?>
