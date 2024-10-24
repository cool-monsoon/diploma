document.addEventListener('DOMContentLoaded', function () {
    window.togglePopup = function (el, event) {
        if (event) {
            event.preventDefault();
        }
        if (el) {
            el.classList.toggle('active');
        }
    };
});