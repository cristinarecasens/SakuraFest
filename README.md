# 🌸 SakuraFest

**SakuraFest** is a web project in catalan that simulates the official website of a Japanese-themed festival held in Barcelona on **April 17, 18 and 19, 2026**. The festival brings together activities related to manga, anime, video games and Japanese culture in general.

## 📖 Description

The website allows users to explore the **150 activities** scheduled during the three days of the festival, including workshops, contests, talks, exhibitions, shops and concerts. Visitors can view the information for each activity, see its location on an interactive map, and leave comments with a voting system.

## ⚙️ Visit the website

The website is hosted at the following link: https://sakurafest.infinityfree.me/. Although it is responsive, it is not well optimized for mobile devices and it is recommended to view it on a computer 💻.

## 🚀 Features

- **Activity listing** with filters by name, day, location, type and price, and alphabetical or time-based sorting.
- **Visual calendar** for the month of April with the number of activities per day and a detailed view when expanded.
- **Activity page** with full information, comments section and voting system (👍/👎).
- **Interactive map** with Google Maps and color markers depending on the location (Pavilion A, Pavilion B, Outdoor).
- **User registration and login** with encrypted passwords (bcrypt). (The email can be fictional, there is no verification).
- **Comment system** linked to the user with one comment per activity.

## 🛠️ Technologies

| Layer | Technology |
|------|------------|
| Backend | PHP (PDO) |
| Database | MySQL / MariaDB |
| Frontend | HTML, CSS, Vanilla JavaScript |
| Maps | Google Maps JavaScript API |
| Local server | XAMPP (Apache) |
| Hosting | InfinityFree |

## 📁 Project Structure

```
sakurafest/
├── index.php              # Main page with activity listing
├── calendari.php          # Calendar view
├── fitxa.php              # Detailed activity page
├── maps.php               # Interactive map with Google Maps
├── login.php              # Login management
├── crear_compte.php       # User registration management
├── afegir_comentari.php   # Comment insertion management
├── logout.php             # Logout
├── config_db.php          # Database connection configuration
├── script.js              # Filter and sorting logic (client-side)
├── bbdd_salo_manga.sql    # SQL script to create the database
├── .env                   # Environment variables (credentials) — NOT included in repo
├── .htaccess              # Protection for the .env file
├── .gitignore             # Git ignored files
└── assets/
    ├── img/               # Festival icons and logo
    └── styles/
        ├── main.css       # General styles: header, cards, calendar
        ├── fitxa.css      # Styles for activity page and comments
        └── login.css      # Styles for login and registration forms
```

## 👤 Autor
Projecte desenvolupat com a treball de DAW2 per a Cristina Recasens.
