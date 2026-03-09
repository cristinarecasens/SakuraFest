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
    $user_name = $_SESSION['user_name'] ?? null;
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
        
        // Votació: només permetre un vot per usuari per comentari
        if ((isset($_GET['positiu']) || isset($_GET['negatiu'])) && isset($_SESSION['user_id'])) {
            $comment_id = isset($_GET['positiu']) ? $_GET['positiu'] : $_GET['negatiu'];
            $user_id = $_SESSION['user_id'];
            $act_id = $id;
            // Comprova si ja ha votat aquest comentari
            $stmt_check = $pdo->prepare("SELECT id FROM vot_comentari WHERE act_id = ? AND comment_id = ? AND user_id = ?");
            $stmt_check->execute([$act_id, $comment_id, $user_id]);
            if (!$stmt_check->fetch()) {
                // No ha votat encara, permet votar i registra
                if (isset($_GET['positiu'])) {
                    $stmt_vot = $pdo->prepare("UPDATE comentaris SET positiu = positiu + 1 WHERE id = ?");
                    $stmt_vot->execute([$comment_id]);
                } else {
                    $stmt_vot = $pdo->prepare("UPDATE comentaris SET negatiu = negatiu + 1 WHERE id = ?");
                    $stmt_vot->execute([$comment_id]);
                }
                // Inserir registre de vot
                $stmt_insert = $pdo->prepare("INSERT INTO vot_comentari (act_id, comment_id, user_id) VALUES (?, ?, ?)");
                $stmt_insert->execute([$act_id, $comment_id, $user_id]);
            }
            // Redirigeix igualment per evitar doble vot
            header("Location: fitxa.php?id=" . $id);
            exit;
        }
        // Si un usuari ja ha introduït un comentari, no li mostrem el formulari
        if (isset($_SESSION['user_id'])) {
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
            $mostrar_formulari = true;
        }

        // Esborrar comentari si s'ha enviat el formulari
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['elimina_comentari'])) {
            $comentari_id = $_POST['elimina_comentari'];
            // Només permet esborrar si l'usuari autenticat és el propietari del comentari
            $stmt_check = $pdo->prepare("SELECT c.id FROM comentaris c JOIN usuaris u ON c.user_id = u.id WHERE c.id = ? AND u.nom = ?");
            $stmt_check->execute([$comentari_id, $user_name]);
            if ($stmt_check->fetch()) {
                // Primer esborrem els vots associats a aquest comentari
                $stmt_delete_votes = $pdo->prepare("DELETE FROM vot_comentari WHERE comment_id = ?");
                $stmt_delete_votes->execute([$comentari_id]);

                // Ara esborrem el comentari
                $stmt_delete = $pdo->prepare("DELETE FROM comentaris WHERE id = ?");
                $stmt_delete->execute([$comentari_id]);
                // Redirigir per evitar re-enviament del formulari
                header("Location: fitxa.php?id=" . $id);
                exit;
            }
        }
    } else {
        die("No s'ha proporcionat cap ID.");
    }
} catch (PDOException $e) {
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
        <a href="info.php" class="nav-link">Inici</a>
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
                <div class="fitxa-comentaris" style="position:relative;">
                    <?php if ($user_name && $user_name === $comentari['nom_usuari']): ?>
                        <form method="POST" action="fitxa.php?id=<?= $id ?>" style="position:absolute;top:10px;right:10px;display:block;z-index:2;">
                            <input type="hidden" name="elimina_comentari" value="<?= $comentari['id'] ?>">
                            <button type="submit" title="Esborrar comentari" style="background:transparent;border:none;cursor:pointer;padding:0;font-size:22px;line-height:1;">🗑️</button>
                        </form>
                    <?php endif; ?>
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
                        <?php
                        $ja_votat = false;
                        $no_login = !isset($_SESSION['user_id']);
                        if (!$no_login) {
                            $stmt_votat = $pdo->prepare("SELECT id FROM vot_comentari WHERE act_id = ? AND comment_id = ? AND user_id = ?");
                            $stmt_votat->execute([$id, $comentari['id'], $_SESSION['user_id']]);
                            $ja_votat = $stmt_votat->fetch() ? true : false;
                        }
                        if ($ja_votat || $no_login) {
                            $disabled = 'disabled style="opacity:0.45;cursor:not-allowed;background:transparent;color:#111;border:none;filter:none;"';
                        } else {
                            $disabled = '';
                        }
                        ?>
                        <button class="boto-positiu" onclick="window.location.href='fitxa.php?id=<?= $id ?>&positiu=<?= $comentari['id'] ?>'" <?= $disabled ?>>👍 <?= $comentari['positiu'] ?></button>
                        <button class="boto-negatiu" onclick="window.location.href='fitxa.php?id=<?= $id ?>&negatiu=<?= $comentari['id'] ?>'" <?= $disabled ?>>👎 <?= $comentari['negatiu'] ?></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>