<?php
session_start();
$db = new SQLite3('objekte.db');

if (!isset($_SESSION['nutzername'])) {
    die("❌ Du musst eingeloggt sein, um eine Anfrage zu senden.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $objekt_id = intval($_POST['objekt_id']);
    $von = $_POST['von'];
    $bis = $_POST['bis'];
    $mieter = $_SESSION['nutzername'];

    // Vermieter holen (aus der vermietungstabelle)
    $stmt = $db->prepare("SELECT vermieter FROM vermietungen WHERE id = :id");
    $stmt->bindValue(':id', $objekt_id, SQLITE3_INTEGER);
    $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    $vermieter = $result['vermieter'];

    // Reservierung eintragen
    $insert = $db->prepare("
        INSERT INTO reservierungen (objekt_id, vermieter, mieter, von, bis, status)
        VALUES (:objekt_id, :vermieter, :mieter, :von, :bis, 'ausstehend')
    ");
    $insert->bindValue(':objekt_id', $objekt_id, SQLITE3_INTEGER);
    $insert->bindValue(':vermieter', $vermieter);
    $insert->bindValue(':mieter', $mieter);
    $insert->bindValue(':von', $von);
    $insert->bindValue(':bis', $bis);
    $insert->execute();

    echo "<p style='color:lime;font-size:18px;'>✅ Deine Anfrage wurde erfolgreich gesendet!</p>";
    echo "<a href='meine_anfragen.php' style='color:#00ff88;'>➡️ Zu deinen Anfragen</a>";
}
?>
