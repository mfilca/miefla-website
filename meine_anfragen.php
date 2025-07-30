 <?php
session_start();
include 'db_verbindung.php'; // Verbindung zur Datenbank

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Abfrage der Anfragen dieses Nutzers
$sql = "SELECT a.id, a.status, a.anfrage_datum, m.titel, m.bild_url, m.kaution, m.preis_pro_tag
        FROM anfragen a
        JOIN mietobjekte m ON a.mietobjekt_id = m.id
        WHERE a.mieter_id = ? ORDER BY a.anfrage_datum DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Anfragen – MiFla</title>
    <style>
        .pay-button {
            background: linear-gradient(45deg, #00cc99, #009973);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            margin-top: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s ease;
        }

        .pay-button:hover {
            background: linear-gradient(45deg, #009973, #007f66);
        }

        body {
            background-color: #121212;
            color: #f1f1f1;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .anfrage {
            background: #f1f1f1;
            color: #000;
            border: 1px solid #333;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }

        .anfrage img {
            width: 100px;
            height: auto;
        }

        .status {
            margin-top: 8px;
            font-weight: bold;
        }

        .status.offen { color: orange; }
        .status.bestätigt { color: lightgreen; }
        .status.abgelehnt { color: crimson; }

        .pdf-link {
            color: #ffce00;
            text-decoration: underline;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Meine Anfragen</h1>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="anfrage">';
        echo '<img src="' . htmlspecialchars($row['bild_url']) . '" alt="Objektbild"><br>';
        echo '<h3>' . htmlspecialchars($row['titel']) . '</h3>';
        echo '<p><strong>Angefragt am:</strong> ' . htmlspecialchars($row['anfrage_datum']) . '</p>';
        echo '<p><strong>Kaution:</strong> ' . htmlspecialchars($row['kaution']) . ' €</p>';
        echo '<p><strong>Preis/Tag:</strong> ' . htmlspecialchars($row['preis_pro_tag']) . ' €</p>';
        echo '<div class="status ' . strtolower($row['status']) . '">Status: ' . ucfirst($row['status']) . '</div>';

        if ($row['status'] === 'bestätigt') {
            echo '<a class="pdf-link" href="rechnung.php?anfrage_id=' . $row['id'] . '" target="_blank">📄 PDF-Rechnung herunterladen</a><br>';
            echo '<a class="pay-button" href="start_payment.php?anfrage_id=' . $row['id'] . '">💳 Jetzt bezahlen</a>';
        }

        echo '</div>';
    }
} else {
    echo '<p>Du hast bisher keine Anfragen gestellt.</p>';
}

$stmt->close();
$conn->close();
?>
</body>
</html>
