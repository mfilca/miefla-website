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

// Dauer berechnen
$tage = (strtotime($res['bis']) - strtotime($res['von'])) / (60 * 60 * 24) + 1;
$gesamt = $tage * floatval($res['preis']) + floatval($res['kaution']) + 2.00; // inkl. 2 € MiFla-Gebühr
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Rechnung #<?= $res['id'] ?></title>
    <style>
        body { background: #fff; color: #000; font-family: Arial; padding: 30px; }
        h1 { border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        td, th { padding: 8px; border: 1px solid #ccc; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>

<h1>Rechnung #<?= $res['id'] ?></h1>
<p><strong>Für:</strong> <?= htmlspecialchars($res['mieter']) ?></p>
<p><strong>Von:</strong> MiFla – Mieten mit Flair</p>
<p><strong>Datum:</strong> <?= date("d.m.Y") ?></p>

<table>
    <tr><th>Leistung</th><th>Betrag</th></tr>
    <tr>
        <td>Miete: <?= $tage ?> Tage × <?= number_format($res['preis'], 2, ',', '.') ?> €</td>
        <td><?= number_format($tage * $res['preis'], 2, ',', '.') ?> €</td>
    </tr>
    <tr>
        <td>Kaution (erstattbar)</td>
        <td><?= number_format($res['kaution'], 2, ',', '.') ?> €</td>
    </tr>
    <tr>
        <td>MiFla Servicegebühr</td>
        <td>2,00 €</td>
    </tr>
    <tr>
        <th>Gesamt</th>
        <th><?= number_format($gesamt, 2, ',', '.') ?> €</th>
    </tr>
</table>

</body>
</html>
