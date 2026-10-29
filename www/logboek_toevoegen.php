<?php
// Eén plek om een beheeractie in het logboek te zetten, zodat deze INSERT
// niet in elk process-bestand opnieuw staat (geen dubbele code).
// Gebruik: logboek($conn, 'Fiets 3 (Sparta) uit de verhuur gehaald');
function logboek($conn, $actie)
{
    // wie het deed komt uit de sessie: de ingelogde medewerker of admin
    $stmt = $conn->prepare("INSERT INTO logboek (gebruiker_id, actie, created_at) VALUES (:gebruiker_id, :actie, NOW())");
    $stmt->execute([
        'gebruiker_id' => $_SESSION['user_id'],
        'actie'        => $actie,
    ]);
}
