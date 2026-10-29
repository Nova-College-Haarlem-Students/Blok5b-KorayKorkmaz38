<?php
// Sessie- en rolcheck voor de statistieken en het logboek: alleen de admin.
// Niet ingelogd of geen admin (ook een medewerker niet)? Dan naar de 403-pagina.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: 403.php');
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: 403.php');
    exit;
}
