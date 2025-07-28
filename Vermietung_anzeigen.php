<?php
$db = new SQLite3('mifla_vermietungen.db');

// Alle Objekte laden
$vermietungen = $db->query('SELECT * FROM vermietungen ORDER BY id DESC');

// Funktion: Verfügbarkeiten abrufen
function lade_verfuegbarkeit($db, $vermietung_id) {
    $daten = [];
    $stmt = $db->prepare("SELECT datum, status FROM verfuegbarkeit WHERE vermietung_id = :id");
    $stmt->bindValue(':id', $vermietung_id);
    $res = $stmt->execute();

    while ($row = $res->fetchArray()) {
        $daten[$row['datum']] = $row['status'];
    }
    return $daten;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>MiFla – Vermietungen mit Kalender</title>
  <style>
    body {
      background-color: #121212;
      color: #fff;
      font-family: Arial, sans-serif;
      padding: 20px;
    }

    .eintrag {
      border: 1px solid #444;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
      background-color: #1e1e1e;
    }

    img {
      max-width: 100%;
      max-height: 200px;
      margin-top: 10px;
      border-radius: 8px;
    }

    .kalender {
      margin-top: 15px;
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 8px;
    }

    .tag {
      text-align: center;
      padding: 8px;
      border-radius: 5px;
      font-size: 14px;
    }

    .verfuegbar {
      background-color: #2e7d32;
    }

    .gebucht {
      background-color: #b71c1c;
    }

    .unbekannt {
      background-color: #424242;
    }
  </style>
</head>
<body>

  <h1>Alle eingetragenen Vermietungen mit Kalender</h1>

  <?php while ($row = $vermietungen->fetchArray()) : ?>
    <?php $verfuegbar = lade_verfuegbarkeit($db, $row['id']); ?>
    <div class="eintrag">
      <strong>Kategorie:</strong> <?= htmlspecialchars($row['kategorie']) ?><br>
      <strong>Beschreibung:</strong> <?= nl2br(htmlspecialchars($row['beschreibung'])) ?><br>
      <strong>Region:</strong> <?= htmlspecialchars($row['region']) ?><br>
      <strong>Zeitraum:</strong> <?= htmlspecialchars($row['zeitraum']) ?><br>
      <strong>Preis:</strong> <?= htmlspecialchars($row['preis']) ?> €<br>
      <?php if (!empty($row['bildpfad']) && file_exists($row['bildpfad'])): ?>
        <img src="<?= $row['bildpfad'] ?>" alt="Bild">
      <?php endif; ?>

      <!-- Kalenderanzeige -->
      <div class="kalender">
        <?php
          $heute = new DateTime();
          for ($i = 0; $i < 30; $i++) {
              $tag = $heute->format('Y-m-d');
              $status = $verfuegbar[$tag] ?? 'unbekannt';

              $klasse = match ($status) {
                  'verfügbar' => 'verfuegbar',
                  'gebucht' => 'gebucht',
                  default => 'unbekannt'
              };

              echo "<div class='tag $klasse'>" . $heute->format('d.m.') . "</div>";
              $heute->modify('+1 day');
          }
        ?>
      </div>
    </div>
  <?php endwhile; ?>

</body>
</html>
