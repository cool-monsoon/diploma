<div class="popup" id="addMoviePopup">
    <div class="popup__container">
        <div class="popup__content">
            <div class="popup__header">
                <h2 class="popup__title">Добавление фильма</h2>
            </div>
            <div class="popup__wrapper">
                <form action="{{ route('movie.store') }}" method="post" accept-charset="utf-8" id="addMovie"
                    enctype="multipart/form-data">
                    @csrf
                    <label class="conf-step__label conf-step__label-fullsize" for="movieName">
                        Название фильма
                        <input class="conf-step__input" id="movieName" type="text" placeholder="Название фильма"
                            name="name"autocomplete="off" required>
                    </label>
                    <label class="conf-step__label conf-step__label-fullsize" for="movieDescription">
                        Описание фильма
                        <textarea class="conf-step__input" id="movieDescription" placeholder="Описание фильма" name="description" required></textarea>
                    </label>
                    <label class="conf-step__label conf-step__label-fullsize" for="movieDuration">
                        Продолжительность фильма, мин
                        <input class="conf-step__input" id="movieDuration" type="text" placeholder="120"
                            name="duration" autocomplete="off" required>
                    </label>
                    <label class="conf-step__label conf-step__label-fullsize" for="movieCountry">
                        Страна производства
                        <input class="conf-step__input" id="movieCountry" type="text" placeholder="Страна"
                            name="country" autocomplete="off" required>
                    </label>
                    <label class="conf-step__label conf-step__label-fullsize" for="moviePoster">
                        Постер
                        <input class="conf-step__input" id="moviePoster" type="file" name="poster" accept="image/*"
                            required>
                    </label>
                    <div class="alert"></div>
                    <div class="conf-step__buttons text-center">
                        <button type="button" class="abort conf-step__button conf-step__button-regular"
                            onclick="togglePopup(document.getElementById('addMoviePopup'))">закрыть</button>
                        <button type="submit" value="Добавить фильм" class="conf-step__button conf-step__button-accent"
                            autocomplete="off"> Добавить фильм </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('/js/admin/togglePopup.js') }}"></script>
