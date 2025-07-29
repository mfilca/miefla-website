<?php
session_start();
$db = new SQLite3('objekte.db');

// Eingaben aus GET
$region = $_GET['region'] ?? '';
$radius = (int) ($_GET['radius'] ?? 0);
$kategorie = $_GET['kategorie'] ?? '';
$unterkategorie = $_GET['unterkategorie'] ?? '';
$zweck = $_GET['zweck'] ?? '';
$anforderungen = $_GET['anforderungen'] ?? '';
$von = $_GET['von'] ?? '';
$bis = $_GET['bis'] ?? '';

// SQL vorbereiten
$query = "SELECT * FROM objekte WHERE region LIKE :region";
$params = [':region' => '%' . $region . '%'];

if ($kategorie) {
  $query .= " AND kategorie = :kat";
  $params[':kat'] = $kategorie;
}
if ($unterkategorie) {
  $query .= " AND unterkategorie = :ukat";
  $params[':ukat'] = $unterkategorie;
}
if ($zweck) {
  $query .= " AND zweck LIKE :zweck";
  $params[':zweck'] = '%' . $zweck . '%';
}
if ($anforderungen) {
  $query .= " AND anforderungen LIKE :anf";
  $params[':anf'] = '%' . $anforderungen . '%';
}

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
  $stmt->bindValue($key, $value);
}
$res = $stmt->execute();
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>MiFla – Ergebnisse</title>
  <style>
    body {
      background: #111;
      color: white;
      font-family: sans-serif;
      font-size: 18px;
    }
    .objekt {
      border: 1px solid #333;
      padding: 15px;
      margin: 15px auto;
      max-width: 700px;
      background: #1c1c1c;
    }
    h2 {
      color: #e60023;
    }
  </style>
</head>
<body>
<?php include('nav.php'); ?>
<h1 style="text-align:center;">Gefundene Mietobjekte</h1>

<?php
while ($obj = $res->fetchArray(SQLITE3_ASSOC)) {
  echo "<div class='objekt'>";
  echo "<h2>" . htmlspecialchars($obj['titel']) . "</h2>";
  echo "<p><strong>Beschreibung:</strong> " . htmlspecialchars($obj['beschreibung']) . "</p>";
  echo "<p><strong>Preis:</strong> " . htmlspecialchars($obj['preis']) . " €/Tag + 2 € Plattformgebühr</p>";
  echo "<p><strong>Kaution:</strong> " . htmlspecialchars($obj['kaution']) . " €</p>";
  echo "<p><strong>Region:</strong> " . htmlspecialchars($obj['region']) . "</p>";
  echo "<p><strong>Verfügbar:</strong> " . htmlspecialchars($obj['von']) . " bis " . htmlspecialchars($obj['bis']) . "</p>";

  // Reservierungsstatus anzeigen
  if ($obj['reserviert'] == 1) {
    echo "<p style='color:orange'><strong>Status:</strong> Reserviert</p>";
  } else {
    echo "<p style='color:lightgreen'><strong>Status:</strong> Verfügbar</p>";
  }

  if (!empty($obj['zusatzoptionen'])) {
    echo "<p><strong>Zusatzoptionen:</strong> " . htmlspecialchars($obj['zusatzoptionen']) . "</p>";
  }

  echo "</div>";
}
?>
</body>
</html>
