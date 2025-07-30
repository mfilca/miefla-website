<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['anfrage_id'])) {
  header("Location: login.html");
  exit();
}
$anfrage_id = intval($_GET['anfrage_id']);
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Zahlung starten</title>
  <style>
    body {
      background: #121212;
      color: white;
      font-family: Arial;
      text-align: center;
      padding: 40px;
    }
    a.btn {
      background: #ff3b3f;
      color: white;
      padding: 14px 28px;
      text-decoration: none;
      border-radius: 6px;
      font-size: 18px;
      display: inline-block;
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <h1>Jetzt bezahlen</h1>
  <p>Du wirst zur sicheren Stripe-Zahlung weitergeleitet.</p>
  <a class="btn" href="start_payment.php?anfrage_id=<?php echo $anfrage_id; ?>">Jetzt bezahlen</a>
</body>
</html>
