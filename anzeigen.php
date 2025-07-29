<?php
$db = new SQLite3('objekte.db');

$von = $_POST['von'];
$bis = $_POST['bis'];

$objekte = $db->query("SELECT * FROM vermietungen");

while ($objekt = $objekte->fetchArray(SQLITE3_ASSOC)) {
    $objekt_id = $objekt['id'];

    // Prüfe auf bestehende bestätigte Reservierungen im gewählten Zeitraum
    $stmt = $db->prepare("
        SELECT COUNT(*) as anzahl FROM reservierungen
        WHERE objekt_id = :id
        AND status = 'bestätigt'
        AND (
            (von <= :bis AND bis >= :von)
        )
    ");
    $stmt->bindValue(':id', $objekt_id, SQLITE3_INTEGER);
    $stmt->bindValue(':von', $von);
    $stmt->bindValue(':bis', $bis);
    $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    $reserviert = $res['anzahl'] > 0;

    // Anzeige starten
    echo "<div class='objekt'>";
    echo "<h2>" . htmlspecialchars($objekt['titel']) . "</h2>";
    echo "<p><strong>Region:</strong> " . htmlspecialchars($objekt['region']) . "</p>";

    if ($reserviert) {
        echo "<p style='color: orange;'>⚠️ In diesem Zeitraum bereits reserviert.</p>";
    } else {
        echo "<p style='color: lime;'>✅ Verfügbar im gewählten Zeitraum.</p>";
        echo "<form method='post' action='vermietung_anzeigen.php'>";
        echo "<input type='hidden' name='objekt_id' value='{$objekt['id']}'>";
        echo "<button>Mietangebot ansehen</button>";
        echo "</form>";
    }

    echo "</div>";
}
?>
