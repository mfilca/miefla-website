<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $db = new SQLite3('objekte.db');

  // Felder aus dem Formular
  $titel = $_POST['titel'] ?? '';
  $beschreibung = $_POST['beschreibung'] ?? '';
  $preis = floatval($_POST['preis'] ?? 0);
  $kaution = floatval($_POST['kaution'] ?? 0);
  $region = $_POST['region'] ?? '';
  $kategorie = $_POST['kategorie'] ?? '';
  $unterkategorie = $_POST['unterkategorie'] ?? '';
  $zweck = $_POST['zweck'] ?? '';
  $anforderungen = $_POST['anforderungen'] ?? '';
  $von = $_POST['von'] ?? '';
  $bis = $_POST['bis'] ?? '';
  $zusatzoptionen = $_POST['zusatzoptionen'] ?? '';

  // Speichern in DB
  $stmt = $db->prepare("INSERT INTO objekte (
    titel, beschreibung, preis, kaution, region, kategorie, unterkategorie,
    zweck, anforderungen, von, bis, reserviert, zusatzoptionen
  ) VALUES (
    :titel, :beschreibung, :preis, :kaution, :region, :kategorie, :unterkategorie,
    :zweck, :anforderungen, :von, :bis, 0, :zusatzoptionen
  )");

  $stmt->bindValue(':titel', $titel, SQLITE3_TEXT);
  $stmt->bindValue(':beschreibung', $beschreibung, SQLITE3_TEXT);
  $stmt->bindValue(':preis', $preis);
  $stmt->bindValue(':kaution', $kaution);
  $stmt->bindValue(':region', $region, SQLITE3_TEXT);
  $stmt->bindValue(':kategorie', $kategorie, SQLITE3_TEXT);
  $stmt->bindValue(':unterkategorie', $unterkategorie, SQLITE3_TEXT);
  $stmt->bindValue(':zweck', $zweck, SQLITE3_TEXT);
  $stmt->bindValue(':anforderungen', $anforderungen, SQLITE3_TEXT);
  $stmt->bindValue(':von', $von, SQLITE3_TEXT);
  $stmt->bindValue(':bis', $bis, SQLITE3_TEXT);
  $stmt->bindValue(':zusatzoptionen', $zusatzoptionen, SQLITE3_TEXT);

  $stmt->execute();

  echo "<h2 style='color:lime;font-family:sans-serif'>✅ Mietobjekt erfolgreich gespeichert!</h2>";
  echo "<a href='vermieten.html' style='color:white;font-size:18px;'>Zurück zur Eingabe</a>";
} else {
  echo "<h2 style='color:red'>Ungültiger Zugriff</h2>";
}
?>
