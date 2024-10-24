<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ИдёмВКино - Подтверждение бронирования</title>
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
                <h2 class="ticket__check-title">Вы выбрали билеты:</h2>
            </header>

            <div class="ticket__info-wrapper">
                @php
                    $totalCost = 0;
                    $firstBooking = $bookings->first();
                @endphp
                <p class="ticket__info">На фильм:
                    <span class="ticket__details ticket__title">{{ $firstBooking->show->movie->name }}</span>
                </p>
                @if ($firstBooking && $firstBooking->seat)
                    @php
                        $seatsByRow = $bookings->groupBy(function ($booking) {
                            return $booking->seat->row_name;
                        });
                    @endphp
                    @foreach ($seatsByRow as $row => $rowBookings)
                        <p class="ticket__info">Ряд:
                            <span class="ticket__details ticket__chairs">{{ $row }}</span>
                        </p>
                        <p class="ticket__info">Места:
                            <span class="ticket__details ticket__chairs">
                                @foreach ($rowBookings as $booking)
                                    {{ $booking->seat->seat_name }}@if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </span>
                        </p>
                    @endforeach
                @else
                    <p>Информация о местах недоступна</p>
                @endif
                <p class="ticket__info">В зале:
                    <span class="ticket__details ticket__hall">
                        @if ($show && $show->hall)
                            {{ $show->hall->name }}
                        @else
                            Зал не указан
                        @endif
                    </span>
                </p>
                <p class="ticket__info">Начало сеанса: <span
                        class="ticket__details ticket__start">{{ $firstBooking->show->start_time }}</span></p>
                @foreach ($bookings as $booking)
                    @php
                        if ($booking->seat && $booking->show && $booking->show->hall) {
                            $totalCost +=
                                $booking->seat->type === 'vip'
                                    ? $booking->show->hall->vip_seat_price
                                    : $booking->show->hall->standard_seat_price;
                        }
                    @endphp
                @endforeach
                <p class="ticket__info">Стоимость: <span class="ticket__details ticket__cost">{{ $totalCost }}</span>
                    рублей</p>
                <form id="confirmForm" action="{{ route('bookings.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="show_id" value="{{ $firstBooking->show_id }}">
                    <input type="hidden" name="seat_ids" value="{{ $bookings->pluck('seat_id')->implode(',') }}">
                    <input type="hidden" name="confirmed" value="1">
                    <button type="submit" class="acceptin-button"> Получить код бронирования</button>
                </form>
                <p class="ticket__hint">После оплаты билет будет доступен в этом окне, а также придёт вам на почту.
                    Покажите QR-код нашему контроллёру у входа в зал.</p>
                <p class="ticket__hint">Приятного просмотра!</p>
            </div>
        </section>
    </main>
</body>

</html>
