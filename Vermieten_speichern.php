<?php
session_start();

// Verbindung zur SQLite-Datenbank
$db = new SQLite3('objekte.db');

// Pflichtfelder prüfen
if (
    empty($_POST['titel']) || empty($_POST['kategorie']) || empty($_POST['unterkategorie']) ||
    empty($_POST['beschreibung']) || empty($_POST['preis']) || empty($_POST['kaution']) ||
    empty($_POST['von']) || empty($_POST['bis']) || empty($_POST['region'])
) {
    die("❌ Bitte fülle alle Pflichtfelder aus.");
}

// Bilder prüfen
if (!isset($_FILES['bilder']) || count($_FILES['bilder']['name']) < 3) {
    die("❌ Bitte mindestens 3 Bilder hochladen.");
}

// Eingaben speichern
$titel = htmlspecialchars($_POST['titel']);
$kategorie = htmlspecialchars($_POST['kategorie']);
$unterkategorie = htmlspecialchars($_POST['unterkategorie']);
$beschreibung = htmlspecialchars($_POST['beschreibung']);
$preis = floatval($_POST['preis']);
$kaution = floatval($_POST['kaution']);
$von = $_POST['von'];
$bis = $_POST['bis'];
$region = htmlspecialchars($_POST['region']);

// Mietobjekt in Datenbank speichern
$stmt = $db->prepare("
    INSERT INTO objekte (titel, kategorie, unterkategorie, beschreibung, preis, kaution, von, bis, region, reserviert)
    VALUES (:titel, :kat, :ukat, :beschr, :preis, :kaution, :von, :bis, :region, 0)
");
$stmt->bindValue(':titel', $titel);
$stmt->bindValue(':kat', $kategorie);
$stmt->bindValue(':ukat', $unterkategorie);
$stmt->bindValue(':beschr', $beschreibung);
$stmt->bindValue(':preis', $preis);
$stmt->bindValue(':kaution', $kaution);
$stmt->bindValue(':von', $von);
$stmt->bindValue(':bis', $bis);
$stmt->bindValue(':region', $region);
$stmt->execute();

$objekt_id = $db->lastInsertRowID();

// Bilder speichern (nur Pfade in Datenbank – Speicherort z. B. „uploads/“)
mkdir("uploads/$objekt_id", 0777, true);
foreach ($_FILES['bilder']['tmp_name'] as $index => $tmpPath) {
    $name = basename($_FILES['bilder']['name'][$index]);
    $ziel = "uploads/$objekt_id/" . time() . "_$name";
    move_uploaded_file($tmpPath, $ziel);
    $db->exec("INSERT INTO bilder (objekt_id, pfad) VALUES ($objekt_id, '$ziel')");
}

// Zusatzoptionen speichern (falls vorhanden)
if (isset($_POST['zusatz_beschreibung'])) {
    $beschreibungen = $_POST['zusatz_beschreibung'];
    $preise = $_POST['zusatz_preis'];
    $kautionen = $_POST['zusatz_kaution'];

    for ($i = 0; $i < count($beschreibungen); $i++) {
        $bez = htmlspecialchars($beschreibungen[$i]);
        $zpreis = floatval($preise[$i]);
        $zkaution = floatval($kautionen[$i]);

        $db->exec("
            INSERT INTO zusatzoptionen (objekt_id, beschreibung, preis, kaution)
            VALUES ($objekt_id, '$bez', $zpreis, $zkaution)
        ");
    }
}

echo "<h2 style='color:lime;'>✅ Objekt erfolgreich eingestellt!</h2>";
echo "<a href='index.html' style='color:white;'>Zurück zur Startseite</a>";
?>
