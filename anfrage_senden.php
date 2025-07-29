<?php
session_start();

// Objekt-ID muss per GET übergeben werden
if (!isset($_GET['id'])) {
  echo "<p style='color:red;'>❌ Kein Objekt ausgewählt.</p>";
  exit;
}

$objekt_id = intval($_GET['id']);

// Formular anzeigen
if ($_SERVER["REQUEST_METHOD"] != "POST") {
  echo "<h2 style='color:white;'>Anfrage stellen für Objekt-ID $objekt_id</h2>";
  echo "<form method='POST'>";
  echo "<label style='color:white;'>Dein Name:</label><br>";
  echo "<input name='name' required><br><br>";
  echo "<label style='color:white;'>Deine Nachricht (optional):</label><br>";
  echo "<textarea name='nachricht'></textarea><br><br>";
  echo "<input type='submit' value='Anfrage senden'>";
  echo "</form>";
  exit;
}

// Anfrage abschicken (später evtl. E-Mail oder Datenbank)
$name = htmlspecialchars($_POST['name']);
$nachricht = htmlspecialchars($_POST['nachricht']);

echo "<h2 style='color:lime;'>✅ Anfrage erfolgreich gesendet!</h2>";
echo "<p style='color:white;'>Angefragt von: <strong>$name</strong></p>";
echo "<p style='color:white;'>Nachricht: $nachricht</p>";
echo "<p style='color:white;'>Du wirst informiert, sobald der Vermieter antwortet.</p>";
?>
