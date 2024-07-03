    // javacript
document.addEventListener('DOMContentLoaded', function() {
    const backgroundOverlay = document.querySelector('.background-overlay');
    const logoContainer = document.querySelector('.logo-container');

    logoContainer.addEventListener('animationend', function(event) {
        if (event.animationName === 'fadeOut') {
            logoContainer.classList.add('hidden');
        }
    });

    backgroundOverlay.addEventListener('animationend', function() {
        backgroundOverlay.classList.add('hidden');
    });
});
