<?php
require 'login_check.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: account.php');
    exit;
}

require 'database.php';

// het id komt uit de sessie en NIET uit het formulier,
// anders zou je het id kunnen veranderen en iemand anders aanpassen
$id       = $_SESSION['user_id'];
$naam     = trim($_POST['naam'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telefoon = trim($_POST['telefoon'] ?? '');

// valideren
if ($naam == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vul een naam en een geldig e-mailadres in.'];
    header('Location: account.php');
    exit;
}
if (strlen($naam) > 255 || strlen($email) > 255 || strlen($telefoon) > 255) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Een van de velden is te lang.'];
    header('Location: account.php');
    exit;
}

// telefoon is niet verplicht, leeg wordt NULL in de database
if ($telefoon == '') {
    $telefoon = null;
}

try {
    // gebruikt iemand anders dit e-mailadres al?
    $stmt = $conn->prepare("SELECT id FROM gebruikers WHERE email = :email AND id != :id");
    $stmt->execute(['email' => $email, 'id' => $id]);
    if ($stmt->fetch()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Dit e-mailadres wordt al door een ander account gebruikt.'];
        header('Location: account.php');
        exit;
    }

    // LET OP: vergeet WHERE niet — anders worden ALLE gebruikers bijgewerkt!
    // Wachtwoord en rol worden hier NIET aangepast
    $stmt = $conn->prepare(
        "UPDATE gebruikers
         SET naam = :naam,
             email = :email,
             telefoon = :telefoon,
             updated_at = NOW()
         WHERE id = :id"
    );
    $stmt->execute([
        'naam'     => $naam,
        'email'    => $email,
        'telefoon' => $telefoon,
        'id'       => $id,
    ]);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

// sessie bijwerken zodat de nieuwe naam en email meteen kloppen
$_SESSION['naam'] = $naam;
$_SESSION['email'] = $email;

$_SESSION['message'] = ['type' => 'success', 'text' => 'Je gegevens zijn opgeslagen.'];
header('Location: account.php');
exit;
