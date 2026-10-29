<?php
require 'admin_check.php';
require 'database.php';

try {
    // wie wat heeft gedaan: het logboek met de naam en rol van de gebruiker erbij
    $stmt = $conn->prepare("SELECT logboek.*, gebruikers.naam AS gebruiker_naam, rollen.naam AS rol
                            FROM logboek
                            LEFT JOIN gebruikers ON logboek.gebruiker_id = gebruikers.id
                            LEFT JOIN rollen ON gebruikers.rol_id = rollen.id
                            ORDER BY logboek.created_at DESC, logboek.id DESC");
    $stmt->execute();
    $regels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

require 'header.php';
?>
<main>
    <div class="container">
        <h1 class="section-title">Logboek <span>(<?php echo count($regels) ?>)</span></h1>

        <?php if (empty($regels)) : ?>
            <p class="empty">Er zijn nog geen beheeracties.</p>
        <?php else : ?>
            <div class="tabel-wrapper">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Wanneer</th>
                            <th>Wie</th>
                            <th>Actie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($regels as $regel) : ?>
                            <tr>
                                <td><?php echo date('d-m-Y H:i', strtotime($regel['created_at'])) ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($regel['gebruiker_naam']) ?></strong>
                                    <span class="badge"><?php echo htmlspecialchars($regel['rol']) ?></span>
                                </td>
                                <td class="opmerking"><?php echo htmlspecialchars($regel['actie']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require 'footer.php' ?>
