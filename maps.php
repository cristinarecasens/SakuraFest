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

        // Consulta preparada para seguridad
        $stmt = $pdo->prepare("SELECT * FROM activitats WHERE id = ?");
        $stmt->execute([$id]);
        $activitat = $stmt->fetch();

        if (!$activitat) {
            die("No s'ha trobat cap activitat amb aquest ID.");
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

        <div id="map"></div>
    </main>

    <script>
        (g => { var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window; b = b[c] || (b[c] = {}); var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => { await (a = m.createElement("script")); e.set("libraries", [...r] + ""); for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]); e.set("callback", c + ".maps." + q); a.src = `https://maps.${c}apis.com/maps/api/js?` + e; d[q] = f; a.onerror = () => h = n(Error(p + " could not load.")); a.nonce = m.querySelector("script[nonce]")?.nonce || ""; m.head.append(a) })); d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n)) })({
            key: "<?= htmlspecialchars($google_maps_key, ENT_QUOTES, 'UTF-8') ?>",
            v: "weekly",
        });
    </script>

    <?php
        // Determinar la imatge de l'activitat
        $tipus = strtolower($activitat['tipus']);
        if (isset($icones[$tipus])) {
            $imatge = $icones[$tipus];
        } else {
            $imatge = 'assets/img/default.png';
        }
    ?>

    <script type="module">
        async function initMap() {
            // Importem les llibreries necessàries utilitzant async/await per tal de no bloquejar la web - asincronia - 
            const { Map, InfoWindow } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");

            // Dades de l'activitat (generades amb PHP)
            var id = <?= $activitat['id'] ?>;
            var nom = "<?= addslashes($activitat['nom_activitat']) ?>";
            var tipus = "<?= addslashes($activitat['tipus']) ?>";
            var tematica = "<?= addslashes($activitat['tematica']) ?>";
            var ubicacio = "<?= addslashes($activitat['ubicacio']) ?>";
            var preu = "<?= $activitat['preu'] ?>";
            var dia = "<?= $activitat['dia'] ?>";
            var horaInici = "<?= $activitat['hora_inici'] ?? '' ?>";
            var horaFi = "<?= $activitat['hora_fi'] ?? '' ?>";
            var imatge = "<?= $imatge ?>";

            // Coordenades i color segons la ubicació
            var lat;
            var lng;
            var color;

            if (ubicacio.toLowerCase() == "pavelló a") {
                lat = 41.3725;
                lng = 2.1530;
                color = "#e63946";
            } else if (ubicacio.toLowerCase() == "pavelló b") {
                lat = 41.3735;
                lng = 2.1505;
                color = "#457b9d";
            } else {
                lat = 41.3715;
                lng = 2.1518;
                color = "#2a9d8f";
            }

            // Crear el mapa centrat a la Fira de Barcelona
            const map = new Map(document.querySelector("#map"), {
                center: { lat: 41.3725, lng: 2.1518 },
                zoom: 17,
                mapId: "DEMO_MAP_ID",
                mapTypeId: "hybrid",
            });

            // Crear el pin amb color segons ubicació
            const pin = new PinElement({
                background: color,
                glyphColor: "white",
                scale: 1.4
            });

            // Crear el marcador
            const marker = new AdvancedMarkerElement({
                map: map,
                position: { lat: lat, lng: lng },
                content: pin.element,
                title: nom
            });

            // Informació de l'hora
            var hora;
            if (horaInici) {
                hora = horaInici.substring(0, 5) + ' - ' + horaFi.substring(0, 5);
            } else {
                hora = 'Tot el dia';
            }

            var preuText;
            if (preu === 'gratis') {
                preuText = 'Gratuït';
            } else {
                preuText = 'Pagament';
            }

            // Crear la InfoWindow amb la informació de l'activitat
            const infoWindow = new InfoWindow({
                content: `
                    <div style="max-width: 250px; font-family: 'Noto Sans', sans-serif; color: #333;">
                        <img src="${imatge}" alt="${tipus}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; float: left; margin-right: 10px;">
                        <h3 style="margin: 0 0 6px; color: #b3002d; font-size: 1rem;">${nom}</h3>
                        <p style="margin: 0 0 4px; font-size: 0.85rem;">
                            <strong>${tipus.charAt(0).toUpperCase() + tipus.slice(1)}</strong> · ${tematica.charAt(0).toUpperCase() + tematica.slice(1)}
                        </p>
                        <p style="margin: 0 0 4px; font-size: 0.85rem;">
                            📅 ${dia} · ${hora}
                        </p>
                        <p style="margin: 0 0 4px; font-size: 0.85rem;">
                            📍 ${ubicacio.charAt(0).toUpperCase() + ubicacio.slice(1)}
                        </p>
                        <p style="margin: 0 0 8px; font-size: 0.85rem;">
                            💰 ${preuText}
                        </p>
                        <a href="fitxa.php?id=${id}" 
                            style="display:inline-block; padding:5px 12px; background:#e63946; color:white; border-radius:6px; text-decoration:none; font-size:0.8rem; font-weight:600;">
                            Veure fitxa
                        </a>
                    </div>
                `
            });

            // Obrir la InfoWindow al fer clic al marcador
            marker.addListener("click", () => {
                infoWindow.open({
                    anchor: marker,
                    map,
                });
            });

            // Obrir la InfoWindow automàticament
            infoWindow.open({
                anchor: marker,
                map,
            });
        }

        initMap();
    </script>
</body>
</html>