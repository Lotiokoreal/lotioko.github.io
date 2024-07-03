document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('nav');

    function showNavbar() {
        navbar.style.top = '0';
    }

    function hideNavbar() {
        navbar.style.top = '-50px';
    }


    document.addEventListener('mousemove', function(event) {
        if (event.clientY < 60) {
            showNavbar();
        } else {
            hideNavbar();
        }
    });
});