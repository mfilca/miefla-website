<?php
// Verbindung zur SQLite-Datenbank
$db = new SQLite3('mifla_vermietungen.db');

// Upload-Verzeichnis vorbereiten
$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Pflichtfelder prüfen
$kategorie      = htmlspecialchars($_POST['kategorie'] ?? '');
$unterkategorie = htmlspecialchars($_POST['unterkategorie'] ?? '');
$beschreibung   = htmlspecialchars($_POST['beschreibung'] ?? '');
$region         = htmlspecialchars($_POST['region'] ?? '');
$zeitraum       = htmlspecialchars($_POST['zeitraum'] ?? '');
$preis          = htmlspecialchars($_POST['preis'] ?? '');

if (!$kategorie || !$unterkategorie || !$beschreibung || !$region || !$zeitraum || !$preis) {
    echo "<body style='background-color:#111; color:white; font-family:sans-serif; text-align:center; padding-top:50px;'>
            <h2 style='color:orange;'>Bitte fülle alle Pflichtfelder aus.</h2>
            <a href='vermieten.html' style='color:white;'>Zurück zum Formular</a>
          </body>";
    exit;
}

// Bilder prüfen
$bilder = $_FILES['bilder'];
$bildpfade = [];

if (!isset($bilder['name']) || count($bilder['name']) < 3) {
    echo "<body style='background-color:#111; color:white; font-family:sans-serif; text-align:center; padding-top:50px;'>
            <h2 style='color:orange;'>Bitte lade mindestens 3 Bilder hoch.</h2>
            <a href='vermieten.html' style='color:white;'>Zurück zum Formular</a>
          </body>";
    exit;
}

// Bilder speichern
for ($i = 0; $i < count($bilder['name']); $i++) {
    if ($bilder['error'][$i] === UPLOAD_ERR_OK) {
        $tmp_name = $bilder['tmp_name'][$i];
        $name = basename($bilder['name'][$i]);
        $ziel = $upload_dir . time() . "_$i" . "_" . $name;
        if (move_uploaded_file($tmp_name, $ziel)) {
            $bildpfade[] = $ziel;
        }
    }
}

// Wenn weniger als 3 gespeichert wurden → Fehler
if (count($bildpfade) < 3) {
    echo "<body style='background-color:#111; color:white; font-family:sans-serif; text-align:center; padding-top:50px;'>
            <h2 style='color:red;'>Fehler beim Hochladen der Bilder. Mindestens 3 müssen erfolgreich hochgeladen werden.</h2>
            <a href='vermieten.html' style='color:white;'>Zurück zum Formular</a>
          </body>";
    exit;
}

// Tabelle erstellen, falls noch nicht vorhanden
$db->exec("CREATE TABLE IF NOT EXISTS vermietungen (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    kategorie TEXT,
    unterkategorie TEXT,
    beschreibung TEXT,
    region TEXT,
    zeitraum TEXT,
    preis TEXT,
    bilder TEXT,
    erstellt_am DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Bilderpfade als JSON speichern
$bilder_json = json_encode($bildpfade);

// Daten einfügen
$stmt = $db->prepare('INSERT INTO vermietungen 
  (kategorie, unterkategorie, beschreibung, region, zeitraum, preis, bilder)
  VALUES 
  (:k, :uk, :b, :r, :z, :p, :pics)');

$stmt->bindValue(':k', $kategorie);
$stmt->bindValue(':uk', $unterkategorie);
$stmt->bindValue(':b', $beschreibung);
$stmt->bindValue(':r', $region);
$stmt->bindValue(':z', $zeitraum);
$stmt->bindValue(':p', $preis);
$stmt->bindValue(':pics', $bilder_json);

$result = $stmt->execute();

// Rückmeldung
if ($result) {
    echo "<body style='background-color:#111; color:white; font-family:sans-serif; text-align:center; padding-top:50px;'>
            <h2 style='color:lime;'>Dein Mietobjekt wurde erfolgreich gespeichert.</h2>
            <a href='index.html' style='color:white;'>Zur Startseite</a>
          </body>";
} else {
    echo "<body style='background-color:#111; color:white; font-family:sans-serif; text-align:center; padding-top:50px;'>
            <h2 style='color:red;'>Fehler beim Speichern. Bitte versuch es erneut.</h2>
            <a href='vermieten.html' style='color:white;'>Zurück zum Formular</a>
          </body>";
}
?>
