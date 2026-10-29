<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trapkracht Fietsverhuur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- ?v=4 zorgt dat de browser de nieuwe CSS laadt in plaats van een oude uit de cache -->
    <link rel="stylesheet" href="css/style.css?v=4">
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

    <?php // success/error melding die een process-bestand in de sessie heeft gezet ?>
    <?php if (!empty($_SESSION['message'])) : ?>
        <div class="message message-<?php echo htmlspecialchars($_SESSION['message']['type']) ?> flash">
            <?php echo htmlspecialchars($_SESSION['message']['text']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
