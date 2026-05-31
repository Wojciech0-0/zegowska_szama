<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sklep</title>

    <link rel="icon" type="image/png" href="logo.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl_sklep.css">
</head>
<?php
// Uruchomienie sesji w celu weryfikacji statusu zalogowania
session_start();

// Sprawdzenie autoryzacji – jeśli użytkownik nie jest zalogowany, odsyłamy go do formularza logowania
if(!isset($_SESSION['user_id'])){
    header("location: logowanie.php");
    exit;
}

// Pobranie identyfikatora sesji (ID użytkownika lub ciąg 'admin')
$status = $_SESSION['user_id'];

// Nawiązanie połączenia z lokalną bazą danych MySQL
$db = mysqli_connect('localhost', 'root', '', 'zegowskaszama');

// Weryfikacja poprawności połączenia z serwerem bazodanowym
if (!$db) {
    die("Błąd połączenia z bazą: " . mysqli_connect_error());
}
// Wymuszenie kodowania UTF-8 dla prawidłowego przesyłania polskich znaków diakrytycznych
mysqli_set_charset($db, "utf8mb4");

// Definicja domyślnego tekstu powitalnego
$imie_uzytkownika = "Użytkownik";

// Sprawdzenie, czy aktualnie zalogowany profil to zwykły użytkownik, czy administrator
if ($status !== 'admin') {
    // Pobranie imienia zalogowanego użytkownika na podstawie jego ID (zabezpieczone przed SQL Injection)
    $sql1 = "SELECT Imie, Email FROM użytkownicy WHERE id = '" . mysqli_real_escape_string($db, $status) . "'";
    $wynik = mysqli_query($db, $sql1);
    
    // Jeśli znaleziono dokładnie jeden pasujący rekord, przypisujemy imię do zmiennej
    if ($wynik && mysqli_num_rows($wynik) == 1) {
        $uzytkownik = mysqli_fetch_assoc($wynik);
        $imie_uzytkownika = $uzytkownik['Imie'];
    }
} else {
    // Jeśli status w sesji to 'admin', od razu ustawiamy sztywną wartość powitania
    $imie_uzytkownika = "Admin";
}
?>

<body style="background-image: url(background.png);">

    <header class="topbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <img src="logo.png" alt="logo" class="logo">
            </div>

            <button class="w-auto rounded-4 bg-light fw-bold border-0 shadow-sm px-3 py-2 btn btn-sm">
                <a href="konto.php" class="text-decoration-none text-dark"><?php echo htmlspecialchars($imie_uzytkownika); ?></a>
            </button>
        </div>
    </header>

    <main class="container py-5">
        <div class="shop-wrapper mx-auto">
            
            <div class="text-center my-5">
                <button class="btn summary-btn shadow-sm" id="koszyk">
                    Podsumowanie Koszyka
                </button>
            </div>

            <h1 class="text-center fw-bold mb-5">Nasza Oferta</h1>
            

            <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
                <button class="btn category-btn active" id="Wszystko">Wszystko</button>
                <button class="btn category-btn" id="Jedzenie">🥪 Jedzenie</button>
                <button class="btn category-btn" id="Napoje">🧃 Napoje</button>
                <button class="btn category-btn" id="Przekaski">🍫 Przekąski</button>
            </div>

            <div class="justify-content-center d-flex align-content-center mb-5">
                <input class="col-8 border-0 rounded-2 px-2 shadow-lg" type="text" id="wyszukiwarka" placeholder="Wyszukaj produkty">
            </div>


            <div class="row justify-content-center g-4" id="produktyS">
                <?php
                // Pobranie wszystkich dostępnych produktów posortowanych alfabetycznie po nazwie
                $sql = "SELECT produkty.id, produkty.Nazwa, produkty.Cena, produkty.Kategoria, produkty.opis FROM produkty ORDER BY produkty.Nazwa ASC";
                $wynik1 = mysqli_query($db, $sql);

                // Pętla iterująca po każdym produkcie zwróconym przez bazę danych
                while($d = mysqli_fetch_array($wynik1)){
                    // Dobór odpowiedniej grafiki podglądowej na podstawie przypisanej kategorii
                    if($d['Kategoria'] == "jedzenie"){
                        $zdjecie = "jedzenie.png";
                    } else if($d['Kategoria'] == "przekąski"){
                        $zdjecie = "przekaski.png";
                    } else if($d['Kategoria'] == "napoje"){
                        $zdjecie = "napoje.png";
                    } else {
                        $zdjecie = "jedzenie.png"; // Zdjęcie awaryjne / domyślne
                    }

                    // Renderowanie pojedynczej karty produktu w HTML za pomocą instrukcji echo
                    // Dodano spację przed kategorią w klasie diva, aby zapobiec zjawisku sklejania się klas CSS
                    echo '<div class="col-xl-3 col-lg-4 col-md-6 mx-sm-0 mx-3 ' . $d['Kategoria'] . '">
                    <div class="card border-0 shadow-sm rounded-4 h-100 product-card">

                        <div class="product-image text-center p-4">
                            <img src="' . $zdjecie . '" alt="" class="img-fluid">
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h4 class="fw-bold">' . $d['Nazwa'] . '</h4>
                            <p class="text-secondary flex-grow-1">' . $d['opis'] . '</p>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fs-3 fw-bold">' . number_format($d['Cena'], 2, ',', '') . ' zł</span>
                                <a href="dodajDoKoszyka.php?id=' . (int)$d['id'] . '" class="btn add-btn p-0 d-flex justify-content-center align-items-center text-white text-decoration-none" style="width: 40px; height: 40px; font-size: 24px; line-height: 1; border-radius: 50%;">+</a>
                            </div>
                        </div>
                    </div>
                    </div>';
                }
                ?>
            </div>
        </div>

        <script>
            // Pobranie referencji do elementów DOM (przycisków filtrów, kontenera produktów oraz inputu wyszukiwarki)
            const Wszystko = document.getElementById('Wszystko');
            const Jedzenie = document.getElementById('Jedzenie');
            const Przekaski = document.getElementById('Przekaski');
            const Napoje = document.getElementById('Napoje');
            const produktyS = document.getElementById('produktyS');
            const Wyszukiwarka = document.getElementById('wyszukiwarka');

            // Przekierowanie użytkownika do podstrony podsumowania koszyka po kliknięciu głównego przycisku
            document.getElementById('koszyk').addEventListener('click', () => {
                window.location.href = 'koszyk.php';
            });

            // Obsługa wyszukiwania asynchronicznego w czasie rzeczywistym (zdarzenie 'input')
            Wyszukiwarka.addEventListener('input',()=>{
                UsunKlasy(); // Resetowanie podświetlenia aktywnych filtrów kategorii
                Wszystko.classList.add('active'); // Wizualne ustawienie kategorii głównej jako aktywnej przy wyszukiwaniu tekstowym
                const szukanie = Wyszukiwarka.value;
                
                // Wysłanie zapytania POST do wyswietl.php z frazą kluczową wpisaną przez klienta
                fetch('wyswietl.php',{
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `szukanie=${encodeURIComponent(szukanie)}`
                }).then(res => res.text())
                .then(data => {
                    // Wstrzyknięcie przefiltrowanego kodu HTML wygenerowanego przez skrypt PHP bezpośrednio do widoku
                    document.getElementById('produktyS').innerHTML = data;
                });
            })

            // Filtracja asynchroniczna: Kategoria - Wszystko
            Wszystko.addEventListener('click',()=>{
                fetch('wyswietl.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `kategoria=${encodeURIComponent('Wszystko')}`
                }).then(res => res.text())
                .then(data => {
                    document.getElementById('produktyS').innerHTML = data;
                    UsunKlasy();
                    Wszystko.classList.add('active'); // Wizualne podświetlenie klikniętego przycisku
                });
            });

            // Filtracja asynchroniczna: Kategoria - Jedzenie
            Jedzenie.addEventListener('click',()=>{
                fetch('wyswietl.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `kategoria=${encodeURIComponent('Jedzenie')}`
                }).then(res => res.text())
                .then(data => {
                    document.getElementById('produktyS').innerHTML = data;
                });
                UsunKlasy();
                Jedzenie.classList.add('active');
            });

            // Filtracja asynchroniczna: Kategoria - Przekąski
            Przekaski.addEventListener('click',()=>{
                fetch('wyswietl.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `kategoria=${encodeURIComponent('Przekąski')}`
                }).then(res => res.text())
                .then(data => {
                    document.getElementById('produktyS').innerHTML = data;
                });
                UsunKlasy();
                Przekaski.classList.add('active');
            });

            // Filtracja asynchroniczna: Kategoria - Napoje
            Napoje.addEventListener('click',()=>{
                fetch('wyswietl.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `kategoria=${encodeURIComponent('Napoje')}`
                }).then(res => res.text())
                .then(data => {
                    document.getElementById('produktyS').innerHTML = data;
                });
                UsunKlasy();
                Napoje.classList.add('active');
            });

            // Pomocnicza funkcja czyszcząca klasę podświetlenia ('.active') ze wszystkich przycisków kategorii
            function UsunKlasy(){
                Wszystko.classList.remove('active');
                Jedzenie.classList.remove('active');
                Napoje.classList.remove('active');
                Przekaski.classList.remove('active');
            }
        </script>
    </main>
</body>
</html>