<?php
session_start();
require 'database.php';

// Controleer of id meegegeven is
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: 404.php');
    exit;
}

$id = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("SELECT fietsen.*, fiets_types.naam AS type_naam
                            FROM fietsen
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            WHERE fietsen.id = :id");
    $stmt->execute(['id' => $id]);
    $fiets = $stmt->fetch(PDO::FETCH_ASSOC);

    // Als fiets niet bestaat, toon 404
    if (!$fiets) {
        header('Location: 404.php');
        exit;
    }
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

require 'header.php';
?>

<main>
    <div class="container">
        <div class="product-detail">
            <div class="row">
                <div class="col">
                    <img src="<?php echo isset($fiets['foto']) ? 'images/' . htmlspecialchars($fiets['foto']) : 'https://placehold.co/400' ?>"
                        alt="<?php echo htmlspecialchars($fiets['merk']) ?>">
                </div>
                <div class="col">
                    <span class="badge"><?php echo htmlspecialchars($fiets['type_naam']) ?></span>
                    <h1><?php echo htmlspecialchars($fiets['merk']) ?></h1>
                    <ul class="specs">
                        <li><strong>Type</strong> <?php echo htmlspecialchars($fiets['type_naam']) ?></li>
                        <li><strong>Merk</strong> <?php echo htmlspecialchars($fiets['merk']) ?></li>
                        <li><strong>Framemaat</strong> <?php echo htmlspecialchars($fiets['framemaat']) ?></li>
                    </ul>
                    <p class="price">€ <?php echo number_format($fiets['dagprijs'], 2, ',', '') ?> <span>/ dag</span></p>
                    <?php if ($fiets['in_verhuur'] == 0) : ?>
                        <p class="message message-error">Deze fiets is tijdelijk uit de verhuur.</p>
                    <?php elseif (isset($_SESSION['user_id'])) : ?>
                        <!-- geen action: het formulier wordt met fetch() (AJAX) verstuurd, zie het script onderaan -->
                        <form class="reserveer-form" id="reserveerForm">
                            <h3>Reserveren</h3>
                            <div class="datums">
                                <div>
                                    <label for="ophaaldatum">Ophaaldatum</label>
                                    <input type="date" name="ophaaldatum" id="ophaaldatum" min="<?php echo date('Y-m-d') ?>" required>
                                </div>
                                <div>
                                    <label for="terugbrengdatum">Terugbrengdatum</label>
                                    <input type="date" name="terugbrengdatum" id="terugbrengdatum" min="<?php echo date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <!-- hier komt het antwoord van de server (AJAX) -->
                            <p class="message" id="beschikbaarMelding" hidden></p>
                            <p id="mijnLink" hidden><a href="mijn_reserveringen.php">Bekijk mijn reserveringen</a></p>
                            <button type="button" class="btn btn-light" id="checkBtn">Is hij vrij?</button>
                            <button type="submit" class="btn btn-orange">Reserveer</button>
                        </form>
                    <?php else : ?>
                        <p><a href="login.php" class="btn btn-orange">Log in om te reserveren</a></p>
                    <?php endif; ?>
                    <p><a href="index.php" class="btn btn-light">Terug naar alle fietsen</a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkBtn = document.querySelector('#checkBtn');
        const melding = document.querySelector('#beschikbaarMelding');
        if (checkBtn && melding) {
            checkBtn.addEventListener('click', function () {

                // AJAX call naar de server maken, de pagina herlaadt niet
                fetch('beschikbaar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        fiets_id: <?php echo (int) $fiets['id']; ?>,
                        ophaaldatum: document.querySelector('#ophaaldatum').value,
                        terugbrengdatum: document.querySelector('#terugbrengdatum').value
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        // groen als de fiets vrij is, anders rood
                        if (data.success && data.vrij) {
                            melding.className = 'message message-success';
                        } else {
                            melding.className = 'message message-error';
                        }
                        melding.textContent = data.melding;
                        melding.hidden = false;
                    })
                    .catch(error => {
                        console.table('Serverfout: ' + error);
                    });
            });
        }

        // Reserveren via AJAX: de pagina wisselt niet
        const reserveerForm = document.querySelector('#reserveerForm');
        const mijnLink = document.querySelector('#mijnLink');
        if (reserveerForm && melding) {
            reserveerForm.addEventListener('submit', function (e) {
                e.preventDefault(); // zorgt dat de browser niet naar een andere pagina gaat

                fetch('reservering-process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        fiets_id: <?php echo (int) $fiets['id']; ?>,
                        ophaaldatum: document.querySelector('#ophaaldatum').value,
                        terugbrengdatum: document.querySelector('#terugbrengdatum').value
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // gelukt: groene melding, datums leegmaken en link naar mijn reserveringen tonen
                            melding.className = 'message message-success';
                            reserveerForm.reset();
                            mijnLink.hidden = false;
                        } else {
                            melding.className = 'message message-error';
                            mijnLink.hidden = true;
                        }
                        melding.textContent = data.melding;
                        melding.hidden = false;
                    })
                    .catch(error => {
                        console.table('Serverfout: ' + error);
                    });
            });
        }
    });
</script>
<?php require 'footer.php' ?>
