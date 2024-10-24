document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.chooseToShow').forEach(radio => {
        radio.addEventListener('click', function () {
            let hallId = this.getAttribute('data-hall-id');
            const elements = document.querySelectorAll('.conf-step__row');
            if (elements) {
                elements.forEach(element => { element.remove(); });
            }
            getSeats(hallId);
        })
    })

    async function getSeats(hallId) {
        let hall = hallId;
        try {
            const response = await fetch(`/seats/${hallId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            if (!response.ok) {
                throw new Error(`Error fetching seats: ${response.statusText}`);
            }
            const data = await response.json();
            displayDimentions(data, hall);
            displaySeats(data, hall);
        } catch (error) {
            console.error('Error:', error.message);
        }
    }

    function displayDimentions(seatsData, hall) {
        const seats = seatsData.seats
        const rowsNumber = seatsData.rowsNumber;
        const seatsNumber = seatsData.seatsNumber;
        const wrapper = document.querySelector('.rows-seats');
        const legendElements = document.querySelectorAll('.rows-seats-legend');
        const legend = `<span class="conf-step__paragraph rows-seats-legend">Рядов ${rowsNumber} x Мест ${seatsNumber}</span>`;
        if (legendElements) {
            legendElements.forEach(element => { element.remove(); });
        }
        if (seats && seats.length > 0) {
            wrapper.insertAdjacentHTML('beforeend', legend);
        } else {
            console.log('No seats found for this hall');
        }
    }

    function displaySeats(seatsData) {
        const wrapper = document.querySelector('.wrapperToRender');
        const rowsNumber = seatsData.rowsNumber;
        const seatsNumber = seatsData.seatsNumber;
        const seats = seatsData.seats;

        for (let row = 0; row < rowsNumber; row++) {
            const insertRow = '<div class="conf-step__row"></div>';
            wrapper.insertAdjacentHTML('beforeend', insertRow);
            const rowWrapper = wrapper.querySelector('.conf-step__row:last-child');
            for (let i = 0; i < seatsNumber; i++) {
                if (seats.length === 0) break;
                const currentSeat = seats.shift();
                if (currentSeat.seat_type === "disabled") {
                    const insertSeat = `<span class="seat conf-step__chair conf-step__chair_disabled"></span>`;
                    rowWrapper.insertAdjacentHTML('beforeend', insertSeat);
                } else if (currentSeat.seat_type === "standart") {
                    const insertSeat = `<span class="seat conf-step__chair conf-step__chair_standart"></span>`;
                    rowWrapper.insertAdjacentHTML('beforeend', insertSeat);
                } else if (currentSeat.seat_type === "vip") {
                    const insertSeat = `<span class="seat conf-step__chair conf-step__chair_vip"></span>`;
                    rowWrapper.insertAdjacentHTML('beforeend', insertSeat);
                }
            }
        }
    }
});