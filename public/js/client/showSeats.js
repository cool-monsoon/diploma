document.addEventListener('DOMContentLoaded', function () {
    const hallLayout = document.querySelector('.buying-scheme__wrapper');
    if (!hallLayout) {
        console.error('Error: Could not find element with class "buying-scheme__wrapper"');
        return;
    }
    const showId = hallLayout.dataset.showId;
    if (!showId) {
        console.error('Error: No show ID provided in hallLayout data attribute');
        return;
    }
    let numberOfRows, numberOfSeats, seatsArray;

    function getShowSeats(showId) {
        fetch(`/api/show-seats/${showId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Show seats data:', data);
                numberOfRows = data.rowsNumber;
                numberOfSeats = data.seatsNumber;
                seatsArray = data.seats;

                displaySeats(seatsArray, numberOfRows, numberOfSeats);
            })
            .catch(error => console.error('Error:', error));
    }

    getShowSeats(showId);
});

function displaySeats(seatsArray, numberOfRows, numberOfSeats) {
    const wrapper = document.querySelector('.buying-scheme__wrapper');
    for (let row = 0; row < numberOfRows; row++) {
        const insertRow = '<div class="buying-scheme__row"></div>';
        wrapper.insertAdjacentHTML('beforeend', insertRow);
        const rowWrapper = wrapper.querySelector('.buying-scheme__row:last-child');
        for (let i = 0; i < numberOfSeats; i++) {
            if (seatsArray.length === 0) break;
            const currentSeat = seatsArray.shift();
            let seatClass = 'seat buying-scheme__chair';
            if (currentSeat.is_booked) {
                seatClass += ' buying-scheme__chair_taken';
            } else {
                switch (currentSeat.seat_type) {
                    case 'disabled':
                        seatClass += ' buying-scheme__chair_disabled';
                        break;
                    case 'standart':
                        seatClass += ' buying-scheme__chair_standart';
                        break;
                    case 'vip':
                        seatClass += ' buying-scheme__chair_vip';
                        break;
                }
            }
            const insertSeat = `<span class="${seatClass}" data-seat-id="${currentSeat.id}"></span>`;
            rowWrapper.insertAdjacentHTML('beforeend', insertSeat);
        }
    }

}


