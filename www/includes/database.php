<?php
// Databaseconnectie (PDO). Wordt in elke pagina met require_once ingeladen.

$dbHost = 'mariadb';
$dbNaam = 'Blok5B';
$dbGebruiker = 'user';
$dbWachtwoord = 'password';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbNaam;charset=utf8mb4",
        $dbGebruiker,
        $dbWachtwoord,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // Geen verbinding: de bezoeker ziet de 500-pagina, niet de foutmelding
    require __DIR__ . '/../errors/500.php';
    exit;
}
