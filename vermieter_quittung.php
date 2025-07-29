<?php
$db = new SQLite3('objekte.db');

if (!isset($_GET['reservierung_id'])) {
    die("❌ Reservierungs-ID fehlt.");
}

$id = intval($_GET['reservierung_id']);
$res = $db->query("
    SELECT r.*, v.titel, v.preis, v.kaution, v.vermieter, v.region
    FROM reservierungen r
    LEFT JOIN vermietungen v ON r.objekt_id = v.id
    WHERE r.id = $id AND r.status = 'bestätigt'
")->fetchArray(SQLITE3_ASSOC);

if (!$res) {
    die("❌ Reservierung nicht gefunden oder nicht bestätigt.");
}

$tage = (strtotime($res['bis']) - strtotime($res['von'])) / (60 * 60 * 24) + 1;
$einnahmen = $tage * floatval($res['preis']);
$provision = 2.00;
$auszahlung = $einnahmen - $provision;
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Quittung für Vermieter – Reservierung #<?= $res['id'] ?></title>
    <style>
        body { background: #fff; color: #000; font-family: Arial, sans-serif; padding: 30px; }
        h1 { border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        td, th { padding: 8px; border: 1px solid #ccc; }
        th { background: #f4f4f4; }
        .btn { margin-top: 20px; background: #007bff; color: #fff; padding: 10px 20px; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h1>Quittung für Vermieter</h1>

<p><strong>Vermieter:</strong> <?= htmlspecialchars($res['vermieter']) ?></p>
<p><strong>Objekt:</strong> <?= htmlspecialchars($res['titel']) ?> (<?= htmlspecialchars($res['region']) ?>)</p>
<p><strong>Zeitraum:</strong> <?= $res['von'] ?> bis <?= $res['bis'] ?> (<?= $tage ?> Tage)</p>
<p><strong>Erstellt am:</strong> <?= date("d.m.Y") ?></p>

<table>
    <tr><th>Beschreibung</th><th>Betrag</th></tr>
    <tr><td>Mieteinnahmen (<?= $tage ?> × <?= number_format($res['preis'], 2, ',', '.') ?> €)</td><td><?= number_format($einnahmen, 2, ',', '.') ?> €</td></tr>
    <tr><td>Abzüglich MiFla-Provision</td><td>-<?= number_format($provision, 2, ',', '.') ?> €</td></tr>
    <tr><th>Auszahlungsbetrag</th><th><?= number_format($auszahlung, 2, ',', '.') ?> €</th></tr>
</table>

<button class="btn" onclick="downloadPDF()">📥 Quittung als PDF herunterladen</button>

<!-- PDF via html2pdf.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadPDF() {
    const element = document.body;
    html2pdf().set({
        margin: 10,
        filename: 'Quittung_Vermieter_<?= $res['id'] ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    }).from(element).save();
}
</script>

</body>
</html>
