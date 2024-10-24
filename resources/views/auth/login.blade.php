<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Авторизация | ИдёмВКино</title>
    <link rel="stylesheet" href="{{ asset('css/admin/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/normalize.css') }}">
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&amp;subset=cyrillic,cyrillic-ext,latin-ext"
        rel="stylesheet">
</head>

<body>

    <header class="page-header">
        <h1 class="page-header__title">Идём<span>в</span>кино</h1>
        <span class="page-header__subtitle">Администраторррская</span>
    </header>

    <main>
        <section class="login">
            <header class="login__header">
                <h2 class="login__title">Авторизация</h2>
            </header>
            <div class="login__wrapper">
                <form class="login__form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <label class="login__label" for="email">
                        E-mail
                        <input id="email" type="email" class="login__input" name="email" id="email"
                            value="{{ old('email') }}" placeholder="example@domain.xyz" required autocomplete="email"
                            autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </label>
                    <label class="login__label" for="password">
                        Пароль
                        <input id="password" type="password" class="login__input" name="password" required
                            autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </label>
                    <div class="text-center">
                        <button value="Авторизоваться" type="submit" class="login__button">Авторизоваться</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>

</html>
