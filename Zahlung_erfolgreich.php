<?php
session_start();
include 'db_verbindung.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['anfrage_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$anfrage_id = intval($_GET['anfrage_id']);

// Optional: Du könntest den Betrag erneut aus der Anfrage laden
$sql = "SELECT m.titel, m.preis_pro_tag, m.kaution
        FROM anfragen a
        JOIN mietobjekte m ON a.mietobjekt_id = m.id
        WHERE a.id = ? AND a.mieter_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $anfrage_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Anfrage nicht gefunden oder nicht erlaubt.");
}

$row = $result->fetch_assoc();
$betrag = ($row['preis_pro_tag'] + $row['kaution']) * 100; // in Cent

// Jetzt Zahlung speichern
$sql_insert = "INSERT INTO zahlungen (anfrage_id, nutzer_id, betrag, bezahlt_am)
               VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("iii", $anfrage_id, $user_id, $betrag);
$stmt->execute();

header("Location: meine_anfragen.php");
exit;
?>
