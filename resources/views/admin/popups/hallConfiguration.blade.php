<div class="popup" id="hallConfigurationPopup">
    <div class="popup__container">
        <div class="popup__content">
            <div class="popup__header">
                <h2 class="popup__title">Конфигурация зала</h2>
            </div>
            <div class="popup__wrapper">
                <form id='seats_form'>
                    @csrf
                    <div class="conf-step__wrapper">
                        <p class="conf-step__paragraph">Выберите зал для конфигурации:</p>
                        <ul class="conf-step__selectors-box hall-configuration">
                            @foreach ($halls as $hall)
                                <li>
                                    <input type="radio" class="conf-step__radio chooseToConfigure"
                                        id="chooseToConfigure_{{ $hall->id }}" data-hall-id="{{ $hall->id }}"
                                        name="chairs-hall" value="{{ $hall->id }}">
                                    <label for="chooseToConfigure_{{ $hall->id }}"
                                        class="conf-step__selector">{{ $hall->name }}</label>
                                </li>
                            @endforeach
                        </ul>
                        <p class="conf-step__paragraph">Укажите количество рядов и максимальное количество кресел в
                            ряду:</p>
                        <div class="conf-step__legend">
                            <label class="conf-step__label" for="rowsNumber">Рядов, шт<input type="text"
                                    class="conf-step__input rowsNumber" id="rowsNumber" placeholder="10" value>
                            </label>
                            <span class="multiplier">x</span>
                            <label class="conf-step__label" for="seatsNumber">Мест, шт
                                <input type="text" class="conf-step__input seatsNumber" id="seatsNumber"
                                    placeholder="8" value=''>
                            </label>
                        </div>
                        <p class="conf-step__paragraph">Теперь вы можете указать типы кресел на схеме зала:</p>
                        <div class="conf-step__legend">
                            <span class="conf-step__chair conf-step__chair_standart"></span> — обычные кресла
                            <span class="conf-step__chair conf-step__chair_vip"></span> — VIP кресла
                            <span class="conf-step__chair conf-step__chair_disabled"></span> — заблокированные (нет
                            кресла)
                            <p class="conf-step__hint">Чтобы изменить вид кресла, нажмите по нему левой кнопкой мыши</p>
                        </div>
                        <div class="conf-step__hall">
                            <div class="conf-step__hall-wrapper">
                            </div>
                        </div>
                        <fieldset form="seats_form" class="conf-step__buttons text-center">
                            <button class="conf-step__button conf-step__button-regular"
                                onclick="togglePopup(document.getElementById('hallConfigurationPopup'))">Закрыть</button>
                            <button type="submit" value="Сохранить" id="submitConfigurationButton"
                                class="conf-step__button conf-step__button-accent save_seats">Сохранить</button>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('/js/admin/createHallConfiguration.js') }}"></script>
<script src="{{ asset('/js/admin/togglePopup.js') }}"></script>
