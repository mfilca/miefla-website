<?php
$db = new SQLite3('mifla_vermietungen.db');

$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$kategorie = htmlspecialchars($_POST['kategorie'] ?? '');
$unterkategorie = htmlspecialchars($_POST['unterkategorie'] ?? '');
$beschreibung = htmlspecialchars($_POST['beschreibung'] ?? '');
$region = htmlspecialchars($_POST['region'] ?? '');
$zeitraum = htmlspecialchars($_POST['zeitraum'] ?? '');
$preis = htmlspecialchars($_POST['preis'] ?? '');
$kaution = htmlspecialchars($_POST['kaution'] ?? '');
$zusatzoptionen = htmlspecialchars($_POST['zusatzoptionen'] ?? '');

if (!$kategorie || !$unterkategorie || !$beschreibung || !$region || !$zeitraum || !$preis || !$kaution) {
    die("Pflichtfelder fehlen.");
}

$bilder = $_FILES['bilder'];
$bildpfade = [];

if (!isset($bilder['name']) || count($bilder['name']) < 3) {
    die("Bitte lade mindestens 3 Bilder hoch.");
}

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

if (count($bildpfade) < 3) {
    die("Fehler beim Hochladen der Bilder.");
}

$db->exec("CREATE TABLE IF NOT EXISTS vermietungen (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    kategorie TEXT,
    unterkategorie TEXT,
    beschreibung TEXT,
    region TEXT,
    zeitraum TEXT,
    preis TEXT,
    kaution TEXT,
    zusatzoptionen TEXT,
    bilder TEXT,
    erstellt_am DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$bilder_json = json_encode($bildpfade);

$stmt = $db->prepare('INSERT INTO vermietungen (kategorie, unterkategorie, beschreibung, region, zeitraum, preis, kaution, zusatzoptionen, bilder) VALUES (:k, :uk, :b, :r, :z, :p, :kaution, :zusatz, :pics)');
$stmt->bindValue(':k', $kategorie);
$stmt->bindValue(':uk', $unterkategorie);
$stmt->bindValue(':b', $beschreibung);
$stmt->bindValue(':r', $region);
$stmt->bindValue(':z', $zeitraum);
$stmt->bindValue(':p', $preis);
$stmt->bindValue(':kaution', $kaution);
$stmt->bindValue(':zusatz', $zusatzoptionen);
$stmt->bindValue(':pics', $bilder_json);
$stmt->execute();

echo "Erfolgreich gespeichert.";
?>
