<?php
session_start();

// al ingelogd? dan hoef je niet nog een keer in te loggen
if (isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit;
}

$messages = [
    'empty'         => 'Vul je e-mail en wachtwoord in.',
    'unknown'       => 'E-mailadres is bij ons onbekend.',
    'wrongpassword' => 'Wachtwoord is onjuist.',
];
$message = $messages[$_GET['message'] ?? ''] ?? '';

require 'header.php';
?>

<main>
    <div class="container">
        <form action="login-process.php" method="post" class="login-form">
            <h1>Inloggen</h1>

            <?php if ($message) : ?>
                <p class="message message-error"><?php echo $message ?></p>
            <?php endif; ?>

            <div>
                <label for="email_form">Email</label>
                <input type="email" name="email_form" id="email_form" required autofocus>
            </div>
            <div>
                <label for="password_form">Wachtwoord</label>
                <input type="password" name="password_form" id="password_form" required>
            </div>
            <button type="submit" class="btn btn-orange">Inloggen</button>
            <p>Nog geen account? <a href="register.php">Maak er een aan</a></p>
        </form>
    </div>
</main>

<?php require 'footer.php' ?>
