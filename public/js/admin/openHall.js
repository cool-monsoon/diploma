document.addEventListener('DOMContentLoaded', function () {
    const radioButtons = document.querySelectorAll('.chooseToOpen');
    const statusMessage = document.getElementById('statusMessage');
    const openSalesButton = document.getElementById('open_sales');
    openSalesButton.classList.add('hidden');

    function updateUIState(seatsAvailable, showsAvailable, isActive) {
        let message = '';
        let showButton = false;

        if (!seatsAvailable) {
            message = 'Создайте конфигурацию для выбранного зала';
        } else if (!showsAvailable) {
            message = 'Создайте сеансы для выбранного зала';
        } else {
            message = 'Всё готово, теперь можно:';
            showButton = true;
        }

        statusMessage.textContent = message;
        statusMessage.classList.toggle('visible', message !== '');
        openSalesButton.textContent = isActive ? 'Приостановить продажу билетов' : 'Открыть продажу билетов';
        openSalesButton.value = isActive ? '0' : '1';
        openSalesButton.disabled = !showButton;
        openSalesButton.classList.toggle('disabled', !showButton);
        openSalesButton.classList.add('visible')
    }

    async function checkSeatsAndShows(hallId) {
        try {
            const response = await fetch(`/check-seats-and-shows?hallId=${hallId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            if (!response.ok) {
                throw new Error(`Error fetching seats and shows: ${response.statusText}`);
            }
            const data = await response.json();
            updateUIState(data.seatsAvailable, data.showsAvailable, data.is_active);
            openSalesButton.setAttribute('data-hall-id', hallId);
            openSalesButton.classList.remove('hidden');
        } catch (error) {
            console.error('Error:', error.message);
        }
    }

    radioButtons.forEach((radioButton) => {
        radioButton.addEventListener('change', (e) => {
            const hallId = e.target.value;
            checkSeatsAndShows(hallId);
            if (hallId) {
                checkSeatsAndShows(hallId);
            } else {
                openSalesButton.classList.add('hidden');
            }
        });
    });

    openSalesButton.addEventListener('click', (e) => {
        const hallId = e.target.getAttribute('data-hall-id');
        if (!hallId) {
            console.error('No hall ID found on the button');
            return;
        }
        const newIsActive = e.target.value === '1';
        updateIsActive(hallId, newIsActive);
    });

    async function updateIsActive(hallId, newIsActive) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found. Make sure you have a meta tag with name="csrf-token"');
            return;
        }

        try {
            const response = await fetch(`/halls/${hallId}/activateHall`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                },
                body: JSON.stringify({ is_active: newIsActive }),
            });
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`Error updating hall status. Status: ${response.status} Text: ${response.statusText}`);
            }
            const data = await response.json();
            console.log('Parsed response:', data);
            if (data.message && data.message.includes('updated successfully')) {
                console.log('Hall status updated successfully');
                if (openSalesButton) {
                    openSalesButton.textContent = newIsActive ? 'Приостановить продажу билетов' : 'Открыть продажу билетов';
                    openSalesButton.value = newIsActive ? '0' : '1';
                } else {
                    console.warn('openSalesButton not found');
                }
            } else {
                console.warn('Unexpected response:', data.message);
            }
        } catch (error) {
            console.error('Error:', error.message);
        }
    }

});






















