<?php
$db = new SQLite3('objekte.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $objekt_id = intval($_POST['objekt_id']);
    $von = $_POST['von'];
    $bis = $_POST['bis'];
    $vermieter = $_POST['vermieter']; // aus Session oder manuell
    $mieter = 'INTERNE_BLOCKIERUNG';

    $db->prepare("
        INSERT INTO reservierungen (objekt_id, vermieter, mieter, von, bis, status)
        VALUES (:objekt_id, :vermieter, :mieter, :von, :bis, 'bestätigt')
    ")->execute([
        ':objekt_id' => $objekt_id,
        ':vermieter' => $vermieter,
        ':mieter' => $mieter,
        ':von' => $von,
        ':bis' => $bis
    ]);

    echo "<p style='color:lime;'>✅ Zeitraum wurde manuell blockiert.</p>";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Manuell blockieren</title>
    <style>
        body { background: #111; color: white; padding: 20px; font-family: Arial; }
        input, select { padding: 8px; margin: 5px 0; width: 100%; }
        button { padding: 10px; background: #e60023; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h2>⛔ Zeitraum manuell blockieren</h2>
<form method="post">
    <label>Objekt ID:</label>
    <input type="number" name="objekt_id" required>

    <label>Vermieter (Name oder E-Mail):</label>
    <input type="text" name="vermieter" required>

    <label>Von (Datum):</label>
    <input type="date" name="von" required>

    <label>Bis (Datum):</label>
    <input type="date" name="bis" required>

    <button>Blockieren</button>
</form>

</body>
</html>
