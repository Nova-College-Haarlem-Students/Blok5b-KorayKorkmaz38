<?php
// Sessie- en rolcheck voor het beheergedeelte: alleen medewerker en admin.
// Niet ingelogd of een andere rol (klant)? Dan naar de 403-pagina.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: 403.php');
    exit;
}

if (!in_array($_SESSION['role'], ['medewerker', 'admin'])) {
    header('Location: 403.php');
    exit;
}
