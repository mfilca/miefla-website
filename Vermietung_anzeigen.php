<?php
$db = new SQLite3('objekte.db');
$ergebnisse = $db->query("SELECT * FROM vermietungen");
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Alle Mietangebote – MiFla</title>
    <style>
        body {
            background-color: #111;
            color: #fff;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .objekt {
            border: 1px solid #444;
            padding: 15px;
            margin-bottom: 30px;
            background-color: #222;
            border-radius: 8px;
        }
        .bilder img {
            height: 150px;
            margin-right: 10px;
            border-radius: 4px;
        }
        .zusatz label {
            display: block;
            margin: 5px 0;
        }
        .summe {
            margin-top: 15px;
            font-weight: bold;
            color: #00ff88;
        }
    </style>
</head>
<body>

<h1>🔧 Mietangebote auf MiFla</h1>

<?php while ($objekt = $ergebnisse->fetchArray(SQLITE3_ASSOC)): ?>
    <div class="objekt">
        <h2><?= htmlspecialchars($objekt['titel']) ?></h2>
        <p><strong>Kategorie:</strong> <?= htmlspecialchars($objekt['kategorie']) ?> – <?= htmlspecialchars($objekt['unterkategorie']) ?></p>
        <p><strong>Beschreibung:</strong> <?= nl2br(htmlspecialchars($objekt['beschreibung'])) ?></p>
        <p><strong>Region:</strong> <?= htmlspecialchars($objekt['region']) ?></p>
        <p><strong>Verfügbar von:</strong> <?= $objekt['verfuegbar_von'] ?> bis <?= $objekt['verfuegbar_bis'] ?></p>
        <p><strong>Preis:</strong> <?= number_format($objekt['preis'], 2) ?> €</p>
        <p><strong>Kaution:</strong> <?= number_format($objekt['kaution'], 2) ?> €</p>

        <div class="bilder">
            <?php
            $bilder = json_decode($objekt['bilder'], true);
            foreach ($bilder as $bild) {
                echo "<img src='$bild' alt='Bild'>";
            }
            ?>
        </div>

        <form method="post" action="anfrage_senden.php">
            <input type="hidden" name="objekt_id" value="<?= $objekt['id'] ?>">

            <div class="zusatz">
                <p><strong>Optional zubuchbare Zusatzoptionen:</strong></p>
                <?php
                $zusatzoptionen = json_decode($objekt['zusatzoptionen'], true);
                foreach ($zusatzoptionen as $index => $option):
                ?>
                    <label>
                        <input type="checkbox" name="zusatz[]" value="<?= $index ?>" data-preis="<?= $option['preis'] ?>" data-kaution="<?= $option['kaution'] ?>">
                        <?= htmlspecialchars($option['beschreibung']) ?> (+<?= number_format($option['preis'], 2) ?> €, Kaution: <?= number_format($option['kaution'], 2) ?> €)
                    </label>
                <?php endforeach; ?>
            </div>

            <p class="summe">Gesamtpreis: <span class="gesamt"><?= number_format($objekt['preis'] + 2, 2) ?></span> € (inkl. 2 € Plattformgebühr)</p>

            <button type="submit">Anfrage stellen</button>
        </form>
    </div>
<?php endwhile; ?>

<script>
    document.querySelectorAll('.objekt').forEach(objekt => {
        const checkboxes = objekt.querySelectorAll('input[type="checkbox"]');
        const ausgabe = objekt.querySelector('.gesamt');
        const grundpreis = parseFloat(ausgabe.textContent.replace(',', '.'));

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                let summe = grundpreis;
                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        summe += parseFloat(cb.dataset.preis) || 0;
                    }
                });
                ausgabe.textContent = summe.toFixed(2).replace('.', ',');
            });
        });
    });
</script>

</body>
</html>
