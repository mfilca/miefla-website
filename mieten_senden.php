<?php
// Verbindung zur SQLite-Datenbank
$db = new SQLite3('mifla_mieten.db');

// Pflichtfelder prüfen
$region = htmlspecialchars($_POST['region'] ?? '');
$radius = htmlspecialchars($_POST['radius'] ?? '');

if (!$region || !$radius) {
    echo "<body style='background-color:#111; color:white; font-family:sans-serif; text-align:center; padding-top:50px;'>
            <h2 style='color:orange;'>Bitte fülle alle Pflichtfelder aus (Region und Umkreis).</h2>
            <a href='mieten.html' style='color:white;'>Zurück zur Suche</a>
          </body>";
    exit;
}

// Optionale Felder verarbeiten
$kategorie       = htmlspecialchars($_POST['kategorie'] ?? '');
$unterkategorie  = htmlspecialchars($_POST['unterkategorie'] ?? '');
$zweck           = htmlspecialchars($_POST['zweck'] ?? '');
$größe           = htmlspecialchars($_POST['größe'] ?? '');
$zeitraum_von    = htmlspecialchars($_POST['zeitraum_von'] ?? '');
$zeitraum_bis    = htmlspecialchars($_POST['zeitraum_bis'] ?? '');

// Tabelle anlegen, falls noch nicht vorhanden
$db->exec("CREATE TABLE IF NOT EXISTS mieten (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    kategorie TEXT,
    unterkategorie TEXT,
    zweck TEXT,
    größe TEXT,
    region TEXT NOT NULL,
    radius TEXT NOT NULL,
    zeitraum_von TEXT,
    zeitraum_bis TEXT,
    erstellt_am DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Eintrag speichern
$stmt = $db->prepare('INSERT INTO mieten 
  (kategorie, unterkategorie, zweck, größe, region, radius, zeitraum_von, zeitraum_bis) 
  VALUES 
  (:k, :uk, :z, :g, :r, :rad, :von, :bis)');

$stmt->bindValue(':k', $kategorie);
$stmt->bindValue(':uk', $unterkategorie);
$stmt->bindValue(':z', $zweck);
$stmt->bindValue(':g', $größe);
$stmt->bindValue(':r', $region);
$stmt->bindValue(':rad', $radius);
$stmt->bindValue(':von', $zeitraum_von);
$stmt->bindValue(':bis', $zeitraum_bis);

$result = $stmt->execute();

// Erfolg oder Fehler
if ($result) {
    echo "<body style='background-color:#111; color:white; font-family:sans-serif; text-align:center; padding-top:50px;'>
            <h2 style='color:lime;'>Deine Mietanfrage wurde gespeichert.</h2>
            <a href='index.html' style='color:white;'>Zurück zur Startseite</a>
          </body>";
} else {
    echo "<body style='background-color:#111; color:white; font-family:sans-serif; text-align:center; padding-top:50px;'>
            <h2 style='color:red;'>Fehler beim Speichern. Bitte versuch es erneut.</h2>
            <a href='mieten.html' style='color:white;'>Zurück zur Suche</a>
          </body>";
}
?>
