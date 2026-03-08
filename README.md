# 🌸 SakuraFest

**SakuraFest** és un projecte web que simula el lloc oficial d'un festival de temàtica japonesa celebrat a Barcelona els dies **17, 18 i 19 d'abril de 2026**. El festival reuneix activitats relacionades amb el manga, l'anime, els videojocs i la cultura japonesa en general.

## 📖 Descripció

El lloc web permet als usuaris explorar les **150 activitats** programades durant els tres dies del festival, incloent tallers, concursos, xerrades, exhibicions, botigues i concerts. Els visitants poden consultar la informació de cada activitat, veure la seva ubicació en un mapa interactiu i deixar comentaris amb sistema de votació.

## ⚙️ Vistia la web

La web està allotjada en el següent enllaç: https://sakurafest.infinityfree.me/, tot i que es responsive, no està ben optimitzada per mòbil i es recomana veure per ordinador 💻.

## 🚀 Funcionalitats

- **Llistat d'activitats** amb filtres per nom, dia, ubicació, tipus i preu, i ordenació alfabètica o per horari.
- **Calendari visual** del mes d'abril amb recompte d'activitats per dia i vista detallada en desplegar.
- **Fitxa d'activitat** amb tota la informació, secció de comentaris i votacions (👍/👎).
- **Mapa interactiu** amb Google Maps i marcadors de colors segons la ubicació (Pavelló A, Pavelló B, Exterior).
- **Registre i login d'usuaris** amb contrasenyes encriptades (bcrypt).
- **Sistema de comentaris** vinculat a l'usuari amb un comentari per activitat.

## 🛠️ Tecnologies

| Capa | Tecnologia |
|------|------------|
| Backend | PHP (PDO) |
| Base de dades | MySQL / MariaDB |
| Frontend | HTML, CSS, JavaScript vanilla |
| Mapes | Google Maps JavaScript API |
| Servidor local | XAMPP (Apache) |
| Hosting | InfinityFree |

## 📁 Estructura del projecte

```
sakurafest/
├── index.php              # Pàgina principal amb llistat d'activitats
├── calendari.php          # Vista de calendari
├── fitxa.php              # Fitxa detallada d'una activitat
├── maps.php               # Mapa interactiu amb Google Maps
├── login.php              # Gestió del login
├── login.html             # Formulari de login
├── signup.html            # Formulari de registre
├── crear_compte.php       # Gestió del registre d'usuaris
├── afegir_comentari.php   # Gestió d'inserció de comentaris
├── logout.php             # Tancament de sessió
├── config_db.php          # Configuració de la connexió a la BBDD
├── script.js              # Lògica de filtres i ordenació (client)
├── bbdd_salo_manga.sql    # Script SQL per crear la base de dades
├── .env                   # Variables d'entorn (credencials) — NO inclòs al repo
├── .htaccess              # Protecció del fitxer .env
├── .gitignore             # Fitxers exclosos de Git
└── assets/
    ├── img/               # Icones i logo del festival
    └── styles/
        ├── main.css       # Estils generals, header, cards, calendari
        ├── fitxa.css      # Estils de la fitxa i comentaris
        └── login.css      # Estils dels formularis de login i registre
```

## 👤 Autoria
Projecte desenvolupat com a treball de DAW2 per a Cristina Recasens.
