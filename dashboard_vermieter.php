<?php
session_start();
$db = new SQLite3('objekte.db');

// Alle Objekte laden
$res = $db->query("SELECT * FROM objekte ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Vermieter-Dashboard – MiFla</title>
  <style>
    body {
      background: #111;
      color: white;
      font-family: sans-serif;
      font-size: 18px;
    }
    .objekt {
      border: 1px solid #444;
      padding: 15px;
      margin: 15px auto;
      max-width: 700px;
      background: #1e1e1e;
    }
    h2 {
      color: #e60023;
    }
    a {
      color: #00aaff;
      margin-right: 10px;
    }
  </style>
</head>
<body>
  <h1 style="text-align:center;">Vermieter-Dashboard</h1>

  <?php
  while ($obj = $res->fetchArray(SQLITE3_ASSOC)) {
    echo "<div class='objekt'>";
    echo "<h2>" . htmlspecialchars($obj['titel']) . "</h2>";
    echo "<p><strong>ID:</strong> " . $obj['id'] . "</p>";
    echo "<p><strong>Status:</strong> " . ($obj['reserviert'] ? "<span style='color:orange'>Reserviert</span>" : "<span style='color:lightgreen'>Frei</span>") . "</p>";
    echo "<p><strong>Verfügbar:</strong> " . $obj['von'] . " bis " . $obj['bis'] . "</p>";

    // Links zur Statusänderung
    if ($obj['reserviert']) {
      echo "<a href='freigeben.php?id=" . $obj['id'] . "'>✅ Freigeben</a>";
    } else {
      echo "<a href='reservieren.php?id=" . $obj['id'] . "'>🔒 Reservieren</a>";
    }

    echo "<a href='anzeigen.php?id=" . $obj['id'] . "'>🔍 Anzeigen</a>";
    echo "</div>";
  }
  ?>
</body>
</html>
