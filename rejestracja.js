document.getElementById('register_button').addEventListener("click", (e) => {
    e.preventDefault();

    let blad = document.getElementById('blad');

    // reset komunikatu
    blad.textContent = "";
    blad.classList.add("d-none"); // ukryj na start

    let imie = document.getElementById('imie');
    let nazwisko = document.getElementById('nazwisko');
    let email = document.getElementById('email');
    let haslo = document.getElementById('haslo');
    let haslo2 = document.getElementById('haslo2');

    // reset borderów
    imie.style.border = "";
    nazwisko.style.border = "";
    email.style.border = "";
    haslo.style.border = "";
    haslo2.style.border = "";

    if (imie.value.trim() === "") {
        imie.style.border = "2px solid red";
        blad.textContent = "Podaj imię!";
        blad.classList.remove("d-none");
    }

        else if (nazwisko.value.trim() === "") {
            nazwisko.style.border = "2px solid red";
            blad.textContent = "Podaj nazwisko!";
            blad.classList.remove("d-none");
        }

        else if (email.value.trim() === "") {
            email.style.border = "2px solid red";
            blad.textContent = "Podaj email!";
            blad.classList.remove("d-none");
        }

        else if (haslo.value.trim() === "") {
            haslo.style.border = "2px solid red";
            blad.textContent = "Podaj hasło!";
            blad.classList.remove("d-none");
        }

        else if (haslo2.value.trim() === "") {
            haslo2.style.border = "2px solid red";
            blad.textContent = "Potwierdź hasło!";
            blad.classList.remove("d-none");
        }

        else if (haslo.value.trim() !== haslo2.value.trim()) {
            haslo.style.border = "2px solid red";
            haslo2.style.border = "2px solid red";
            blad.textContent = "Podane hasła się różnią!";
            blad.classList.remove("d-none");
        }

    else {
        blad.classList.add("d-none");
        window.location.href = "logowanie.html";
    }
});