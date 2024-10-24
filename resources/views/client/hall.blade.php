<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ИдёмВКино</title>
    <link rel="stylesheet" href="{{ asset('css/client/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/normalize.css') }}">
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&amp;subset=cyrillic,cyrillic-ext,latin-ext"
        rel="stylesheet">
</head>

<body data-payment-url="{{ route('client.payment') }}">
    <header class="page-header">
        <h1 class="page-header__title">Идём<span>в</span>кино</h1>
    </header>

    <main>
        <section class="buying">
            <div class="buying__info">
                <div class="buying__info-description">
                    <h2 class="buying__info-title">{{ $show->movie->name }}</h2>
                    <p class="buying__info-start">Начало сеанса:
                        @if ($show->start_time)
                            {{ $show->start_time }}
                        @else
                            Время не указано
                        @endif
                    </p>
                    <p class="buying__info-hall">{{ $show->hall->name }}</p>
                </div>
            </div>
            <div class="buying-scheme">
                <div class="buying-scheme__wrapper" id="hallLayout" data-show-id="{{ $show->id }}">
                </div>
                <div class="buying-scheme__legend">
                    <div class="col">
                        <p class="buying-scheme__legend-price"><span
                                class="buying-scheme__chair buying-scheme__chair_standart"></span> Свободно (<span
                                class="buying-scheme__legend-value">{{ $show->price_standard }}</span>руб)</p>
                        <p class="buying-scheme__legend-price"><span
                                class="buying-scheme__chair buying-scheme__chair_vip"></span> Свободно VIP (<span
                                class="buying-scheme__legend-value">{{ $show->price_vip }}</span>руб)</p>
                    </div>
                    <div class="col">
                        <p class="buying-scheme__legend-price"><span
                                class="buying-scheme__chair buying-scheme__chair_taken"></span> Занято</p>
                        <p class="buying-scheme__legend-price"><span
                                class="buying-scheme__chair buying-scheme__chair_selected"></span> Выбрано</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('bookings.process') }}" method="POST" id="booking-form">
                @csrf
                <input type="hidden" name="show_id" value="{{ $show->id }}">
                <input type="hidden" name="seat_ids" id="selected-seats" value="">
                <button class="acceptin-button" type="submit" id="booking-button" disabled>Забронировать</button>
            </form>
        </section>
    </main>

</body>

    <script src="{{ asset('/js/client/hall.js') }}"></script>
    <script src="{{ asset('/js/client/showSeats.js') }}"></script>

</html>
