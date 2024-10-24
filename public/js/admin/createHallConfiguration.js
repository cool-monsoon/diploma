document.addEventListener('DOMContentLoaded', function () {
    const rowInput = document.getElementById('rowsNumber');
    const seatInput = document.getElementById('seatsNumber');
    const formSubmit = document.getElementById('seats_form');
    const submitButton = document.getElementById('submitConfigurationButton');
    submitButton.disabled = true;

    let rowsValue;
    let seatsValue;
    let hallDimentions = {};

    function checkHallSelection() {
        const chosenHallId = document.querySelector('.conf-step__radio:checked');
        submitButton.disabled = !chosenHallId;
    }

    rowInput.addEventListener('change', (event) => {
        rowsValue = rowInput.value;
        hallDimentions.rowsValue = rowsValue;
        const elements = document.querySelectorAll('.conf-step__row');
        if (elements) {
            elements.forEach(element => { element.remove(); });
        };
        if (hallDimentions.rowsValue > 0 && hallDimentions.seatsValue > 0) {
            createHallConfiguration(hallDimentions);
        }
        checkHallSelection();
    });

    seatInput.addEventListener('change', (event) => {
        seatsValue = seatInput.value;
        hallDimentions.seatsValue = seatsValue;
        const elements = document.querySelectorAll('.conf-step__row');
        if (elements) {
            elements.forEach(element => { element.remove(); });
        };
        if (hallDimentions.rowsValue > 0 && hallDimentions.seatsValue > 0) {
            createHallConfiguration(hallDimentions);
        }
        checkHallSelection();
    });

    function createHallConfiguration(hallDimentions) {
        const wrapper = document.querySelector('.conf-step__hall-wrapper');
        rowsNumber = hallDimentions.rowsValue;
        seatsNumber = hallDimentions.seatsValue;

        for (let row = 0; row < rowsNumber; row++) {
            const insertRow = '<div class="conf-step__row"></div>';
            wrapper.insertAdjacentHTML('beforeend', insertRow);
            const rowWrapper = wrapper.querySelector('.conf-step__row:last-child');
            for (let seat = 0; seat < seatsNumber; seat++) {
                const insertSeat = `<span class="seat conf-step__chair conf-step__chair_disabled "></span>`;
                rowWrapper.insertAdjacentHTML('beforeend', insertSeat);
            };
        };

        const seats = [...document.getElementsByClassName('seat')];

        for (let i = 0; i < seats.length; i++) {
            seats[i].addEventListener('click', function () {
                if (seats[i].classList.contains('conf-step__chair_disabled')) {
                    seats[i].classList.add('conf-step__chair_standart');
                    seats[i].classList.remove('conf-step__chair_disabled');
                } else if (seats[i].classList.contains('conf-step__chair_standart')) {
                    seats[i].classList.add('conf-step__chair_vip');
                    seats[i].classList.remove('conf-step__chair_standart');
                } else if (seats[i].classList.contains('conf-step__chair_vip')) {
                    seats[i].classList.remove('conf-step__chair_vip');
                    seats[i].classList.add('conf-step__chair_disabled');
                }
            });
        }
    }

    if (hallDimentions.rowsValue > 0 && hallDimentions.seatsValue > 0) {
        createHallConfiguration(hallDimentions);
    }

    async function saveHallDimentions() {
        const chosenHallId = document.querySelector('.conf-step__radio:checked').value;
        const rowInput = document.getElementById('rowsNumber');
        const seatInput = document.getElementById('seatsNumber');
        const token = document.querySelector('input[name="_token"]').value;

        try {
            const response = await fetch(`/halls/${chosenHallId}/dimentions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({
                    hall_id: chosenHallId,
                    rows_number: rowInput.value,
                    seats_number: seatInput.value
                }),
            });

            if (!response.ok) {
                const errorMessage = await response.json();
                throw new Error(errorMessage.message || 'Error updating hall dimensions');
            }
            console.log('Hall updated successfully');
        } catch (error) {
            console.error('Error updating hall:', error.message);
        }
    };

    function saveSeats() {
        const seats = [...document.getElementsByClassName('seat')];
        const hallId = document.querySelector('.conf-step__radio:checked').value;
        const rows = parseInt(document.getElementById('rowsNumber').value, 10);
        const seatsPerRow = parseInt(document.getElementById('seatsNumber').value, 10);

        let rowCounter = 0;
        const totalSeats = seats.length;
        const seatsArray = [];

        for (let i = 0; i < rows; i++) {
            let hasNonDisabledSeat = false;
            let nonDisabledSeatCounter = 0;

            for (let j = 0; j < seatsPerRow; j++) {
                const seatIndex = i * seatsPerRow + j;
                if (seatIndex >= totalSeats) break;

                let seatType;
                if (seats[seatIndex].classList.contains('conf-step__chair_disabled')) {
                    seatType = 'disabled';
                } else if (seats[seatIndex].classList.contains('conf-step__chair_standart')) {
                    seatType = 'standart';
                    hasNonDisabledSeat = true;
                    nonDisabledSeatCounter++;
                } else if (seats[seatIndex].classList.contains('conf-step__chair_vip')) {
                    seatType = 'vip';
                    hasNonDisabledSeat = true;
                    nonDisabledSeatCounter++;
                }

                seatsArray.push({
                    hall_id: hallId,
                    seat_type: seatType,
                    row_name: hasNonDisabledSeat ? rowCounter + 1 : 1,
                    seat_name: seatType === 'disabled' ? 0 : nonDisabledSeatCounter
                });
            }

            if (hasNonDisabledSeat) {
                rowCounter++;
            }
        }

        if (seatsArray.length > 0) {
            updateSeats(hallId, seatsArray);
        }
    }

    const updateSeats = async (chosenHallId, seatsArray) => {
        try {
            const response = await fetch(`/seats/${chosenHallId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify({ seats: seatsArray }),
            });
            if (!response.ok) {
                const errorMessage = await response.json();
                throw new Error(errorMessage.message || 'Error updating seats');
            }
            console.log('Seats updated successfully');
        } catch (error) {
            console.error('Error updating seats:', error.message);
        }
    };

    formSubmit.addEventListener('submit', function (event) {
        event.preventDefault();
        const clickedButton = event.submitter;
        if (clickedButton.classList.contains('save_seats')) {
            saveSeats();
            saveHallDimentions();
            const elements = document.querySelectorAll('.conf-step__row');
            if (elements) {
                elements.forEach(element => { element.remove(); });
            };
        } else if (clickedButton.classList.contains('abort')) {
            console.log('something went wron again');
        }
    });

});

