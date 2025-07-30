<?php
session_start();
include 'db_verbindung.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['anfrage_id'])) {
  header("Location: login.html");
  exit();
}

$anfrage_id = intval($_GET['anfrage_id']);
$mieter_id = $_SESSION['user_id'];

// Mietdaten holen
$sql = "SELECT m.titel, m.preis_pro_tag, m.kaution
        FROM anfragen a
        JOIN mietobjekte m ON a.mietobjekt_id = m.id
        WHERE a.id = ? AND a.mieter_id = ? AND a.status = 'bestätigt'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $anfrage_id, $mieter_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  die("Anfrage nicht gefunden oder nicht erlaubt.");
}

$row = $result->fetch_assoc();
$betrag = ($row['preis_pro_tag'] + $row['kaution']) * 100; // in Cent

// Stripe Session starten (ohne Composer)
$stripe_api_key = 'sk_test_51RqeF3KUH...'; // DEIN SCHLÜSSEL HIER!
$url = 'https://api.stripe.com/v1/checkout/sessions';

$data = [
  'payment_method_types[]' => 'card',
  'line_items[0][price_data][currency]' => 'eur',
  'line_items[0][price_data][product_data][name]' => 'Miete: ' . $row['titel'],
  'line_items[0][price_data][unit_amount]' => $betrag,
  'line_items[0][quantity]' => 1,
  'mode' => 'payment',
  'success_url' => 'https://miefla.de/zahlung_erfolgreich.php?anfrage_id=' . $anfrage_id,
  'cancel_url' => 'https://miefla.de/meine_anfragen.php',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, $stripe_api_key . ":");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$response = curl_exec($ch);
curl_close($ch);

$session = json_decode($response, true);

if (isset($session['url'])) {
  header("Location: " . $session['url']);
  exit();
} else {
  echo "Fehler bei Stripe: ";
  print_r($session);
}
