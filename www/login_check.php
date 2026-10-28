<?php
// Sessiecheck voor de klantpagina's: alleen ingelogde gebruikers, anders naar de 403-pagina.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: 403.php');
    exit;
}
