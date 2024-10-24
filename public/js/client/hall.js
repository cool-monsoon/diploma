document.addEventListener('DOMContentLoaded', function () {
    const hallLayout = document.querySelector('.buying-scheme__wrapper');
    const selectedSeatsInput = document.getElementById('selected-seats');
    const submitButton = document.querySelector('.acceptin-button');
    const selectedSeats = new Set();

    hallLayout.addEventListener('click', function (event) {
        const clickedSeat = event.target.closest('.buying-scheme__chair');
        if (!clickedSeat) return;
        if (clickedSeat.classList.contains('buying-scheme__chair_taken') ||
            clickedSeat.classList.contains('buying-scheme__chair_disabled')) {
            return;
        }

        const seatId = clickedSeat.dataset.seatId;
        if (clickedSeat.classList.contains('buying-scheme__chair_selected')) {
            clickedSeat.classList.remove('buying-scheme__chair_selected');
            selectedSeats.delete(seatId);
        } else {
            clickedSeat.classList.add('buying-scheme__chair_selected');
            selectedSeats.add(seatId);
        }

        updateSelectedSeatsInput();
        updateSubmitButton();
    });

    function updateSelectedSeatsInput() {
        selectedSeatsInput.value = Array.from(selectedSeats).join(',');
    }

    function updateSubmitButton() {
        submitButton.disabled = selectedSeats.size === 0;
    }

    updateSubmitButton();

    document.getElementById('booking-form').addEventListener('submit', function (e) {
        if (selectedSeats.size === 0) {
            e.preventDefault();
            alert('Пожалуйста, выберите хотя бы одно место.');
        } else {
            console.log('Submitting form with seats:', Array.from(selectedSeats));
        }
    });

    const bookForm = document.getElementById('booking-form');
    if (bookForm) {
        bookForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const selectedSeatsInput = document.getElementById('selected-seats');
            const showId = document.querySelector('input[name="show_id"]');
            const csrfToken = document.querySelector('input[name="_token"]');
            if (!selectedSeatsInput || !showId) {
                console.error('Required form elements are missing');
                return;
            }
            if (!csrfToken) {
                console.error('CSRF token meta tag is missing');
                return;
            }

            const seatIds = selectedSeatsInput.value;
            const showIdValue = showId.value;
            const paymentUrl = document.body.dataset.paymentUrl;

            fetch('/store-seats', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.value
                },
                body: JSON.stringify({
                    seat_ids: seatIds,
                    show_id: showIdValue
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.location.href = paymentUrl;
                    } else {
                        console.error('Server returned success: false');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    }

});