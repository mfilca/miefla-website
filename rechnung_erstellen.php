<?php
$db = new SQLite3('objekte.db');

if (!isset($_GET['reservierung_id'])) {
    die("❌ Reservierungs-ID fehlt.");
}

$id = intval($_GET['reservierung_id']);
$res = $db->query("
    SELECT r.*, v.titel, v.preis, v.kaution, v.zusatzoptionen, v.vermieter, v.region
    FROM reservierungen r
    LEFT JOIN vermietungen v ON r.objekt_id = v.id
    WHERE r.id = $id AND r.status = 'bestätigt'
")->fetchArray(SQLITE3_ASSOC);

if (!$res) {
    die("❌ Reservierung nicht gefunden oder nicht bestätigt.");
}

$tage = (strtotime($res['bis']) - strtotime($res['von'])) / (60 * 60 * 24) + 1;
$gesamt_miete = $tage * floatval($res['preis']);
$kaution = floatval($res['kaution']);
$gesamt = $gesamt_miete + $kaution + 2.00; // inkl. 2 € MiFla-Provision
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Rechnung #<?= $res['id'] ?></title>
    <style>
        body {
            background: #fff;
            color: #000;
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        h1 {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .download-btn {
            margin-top: 20px;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background: #f4f4f4;
            text-align: left;
        }
    </style>
</head>
<body>

<h1>Rechnung #<?= $res['id'] ?></h1>

<p><strong>Rechnung für:</strong> <?= htmlspecialchars($res['mieter']) ?></p>
<p><strong>Vermieter:</strong> <?= htmlspecialchars($res['vermieter']) ?></p>
<p><strong>Objekt:</strong> <?= htmlspecialchars($res['titel']) ?> (<?= $res['region'] ?>)</p>
<p><strong>Zeitraum:</strong> <?= $res['von'] ?> bis <?= $res['bis'] ?> (<?= $tage ?> Tage)</p>
<p><strong>Erstellt am:</strong> <?= date("d.m.Y") ?></p>

<table>
    <tr>
        <th>Leistung</th>
        <th>Betrag</th>
    </tr>
    <tr>
        <td>Miete (<?= $tage ?> × <?= number_format($res['preis'], 2, ',', '.') ?> €)</td>
        <td><?= number_format($gesamt_miete, 2, ',', '.') ?> €</td>
    </tr>
    <tr>
        <td>Kaution (erstattbar)</td>
        <td><?= number_format($kaution, 2, ',', '.') ?> €</td>
    </tr>
    <tr>
        <td>MiFla Servicegebühr</td>
        <td>2,00 €</td>
    </
