<?php
require 'login_check.php';
require 'database.php';

try {
    // het id komt uit de sessie, zo kan een klant alleen zijn EIGEN gegevens zien
    $stmt = $conn->prepare("SELECT * FROM gebruikers WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

if (!$user) {
    header('Location: logout.php');
    exit;
}

require 'header.php';
?>

<main>
    <div class="container">
        <!-- Wachtwoord zit hier bewust niet in, dat is een apart proces -->
        <form method="POST" action="account-process.php" class="login-form">
            <h1>Mijn gegevens</h1>

            <div>
                <label for="naam">Naam*</label>
                <input type="text" id="naam" name="naam" maxlength="255" value="<?php echo htmlspecialchars($user['naam']) ?>" required>
            </div>
            <div>
                <label for="email">Email*</label>
                <input type="email" id="email" name="email" maxlength="255" value="<?php echo htmlspecialchars($user['email']) ?>" required>
            </div>
            <div>
                <label for="telefoon">Telefoon</label>
                <input type="text" id="telefoon" name="telefoon" maxlength="255" value="<?php echo htmlspecialchars($user['telefoon'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-orange">Opslaan</button>
        </form>
    </div>
</main>

<?php require 'footer.php' ?>
