<?php
// Verbindung zur SQLite-Datenbank
$db = new SQLite3('mifla_vermietungen.db');

// Upload-Verzeichnis
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Bild verarbeiten
$bildpfad = "";
if (isset($_FILES['bild']) && $_FILES['bild']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['bild']['tmp_name'];
    $name = basename($_FILES['bild']['name']);
    $zielpfad = $upload_dir . time() . "_" . $name;

    if (move_uploaded_file($tmp_name, $zielpfad)) {
        $bildpfad = $zielpfad;
    }
}

// Formulardaten einlesen
$kategorie = htmlspecialchars($_POST['kategorie'] ?? '');
$unterkategorie = htmlspecialchars($_POST['unterkategorie'] ?? '');
$beschreibung = htmlspecialchars($_POST['beschreibung'] ?? '');
$region = htmlspecialchars($_POST['region'] ?? '');
$zeitraum = htmlspecialchars($_POST['zeitraum'] ?? '');
$preis = htmlspecialchars($_POST['preis'] ?? '');
$kaution = htmlspecialchars($_POST['kaution'] ?? '');
$zusatzoptionen = isset($_POST['zusatzoptionen']) ? json_encode($_POST['zusatzoptionen']) : '[]';

// In Datenbank speichern
$stmt = $db->prepare('INSERT INTO vermietungen (kategorie, unterkategorie, beschreibung, region, zeitraum, preis, kaution, zusatzoptionen, bild) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->bindValue(1, $kategorie);
$stmt->bindValue(2, $unterkategorie);
$stmt->bindValue(3, $beschreibung);
$stmt->bindValue(4, $region);
$stmt->bindValue(5, $zeitraum);
$stmt->bindValue(6, $preis);
$stmt->bindValue(7, $kaution);
$stmt->bindValue(8, $zusatzoptionen);
$stmt->bindValue(9, $bildpfad);
$result = $stmt->execute();

// Wenn erfolgreich → JSON-Datei schreiben
if ($result) {
    $lastId = $db->lastInsertRowID();
    $daten = [
        'id' => $lastId,
        'kategorie' => $kategorie,
        'unterkategorie' => $unterkategorie,
        'beschreibung' => $beschreibung,
        'region' => $region,
        'zeitraum' => $zeitraum,
        'preis' => $preis,
        'kaution' => $kaution,
        'zusatzoptionen' => json_decode($zusatzoptionen, true),
        'bild' => $bildpfad
    ];

    $jsonPfad = __DIR__ . "/json/objekt_$lastId.json";
    file_put_contents($jsonPfad, json_encode($daten, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "<h2 style='color:lime;'>Dein Objekt wurde gespeichert und exportiert.</h2>";
} else {
    echo "<h2 style='color:red;'>Fehler beim Speichern.</h2>";
}
?>
