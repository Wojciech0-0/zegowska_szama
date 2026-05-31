<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Koszyk - Zegowska Szama</title>

    <link rel="icon" type="image/png" href="logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl_sklep.css">
</head>
<?php
// Uruchomienie lub wznowienie sesji – niezbędne, aby mieć dostęp do tablicy $_SESSION
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
// Jeśli zmienna sesyjna 'user_id' nie istnieje, oznacza to brak autoryzacji
if(!isset($_SESSION['user_id'])){
    // Przekierowanie niezalogowanego użytkownika z powrotem na stronę logowania
    header("location: logowanie.php");
    // Zakończenie działania skryptu, aby nie załadowała się reszta strony
    exit;
}

// Przypisanie identyfikatora użytkownika (lub roli, np. 'admin') do zmiennej
$status = $_SESSION['user_id'];

// Nawiązanie połączenia z bazą danych MySQL (host, użytkownik, hasło, nazwa bazy)
$db = mysqli_connect('localhost', 'root', '', 'zegowskaszama');

// Weryfikacja, czy połączenie z bazą danych powiodło się
if (!$db) {
    // Jeśli wystąpił błąd, przerywamy działanie skryptu i wyświetlamy komunikat z błędem
    die("Błąd połączenia z bazą: " . mysqli_connect_error());
}

// Ustawienie kodowania znaków na utf8mb4 (zapewnia poprawne wyświetlanie polskich znaków oraz emoji)
mysqli_set_charset($db, "utf8mb4");

// Inicjalizacja zmiennych określających stan i wartość koszyka
$stan_koszyka = 0; // 0 - pusty, 1 - zawiera produkty
$suma_koszyka = 0; // Początkowa kwota w koszyku

// Sprawdzenie, czy zalogowany użytkownik NIE JEST administratorem
if ($status !== 'admin') {
    
    // Przygotowanie zapytania SQL, które pobiera produkty z koszyka dla zalogowanego użytkownika.
    // Użyto mysqli_real_escape_string dla bezpieczeństwa, aby zapobiec atakom SQL Injection.
    $sql1 = "SELECT produkty.id, produkty.Nazwa, produkty.Cena, produkty.Kategoria FROM koszyk 
             JOIN produkty ON koszyk.id_produktu = produkty.id 
             JOIN użytkownicy ON użytkownicy.id = koszyk.id_uzytkownika 
             WHERE użytkownicy.id = '" . mysqli_real_escape_string($db, $status) . "'";
    
    // Wykonanie zapytania do bazy danych
    $wynik = mysqli_query($db, $sql1);
    
    // Sprawdzenie, czy w koszyku znajdują się jakieś produkty
    if(mysqli_num_rows($wynik) < 1){
        $stan_koszyka = 0; // Koszyk jest pusty
    }else{
        $stan_koszyka = 1; // W koszyku są produkty
    }
    
    // Przygotowanie drugiego zapytania – pobranie imienia użytkownika na podstawie jego ID
    $sql2 = "SELECT Imie FROM użytkownicy WHERE id = '" . mysqli_real_escape_string($db, $status) . "'";
    $wynik2 = mysqli_query($db, $sql2);
    
    // Weryfikacja, czy zapytanie się powiodło i czy znaleziono dokładnie jednego użytkownika
    if($wynik2 && mysqli_num_rows($wynik2) == 1){
        // Wyciągnięcie danych z wyniku zapytania do tablicy asocjacyjnej
        $uzytkownik = mysqli_fetch_assoc($wynik2);
        // Przypisanie pobranego imienia do zmiennej
        $imie_uzytkownika = $uzytkownik['Imie']; 
    }else{
        // Jeśli coś poszło nie tak (np. brak imienia w bazie), ustawienie wartości domyślnej
        $imie_uzytkownika = "Użytkownik";
    }

}else{
    // Jeśli zalogowany użytkownik jest administratorem, ustawiamy jego "imię" na "Admin"
    // Omijamy wtedy zapytania o koszyk, ponieważ admin najprawdopodobniej go nie posiada/nie potrzebuje
    $imie_uzytkownika = "Admin";
}
?>

<body style="background-image: url(background.png);">

    <header class="topbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a href="sklep.php"><img src="logo.png" alt="logo" class="logo" style="cursor: pointer;"></a>
            </div>

            <button class="w-auto rounded-4 bg-light fw-bold border-0 shadow-sm px-3 py-2 btn btn-sm">
                <a href="konto.php" class="text-decoration-none text-dark"><?php echo htmlspecialchars($imie_uzytkownika); ?></a>
            </button>
        </div>
    </header>

    <main class="container py-5">
        <div class="shop-wrapper mx-auto bg-white p-4 p-md-5 rounded-4 shadow-sm" style="max-width: 900px;">
            
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h1 class="fw-bold m-0">Twój Koszyk</h1>
                <a href="sklep.php" class="btn btn-outline-secondary rounded-4 px-4 fw-bold">← Powrót do oferty</a>
            </div>

            <div class="row g-3">
                <?php
                if($stan_koszyka == 0){
                    echo '<div class="col-12 text-center py-5 text-secondary fs-3">
                            🛒 Twój koszyk jest pusty.
                          </div>';
                } else {
                    while($d = mysqli_fetch_assoc($wynik)){
                        $suma_koszyka += $d['Cena'];
                        
                        if($d['Kategoria'] == "jedzenie"){
                            $zdjecie = "jedzenie.png";
                        } else if($d['Kategoria'] == "przekąski"){
                            $zdjecie = "przekaski.png";
                        } else if($d['Kategoria'] == "napoje"){
                            $zdjecie = "napoje.png";
                        } else {
                            $zdjecie = "jedzenie.png";
                        }

                        echo '
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
                                <div class="row align-items-center">
                                    <div class="col-md-2 col-3 text-center">
                                        <img src="' . $zdjecie . '" alt="" class="img-fluid" style="max-height: 60px;">
                                    </div>
                                    <div class="col-md-7 col-6">
                                        <h5 class="fw-bold mb-1">' . htmlspecialchars($d['Nazwa']) . '</h5>
                                        <span class="badge bg-secondary text-capitalize">' . htmlspecialchars($d['Kategoria']) . '</span>
                                    </div>
                                    <div class="col-md-3 col-3 text-end">
                                        <span class="fs-4 fw-bold text-dark">' . number_format($d['Cena'], 2, ',', '') . ' zł</span>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }

                    echo '
                    <div class="col-12 mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-4 fw-bold">Razem do zapłaty:</span>
                            <span class="fs-2 fw-bold text-success">' . number_format($suma_koszyka, 2, ',', '') . ' zł</span>
                        </div>
                        <div class="text-center">
                            <button id="zamowienie" class="btn btn-success btn-lg w-100 rounded-4 py-3 fw-bold shadow-sm" style="background-color: #198754; border: 0;">
                                Złóż zamówienie i odbierz w sklepiku
                            </button>
                        </div>
                    </div>';
                }
                ?>
            </div> 
        </div> </main>

    <script>
        // Pobranie elementu przycisku (lub linku) do składania zamówienia na podstawie jego ID
        const zamowienie = document.getElementById('zamowienie');

        // Sprawdzenie, czy element o ID 'zamowienie' w ogóle istnieje na danej stronie.
        // Zabezpiecza to przed błędami w konsoli (np. "Cannot read properties of null"), 
        // jeśli skrypt zostanie załadowany na podstronie, gdzie nie ma tego przycisku.
        if(zamowienie){
            
            // Podpięcie nasłuchiwacza zdarzeń, który zareaguje na kliknięcie ('click') w przycisk
            zamowienie.addEventListener('click', () => {
                
                // Wyświetlenie natywnego okna dialogowego z prośbą o potwierdzenie.
                // Kod wewnątrz instrukcji warunkowej wykona się tylko wtedy, gdy użytkownik kliknie "OK".
                if(confirm("Czy na pewno chcesz dokonać zakupu?")){
                    
                    // Wysłanie asynchronicznego żądania (metodą POST) do skryptu zamowienie.php,
                    // który prawdopodobnie przetwarza logikę zamówienia po stronie serwera (np. zapis do bazy).
                    fetch('zamowienie.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    })
                    // Obsługa obietnicy (Promise) - co ma się wydarzyć po otrzymaniu odpowiedzi z serwera
                    .then(res => {
                        // Wyświetlenie powiadomienia o udanej transakcji
                        alert('Zakup wykonano pomyślnie');
                        // Przekierowanie (lub odświeżenie) na stronę koszyka, by zaktualizować widok po zakupie
                        window.location.href = 'koszyk.php';
                    })
                    // Obsługa ewentualnych błędów (np. problem z połączeniem internetowym, błąd 500 serwera)
                    .catch(err => {
                        // Wyświetlenie szczegółów błędu w konsoli przeglądarki (F12) ułatwiające debugowanie
                        console.error('Błąd podczas składania zamówienia:', err);
                    });
                }
            });
        }
    </script>
</body>
</html>