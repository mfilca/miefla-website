<?php
session_start();
include 'db_verbindung.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

if (!isset($_GET['anfrage_id'])) {
  echo "Keine Anfrage-ID angegeben.";
  exit();
}

$anfrage_id = intval($_GET['anfrage_id']);
$user_id = $_SESSION['user_id'];

// Daten aus Anfrage + Mietobjekt holen
$sql = "SELECT a.anfrage_datum, m.titel, m.preis_pro_tag, m.kaution, u.vorname, u.nachname, u.email
        FROM anfragen a
        JOIN mietobjekte m ON a.mietobjekt_id = m.id
        JOIN nutzer u ON a.mieter_id = u.id
        WHERE a.id = ? AND a.mieter_id = ? AND a.status = 'bestätigt'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $anfrage_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Keine gültige bestätigte Anfrage gefunden.";
  exit();
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Rechnung – MiFla</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    body {
      background-color: #121212;
      color: #f1f1f1;
      font-family: Arial, sans-serif;
      padding: 30px;
    }
    #pdf-inhalt {
      background: #fff;
      color: #000;
      padding: 30px;
      border-radius: 8px;
      max-width: 600px;
      margin: 0 auto;
    }
    h1 {
      text-align: center;
      color: #333;
    }
    .button {
      margin-top: 20px;
      background-color: #ff3b3f;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }
  </style>
</head>
<body>

<div id="pdf-inhalt">
  <h1>MiFla – Miet-Rechnung</h1>
  <p><strong>Rechnungsdatum:</strong> <?php echo date("d.m.Y"); ?></p>
  <p><strong>Mieter:</strong> <?php echo htmlspecialchars($row['vorname'] . " " . $row['nachname']); ?></p>
  <p><strong>E-Mail:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
  <hr>
  <p><strong>Mietobjekt:</strong> <?php echo htmlspecialchars($row['titel']); ?></p>
  <p><strong>Anfrage gestellt am:</strong> <?php echo htmlspecialchars($row['anfrage_datum']); ?></p>
  <p><strong>Preis pro Tag:</strong> <?php echo htmlspecialchars($row['preis_pro_tag']); ?> €</p>
  <p><strong>Kaution:</strong> <?php echo htmlspecialchars($row['kaution']); ?> €</p>
  <hr>
  <p><strong>Gesamtbetrag:</strong> <?php echo htmlspecialchars($row['preis_pro_tag'] + $row['kaution']); ?> €</p>
  <p style="margin-top: 30px;">Diese Rechnung dient der Abrechnung über die Plattform <strong>MiFla – Mieten mit Flair</strong>.</p>
</div>

<button class="button" onclick="downloadPDF()">PDF herunterladen</button>

<script>
  function downloadPDF() {
    const element = document.getElementById('pdf-inhalt');
    html2pdf().from(element).save('mifla_rechnung.pdf');
  }
</script>

</body>
</html>
