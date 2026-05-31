<?php
// Uruchomienie lub wznowienie sesji (potrzebne do zapisu danych zalogowanego użytkownika)
session_start();

// Inicjalizacja zmiennych do obsługi komunikatów dla użytkownika (np. o błędzie lub sukcesie)
$komunikat = "";
$klasa_komunikatu = "d-none"; // Domyślnie klasa CSS ukrywająca element (np. w Bootstrapie)

// Sprawdzenie, czy formularz został przesłany metodą POST oraz czy kliknięto przycisk "zaloguj"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['zaloguj'])) {
    
    // Nawiązanie połączenia z bazą danych
    $db = mysqli_connect('localhost', 'root', '', 'zegowskaszama');

    // Weryfikacja połączenia
    if (!$db) {
        die("Błąd połączenia z bazą: " . mysqli_connect_error());
    }

    // Pobranie danych z formularza, usunięcie zbędnych spacji (trim) 
    // oraz zabezpieczenie adresu e-mail przed atakami SQL Injection
    $email = mysqli_real_escape_string($db, trim($_POST['email']));
    $haslo = trim($_POST['password']);

    // Walidacja – sprawdzenie, czy pola nie są puste
    if (empty($email) || empty($haslo)) {
        $komunikat = "Uzupełnij wszystkie pola!";
        $klasa_komunikatu = "alert-danger"; // Czerwona ramka błędu
    } else {
        // Przygotowanie zapytania sprawdzającego tabelę zwykłych użytkowników
        $sql = "SELECT * FROM użytkownicy WHERE Email = '$email'";
        $result = mysqli_query($db, $sql);
        
        // Przygotowanie zapytania sprawdzającego tabelę administratorów
        $sql1 = "SELECT * FROM `admin` WHERE Email = '$email'";
        $result1 = mysqli_query($db, $sql1);

        // 1. KROK: Sprawdzenie, czy podany e-mail należy do administratora
        if(mysqli_num_rows($result1) == 1){
            $admin = mysqli_fetch_assoc($result1);

            // Weryfikacja hasła admina (uwaga: w tym miejscu porównywany jest czysty tekst, a nie hash)
            if($haslo == $admin['Hasło']){
                // Zapisanie roli administratora w sesji
                $_SESSION['user_id'] = 'admin';

                $komunikat = "Zalogowano pomyślnie!";
                $klasa_komunikatu = "alert-success"; // Zielona ramka sukcesu
                // Przekierowanie do panelu admina po 1 sekundzie
                header("refresh:1; url=panelAdmina.php");
            }
        } else {
            // 2. KROK: Jeśli to nie admin, sprawdzenie czy podany e-mail należy do zwykłego użytkownika
            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                
                // Bezpieczna weryfikacja hasła użytkownika (porównanie wpisanego hasła z hashem z bazy)
                if (password_verify($haslo, $user['Haslo'])) {
                    // Zapisanie ID oraz imienia użytkownika w sesji
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_imie'] = $user['Imie'];

                    $komunikat = "Zalogowano pomyślnie!";
                    $klasa_komunikatu = "alert-success";
                    // Przekierowanie do sklepu po 1 sekundzie
                    header("refresh:1; url=sklep.php");
                } else {
                    // Jeśli hash hasła się nie zgadza
                    $komunikat = "Nieprawidłowe hasło!";
                    $klasa_komunikatu = "alert-danger";
                }
            } else {
                // Jeśli adres e-mail nie istnieje ani w tabeli adminów, ani użytkowników
                $komunikat = "Nie znaleziono użytkownika o podanym adresie e-mail!";
                $klasa_komunikatu = "alert-danger";
            }
        }
    }
    // Zamknięcie połączenia z bazą danych na koniec działania skryptu
    mysqli_close($db);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zaloguj</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="styl.css
    ">
</head>
<body style="background-image: url(background.png);">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        
        <div class="bg-white border rounded-4 shadow-lg p-5 text-center" style="max-width: 400px; width: 100%;">
            
            <form action="logowanie.php" method="post">
                <div class="mb-4">
                    <img src="logo.png" alt="logo" class="img-fluid" style="max-height: 80px;">
                </div>
                
                <div class="mb-4">
                    <h2 class="fw-bold">Zaloguj się</h2>
                </div>
                
                <div class="mb-3">
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Adres e-mail..." id="email_logowanie">
                </div>
                
                <div class="mb-3">
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Hasło" id="haslo_logowanie">
                </div>

                <div class="text-end mb-4">
                    <a id="zapomniales" class="text-decoration-none text-muted small">Zapomniałeś hasła?</a>
                </div>
                
                <div class="alert <?php echo $klasa_komunikatu; ?>">
                    <?php echo $komunikat; ?>
                </div>

                <input type="submit" name="zaloguj" class="btn btn-success btn-lg w-100 mb-3" value="Zaloguj się" style="background-color: #8db63f; border: none;" id="login_button">
                </input>

                <div class="small">
                    Nie masz konta? <a href="rejestracja.php"  class="text-success text-decoration-none fw-bold">Zarejestruj się</a>
                </div>
            </form>
            
        </div>
    </div>

    <script>
        // Pobranie elementu (np. linku lub przycisku) o ID 'zapomniales'
        // Prawdopodobnie odnosi się do sekcji "Zapomniałeś hasła?"
        const zapomniales = document.getElementById('zapomniales');

        // Podpięcie nasłuchiwacza zdarzeń na kliknięcie w ten element
        zapomniales.addEventListener('click', () => {
            // Wyświetlenie mało pomocnego, ale za to bardzo empatycznego komunikatu dla zapominalskiego użytkownika ;)
            alert("To kiepsko :(");
        });
    </script>
</body>
</html>