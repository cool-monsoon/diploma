document.addEventListener('DOMContentLoaded', function () {
    const priceSubmit = document.getElementById('price_form');
    const submitButton = document.getElementById('submitPriceButton');
    submitButton.disabled = true;

    document.querySelectorAll('.choosePrice').forEach(radio => {
        radio.addEventListener('change', function () {
            let hallId = document.querySelector('.conf-step__radio:checked').value;
            console.log('hallId', hallId);
            const elements = document.querySelectorAll('.conf-step__row');
            if (elements) {
                elements.forEach(element => { element.remove(); });
            }

            getPrice(hallId);

            if (hallId) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        });
    });

    async function getPrice(hallId) {
        try {
            const response = await fetch(`/halls/${hallId}/price`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            if (!response.ok) {
                throw new Error(`Error fetching price: ${response.statusText}`);
            }
            const data = await response.json();
            const standardSeatPrice = data.standard_seat_price;
            const vipSeatPrice = data.vip_seat_price;
            
            displayPrice(standardSeatPrice, vipSeatPrice);
        } catch (error) {
            console.error('Error:', error.message);
        }
    }

    function displayPrice(standardSeatPrice, vipSeatPrice) {
        const priceWrapper = document.querySelector('.current-price');
        const priceWrapperElements = document.querySelectorAll('.current-price-legend');
        const insertPrice = `
          <p class="conf-step__legend current-price-legend">Текущая цена за обычные кресла : ${standardSeatPrice} рублей
          </br>
          Текущая цена за VIP кресла: ${vipSeatPrice} рублей</p>`;
        if (priceWrapperElements) {
            priceWrapperElements.forEach(element => { element.remove(); });
        }
        priceWrapper.insertAdjacentHTML('beforeend', insertPrice);
    }

    async function priceUpdate() {
        const chosenHallId = document.querySelector('.conf-step__radio:checked').value;
        const standartPriceInput = document.getElementById('standartPrice');
        const vipPriceInput = document.getElementById('vipPrice');
        const token = document.querySelector('input[name="_token"]').value;

        try {
            const response = await fetch(`/halls/${chosenHallId}/price`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({
                    hall_id: chosenHallId,
                    standard_seat_price: standartPriceInput.value,
                    vip_seat_price: vipPriceInput.value
                }),
            });
            if (!response.ok) {
                throw new Error(`Error updating hall: ${response.statusText}`);
            }
            console.log('Hall updated successfully');
            standartPriceInput.value = '';
            vipPriceInput.value = '';
        } catch (error) {
            console.error('Error:', error.message);
        }
    }

    if (priceSubmit) {
        priceSubmit.addEventListener('submit', function (event) {
            event.preventDefault();
            const buttonClicked = event.submitter;
            if (buttonClicked.classList.contains('save-price')) {
                priceUpdate();
            } else if (buttonClicked.classList.contains('abort')) {
                console.log('Form submission aborted');
            }
        });
    } else {
        console.warn('Price form not found');
    }

});

