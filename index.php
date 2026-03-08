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
    
    // Consulta SQL per recuperar TOTS els alumnes de la taula 'alumnes'
    $stmt = $pdo->query("SELECT * FROM activitats");
    
    // Recuperem tots els resultats com un array associatiu
    $activitats = $stmt->fetchAll();

    session_start();
    $user_name = $_SESSION['session'] ?? null;


} catch (\PDOException $e) {
    // Si hi ha error de connexió, mostrem un missatge d'error i aturem l'execució
    die("Error de connexió: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sakura Fest</title>
    <link rel="stylesheet" href="assets/styles/main.css">
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
        <a href="index.php" class="nav-link nav-actiu">Inici</a>
        <a href="calendari.php" class="nav-link">Calendari</a>
    </nav>

    <main>
        <div id="botons-filtre">
            <button class="estil-boto-mostrar" id="boto-mostra-filtres">
            <img src="assets/img/icon-filter.svg" alt="Filtre" class="btn-filtre-icon">    
                Filtres
            </button>
            <button class="estil-boto-mostrar" id="boto-mostra-ordre">
                <img src="assets/img/icon-order.svg" alt="Ordena" class="btn-filtre-icon">
                Ordena
            </button>
        </div>

        <div id="div-filtres">
            <div class="div-filtre div-filtre-nom">
                <label for="filterNom">Cerca per nom:</label>
                <input type="text" id="filterNom" placeholder="Escriu el nom...">
            </div>

            <div class="div-filtre">
                <label for="filterDia">Filtra per Dia:</label>
                <select id="filterDia">
                    <option value="">Tots els dies</option>
                    <option value="2026-04-17">Divendres 17</option>
                    <option value="2026-04-18">Dissabte 18</option>
                    <option value="2026-04-19">Diumenge 19</option>
                </select>
            </div>

            <div class="div-filtre">
                <label for="filterUbi">Filtra per Ubicació:</label>
                <select id="filterUbi">
                    <option value="">Totes</option>
                    <option value="pavelló A">Pavelló A</option>
                    <option value="pavelló B">Pavelló B</option>
                    <option value="exterior">Exterior</option>
                </select>
            </div>

            <div class="div-filtre">
                <label for="filterTipus">Filtra per Tipus:</label>
                <select id="filterTipus">
                    <option value="">Tots</option>
                    <option value="botiga">Botigues</option>
                    <option value="concert">Concerts</option>
                    <option value="concurs">Concursos</option>
                    <option value="exhibicio">Exhibicions</option>
                    <option value="taller">Tallers</option>
                    <option value="xerrada">Xerrades</option>
                </select>
            </div>

            <div class="div-filtre">
                <label for="filterPreu">Filtra per Preu:</label>
                <select id="filterPreu">
                    <option value="">Tots</option>
                    <option value="gratis">Gratuït</option>
                    <option value="pagament">Pagament</option>
                </select>
            </div>

        </div>

        <div id="div-ordenar">
            <div class="div-ordre">
                <label for="ordreAlf">Ordrena alfabèticament:</label>
                <select id="ordreAlf">
                    <option value="">-</option>
                    <option value="az">A-Z</option>
                    <option value="za">Z-A</option>
                </select>
            </div>

            <div class="div-ordre">
                <label for="ordreInici">Ordena per data d'inici:</label>
                <select id="ordreInici">
                    <option value="">-</option>
                    <option value="9a21">9:00 a 21:00</option>
                    <option value="21a9">21:00 a 9:00</option>
                </select>
            </div>

        </div>
        <div id="esborrar-filtres">
            <button id="boto-esborrar-filtres">
                Esborra filtres X
            </button>
        </div>
        
        <!-- PHP -->
        <?php foreach ($activitats as $activitat): ?>
            
            <?php 
                $tipus = strtolower($activitat['tipus']); 
                $imatge = $icones[$tipus] ?? 'assets/img/default.png'; 
            ?>

            <div class="card" data-dia="<?= $activitat['dia'] ?>" data-name="<?= $activitat['nom_activitat'] ?>" data-tipus="<?= $activitat['tipus'] ?>" data-ubi="<?= $activitat['ubicacio'] ?>" data-preu="<?= $activitat['preu'] ?>" data-inici="<?= $activitat['hora_inici'] ?>">
                
                <img src="<?= $imatge ?>" alt="<?= $activitat['tipus'] ?>">
                <div class="text-container">
                    <h3 class="titol-targeta">
                        <?= $activitat['nom_activitat'] ?>
                    </h3>
                    <p class="tipus-tematica-targeta">
                        <?= ucfirst($activitat['tipus']) . ' · ' . ucfirst($activitat['tematica']) ?>   
                    </p>
                    <p class="dia-lloc-targeta">
                    <?= date("d-m-Y", strtotime($activitat['dia'])) . ' · ' 
                        . date("H:i", strtotime($activitat['hora_inici'])) . '-' 
                        . date("H:i", strtotime($activitat['hora_fi'])) ?>
                    · <?= ucfirst($activitat['ubicacio']) ?>
                    </p>

                </div>
                <div class="preu-boto">
                    <p class="preu-targeta">
                        <?= strtoupper($activitat['preu'])  ?>
                    </p>
                    <button class="boto-compra"
                       onclick="window.location.href='fitxa.php?id=<?= urlencode($activitat['id']); ?>'">
                        + Info
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
       
    </main>
    <script src="script.js"></script>
</body>
</html>