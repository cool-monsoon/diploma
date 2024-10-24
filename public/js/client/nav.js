document.addEventListener('DOMContentLoaded', function () {
    let today = new Date();
    let currentStartDate = new Date(today);
    let currentEndDate = new Date(currentStartDate);
    currentEndDate.setDate(currentStartDate.getDate() + 6);

    function updateNavBar() {
        const navLinks = document.querySelectorAll('.page-nav__day:not(#back-button):not(#forward-button)');
        navLinks.forEach((link, index) => {
            const date = new Date(currentStartDate);
            date.setDate(currentStartDate.getDate() + index);
            link.setAttribute('data-date', date.toISOString().split('T')[0]);
            const weekElement = link.querySelector('.page-nav__day-week');
            const numberElement = link.querySelector('.page-nav__day-number');
            if (weekElement) {
                weekElement.textContent = date.toLocaleString('ru-RU', { weekday: 'short' });
            }
            if (numberElement) {
                numberElement.textContent = date.getDate();
            }
            link.className = 'page-nav__day';
            if (date.toDateString() === today.toDateString()) {
                link.classList.add('page-nav__day_today');
            }
            if (date.getDay() === 0 || date.getDay() === 6) {
                link.classList.add('page-nav__day_weekend');
            }
        });

        const backButton = document.getElementById('back-button');
        if (backButton) {
            backButton.disabled = currentStartDate.toDateString() === new Date().toDateString();
        }
    }

    const backButton = document.getElementById('back-button');
    if (backButton) {
        backButton.addEventListener('click', function () {
            if (currentStartDate > today) {
                currentStartDate.setDate(currentStartDate.getDate() - 1);
                currentEndDate.setDate(currentEndDate.getDate() - 1);
                updateNavBar();
            }
        });
    }

    const forwardButton = document.getElementById('forward-button');
    if (forwardButton) {
        forwardButton.addEventListener('click', function () {
            currentStartDate.setDate(currentStartDate.getDate() + 1);
            currentEndDate.setDate(currentEndDate.getDate() + 1);
            updateNavBar();
        });
    }

    document.querySelectorAll('.page-nav__day').forEach(link => {
        link.addEventListener('click', function (event) {
            if (this.id !== 'back-button' && this.id !== 'forward-button') {
                event.preventDefault();
                const selectedDate = this.getAttribute('data-date');
                loadContentForDate(selectedDate);
                document.querySelectorAll('.page-nav__day').forEach(l => l.classList.remove('page-nav__day_chosen'));
                this.classList.add('page-nav__day_chosen');
            }
        });
    });

    function loadContentForDate(date) {
        console.log("Loading content for date:", date);
        fetch(`/get-content?date=${date}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text(); // Change this from response.json() to response.text()
            })
            .then(html => {
                document.getElementById('content-area').textContent = '';
                document.getElementById('content-area').insertAdjacentHTML('beforeend', html);
                document.querySelectorAll('.page-nav__day').forEach(link => {
                    if (link.getAttribute('data-date') === date) {
                        link.classList.add('page-nav__day_chosen');
                    } else {
                        link.classList.remove('page-nav__day_chosen');
                    }
                });
            });
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    updateNavBar();
    loadContentForDate(formatDate(new Date()));
    
});