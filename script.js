document.querySelector('.pytania-btn').addEventListener("click", (e) => {

    e.preventDefault();

    window.location.href = "pytania.html";

});


document.querySelector('.magazyn-btn').addEventListener("click", (e) => {

    e.preventDefault();

    window.location.href = "magazyn.html";

});


document.querySelector('.szczegoly-btn').addEventListener("click", (e) => {

    e.preventDefault();

    window.location.href = "szczegoly.html";

});


document.querySelector('.historia-btn').addEventListener("click", (e) => {

    e.preventDefault();

    window.location.href = "historia.html";

});


document.querySelectorAll('.details-btn').forEach(button => {

    button.addEventListener('click', (e) => {

        e.preventDefault();

        window.location.href = "szczegoly.html";

    });

});


document.querySelectorAll('.status-btn').forEach(button => {

    button.addEventListener('click', (e) => {

        e.preventDefault();

        window.location.href = "szczegoly.html";

    });

});