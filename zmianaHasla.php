<?php
// Uruchomienie lub wznowienie sesji – niezbędne do autoryzacji użytkownika
session_start();

// --- KONFIGURACJA RAPORTOWANIA BŁĘDÓW ---
// Włączenie wyświetlania błędów bezpośrednio na stronie (przydatne w fazie deweloperskiej)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- KONTROLA DOSTĘPU (AUTORYZACJA) ---
// Brak aktywnej sesji skutkuje natychmiastowym przerwaniem skryptu i odesłaniem do logowania
if (!isset($_SESSION['user_id'])) {
    header("Location: logowanie.php");
    exit;
}

// Pobranie identyfikatora zalogowanego użytkownika (ID numeryczne lub ciąg 'admin')
$status = $_SESSION['user_id'];

// Nawiązanie połączenia z bazą danych MySQL
$db = mysqli_connect('localhost', 'root', '', 'zegowskaszama');

// Weryfikacja poprawności połączenia z serwerem bazodanowym
if (!$db) {
    die("Błąd połączenia z bazą: " . mysqli_connect_error());
}
// Wymuszenie kodowania UTF-8 dla prawidłowego przesyłania polskich znaków
mysqli_set_charset($db, "utf8mb4");

// Definicja domyślnego tekstu powitalnego w nagłówku
$imie_uzytkownika = "Użytkownik";

// Sprawdzenie, czy zalogowany profil należy do zwykłego użytkownika, czy administratora
if ($status !== 'admin') {
    // Pobranie imienia klienta z bazy na podstawie ID zapisanego w sesji (zabezpieczone przed SQL Injection)
    $sql = "SELECT Imie FROM uzytkownicy WHERE id = '" . mysqli_real_escape_string($db, $status) . "'";
    $wynik = mysqli_query($db, $sql);
    
    // Jeśli znaleziono dokładnie jeden pasujący rekord, przypisujemy imię do zmiennej
    if ($wynik && mysqli_num_rows($wynik) == 1) {
        $uzytkownik = mysqli_fetch_assoc($wynik);
        $imie_uzytkownika = $uzytkownik['Imie'];
    }
} else {
    // Jeśli status w sesji wskazuje na administratora, ustawiamy sztywną wartość powitania
    $imie_uzytkownika = "Admin";
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zmień hasło - Zegowska Szama</title>
    
    <link rel="icon" type="image/png" href="logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
    
    <style>
        /* Ukrywanie klasycznego stacjonarnego sidebaru na urządzeniach mobilnych i tabletach (< lg) */
        @media (max-width: 991.98px) {
            .desktop-sidebar {
                display: none !important;
            }
        }
        
        /* Stylizacja przycisku wywołującego menu hamburgerowe na telefonach */
        .hamburger-btn {
            background: none;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            border-radius: 12px;
            color: #333;
            transition: all 0.2s ease;
        }
        .hamburger-btn:hover {
            background-color: #f8f9fa;
        }

        /* Klasa zapewniająca płynne przejścia kolorów dla pól wejściowych przy walidacji "w locie" */
        .inpat {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>

<body style="background-image: url(background.png);">

    <header class="topbar sticky-top bg-white shadow-sm">
        <div class="container-fluid d-flex justify-content-between align-items-center py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <button class="hamburger-btn d-lg-none shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                    ☰ <span class="ms-1 small fw-bold">Menu</span>
                </button>
                <a href="sklep.php"><img src="logo.png" alt="logo" class="logo" style="max-height: 45px; width: auto;"></a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="w-auto rounded-4 bg-light fw-bold border-0 shadow-sm px-3 py-2 btn btn-sm">
                    <?php echo htmlspecialchars($imie_uzytkownika); ?>
                </button>
            </div>
        </div>
    </header>

    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: 280px;">
        <div class="offcanvas-header border-bottom bg-light">
            <h5 class="offcanvas-title fw-bold text-muted" id="mobileSidebarLabel">NAWIGACJA</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4 bg-light d-flex flex-column justify-content-between">
            <div class="d-flex flex-column gap-2">
                <a href="sklep.php" class="text-decoration-none mb-3">
                    <button class="btn menu-btn w-100 text-start">🛒 Powrót do sklepu</button>
                </a>
                
                <a href="konto.php" class="text-decoration-none">
                    <button class="btn menu-btn text-start w-100">👤 Moje konto</button>
                </a>

                <?php if ($status !== 'admin'): ?>
                    <a href="szczegoly.php" class="text-decoration-none">
                        <button class="btn menu-btn text-start w-100">📄 Szczegóły konta</button>
                    </a>
                    <a href="historia.php" class="text-decoration-none">
                        <button class="btn menu-btn text-start w-100">📋 Historia zamówień</button>
                    </a>
                    <a href="zmianaHasla.php" class="text-decoration-none">
                        <button class="btn menu-btn text-start w-100 active">🔑 Zmień hasło</button>
                    </a>
                <?php endif; ?>

                <?php if ($status === 'admin'): ?>
                    <a href="panel_admina.php" class="text-decoration-none">
                        <button class="btn menu-btn text-start w-100">🛠️ Panel Administratora</button>
                    </a>
                <?php endif; ?>

                <button class="btn menu-btn text-start text-danger w-100 mt-2 mobile-logout-btn">↪ Wyloguj się</button>
            </div>
        </div>
    </div>

    <main class="container-fluid">
        <div class="row">

            <aside class="col-xl-3 col-lg-4 sidebar desktop-sidebar p-4 bg-light shadow-sm min-vh-100">
                <h5 class="sidebar-title mb-4 fw-bold text-muted">NAWIGACJA</h5>

                <div class="d-flex flex-column gap-2">
                    <a href="sklep.php" class="text-decoration-none">
                        <button class="btn menu-btn w-100 text-start">🛒 Powrót do sklepu</button>
                    </a>
                    <a href="konto.php" class="text-decoration-none">
                        <button class="btn menu-btn w-100 text-start">👤 Moje konto</button>
                    </a>
                </div>

                <hr class="my-4">

                <div class="d-flex flex-column gap-2">
                    <?php if ($status !== 'admin'): ?>
                        <a href="szczegoly.php" class="text-decoration-none">
                            <button class="btn menu-btn text-start w-100">📄 Szczegóły konta</button>
                        </a>
                        <a href="historia.php" class="text-decoration-none">
                            <button class="btn menu-btn text-start w-100">📋 Historia zamówień</button>
                        </a>
                        <a href="zmianaHasla.php" class="text-decoration-none">
                            <button class="btn menu-btn text-start w-100 active">🔑 Zmień hasło</button>
                        </a>
                    <?php endif; ?>

                    <?php if ($status === 'admin'): ?>
                        <a href="panel_admina.php" class="text-decoration-none">
                            <button class="btn menu-btn text-start w-100">🛠️ Panel Administratora</button>
                        </a>
                    <?php endif; ?>

                    <button class="btn menu-btn text-start text-danger w-100 mt-2 desktop-logout-btn">↪ Wyloguj się</button>
                </div>
            </aside>

            <section class="col-xl-9 col-lg-8 p-3 p-sm-4 p-md-5">
                <h1 class="fw-bold text-center mb-4 mb-md-5 text-dark fs-2">Bezpieczeństwo konta</h1>

                <div class="card border-0 shadow-sm rounded-4 p-4 p-sm-5 bg-white mx-auto" style="max-width: 600px;">
                    <h3 class="fw-bold mb-4 text-dark fs-4 text-center">Zmień swoje hasło</h3>
                    
                    <div class="row g-3 justify-content-center">
                        <div class="col-12">
                            <label for="stareHaslo" class="form-label fw-semibold text-muted">Aktualne hasło</label>
                            <input type="password" placeholder="Podaj stare hasło" name="starehaslo" id="stareHaslo" class="form-control form-control-lg bg-light border-1 rounded-3">
                        </div>

                        <div class="col-12">
                            <label for="nowehaslo1" class="form-label fw-semibold text-muted">Nowe hasło</label>
                            <input type="password" placeholder="Podaj nowe hasło" name="nowehaslo" id="nowehaslo1" class="form-control form-control-lg bg-light border-1 rounded-3">
                        </div>

                        <div class="col-12">
                            <label for="nowehaslo2" class="form-label fw-semibold text-muted">Powtórz nowe hasło</label>
                            <input type="password" placeholder="Potwierdź nowe hasło" name="potwierdznowe" id="nowehaslo2" class="form-control form-control-lg bg-light border-1 rounded-3">
                        </div>

                        <div class="col-12">
                            <div class="alert p-3 rounded-3 d-none text-center fw-bold shadow-sm" id="alert" role="alert"></div>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button id="zmien" type="submit" class="btn btn-lg w-100 rounded-3 fw-bold py-2 shadow-sm" style="background-color: #8db63f; color: white;">
                                Zaktualizuj hasło
                            </button>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Pobranie referencji do elementów DOM odpowiedzialnych za komunikację i wprowadzanie danych
        const alertBox = document.getElementById('alert');
        const zmien = document.getElementById('zmien');
        const n1 = document.getElementById('nowehaslo1');
        const n2 = document.getElementById('nowehaslo2');

        // Walidacja zgodności haseł w czasie rzeczywistym (zdarzenie 'input')
        n2.addEventListener('input', () => {
            if (n1.value !== n2.value) {
                // Jeśli hasła się różnią, zabarwiamy tło na pastelowy czerwony
                n2.style.backgroundColor = "#ffcccc";
                n2.style.color = "#b30000";
            } else {
                // Jeśli hasła są identyczne, zmieniamy tło na pastelowy zielony
                n2.style.backgroundColor = "#d4edda";
                n2.style.color = "#155724";
            }
        });
        
        // Czyszczenie stylizacji pola powtórzenia hasła, gdy użytkownik je opuści (blur) i jest ono puste
        n2.addEventListener('blur', () => {
            if(n2.value === "") {
                n2.style.backgroundColor = "";
                n2.style.color = "";
            }
        });

        // Obsługa asynchronicznego przesyłania formularza zmiany hasła po kliknięciu przycisku
        zmien.addEventListener('click', (e) => {
            // Zablokowanie standardowego odświeżenia strony wywoływanego przez przycisk typu submit/click
            e.preventDefault();

            // Odczytanie wartości wprowadzonych przez użytkownika
            const stareHaslo = document.getElementById('stareHaslo').value;
            const nowehaslo1 = n1.value;
            const nowehaslo2 = n2.value;

            // Weryfikacja po stronie przeglądarki, czy żadne z wymaganych pól nie pozostało puste
            if (!stareHaslo || !nowehaslo1 || !nowehaslo2) {
                alertBox.classList.remove('d-none');
                alertBox.className = "alert alert-danger text-center fw-bold";
                alertBox.innerHTML = "Wypełnij wszystkie pola!";
                return;
            }

            // Wysłanie asynchronicznego żądania POST z parametrami formularza do pliku zmienHaslo.php
            fetch('zmienHaslo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                // Parametry są kodowane za pomocą encodeURIComponent dla bezpieczeństwa i poprawności przesyłu znaków specjalnych
                body: "status=" + encodeURIComponent("<?php echo $status; ?>") + "&stareHaslo=" + encodeURIComponent(stareHaslo) + "&noweHaslo1=" + encodeURIComponent(nowehaslo1) + "&noweHaslo2=" + encodeURIComponent(nowehaslo2)
            })
            .then(res => res.text()) // Odczytanie odpowiedzi serwera w formie czystego tekstu
            .then(data => {
                // Wyświetlenie ukrytego kontenera alertu
                alertBox.classList.remove('d-none');
                
                // Umowna logika: Jeśli odpowiedź serwera zaczyna się od słowa "Hasło" (np. "Hasło zostało zmienione")
                if (data.trim().startsWith("Hasło")) {
                    // Prezentacja powiadomienia o sukcesie (zielony alert)
                    alertBox.className = "alert alert-success text-center fw-bold shadow-sm";
                    alertBox.innerHTML = data;
                    
                    // Resetowanie wartości wszystkich pól formularza po udanej operacji
                    document.getElementById('stareHaslo').value = "";
                    n1.value = "";
                    n2.value = "";
                    n2.style.backgroundColor = "";
                    n2.style.color = "";
                } else {
                    // W przypadku zwrócenia błędu z pliku PHP (np. niepoprawne stare hasło), wyświetlamy czerwony alert
                    alertBox.className = "alert alert-danger text-center fw-bold shadow-sm";
                    alertBox.innerHTML = data;
                }
            });
        });

        // Definicja funkcji realizującej proces asynchronicznego wylogowania użytkownika
        const ObslugaWylogowania = () => {
            fetch('wyloguj.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            })
            .then(() => {
                // Przekierowanie użytkownika do formularza logowania po zniszczeniu sesji na serwerze
                window.location.href = 'logowanie.php';
            });
        };

        // Bezpieczne podpięcie funkcji wylogowania (Optional Chaining `?.` chroni przed błędami, gdy elementu brak w DOM)
        document.querySelector('.desktop-logout-btn')?.addEventListener('click', ObslugaWylogowania);
        document.querySelector('.mobile-logout-btn')?.addEventListener('click', ObslugaWylogowania);
    </script>
</body>
</html>