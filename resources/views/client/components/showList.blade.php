@if (isset($carbonDate))
    <h2>Расписание сеансов на {{ $carbonDate->format('d-m-Y') }}</h2>
@else
    <h2>Showtimes</h2>
@endif

@if ($shows->isEmpty())
    <h3>В этот день сеансов нет </h3>
@else
    @foreach ($shows as $movieShows)
        @php
            $movie = $movieShows->first()->movie;
        @endphp
        <section class="movie">
            <div class="movie__info">
                <div class="movie__poster">
                    <img class="movie__poster-image" alt="{{ $movie->name }} постер"
                        src="{{ $movie->poster ? asset($movie->poster) : asset('images/client/poster1.jpg') }}">
                </div>
                <div class="movie__description">
                    <h2 class="movie__title">{{ $movie->name }}</h2>
                    <p class="movie__synopsis">{{ $movie->description }}</p>
                    <p class="movie__data">
                        <span class="movie__data-duration">{{ $movie->duration }} минут</span>
                        <span class="movie__data-origin">{{ $movie->country }}</span>
                    </p>
                </div>
            </div>
            @foreach ($movieShows->groupBy('hall_id') as $hallId => $hallShows)
                <div class="movie-seances__hall">
                    <h3 class="movie-seances__hall-title">{{ $hallShows->first()->hall->name }}</h3>
                    <ul class="movie-seances__list">
                        @foreach ($hallShows as $show)
                            <li class="movie-seances__time-block">
                                <a class="movie-seances__time"
                                    href="{{ route('client.hall', ['show' => $show->id]) }}">{{ $show->start_time }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </section>
    @endforeach
@endif
