<?php
// Verbindung zur SQLite-Datenbank
$db = new SQLite3('mifla_vermietungen.db');

// Upload-Verzeichnis (muss existieren und beschreibbar sein!)
$upload_dir = "uploads/";
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

// Formulardaten absichern und einlesen
$kategorie    = htmlspecialchars($_POST['kategorie'] ?? '');
$beschreibung = htmlspecialchars($_POST['beschreibung'] ?? '');
$region       = htmlspecialchars($_POST['region'] ?? '');
$zeitraum     = htmlspecialchars($_POST['zeitraum'] ?? '');
$preis        = htmlspecialchars($_POST['preis'] ?? '');

// SQL einfügen
$stmt = $db->prepare('INSERT INTO vermietungen (kategorie, beschreibung, region, zeitraum, preis, bildpfad) VALUES (:kat, :beschr, :reg, :zeit, :preis, :bild)');
$stmt->bindValue(':kat', $kategorie);
$stmt->bindValue(':beschr', $beschreibung);
$stmt->bindValue(':reg', $region);
$stmt->bindValue(':zeit', $zeitraum);
$stmt->bindValue(':preis', $preis);
$stmt->bindValue(':bild', $bildpfad);

$result = $stmt->execute();

// Erfolg oder Fehler anzeigen
if ($result) {
    echo "<h2 style='color: lime;'>Danke! Dein Objekt wurde gespeichert.</h2>";
} else {
    echo "<h2 style='color: red;'>Fehler beim Speichern.</h2>";
}
?>
