<?php
// Empfängeradresse
$empfaenger = "mikeyflachs@googlemail.com";

// Formular-Daten auslesen
$kategorie = $_POST['kategorie'] ?? '';
$beschreibung = $_POST['beschreibung'] ?? '';
$region = $_POST['region'] ?? '';
$preis = $_POST['preis'] ?? '';
$zeitraum = $_POST['zeitraum'] ?? '';
$dateiname = $_FILES['bild']['name'] ?? 'kein Bild';

// Betreff und Nachricht
$betreff = "Neue Vermietungsanfrage über MiFla";
$nachricht = "Neue Anfrage:\n\n";
$nachricht .= "Kategorie: $kategorie\n";
$nachricht .= "Beschreibung: $beschreibung\n";
$nachricht .= "Region: $region\n";
$nachricht .= "Preisvorstellung: $preis\n";
$nachricht .= "Verfügbarkeitszeitraum: $zeitraum\n";
$nachricht .= "Dateiname: $dateiname\n";

// Header
$headers = "From: mifla-website@deinserver.de";

// Mail senden
$mail_gesendet = mail($empfaenger, $betreff, $nachricht, $headers);

// Rückmeldung an Benutzer
if ($mail_gesendet) {
    echo "<h2 style='color:lime;'>Danke! Deine Anfrage wurde gesendet.</h2>";
} else {
    echo "<h2 style='color:red;'>Fehler! Anfrage konnte nicht gesendet werden.</h2>";
}
?>
