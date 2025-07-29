<?php
$db = new SQLite3('mifla_vermietungen.db');
$result = $db->query("SELECT * FROM vermietungen ORDER BY erstellt_am DESC");
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>MiFla – Mietangebote</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background-color: #111;
      color: white;
      font-family: sans-serif;
    }
    h1 {
      text-align: center;
      margin-top: 40px;
    }
    .angebot {
      background-color: #1c1c1c;
      padding: 20px;
      margin: 30px auto;
      max-width: 800px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.6);
    }
    .bilder {
      display: flex;
      gap: 10px;
      overflow-x: auto;
    }
    .bilder img {
      height: 150px;
      border-radius: 5px;
      object-fit: cover;
    }
    .details {
      margin-top: 15px;
    }
    .details p {
      margin: 5px 0;
    }
  </style>
</head>
<body>

<h1>🔍 Mietangebote auf MiFla</h1>

<?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
  <div class="angebot">
    <div class="bilder">
      <?php
        $bilder = json_decode($row['bilder'], true);
        foreach ($bilder as $pfad) {
            echo "<img src='$pfad' alt='Bild'>";
        }
      ?>
    </div>
    <div class="details">
      <p><strong>Kategorie:</strong> <?= htmlspecialchars($row['kategorie']) ?> – <?= htmlspecialchars($row['unterkategorie']) ?></p>
      <p><strong>Region:</strong> <?= htmlspecialchars($row['region']) ?></p>
      <p><strong>Verfügbar ab:</strong> <?= htmlspecialchars($row['zeitraum']) ?></p>
      <p><strong>Preis:</strong> <?= htmlspecialchars($row['preis']) ?> € / Tag</p>
      <p><strong>Kaution:</strong> <?= htmlspecialchars($row['kaution']) ?> €</p>
      <p><strong>Beschreibung:</strong><br><?= nl2br(htmlspecialchars($row['beschreibung'])) ?></p>
      <?php if (!empty($row['zusatzoptionen'])): ?>
        <p><strong>Zusatzoptionen:</strong><br><?= nl2br(htmlspecialchars($row['zusatzoptionen'])) ?></p>
      <?php endif; ?>
    </div>
  </div>
<?php endwhile; ?>

</body>
</html>
