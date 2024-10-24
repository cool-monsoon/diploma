<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ИдёмВКино</title>
    <link rel="stylesheet" href="{{ asset('css/admin/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/normalize.css') }}">
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&amp;subset=cyrillic,cyrillic-ext,latin-ext"
        rel="stylesheet">
</head>

@include('admin.popups.addHall');
@include('admin.popups.addMovie');
@include('admin.popups.addShow');
@include('admin.popups.hallConfiguration');

<body>
    <header class="page-header" style="display:flex; align-items: center; justify-content: space-between">
        <div class="header__info">
            <h1 class="page-header__title">Идём<span>в</span>кино</h1>
            <span class="page-header__subtitle">Администраторррская</span>
        </div>
        <div class="logout__block">
            <a class="logout__link" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                {{ __('Выйти') }}
            </a>
            <form id="logout-form" class="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
            </form>
        </div>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main class="conf-steps">
        <section class="conf-step">
            <header class="conf-step__header conf-step__header_opened">
                <h2 class="conf-step__title">Управление залами</h2>
            </header>
            <div class="conf-step__wrapper">
                <p class="conf-step__paragraph">Доступные залы:</p>
                <ul class="conf-step__list" style="display:flex; flex-direction:column;">
                    @foreach ($halls as $hall)
                        <li style="display:flex; align-content: center;">
                            <p data-id="{{ $hall->id }}">{{ $hall->name }} </p>
                            <form action="{{ route('hall.destroy', $hall->id) }}" method="post" accept-charset="utf-8" style="margin-left: 10px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="conf-step__button conf-step__button-trash trash_hall"></button>
                            </form>
                        </li>
                    @endforeach
                </ul>
                <button class="conf-step__button conf-step__button-accent"
                    onclick="togglePopup(document.getElementById('addHallPopup'))"> Создать зал</button>
            </div>
        </section>

        <section class="conf-step">
            @csrf
            <header class="conf-step__header conf-step__header_opened">
                <h2 class="conf-step__title">Конфигурация залов</h2>
            </header>
            <div class="conf-step__wrapper">
                <p class="conf-step__paragraph">Выберите зал для конфигурации:</p>
                <ul class="conf-step__selectors-box hall-configuration">
                    @foreach ($halls as $hall)
                        <li>
                            <input type="radio" class="conf-step__radio chooseToShow"
                                id="displaySeats_{{ $hall->id }}" data-hall-id="{{ $hall->id }}"
                                name="chairs-hall" value="{{ $hall->id }}">
                            <label class="conf-step__selector"
                                for="displaySeats_{{ $hall->id }}">{{ $hall->name }}</label>
                        </li>
                    @endforeach
                </ul>
                <div class="conf-step__legend rows-seats">

                </div>
                <div class="conf-step__legend">
                    <span class="conf-step__chair conf-step__chair_standart"></span> — обычные кресла
                    <span class="conf-step__chair conf-step__chair_vip"></span> — VIP кресла
                    <span class="conf-step__chair conf-step__chair_disabled"></span> — заблокированные (нет кресла)
                </div>
                <div class="conf-step__hall ">
                    <div class="conf-step__hall-wrapper wrapperToRender">

                    </div>
                </div>
                <fieldset form="seats_form" class="conf-step__buttons text-center">
                    <button class="conf-step__button conf-step__button-accent change_seats"
                        onclick="togglePopup(document.getElementById('hallConfigurationPopup'))">Изменить
                        конфигурацию</button>
                </fieldset>
            </div>
        </section>

        <section class="conf-step">
            <form id='price_form'>
                <header class="conf-step__header conf-step__header_opened">
                    <h2 class="conf-step__title">Конфигурация цен</h2>
                </header>
                <div class="conf-step__wrapper">
                    <p class="conf-step__paragraph">Выберите зал для конфигурации:</p>
                    <ul class="conf-step__selectors-box">
                        @foreach ($halls as $hall)
                            <li>
                                <input type="radio" class="conf-step__radio choosePrice"
                                    id="chairs-price_{{ $hall->id }}" name="chairs-price"
                                    data-hall-id="{{ $hall->id }}" value="{{ $hall->id }}">
                                <label for="chairs-price_{{ $hall->id }}"
                                    class="conf-step__selector">{{ $hall->name }}</label>
                            </li>
                        @endforeach
                    </ul>
                    <div class="conf-step__legend current-price">

                    </div>
                    <p class="conf-step__paragraph">Установите цены для типов кресел:</p>
                    <div class="conf-step__legend">
                        <label class="conf-step__label" for="standartPrice">Цена, рублей
                            <input type="text" class="conf-step__input" placeholder="0" id="standartPrice"
                                autocomplete="off">
                        </label>
                        за
                        <span class="conf-step__chair conf-step__chair_standart"></span>
                        обычные кресла
                    </div>
                    <div class="conf-step__legend">
                        <label class="conf-step__label" for="vipPrice">Цена, рублей
                            <input type="text" class="conf-step__input" placeholder="0" value=""
                                id="vipPrice" autocomplete="off">
                        </label>
                        за
                        <span class="conf-step__chair conf-step__chair_vip"></span>
                        VIP кресла
                    </div>
                    <fieldset form="hall_update" class="conf-step__buttons text-center">
                        <button type="reset"
                            class="abort conf-step__button conf-step__button-regular">Отмена</button>
                        <button type="submit" value="Сохранить" id="submitPriceButton"
                            class="conf-step__button conf-step__button-accent save-price">Сохранить</button>
                    </fieldset>
                </div>
            </form>
        </section>

        <section class="conf-step">
            <header class="conf-step__header conf-step__header_opened">
                <h2 class="conf-step__title">Сетка сеансов</h2>
            </header>
            <div class="conf-step__wrapper">
                <p class="conf-step__paragraph">
                    <button class="conf-step__button conf-step__button-accent" onclick="addMovie()">Добавить
                        фильм</button>
                </p>
                <div class="conf-step__movies">
                    @foreach ($movies as $movie)
                        <div class="conf-step__movie">
                            <form action="{{ route('movie.destroy', $movie->id) }}" method="post"
                                accept-charset="utf-8">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="conf-step__movie-delete"></button>
                            </form>
                            <img class="conf-step__movie-poster" alt="poster"
                                src={{ asset('images/admin/poster.png') }}>
                            <h3 class="conf-step__movie-title">{{ $movie->name }}</h3>
                            <p class="conf-step__movie-duration">{{ $movie->duration }} минут</p>
                        </div>
                    @endforeach
                </div>
                <div class="conf-step__seances">
                    <p class="conf-step__paragraph">
                        <button class="conf-step__button conf-step__button-accent" id="addshowforsure"
                            onclick="addShow()">Добавить
                            сеанс</button>
                    </p>
                    @foreach ($halls as $hall)
                        @php
                            $hasShows = false;
                        @endphp
                        @foreach ($groupedShows as $date => $showsOnDate)
                            @if (isset($showsOnDate[$hall->id]) && $showsOnDate[$hall->id]->isNotEmpty())
                                @php
                                    $hasShows = true;
                                @endphp
                            @endif
                        @endforeach
                        @if ($hasShows)
                            <div class="hall-timeline">
                                <h2 class="conf-step__seances-title">{{ $hall->name }}</h2>
                                @foreach ($groupedShows as $date => $showsOnDate)
                                    @if (isset($showsOnDate[$hall->id]) && $showsOnDate[$hall->id]->isNotEmpty())
                                        <h3 class="conf-step__movie-title">{{ $date }}</h3>
                                        <div class="conf-step__seances-timeline">
                                            @foreach ($showsOnDate[$hall->id] as $show)
                                                @php
                                                    // Convert start time to minutes
                                                    $startTime = explode(':', $show->start_time);
                                                    $startMinutes = $startTime[0] * 60 + $startTime[1];
                                                    // Calculate width and left position
                                                    $width = ($show->movie->duration / 1440) * 100;
                                                    $left = ($startMinutes / 1440) * 100;
                                                @endphp
                                                <div class="conf-step__movie conf-step__seances-movie"
                                                    style="width: {{ $width }}%; left: {{ $left }}%;">
                                                    <form action="{{ route('show.destroy', $show->id) }}"
                                                        method="post" accept-charset="utf-8">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="conf-step__show-delete">X</button>
                                                    </form>
                                                    <p class="conf-step__seances-movie-title">{{ $show->movie->name }}
                                                    </p>
                                                    <p class="conf-step__seances-movie-start">{{ $show->start_time }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>

        <section class="conf-step">
            <header class="conf-step__header conf-step__header_opened">
                <h2 class="conf-step__title">Открыть продажи</h2>
            </header>
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="conf-step__wrapper">
                <p class="conf-step__paragraph">Выберите зал:</p>
                <ul class="conf-step__selectors-box hall-configuration">
                    @foreach ($halls as $hall)
                        <li>
                            <input type="radio" class="conf-step__radio chooseToOpen" name="open-hall"
                                value="{{ $hall->id }}" id="open-hall_{{ $hall->id }}">
                            <label for="open-hall_{{ $hall->id }}"
                                class="conf-step__selector">{{ $hall->name }}</label>
                        </li>
                    @endforeach
                    <input type="hidden" name="selected_hall_id" value="">
                </ul>
            </div>
            <div class="conf-step__wrapper text-center message-container">
                <p class="conf-step__paragraph message" id="statusMessage"></p>
                <button class="conf-step__button conf-step__button-accent" id="open_sales"> Открыть продажу
                    билетов</button>
            </div>
        </section>
    </main>

    <script src="{{ asset('/js/admin/accordeon.js') }}" defer></script>
    <script src="{{ asset('/js/admin/priceUpdate.js') }}" defer></script>
    <script src="{{ asset('/js/admin/addMovie.js') }}" defer></script>
    <script src="{{ asset('/js/admin/addShow.js') }}" defer></script>
    <script src="{{ asset('/js/admin/togglePopup.js') }}" defer></script>
    <script src="{{ asset('/js/admin/getSeats.js') }}" defer></script>
    <script src="{{ asset('/js/admin/openHall.js') }}" defer></script>

</body>

</html>
