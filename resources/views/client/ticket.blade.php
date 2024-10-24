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

<body>
    <header class="page-header">
        <h1 class="page-header__title">Идём<span>в</span>кино</h1>
    </header>

    <main>
        <section class="ticket">
            <header class="tichet__check">
                <h2 class="ticket__check-title">Электронный билет</h2>
            </header>

            <div class="ticket__info-wrapper">
                <p class="ticket__info">На фильм: <span class="ticket__details ticket__title">{{ $movieTitle }}</span>
                </p>
                @if ($seatsByRow)
                    @foreach ($seatsByRow as $row => $seats)
                        <p class="ticket__info">Ряд:
                            <span class="ticket__details ticket__chairs">
                                {{ $row }}
                            </span>
                        </p>
                        <p class="ticket__info">Места:
                            <span class="ticket__details ticket__chairs">
                                @foreach ($seats as $index => $seat)
                                    {{ $seat['seat_name'] }}
                                    @if ($index < count($seats) - 1)
                                    @endif
                                @endforeach
                            </span>
                        </p>
                    @endforeach
                @else
                    <p>No seat information available.</p>
                @endif
                <p class="ticket__info">В зале: <span class="ticket__details ticket__hall">{{ $hall }}</span>
                </p>
                <p class="ticket__info">Начало сеанса: <span
                        class="ticket__details ticket__start">{{ $startTime }}</span></p>
                <img class="ticket__info-qr" src="{{ $qrCode }}" alt="Booking QR Code">
                <p class="ticket__hint">Покажите QR-код нашему контроллеру для подтверждения бронирования.</p>
                <p class="ticket__hint">Приятного просмотра!</p>
            </div>
        </section>
    </main>

</body>

</html>
