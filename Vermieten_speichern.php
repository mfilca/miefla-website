<?php
// DB verbinden
$db = new SQLite3('objekte.db');

// Formulardaten abholen
$titel = $_POST['titel'];
$kategorie = $_POST['kategorie'];
$unterkategorie = $_POST['unterkategorie'];
$beschreibung = $_POST['beschreibung'];
$preis = floatval($_POST['preis']);
$kaution = floatval($_POST['kaution']);
$von = $_POST['von'];
$bis = $_POST['bis'];
$region = $_POST['region'];

// Bilder verarbeiten
$bildNamen = [];
$uploadOrdner = "uploads/";
if (!file_exists($uploadOrdner)) {
    mkdir($uploadOrdner, 0777, true);
}

foreach ($_FILES['bilder']['tmp_name'] as $index => $tmpName) {
    if ($_FILES['bilder']['error'][$index] === 0) {
        $zielname = $uploadOrdner . time() . "_" . basename($_FILES['bilder']['name'][$index]);
        move_uploaded_file($tmpName, $zielname);
        $bildNamen[] = $zielname;
    }
}
$bilderJSON = json_encode($bildNamen);

// Zusatzoptionen verarbeiten
$zusatzOptionen = [];
if (isset($_POST['zusatz_beschreibung'])) {
    foreach ($_POST['zusatz_beschreibung'] as $i => $beschreibung) {
        $option = [
            "beschreibung" => $beschreibung,
            "preis" => floatval($_POST['zusatz_preis'][$i]),
            "kaution" => floatval($_POST['zusatz_kaution'][$i])
        ];
        $zusatzOptionen[] = $option;
    }
}
$zusatzJSON = json_encode($zusatzOptionen);

// In Datenbank speichern
$stmt = $db->prepare("INSERT INTO vermietungen (titel, kategorie, unterkategorie, beschreibung, preis, kaution, verfuegbar_von, verfuegbar_bis, region, bilder, zusatzoptionen) 
VALUES (:titel, :kategorie, :unterkategorie, :beschreibung, :preis, :kaution, :von, :bis, :region, :bilder, :zusatz)");

$stmt->bindValue(':titel', $titel);
$stmt->bindValue(':kategorie', $kategorie);
$stmt->bindValue(':unterkategorie', $unterkategorie);
$stmt->bindValue(':beschreibung', $beschreibung);
$stmt->bindValue(':preis', $preis);
$stmt->bindValue(':kaution', $kaution);
$stmt->bindValue(':von', $von);
$stmt->bindValue(':bis', $bis);
$stmt->bindValue(':region', $region);
$stmt->bindValue(':bilder', $bilderJSON);
$stmt->bindValue(':zusatz', $zusatzJSON);

if ($stmt->execute()) {
    // JSON-Datei speichern
    $objektdaten = [
        "titel" => $titel,
        "kategorie" => $kategorie,
        "unterkategorie" => $unterkategorie,
        "beschreibung" => $beschreibung,
        "preis" => $preis,
        "kaution" => $kaution,
        "von" => $von,
        "bis" => $bis,
        "region" => $region,
        "bilder" => $bildNamen,
        "zusatzoptionen" => $zusatzOptionen
    ];

    if (!is_dir("json")) {
        mkdir("json", 0777, true);
    }

    $jsonName = "json/objekt_" . time() . ".json";
    file_put_contents($jsonName, json_encode($objektdaten, JSON_PRETTY_PRINT));

    echo "<p style='color:lime;'>✅ Objekt wurde gespeichert!</p>";
    echo "<a href='vermieten.html'>Neues Objekt einstellen</a>";
} else {
    echo "<p style='color:red;'>❌ Fehler beim Speichern!</p>";
}
?>
