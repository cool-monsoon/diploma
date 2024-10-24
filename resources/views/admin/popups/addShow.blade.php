<div class="popup" id="addShowPopup">
    <div class="popup__container">
        <div class="popup__content">
            <div class="popup__header">
                <h2 class="popup__title">Добавление сеанса</h2>
            </div>
            <div class="popup__wrapper">
                <form action="{{ route('show.store') }}" method="post" accept-charset="utf-8" id="addShow">
                    @csrf
                    <label class="conf-step__label conf-step__label-fullsize" for="hall_id">Выберите зал:</label>
                    <select class="conf-step__select" name="hall_id" id="hall_id">
                        @foreach ($halls as $hall)
                            <option value="{{ $hall->id }}">{{ $hall->name }}</option>
                        @endforeach
                    </select>
                    <label class="conf-step__label conf-step__label-fullsize" for="movie_id">Выберите фильм:</label>
                    <select class="conf-step__select" name="movie_id" id="movie_id">
                        @foreach ($movies as $movie)
                            <option value="{{ $movie->id }}">{{ $movie->name }}</option>
                        @endforeach
                    </select>
                    <label class="conf-step__label conf-step__label-fullsize" for="date">
                        Дата сеанса
                        <input class="conf-step__input" id="date" type="date" name="date"
                            placeholder="2000-10-20" required>
                    </label>
                    <label class="conf-step__label conf-step__label-fullsize" for="start_time">
                        Время начала сеанса
                        <input class="conf-step__input" id="start_time" type="text" placeholder="00:00"
                            name="start_time" autocomplete="off" required>
                    </label>
                    <div class="conf-step__buttons text-center">
                        <button type="button" class="abort conf-step__button conf-step__button-regular"
                            onclick="togglePopup(document.getElementById('addShowPopup'))">закрыть</button>
                        <button type="submit" value="Добавить ceaнc"
                            class="conf-step__button conf-step__button-accent">Добавить ceaнc</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('/js/admin/togglePopup.js') }}"></script>
