document.addEventListener('DOMContentLoaded', function() {
    const menu = document.getElementById('menu');
    const dropdownContent = document.getElementById('dropdownContent');

    menu.addEventListener('mouseover', function() {
        dropdownContent.style.display = 'block';
    });

    menu.addEventListener('mouseout', function() {
        dropdownContent.style.display = 'none';
    });
});
