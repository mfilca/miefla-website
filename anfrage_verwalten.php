<?php
session_start();
$db = new SQLite3('objekte.db');

// Alle offenen Anfragen laden
$res = $db->query("
  SELECT a.id, a.objekt_id, a.name, a.nachricht, a.zeitstempel, a.status, o.titel
  FROM anfragen a
  LEFT JOIN objekte o ON a.objekt_id = o.id
  ORDER BY a.zeitstempel DESC
");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Anfragen verwalten – MiFla</title>
  <style>
    body {
      background: #111;
      color: white;
      font-family: sans-serif;
      font-size: 18px;
    }
    .anfrage {
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
  <h1 style="text-align:center;">Alle Anfragen verwalten</h1>

  <?php
  while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    echo "<div class='anfrage'>";
    echo "<h2>Anfrage zu: " . htmlspecialchars($row['titel']) . " (ID " . $row['objekt_id'] . ")</h2>";
    echo "<p><strong>Anfragender:</strong> " . htmlspecialchars($row['name']) . "</p>";
    echo "<p><strong>Nachricht:</strong> " . nl2br(htmlspecialchars($row['nachricht'])) . "</p>";
    echo "<p><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</p>";
    echo "<p><strong>Zeit:</strong> " . $row['zeitstempel'] . "</p>";

    // Nur bei Status "offen" kann entschieden werden
    if ($row['status'] === "offen") {
      echo "<a href='anfrage_status.php?id={$row['id']}&aktion=annehmen'>✅ Annehmen</a>";
      echo "<a href='anfrage_status.php?id={$row['id']}&aktion=ablehnen'>❌ Ablehnen</a>";
    }

    echo "</div>";
  }
  ?>
</body>
</html>
