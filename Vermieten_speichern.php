<?php
// Verbindung zur neuen Vermietungsdatenbank
$db = new SQLite3('mifla_vermietungen.db');

// Upload-Verzeichnis
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Bilder verarbeiten (erlaube min. 3)
$bildpfade = [];
if (isset($_FILES['bilder']) && count($_FILES['bilder']['name']) >= 3) {
    foreach ($_FILES['bilder']['tmp_name'] as $index => $tmp_name) {
        if ($_FILES['bilder']['error'][$index] === UPLOAD_ERR_OK) {
            $name = basename($_FILES['bilder']['name'][$index]);
            $ziel = $upload_dir . time() . "_" . $index . "_" . $name;
            move_uploaded_file($tmp_name, $ziel);
            $bildpfade[] = $ziel;
        }
    }
} else {
    die("<h2 style='color:red;'>Bitte mindestens 3 Bilder hochladen!</h2>");
}

// Formulardaten
$kategorie      = htmlspecialchars($_POST['kategorie'] ?? '');
$unterkategorie = htmlspecialchars($_POST['unterkategorie'] ?? '');
$beschreibung   = htmlspecialchars($_POST['beschreibung'] ?? '');
$region         = htmlspecialchars($_POST['region'] ?? '');
$preis          = floatval($_POST['preis'] ?? 0);
$kaution        = floatval($_POST['kaution'] ?? 0);

// Zusatzoptionen verarbeiten (als JSON speichern)
$zusatzoptionen = [];
if (isset($_POST['zusatz_option'])) {
    foreach ($_POST['zusatz_option'] as $i => $opt) {
        $zusatzoptionen[] = [
            'text' => htmlspecialchars($opt),
            'preis' => floatval($_POST['zusatz_preis'][$i] ?? 0),
            'kaution' => floatval($_POST['zusatz_kaution'][$i] ?? 0)
        ];
    }
}
$zusatzoptionen_json = json_encode($zusatzoptionen);

// Bildpfade als JSON
$bilder_json = json_encode($bildpfade);

// In DB einfügen
$stmt = $db->prepare("INSERT INTO vermietungen (kategorie, unterkategorie, beschreibung, region, preis, kaution, bild, zusatzoptionen)
VALUES (:kat, :ukat, :beschr, :reg, :preis, :kaution, :bild, :zusatz)");
$stmt->bindValue(':kat', $kategorie);
$stmt->bindValue(':ukat', $unterkategorie);
$stmt->bindValue(':beschr', $beschreibung);
$stmt->bindValue(':reg', $region);
$stmt->bindValue(':preis', $preis);
$stmt->bindValue(':kaution', $kaution);
$stmt->bindValue(':bild', $bilder_json);
$stmt->bindValue(':zusatz', $zusatzoptionen_json);

$result = $stmt->execute();

// Erfolg oder Fehler anzeigen
if ($result) {
    echo "<h2 style='color:lime;'>✅ Objekt erfolgreich gespeichert!</h2>";
} else {
    echo "<h2 style='color:red;'>❌ Fehler beim Speichern.</h2>";
}
?>
