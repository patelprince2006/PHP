
var swiper = new Swiper(".mySwiper", {
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});

// toggle button
function showHide(){
    var navbar=document.getElementByClassName('.btn');
    navbar.addEventListener('click',function(){
        navbar.toggle('showHide');
    });
}

// function showHide() {
//     const toggler = document.querySelector('.navbar-toggler');
//     // const navbarCollapse = document.querySelector('#navbarNav');

//     toggler.addEventListener('click', () => {
//         navbarCollapse.classList.toggle('show');
//     });

// }


// function loadPHP(elementId, filePath) {
//     fetch(filePath)
//         .then(response => response.text())
//         .then(data => document.getElementById(elementId).innerPHP = data)
//         .catch(err => console.error('Error loading PHP:', err));
// }
// window.onload = function () {
//     loadPHP('.navbar', 'navbar.php');
//     loadPHP('.footer', 'footer.php');
// }; 