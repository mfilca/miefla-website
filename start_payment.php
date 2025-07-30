<?php
require 'vendor/autoload.php'; // Nur wenn du Composer verwendest

\Stripe\Stripe::setApiKey('sk_test_...DEIN_SECRET_KEY_HIER...'); // ⚠️ DEIN KEY

session_start();
include 'db_verbindung.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['anfrage_id'])) {
  header("Location: login.html");
  exit();
}

$anfrage_id = intval($_GET['anfrage_id']);
$mieter_id = $_SESSION['user_id'];

// Mietdaten aus DB holen
$sql = "SELECT m.titel, m.preis_pro_tag, m.kaution
        FROM anfragen a
        JOIN mietobjekte m ON a.mietobjekt_id = m.id
        WHERE a.id = ? AND a.mieter_id = ? AND a.status = 'bestätigt'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $anfrage_id, $mieter_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  die("Anfrage nicht gefunden oder nicht bestätigt.");
}

$row = $result->fetch_assoc();
$betrag = ($row['preis_pro_tag'] + $row['kaution']) * 100; // in Cent

// Stripe-Checkout-Session erstellen
$checkout_session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'], // Später auch: 'sofort', 'klarna', ...
  'line_items' => [[
    'price_data' => [
      'currency' => 'eur',
      'product_data' => [
        'name' => 'Miete: ' . $row['titel'],
      ],
      'unit_amount' => $betrag,
    ],
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'success_url' => 'https://miefla.de/zahlung_erfolgreich.php?anfrage_id=' . $anfrage_id,
  'cancel_url' => 'https://miefla.de/meine_anfragen.php',
]);

// Weiterleitung zur Stripe-Seite
header("Location: " . $checkout_session->url);
exit();
