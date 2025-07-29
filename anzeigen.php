<?php
// Verbindung zur SQLite-Datenbank
$db = new SQLite3('mifla_vermietungen.db');

// Formularwerte holen
$region = $_POST['region'] ?? '';
$radius = $_POST['radius'] ?? '';
$kategorie = $_POST['kategorie'] ?? '';
$unterkategorie = $_POST['unterkategorie'] ?? '';
$zweck = $_POST['zweck'] ?? '';
$anforderungen = $_POST['anforderungen'] ?? '';
$von = $_POST['von'] ?? '';
$bis = $_POST['bis'] ?? '';

// SQL-Basisabfrage
$sql = "SELECT * FROM vermietungen WHERE region LIKE :region";
$params = [':region' => "%$region%"];

// Optional: Radius (wenn du irgendwann PLZ/Karten einbaust – jetzt erstmal ignoriert)

// Weitere Filter
if (!empty($kategorie)) {
    $sql .= " AND kategorie = :kategorie";
    $params[':kategorie'] = $kategorie;
}
if (!empty($unterkategorie)) {
    $sql .= " AND unterkategorie = :unterkategorie";
    $params[':unterkategorie'] = $unterkategorie;
}
if (!empty($von) && !empty($bis)) {
    $sql .= " AND (reserviert_von IS NULL OR reserviert_bis IS NULL OR reserviert_bis < :von OR reserviert_von > :bis)";
    $params[':von'] = $von;
    $params[':bis'] = $bis;
}

// Anfrage vorbereiten
$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$result = $stmt->execute();
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Suchergebnisse – MiFla</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background-color: #111;
      color: white;
      font-family: sans-serif;
      font-size: 18px;
      padding: 20px;
    }
    .angebot {
      border: 1px solid #333;
      margin-bottom: 20px;
      padding: 15px;
      background-color: #1a1a1a;
      border-radius: 5px;
    }
    img {
      max-width: 100%;
      height: auto;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

<h2>Gefundene Mietangebote:</h2>

<?php
$found = false;
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $found = true;
    echo '<div class="angebot">';
    if (!empty($row['bild'])) {
        echo '<img src="' . htmlspecialchars($row['bild']) . '" alt="Bild">';
    }
    echo '<strong>' . htmlspecialchars($row['kategorie']) . '</strong>';
    if (!empty($row['unterkategorie'])) {
        echo ' – ' . htmlspecialchars($row['unterkategorie']);
    }
    echo '<br>';
    echo 'Region: ' . htmlspecialchars($row['region']) . '<br>';
    echo 'Beschreibung: ' . htmlspecialchars($row['beschreibung']) . '<br>';
    echo 'Preis: ' . htmlspecialchars($row['preis']) . ' €<br>';
    echo 'Kaution: ' . htmlspecialchars($row['kaution']) . ' €<br>';

    if (!empty($row['zusatzoptionen'])) {
        echo 'Zusatzoptionen: ' . htmlspecialchars($row['zusatzoptionen']) . '<br>';
    }

    if (!empty($row['reserviert_von']) && !empty($row['reserviert_bis'])) {
        echo '<span style="color: orange;">Reserviert von ' . $row['reserviert_von'] . ' bis ' . $row['reserviert_bis'] . '</span><br>';
    }

    echo '<form action="anfrage_senden.php" method="POST">';
    echo '<input type="hidden" name="objekt_id" value="' . $row['id'] . '">';
    echo '<input type="submit" value="Anfrage stellen">';
    echo '</form>';

    echo '</div>';
}

if (!$found) {
    echo '<p>Keine passenden Angebote gefunden.</p>';
}
?>

</body>
</html>
