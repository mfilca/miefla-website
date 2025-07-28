<?php
// Verbindung zur SQLite-Datenbank
$db = new SQLite3('mifla_vermietungen.db');

// Wenn das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vermietung_id = intval($_POST['vermietung_id'] ?? 0);
    $datum = $_POST['datum'] ?? '';
    $status = $_POST['status'] ?? '';

    if ($vermietung_id && $datum && in_array($status, ['verfügbar', 'gebucht'])) {
        $stmt = $db->prepare('INSERT INTO verfuegbarkeit (vermietung_id, datum, status) VALUES (:vid, :datum, :status)');
        $stmt->bindValue(':vid', $vermietung_id);
        $stmt->bindValue(':datum', $datum);
        $stmt->bindValue(':status', $status);
        $stmt->execute();

        echo "<p style='color:lime;'>Verfügbarkeit gespeichert!</p>";
    } else {
        echo "<p style='color:red;'>Bitte alle Felder korrekt ausfüllen.</p>";
    }
}

// Alle vorhandenen Vermietungen laden
$eintraege = $db->query('SELECT id, kategorie, beschreibung FROM vermietungen ORDER BY id DESC');
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Verfügbarkeit eintragen</title>
  <style>
    body {
      background-color: #121212;
      color: #fff;
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    form {
      background-color: #1f1f1f;
      padding: 20px;
      border-radius: 10px;
      max-width: 500px;
    }
    label {
      display: block;
      margin-top: 15px;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      background-color: #2a2a2a;
      color: white;
      border: none;
      border-radius: 5px;
    }
    input[type="submit"] {
      margin-top: 20px;
      background-color: #e50914;
      cursor: pointer;
    }
    input[type="submit"]:hover {
      background-color: #c40812;
    }
  </style>
</head>
<body>

  <h2>Verfügbarkeit für ein Objekt eintragen</h2>

  <form method="POST" action="">
    <label for="vermietung_id">Objekt wählen</label>
    <select name="vermietung_id" id="vermietung_id" required>
      <option value="">Bitte wählen</option>
      <?php while ($row = $eintraege->fetchArray()) : ?>
        <option value="<?= $row['id'] ?>">
          <?= htmlspecialchars($row['kategorie']) ?> – <?= htmlspecialchars(substr($row['beschreibung'], 0, 
