<?php

//database connection
$dbhost = "mariadb";
$dbuser = "user";
$dbpass = "password";
$dbname = "Blok5B";

try {
    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    // fouten als exception gooien, zodat we ze met try-catch kunnen opvangen
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!) en toon de 500-pagina
    error_log("Database connectie mislukt: " . $e->getMessage());
    require '500.php';
    exit;
}
