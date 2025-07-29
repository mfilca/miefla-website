<?php
session_start();
$db = new SQLite3('objekte.db');

if (!isset($_SESSION['nutzername'])) {
    header("Location: login.html");
    exit;
}

$mieter = $_SESSION['nutzername'];

$anfragen = $db->prepare("
    SELECT r.*, v.titel, v.region
    FROM reservierungen r
    LEFT JOIN vermietungen v ON r.objekt_id = v.id
    WHERE r.mieter = :mieter
    ORDER BY r.erstellt_am DESC
");
$anfragen->bindValue(':mieter', $mieter);
$ergebnisse = $anfragen->execute();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Anfragen – MiFla</title>
    <style>
        body {
            background-color: #111;
            color: white;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h1 {
            color: #00ff88;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #222;
        }
        th, td {
            padding: 12px;
            border: 1px solid #444;
            text-align: left;
        }
        th {
            background: #333;
        }
        .status-bestätigt {
            color: lime;
        }
        .status-ausstehend {
            color: orange;
        }
        .status-storniert {
            color: red;
        }
    </style>
</head>
<body>

<h1>📋 Meine Anfragen</h1>

<table>
    <tr>
        <th>Objekt</th>
        <th>Region</th>
        <th>Zeitraum</th>
        <th>Status</th>
        <th>Gesendet am</th>
    </tr>

    <?php while ($a = $ergebnisse->fetchArray(SQLITE3_ASSOC)): ?>
    <tr>
        <td><?= htmlspecialchars($a['titel']) ?></td>
        <td><?= htmlspecialchars($a['region']) ?></td>
        <td><?= $a['von'] ?> – <?= $a['bis'] ?></td>
        <td class="status-<?= strtolower($a['status']) ?>">
            <?= ucfirst($a['status']) ?>
        </td>
        <td><?= $a['erstellt_am'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
