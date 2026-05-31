<?php
    // Nawiązanie połączenia z bazą danych 'zegowskaszama'
    $db = mysqli_connect('localhost', 'root', '', 'zegowskaszama');

    // Pobranie kryteriów filtrowania z żądania POST. 
    // Jeśli parametry nie zostały przesłane, ustawiane są wartości domyślne (brak filtrowania).
    $kategoria = $_POST['kategoria'] ?? 'Wszystko';
    $szukanie = $_POST['szukanie'] ?? '';

    // --- BUDOWANIE ZAPYTANIA SQL ---
    // Sprawdzenie, czy użytkownik wybrał konkretną kategorię (np. Jedzenie, Napoje)
    if($kategoria != "Wszystko"){
        // Zapytanie filtrujące produkty jednocześnie po wybranej kategorii ORAZ frazie z wyszukiwarki (klauzula LIKE)
        $sql = "SELECT produkty.id, produkty.Nazwa, produkty.Cena, produkty.Kategoria, produkty.opis 
                FROM produkty 
                WHERE produkty.Kategoria = '" . mysqli_real_escape_string($db, $kategoria) . "' 
                AND produkty.Nazwa LIKE '%" . mysqli_real_escape_string($db, $szukanie) . "%' 
                ORDER BY produkty.Nazwa ASC";
    }else{
        // Jeśli wybrana jest kategoria "Wszystko", filtrujemy produkty wyłącznie po frazie wpisanej w wyszukiwarkę
        $sql = "SELECT produkty.id, produkty.Nazwa, produkty.Cena, produkty.Kategoria, produkty.opis 
                FROM produkty 
                WHERE produkty.Nazwa LIKE '%" . mysqli_real_escape_string($db, $szukanie) . "%' 
                ORDER BY produkty.Nazwa ASC";
    }

    // Wykonanie zbudowanego zapytania SQL w bazie danych
    $wynik1 = mysqli_query($db, $sql);

    // --- RENDEROWANIE KART PRODUKTÓW ---
    // Pętla przetwarzająca kolejno każdy produkt pasujący do kryteriów wyszukiwania
    while($d = mysqli_fetch_array($wynik1)){
        
        // Dynamiczny dobór grafiki podglądowej (ikony) na podstawie kategorii produktu
        if($d['Kategoria'] == "jedzenie"){
            $zdjecie = "jedzenie.png";
        }else if($d['Kategoria'] == "przekąski"){
            $zdjecie = "przekaski.png";
        }else if($d['Kategoria'] == "napoje"){
            $zdjecie = "napoje.png";
        }else{
            $zdjecie = "jedzenie.png"; // Zdjęcie domyślne w razie braku dopasowania
        }

        // Generowanie i wysyłanie (echo) fragmentu kodu HTML dla pojedynczej karty produktu.
        // Ten kod zostanie odebrany przez JavaScript (jako tekst) i wstrzyknięty do diva #produktyS.
        // Poprawka: dodałem spację przed '. $d['Kategoria']', aby klasy CSS się nie łączyły w np. "mx-3jedzenie".
        echo '<div class="col-xl-3 col-lg-4 col-md-6 mx-sm-0 mx-3 ' . $d['Kategoria'] . '">

        <div class="card border-0 shadow-sm rounded-4 h-100 product-card">

            <div class="product-image text-center p-4">
                <img src="' . $zdjecie . '" alt="" class="img-fluid">
            </div>

            <div class="card-body d-flex flex-column">

                <h4 class="fw-bold">
                    ' . $d['Nazwa'] . '
                </h4>

                <p class="text-secondary flex-grow-1">
                    ' . $d['opis'] . '
                </p>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="fs-3 fw-bold">' . number_format($d['Cena'], 2, ',', '') . ' zł</span>
                    <a href="dodajDoKoszyka.php?id=' . (int)$d['id'] . '" class="btn add-btn p-0 d-flex justify-content-center align-items-center text-white text-decoration-none" style="width: 40px; height: 40px; font-size: 24px; line-height: 1; border-radius: 50%;">+</a>
                </div>
            </div>
        </div>
        </div>';
    }
?>