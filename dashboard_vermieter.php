<?php
session_start();
include 'db_verbindung.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

$vermieter_id = $_SESSION['user_id'];

// Anfragen für Objekte dieses Vermieters
$sql = "SELECT a.id, a.status, a.anfrage_datum, m.titel, u.vorname, u.nachname
        FROM anfragen a
        JOIN mietobjekte m ON a.mietobjekt_id = m.id
        JOIN nutzer u ON a.mieter_id = u.id
        WHERE m.vermieter_id = ?
        ORDER BY a.anfrage_datum DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vermieter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Erhaltene Anfragen – MiFla</title>
  <style>
    body { background-color: #121212; color: #f1f1f1; font-family: Arial, sans-serif; padding: 20px; }
    .anfrage { background: #1f1f1f; border: 1px solid #333; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    a.link { color: #ffcc00; text-decoration: underline; }
  </style>
</head>
<body>

<h1>Erhaltene Anfragen</h1>

<?php
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<div class='anfrage'>";
    echo "<p><strong>Mietobjekt:</strong> " . htmlspecialchars($row['titel']) . "</p>";
    echo "<p><strong>Anfrage von:</strong> " . htmlspecialchars($row['vorname'] . " " . $row['nachname']) . "</p>";
    echo "<p><strong>Datum:</strong> " . htmlspecialchars($row['anfrage_datum']) . "</p>";
    echo "<p><strong>Status:</strong> " . ucfirst($row['status']) . "</p>";

    // Quittung nur zeigen, wenn Anfrage bestätigt ist
    if (strtolower($row['status']) === 'bestätigt') {
      echo "<p><a class='link' href='quittung.php?anfrage_id=" . $row['id'] . "' target='_blank'>Quittung anzeigen</a></p>";
    }

    echo "</div>";
  }
} else {
  echo "<p>Keine Anfragen vorhanden.</p>";
}

$stmt->close();
$conn->close();
?>

</body>
</html>
