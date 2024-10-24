<div class="popup" id="addHallPopup">
    <div class="popup__container">
        <div class="popup__content">
            <div class="popup__header">
                <h2 class="popup__title">Добавление зала</h2>
            </div>
            <div class="popup__wrapper">
                <form action="{{ route('hall.store') }}" method="post" accept-charset="utf-8">
                    @csrf
                    <label class="conf-step__label conf-step__label-fullsize" for="hall-name"> Название зала
                        <input class="conf-step__input" type="text" id="hall-name" placeholder="Зал 1" name="name"
                            autocomplete="off" required>
                    </label>
                    <div class="conf-step__buttons text-center"
                        style="display: flex; justify-content: center; align-items: center;">
                        <button type="button" class="conf-step__button conf-step__button-regular"
                            onclick="togglePopup(document.getElementById('addHallPopup'))">Закрыть</button>
                        <button type="submit" class="conf-step__button conf-step__button-accent">Добавить зал</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('/js/admin/togglePopup.js') }}"></script>
