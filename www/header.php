<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trapkracht Fietsverhuur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- ?v=6 zorgt dat de browser de nieuwe CSS laadt in plaats van een oude uit de cache -->
    <link rel="stylesheet" href="css/style.css?v=6">
</head>

<body>
    <header>
        <a href="index.php" class="logo">
            <span class="logo-icon">🚲</span>
            <span>Trap<strong>kracht</strong></span>
        </a>
        <nav>
            <ul>
                <li><a href="index.php">Fietsen</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="mijn_reserveringen.php">Mijn reserveringen</a></li>
                    <li><a href="account.php">Mijn gegevens</a></li>
                    <li><a href="logout.php" class="btn btn-orange">Uitloggen</a></li>
                <?php else: ?>
                    <li><a href="login.php">Inloggen</a></li>
                    <li><a href="register.php" class="btn btn-orange">Registreren</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <?php // menu van het beheergedeelte: alleen voor medewerker en admin ?>
    <?php if (isset($_SESSION['user_id']) && in_array($_SESSION['role'], ['medewerker', 'admin'])): ?>
        <nav class="beheer-nav">
            <ul>
                <li><strong>Beheer:</strong></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="fietsen_index.php">Fietsen</a></li>
                <li><a href="reserveringen_index.php">Reserveringen</a></li>
                <li><a href="fietsen_nooit_verhuurd.php">Nooit verhuurd</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="statistieken.php">Statistieken</a></li>
                    <li><a href="logboek.php">Logboek</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <?php // success/error melding die een process-bestand in de sessie heeft gezet ?>
    <?php if (!empty($_SESSION['message'])) : ?>
        <div class="message message-<?php echo htmlspecialchars($_SESSION['message']['type']) ?> flash">
            <?php echo htmlspecialchars($_SESSION['message']['text']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
