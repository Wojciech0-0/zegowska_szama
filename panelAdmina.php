<?php
// Uruchomienie lub wznowienie istniejącej sesji, aby sprawdzić uprawnienia użytkownika
session_start();

// --- KONFIGURACJA RAPORTOWANIA BŁĘDÓW ---
// Włączenie wyświetlania błędów bezpośrednio na stronie (przydatne w fazie deweloperskiej)
ini_set('display_errors', 1);
// Włączenie wyświetlania błędów, które występują podczas uruchamiania PHP (np. błędy składni w innych plikach)
ini_set('display_startup_errors', 1);
// Raportowanie absolutnie wszystkich typów błędów, ostrzeżeń i uwag (E_ALL)
error_reporting(E_ALL);


// --- KONTROLA DOSTĘPU (AUTORYZACJA) ---
// TYLKO ADMIN MA TU WSTĘP
// Sprawdzenie, czy użytkownik NIE jest zalogowany (brak 'user_id') LUB czy jego identyfikator NIE jest równy 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'admin') {
    // Jeśli warunek jest spełniony (użytkownik nie jest adminem), przekieruj go na stronę główną sklepu
    header("Location: sklep.php");
    // Przerwij dalsze wykonywanie skryptu, aby zablokować ładowanie panelu administracyjnego
    exit;
}


// --- POŁĄCZENIE Z BAZĄ DANYCH ---
// Nawiązanie połączenia z bazą danych MySQL (host, użytkownik, hasło, nazwa bazy danych)
$db = mysqli_connect('localhost', 'root', '', 'zegowskaszama');

// Sprawdzenie, czy połączenie z bazą się powiodło
if (!$db) {
    // Jeśli wystąpił błąd, zatrzymaj skrypt i wyświetl komunikat o błędzie połączenia
    die("Błąd połączenia z bazą: " . mysqli_connect_error());
}

// Ustawienie systemu kodowania znaków na utf8mb4 w celu poprawnego wsparcia polskich znaków i emoji
mysqli_set_charset($db, "utf8mb4");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora - Zegowska Szama</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
    <style>
        @media (max-width: 991.98px) {
            .desktop-sidebar { display: none !important; }
        }
        .hamburger-btn {
            background: none; border: 1px solid #dee2e6; padding: 8px 12px; border-radius: 12px; color: #333; transition: all 0.2s ease;
        }
        .hamburger-btn:hover { background-color: #f8f9fa; }
        
        /* Styl dla kafelków menu admina */
        .admin-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
    </style>
</head>

<body style="background-image: url(background.png);">

    <header class="topbar sticky-top bg-white shadow-sm">
        <div class="container-fluid d-flex justify-content-between align-items-center py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <button class="hamburger-btn d-lg-none shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    ☰ <span class="ms-1 small fw-bold">Menu</span>
                </button>
                <a href="sklep.php"><img src="logo.png" alt="logo" class="logo" style="max-height: 45px; width: auto;"></a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="w-auto rounded-4 bg-light fw-bold border-0 shadow-sm px-3 py-2 btn btn-sm">
                    <a href="konto.php" class="text-decoration-none text-dark">Admin</a>
                </button>
            </div>
        </div>
    </header>

    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar" style="width: 280px;">
        <div class="offcanvas-header border-bottom bg-light">
            <h5 class="offcanvas-title fw-bold text-muted">PANEL CONTROL</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-4 bg-light d-flex flex-column justify-content-between">
            <div class="d-flex flex-column gap-2">
                <a href="sklep.php" class="text-decoration-none mb-3">
                    <button class="btn menu-btn w-100 text-start">🛒 Powrót do sklepu</button>
                </a>
                <a href="konto.php" class="text-decoration-none">
                    <button class="btn menu-btn text-start w-100">🏠 Profil główny</button>
                </a>
                <a href="panelAdmina.php" class="text-decoration-none">
                    <button class="btn menu-btn text-start w-100 active">🛠️ Panel Admina</button>
                </a>
                <button class="btn menu-btn text-start text-danger w-100 mt-2 mobile-logout-btn">↪ Wyloguj się</button>
            </div>
        </div>
    </div>

    <main class="container-fluid">
        <div class="row">

            <aside class="col-xl-3 col-lg-4 sidebar desktop-sidebar p-4 bg-light shadow-sm min-vh-100">
                <h5 class="sidebar-title mb-4 fw-bold text-muted">NAWIGACJA ADMINA</h5>
                <div class="d-flex flex-column gap-2">
                    <a href="sklep.php" class="text-decoration-none">
                        <button class="btn menu-btn w-100 text-start">🛒 Powrót do sklepu</button>
                    </a>
                    <a href="konto.php" class="text-decoration-none">
                        <button class="btn menu-btn w-100 text-start">🏠 Profil główny</button>
                    </a>
                    <a href="panelAdmina.php" class="text-decoration-none">
                        <button class="btn menu-btn text-start w-100 active">🛠️ Panel Administratora</button>
                    </a>
                </div>
                <hr class="my-4">
                <div class="d-flex flex-column gap-2">
                    <button class="btn menu-btn text-start text-danger w-100 desktop-logout-btn">↪ Wyloguj się</button>
                </div>
            </aside>

            <section class="col-xl-9 col-lg-8 p-3 p-sm-4 p-md-5">
                <h1 class="fw-bold text-center mb-4 mb-md-5 text-dark fs-2">Centrum Zarządzania Sklepem</h1>

                <div class="row g-4 justify-content-center">
                    
                    <div class="col-12 col-md-4 text-center">
                        <a href="admin_zamowienia.php" class="text-decoration-none text-dark h-100 d-block">
                            <div class="p-4 border rounded-4 bg-white h-100 shadow-sm border-0 admin-card">
                                <div class="fs-1 mb-2">📋</div>
                                <h4 class="fw-bold fs-5">Zamówienia Uczniów</h4>
                                <p class="text-muted small m-0">Zarządzaj statusem zamówień oraz odbiorem w sklepiku.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-12 col-md-4 text-center">
                        <a href="admin_uzytkownicy.php" class="text-decoration-none text-dark h-100 d-block">
                            <div class="p-4 border rounded-4 bg-white h-100 shadow-sm border-0 admin-card">
                                <div class="fs-1 mb-2">👤</div>
                                <h4 class="fw-bold fs-5">Baza Użytkowników</h4>
                                <p class="text-muted small m-0">Przeglądaj zarejestrowane konta i usuwaj profile uczniów.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-12 col-md-4 text-center">
                        <a href="admin_produkty.php" class="text-decoration-none text-dark h-100 d-block">
                            <div class="p-4 border rounded-4 bg-white h-100 shadow-sm border-0 admin-card">
                                <div class="fs-1 mb-2">🍎</div>
                                <h4 class="fw-bold fs-5">Asortyment Sklepu</h4>
                                <p class="text-muted small m-0">Dodawaj nowe towary do oferty lub usuwaj te wyprzedane.</p>
                            </div>
                        </a>
                    </div>

                </div>
            </section>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Definicja funkcji obsługującej proces wylogowania użytkownika
        const ObslugaWylogowania = () => {
            // Wysłanie asynchronicznego żądania POST do pliku 'wyloguj.php' (który czyści sesję na serwerze)
            fetch('wyloguj.php', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' } 
            })
            // Po pomyślnym wykonaniu żądania (odpowiedź z serwera) następuje przekierowanie
            .then(() => { 
                // Przeniesienie użytkownika na stronę logowania
                window.location.href = 'logowanie.php'; 
            });
        };

        // Podpięcie funkcji wylogowania pod przyciski w wersji na komputer (desktop) oraz telefon (mobile).
        // Użycie znaku zapytania `?.` (Optional Chaining) zapobiega powstawaniu błędów w konsoli,
        // jeśli dany przycisk nie znajduje się na aktualnie wyświetlanej podstronie.
        document.querySelector('.desktop-logout-btn')?.addEventListener('click', ObslugaWylogowania);
        document.querySelector('.mobile-logout-btn')?.addEventListener('click', ObslugaWylogowania);
    </script>
</body>
</html>