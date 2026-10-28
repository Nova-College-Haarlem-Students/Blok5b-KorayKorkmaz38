<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("location: login.php");
    exit;
}

$email = trim($_POST['email_form'] ?? '');
$password = $_POST['password_form'] ?? '';

if ($email == '' || $password == '') {
    header("location: login.php?message=empty");
    exit;
}

require 'database.php';

try {
    // de naam van de rol ophalen met een LEFT JOIN, net als in Hoofdstuk 5
    $stmt = $conn->prepare("SELECT gebruikers.*, rollen.naam AS rol
                            FROM gebruikers
                            LEFT JOIN rollen ON gebruikers.rol_id = rollen.id
                            WHERE gebruikers.email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

if (!is_array($user)) {
    header("location: login.php?message=unknown");
    exit;
}

// wachtwoorden staan gehasht in de database, dus vergelijken met password_verify
if (!password_verify($password, $user['wachtwoord'])) {
    header("location: login.php?message=wrongpassword");
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['rol'];
$_SESSION['email'] = $user['email'];
$_SESSION['naam'] = $user['naam'];

$_SESSION['message'] = ['type' => 'success', 'text' => 'Welkom terug, ' . $user['naam'] . '!'];
header("location: index.php");
exit;
