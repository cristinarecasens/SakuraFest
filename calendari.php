<?php
require_once 'config_db.php';

try {
    
    $stmt = $pdo->query("SELECT * FROM activitats ORDER BY dia, hora_inici");
    $activitats = $stmt->fetchAll();

    session_start();
    $user_name = $_SESSION['user_name'] ?? null;

} catch (\PDOException $e) {
    die("Error de connexió: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sakura Fest - Calendari</title>
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
        <a href="index.php" class="nav-link">Inici</a>
        <a href="calendari.php" class="nav-link nav-actiu">Calendari</a>
    </nav>

    <main>
        <div class="calendar-container">
            <div class="calendar-header">
                <h2>Abril 2026</h2>
            </div>
            <div class="calendar-grid" id="calendarGrid">
                <div class="weekday">Dl</div>
                <div class="weekday">Dt</div>
                <div class="weekday">Dc</div>
                <div class="weekday">Dj</div>
                <div class="weekday">Dv</div>
                <div class="weekday">Ds</div>
                <div class="weekday">Dg</div>
            </div>
        </div>

        <div id="detailRow" class="detail-row">
            <div id="detailInner"></div>
        </div>

    <?php
        // Agrupar activitats per dia
        $activitatsDia = [];
        foreach ($activitats as $act) {
            $activitatsDia[$act['dia']][] = $act;
        }
    ?>

    <script>
        const grid = document.getElementById('calendarGrid');
        const detailRow = document.getElementById('detailRow');
        const detailInner = document.getElementById('detailInner');

        // Activitats agrupades per dia (generades amb PHP)
        const events = {
            <?php foreach ($activitatsDia as $dia => $acts): ?>
            "<?= $dia ?>": [
                <?php foreach ($acts as $act): ?>
                {
                    id: <?= $act['id'] ?>,
                    nom_activitat: "<?= addslashes($act['nom_activitat']) ?>",
                    tipus: "<?= addslashes($act['tipus']) ?>",
                    tematica: "<?= addslashes($act['tematica']) ?>",
                    ubicacio: "<?= addslashes($act['ubicacio']) ?>",
                    preu: "<?= $act['preu'] ?>",
                    hora_inici: "<?= $act['hora_inici'] ?? '' ?>",
                    hora_fi: "<?= $act['hora_fi'] ?? '' ?>"
                },
                <?php endforeach; ?>
            ],
            <?php endforeach; ?>
        };

        function generateCalendar() {
            // Abril 2026 comença en dimecres (Dl=0, Dt=1, Dc=2)
            const firstDayOffset = 2;
            for (let i = 0; i < firstDayOffset; i++) {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'day day-empty';
                grid.appendChild(emptyDiv);
            }

            // Abril té 30 dies
            for (let i = 1; i <= 30; i++) {
                const dateStr = `2026-04-${i.toString().padStart(2, '0')}`;
                const dayEvents = events[dateStr] || [];
                const dayDiv = document.createElement('div');
                dayDiv.className = 'day';

                let html = `<div class="day-number">${i}</div>`;

                if (dayEvents.length > 0) {
                    dayDiv.classList.add('day-has-events');
                    html += `<div class="event-badge">+${dayEvents.length}</div>`;
                }

                dayDiv.innerHTML = html;

                if (dayEvents.length > 0) {
                    dayDiv.onclick = () => showDetail(dateStr, dayDiv);
                    dayDiv.style.cursor = 'pointer';
                }

                grid.appendChild(dayDiv);
            }
        }

        function showDetail(date, element) {
            const dayEvents = events[date];

            // Si cliquem el mateix dia que ja està obert, el pleguem
            if (element.classList.contains('active')) {
                element.classList.remove('active');
                detailRow.style.display = 'none';
                return;
            }

            document.querySelectorAll('.day').forEach(d => d.classList.remove('active'));

            if (!dayEvents || dayEvents.length === 0) {
                detailRow.style.display = 'none';
                return;
            }

            element.classList.add('active');

            const dateObj = new Date(date);
            const dies = ['Diumenge', 'Dilluns', 'Dimarts', 'Dimecres', 'Dijous', 'Divendres', 'Dissabte'];
            const diaSetmana = dies[dateObj.getDay()];
            const diaNum = dateObj.getDate();

            let detailHtml = `<h4>${diaSetmana} ${diaNum} d'abril — ${dayEvents.length} activitats</h4>`;
            detailHtml += `<div class="events-list">`;

            dayEvents.forEach(event => {
                const hora = event.hora_inici
                    ? event.hora_inici.substring(0, 5) + ' - ' + event.hora_fi.substring(0, 5)
                    : 'Tot el dia';
                const preu = event.preu === 'gratis' ? 'Gratuït' : 'Pagament';

                detailHtml += `
                <div class="event-item-detail">
                    <div class="event-info">
                        <strong>${event.nom_activitat}</strong>
                        <small>${event.tipus.charAt(0).toUpperCase() + event.tipus.slice(1)} · ${event.ubicacio.charAt(0).toUpperCase() + event.ubicacio.slice(1)} · ${hora} · ${preu}</small>
                    </div>
                    <a href="fitxa.php?id=${event.id}" class="btn-fitxa">Veure fitxa</a>
                </div>`;
            });

            detailHtml += `</div>`;
            detailInner.innerHTML = detailHtml;

            // Posicionar el detall després de la fila del dia clicat
            const allDays = Array.from(document.querySelectorAll('.day'));
            const index = allDays.indexOf(element);
            const rowEndIndex = Math.floor(index / 7) * 7 + 6;
            const target = allDays[Math.min(rowEndIndex, allDays.length - 1)];
            target.after(detailRow);

            detailRow.style.display = 'block';
        }

        generateCalendar();
    </script>
    </main>
</body>
</html>
