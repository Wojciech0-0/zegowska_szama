<?php
// Inicjalizacja zmiennych do obsługi komunikatów dla użytkownika (np. o błędzie lub sukcesie)
$komunikat = "";
$klasa_komunikatu = "d-none"; // Domyślnie ukrywa element z komunikatem w warstwie HTML/CSS (np. Bootstrap)

// Sprawdzenie, czy formularz został przesłany metodą POST oraz czy kliknięto przycisk "zarejestruj"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['zarejestruj'])) {
    
    // Nawiązanie połączenia z bazą danych
    $db = mysqli_connect('localhost', 'root', '', 'zegowskaszama');

    // Weryfikacja, czy połączenie z bazą danych powiodło się
    if (!$db) {
        die("Błąd połączenia z bazą: " . mysqli_connect_error());
    }

    // Pobranie danych z formularza, oczyszczenie ich ze zbędnych spacji na początku i końcu (trim)
    // oraz zabezpieczenie przed atakami typu SQL Injection za pomocą mysqli_real_escape_string
    $imie = mysqli_real_escape_string($db, trim($_POST['Imie']));
    $nazwisko = mysqli_real_escape_string($db, trim($_POST['Nazwisko']));
    $email = mysqli_real_escape_string($db, trim($_POST['email']));
    $haslo = trim($_POST['password']);
    $haslo2 = trim($_POST['password2']); // Powtórzone hasło do weryfikacji poprawności

    // KROK 1: Walidacja – sprawdzenie, czy wszystkie wymagane pola zostały wypełnione
    if (empty($imie) || empty($nazwisko) || empty($email) || empty($haslo) || empty($haslo2)) {
        $komunikat = "Wszystkie pola są wymagane!";
        $klasa_komunikatu = "alert-danger"; // Czerwona ramka błędu
    
    // KROK 2: Sprawdzenie, czy oba wprowadzone hasła są dokładnie takie same
    } elseif ($haslo !== $haslo2) {
        $komunikat = "Podane hasła nie są identyczne!";
        $klasa_komunikatu = "alert-danger";
    
    } else {
        // KROK 3: Sprawdzenie, czy podany adres e-mail nie istnieje już w bazie danych
        // Szukamy go zarówno w tabeli użytkowników, jak i w tabeli administratorów
        $sql_check = "SELECT id FROM użytkownicy WHERE email = '$email'";
        $result_check = mysqli_query($db, $sql_check);
        
        $sql_check1 = "SELECT id FROM `admin` WHERE email = '$email'";
        $result_check1 = mysqli_query($db, $sql_check1);

        // Jeśli zapytanie zwróciło chociaż jeden wiersz w którejś z tabel, e-mail jest zajęty
        if (mysqli_num_rows($result_check) > 0 || mysqli_num_rows($result_check1) > 0) {
            $komunikat = "Ten adres e-mail jest już zajęty!";
            $klasa_komunikatu = "alert-danger";
        
        } else {
            // KROK 4: Bezpieczne hashowanie hasła za pomocą algorytmu bcrypt (PASSWORD_DEFAULT)
            // Nigdy nie zapisujemy haseł w bazie danych w formie czystego tekstu!
            $haslo_hash = password_hash($haslo, PASSWORD_DEFAULT);

            // Przygotowanie zapytania dodającego nowego użytkownika do bazy danych
            $sql_insert = "INSERT INTO użytkownicy (Imie, Nazwisko, Email, Haslo) VALUES ('$imie', '$nazwisko', '$email', '$haslo_hash')";

            // Wykonanie zapytania i sprawdzenie, czy rejestracja się powiodła
            if (mysqli_query($db, $sql_insert)) {
                $komunikat = "Rejestracja pomyślna! Za chwilę nastąpi przekierowanie...";
                $klasa_komunikatu = "alert-success"; // Zielona ramka sukcesu
                
                // Przekierowanie użytkownika na stronę logowania po upływie 2 sekund
                header("refresh:2; url=logowanie.php");
            } else {
                // Obsługa ewentualnego błędu bazy danych podczas zapisu
                $komunikat = "Błąd podczas rejestracji: " . mysqli_error($db);
                $klasa_komunikatu = "alert-danger";
            }
        }
    }
    // Zamknięcie połączenia z bazą danych
    mysqli_close($db);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-image: url(background.png);">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="bg-white border rounded-4 shadow-lg p-5 text-center" style="max-width: 400px; width: 100%;">
            
            <form action="rejestracja.php" method="post">
                <div class="mb-4">
                    <img src="logo.png" alt="logo" class="img-fluid" style="max-height: 80px;">
                </div>
                
                <div class="mb-4">
                    <h2 class="fw-bold">Rejestracja użytkownika</h2>
                </div>

                <div class="mb-3">
                    <input type="text" name="Imie"
                    class="form-control form-control-lg" placeholder="Imie" id="imie">
                </div>

                <div class="mb-3">
                    <input type="text" name="Nazwisko" class="form-control form-control-lg"
                    placeholder="Nazwisko" id="nazwisko">
                </div>
                
                <div class="mb-3">
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Adres e-mail..." id="email">
                </div>
                
                <div class="mb-3">
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Hasło" id="haslo">
                </div>

                <div class="mb-3">
                    <input type="password" name="password2" class="form-control form-control-lg" placeholder="Potwierdź hasło" id="haslo2">
                </div>
                
                <div id="blad" class="alert <?php echo $klasa_komunikatu; ?>">
                    <?php echo $komunikat; ?>
                </div>

                <button type="submit" name="zarejestruj" class="btn btn-success btn-lg w-100 mb-3" style="background-color: #8db63f; border: none;">
                    Zarejestruj się
                </button>
                <div class="small">
                    Masz już konto? <a href="logowanie.php"  class="text-success text-decoration-none fw-bold">Zaloguj się</a>
                </div>
            </form>
            
        </div>
    </div>
</body>
</html>