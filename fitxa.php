<?php
require_once 'config_db.php';

$icones = [
    'taller'     => 'assets/img/taller.png',
    'concurs'    => 'assets/img/concurs.png',
    'xerrada'    => 'assets/img/xerrada.png',
    'exhibicio'  => 'assets/img/exhibicio.png',
    'botiga'     => 'assets/img/botiga.png',
    'concert'   => 'assets/img/concert.png'
];

try {

    session_start();
    $user_name = $_SESSION['session'] ?? null;

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Consulta preparada per seguretat contra SQL injection per trobar l'activitat
        $stmt = $pdo->prepare("SELECT * FROM activitats WHERE id = ?");
        $stmt->execute([$id]);
        $activitat = $stmt->fetch();

        if (!$activitat) {
            die("No s'ha trobat cap activitat amb aquest ID.");
        }

        // Consulta per a trobar els comentaris associats a aquesta activitat (amb el email de l'usuari)
        $stmt_comentaris = $pdo->prepare("SELECT c.*, u.nom AS nom_usuari FROM comentaris c JOIN usuaris u ON c.user_id = u.id WHERE c.activitat_id = ?");
        $stmt_comentaris->execute([$id]);
        $comentaris = $stmt_comentaris->fetchAll();

        // Si ens arriba un paràmetre per sumar vot, fem l'UPDATE directament
        if (isset($_GET['positiu'])) {
            $stmt_vot = $pdo->prepare("UPDATE comentaris SET positiu = positiu + 1 WHERE id = ?");
            $stmt_vot->execute([$_GET['positiu']]);
            header("Location: fitxa.php?id=" . $id);
            exit;
        }
        if (isset($_GET['negatiu'])) {
            $stmt_vot = $pdo->prepare("UPDATE comentaris SET negatiu = negatiu + 1 WHERE id = ?");
            $stmt_vot->execute([$_GET['negatiu']]);
            header("Location: fitxa.php?id=" . $id);
            exit;
        }

        // Si un usuari ja ha introduït un comentari, no li mostrem el formulari
        $stmt_ja_he_comentat = $pdo->prepare("SELECT * FROM comentaris WHERE user_id = ? AND activitat_id = ?");
        $stmt_ja_he_comentat->execute([$_SESSION['user_id'], $id]);
        $ja_he_comentat = $stmt_ja_he_comentat->fetch();

        if($ja_he_comentat) {
            // Si ja ha comentat, no li mostrem el formulari
            $mostrar_formulari = false;
        } else {
            $mostrar_formulari = true;
        }

    } else {
        die("No s'ha proporcionat cap ID.");
    }

} catch (\PDOException $e) {
    die("Error de connexió: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sakura Fest</title>
    <link rel="stylesheet" href="assets/styles/fitxa.css">
</head>
<body>
    <header class="header-sakura">
        
    <div class="header-container">
        <img src="assets/img/logo_sakura.png" alt="SakuraFest Logo" class="logo-header">
        <div id="titol-subtitol">
            <h1 class="titol-header">SAKURAFEST</h1>
            <p id="subtitol">17, 18 i 19 ABRIL 2026 - BARCELONA</p>
        </div>
    </div>
    <div id="botons-login">
        <?php if ($user_name): ?>
            <button onclick="location.href='logout.php'" id="boto-login">
                <img src="assets/img/icon-login.svg" alt="Logout" class="btn-filtre-icon">
                <?= htmlspecialchars($user_name) ?> · Logout
            </button>
        <?php else: ?>
            <button onclick="location.href='login.php'" id="boto-login">
                <img src="assets/img/icon-login.svg" alt="Login" class="btn-filtre-icon">
                Login
            </button>
        <?php endif; ?>
    </div>
    </header>

    <nav class="nav-menu">
        <a href="index.php" class="nav-link">Inici</a>
        <a href="calendari.php" class="nav-link">Calendari</a>
    </nav>

    <main>

        <?php 
                $tipus = strtolower($activitat['tipus']); 
                $imatge = $icones[$tipus] ?? 'assets/img/default.png'; 
        ?>

        <div id=fitxa-activitat>
            <img src="<?= $imatge ?>" alt="<?= $activitat['tipus'] ?>">
            <div class="text-fitxa">
                <h3 class="titol-fitxa">
                    <?= $activitat['nom_activitat'] ?>
                </h3>
                <p class="tipus-tematica-fitxa">
                    <?= ucfirst($activitat['tipus']) . ' · ' . ucfirst($activitat['tematica']) ?>   
                </p>
                <p class="dia-lloc-fitxa">
                <?= date("d-m-Y", strtotime($activitat['dia'])) . ' · ' 
                    . date("H:i", strtotime($activitat['hora_inici'])) . '-' 
                    . date("H:i", strtotime($activitat['hora_fi'])) ?>
                </p>
                <p class="dia-lloc-fitxa">
                <?= ucfirst($activitat['ubicacio']) ?>
                </p>

            </div>
            
            <div id=div-boto-preu-fitxa>
                <p class="preu-fitxa">
                    <?= strtoupper($activitat['preu'])  ?>
                </p>

                <button class="boto-ubicacio"
                    onclick="window.location.href='maps.php?id=<?= urlencode($activitat['id']); ?>'">
                    Ubicació
                </button>
            </div>

        </div>

        <div id="div-comentaris">
            <h2>COMENTARIS</h2>
            
            <?php if ($user_name && $mostrar_formulari): ?>
                <form class="form-comentari" method="POST" action="afegir_comentari.php">
                    <textarea id="text-comentari" name="comentari" placeholder="Escriu el teu comentari aquí..." rows="4"></textarea>
                    <input type="hidden" name="id_activitat" value="<?= $id ?>">
                    <button type="submit" class="boto-enviar-comentari">Enviar</button>  
                </form>
            <?php endif; ?>

            <?php foreach ($comentaris as $comentari): ?>
                <div class="fitxa-comentaris">
                    <div class="div-autor-hora">
                        <h3 class="autor-comentari">
                            <?= htmlspecialchars($comentari['nom_usuari']) ?>
                        </h3>
                        <p class="hora-comentari">
                            <?= date("d-m-Y H:i", strtotime($comentari['data_hora'])) ?>
                        </p>
                    </div>
                    <p class="text-comentari">
                        <?= htmlspecialchars($comentari['text']) ?>
                    </p>
                
                    <div class="div-boto-vots">
                        <button class="boto-positiu" onclick="window.location.href='fitxa.php?id=<?= $id ?>&positiu=<?= $comentari['id'] ?>'">👍 <?= $comentari['positiu'] ?></button>
                        <button class="boto-negatiu" onclick="window.location.href='fitxa.php?id=<?= $id ?>&negatiu=<?= $comentari['id'] ?>'">👎 <?= $comentari['negatiu'] ?></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>