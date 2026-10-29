<?php
require 'medewerker_check.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: fietsen_index.php');
    exit;
}

require 'database.php';
require 'logboek_toevoegen.php';

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: 404.php');
    exit;
}

$id = (int) $_POST['id'];

try {
    // Soft delete: in plaats van DELETE FROM fietsen
    $stmt = $conn->prepare("UPDATE fietsen SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL");
    $stmt->execute(['id' => $id]);

    // geen rij aangepast = fiets bestaat niet (of is al uit de verhuur)
    if ($stmt->rowCount() === 0) {
        header('Location: 404.php');
        exit;
    }

    // merk ophalen voor een duidelijke regel in het logboek
    $stmt = $conn->prepare("SELECT merk FROM fietsen WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $merk = $stmt->fetchColumn();

    logboek($conn, 'Fiets ' . $id . ' (' . $merk . ') uit de verhuur gehaald');
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

$_SESSION['message'] = ['type' => 'success', 'text' => 'Fiets ' . $merk . ' is uit de verhuur gehaald.'];
header('Location: fietsen_index.php');
exit;
