<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("location: register.php");
    exit;
}

require 'database.php';

$naam               = trim($_POST['naam'] ?? '');
$email              = trim($_POST['email'] ?? '');
$telefoon           = trim($_POST['telefoon'] ?? '');
$wachtwoord         = $_POST['wachtwoord'] ?? '';
$wachtwoord_herhaal = $_POST['wachtwoord_herhaal'] ?? '';

// valideren voordat er iets wordt opgeslagen
if ($naam == '' || $email == '' || $wachtwoord == '') {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vul je naam, email en wachtwoord in.'];
    header("location: register.php");
    exit;
}
if (strlen($naam) > 255 || strlen($email) > 255 || strlen($telefoon) > 255) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Een van de velden is te lang.'];
    header("location: register.php");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Dit is geen geldig e-mailadres.'];
    header("location: register.php");
    exit;
}
if (strlen($wachtwoord) < 8) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Je wachtwoord moet minimaal 8 tekens zijn.'];
    header("location: register.php");
    exit;
}
if ($wachtwoord !== $wachtwoord_herhaal) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'De wachtwoorden zijn niet hetzelfde.'];
    header("location: register.php");
    exit;
}

// telefoon is niet verplicht, leeg wordt NULL in de database
if ($telefoon == '') {
    $telefoon = null;
}

try {
    // bestaat dit e-mailadres al?
    $stmt = $conn->prepare("SELECT id FROM gebruikers WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Er bestaat al een account met dit e-mailadres.'];
        header("location: register.php");
        exit;
    }

    // de rol komt NOOIT uit het formulier: wie zich registreert is altijd klant
    $stmt = $conn->prepare("SELECT id FROM rollen WHERE naam = 'klant'");
    $stmt->execute();
    $rol_id = $stmt->fetchColumn();

    // wachtwoord gehasht opslaan
    $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO gebruikers (rol_id, naam, email, wachtwoord, telefoon, created_at, updated_at)
                            VALUES (:rol_id, :naam, :email, :wachtwoord, :telefoon, NOW(), NOW())");
    $stmt->execute([
        'rol_id'     => $rol_id,
        'naam'       => $naam,
        'email'      => $email,
        'wachtwoord' => $hash,
        'telefoon'   => $telefoon,
    ]);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

$_SESSION['message'] = ['type' => 'success', 'text' => 'Je account is aangemaakt. Je kunt nu inloggen.'];
header("location: login.php");
exit;
