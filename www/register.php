<?php
session_start();

// al ingelogd? dan hoef je geen account meer aan te maken
if (isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit;
}

require 'header.php';
?>

<main>
    <div class="container">
        <form action="register-process.php" method="post" class="login-form">
            <h1>Account aanmaken</h1>
            <p>Met een account kun je fietsen reserveren en je reserveringen bekijken.</p>

            <div>
                <label for="naam">Naam*</label>
                <input type="text" name="naam" id="naam" maxlength="255" required autofocus>
            </div>
            <div>
                <label for="email">Email*</label>
                <input type="email" name="email" id="email" maxlength="255" required>
            </div>
            <div>
                <label for="telefoon">Telefoon</label>
                <input type="text" name="telefoon" id="telefoon" maxlength="255">
            </div>
            <div>
                <label for="wachtwoord">Wachtwoord* (minimaal 8 tekens)</label>
                <input type="password" name="wachtwoord" id="wachtwoord" minlength="8" required>
            </div>
            <div>
                <label for="wachtwoord_herhaal">Herhaal wachtwoord*</label>
                <input type="password" name="wachtwoord_herhaal" id="wachtwoord_herhaal" minlength="8" required>
            </div>
            <!-- let op: er zit hier bewust GEEN veld voor de rol in, die zet de server -->
            <button type="submit" class="btn btn-orange">Account aanmaken</button>
            <p>Heb je al een account? <a href="login.php">Log hier in</a></p>
        </form>
    </div>
</main>

<?php require 'footer.php' ?>
