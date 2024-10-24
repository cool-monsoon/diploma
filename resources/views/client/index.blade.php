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

        <nav class="page-nav">
            <button id="back-button" class="page-nav__day page-nav__day_previous"></button>
            @foreach ($showtimePeriod as $day)
                <a class="page-nav__day {{ $day['class'] }}" href="#" data-date="{{ $day['date'] }}">
                    <span class="page-nav__day-week">{{ $day['weekday'] }}</span>
                    <span class="page-nav__day-number">{{ $day['mday'] }}</span>
                </a>
            @endforeach
            <button id="forward-button" class="page-nav__day page-nav__day_next"></button>
        </nav>

    <main id="content-area">
        @include('client.components.showList');
    </main>

</body>

<script src="{{ asset('/js/client/nav.js') }}"></script>
