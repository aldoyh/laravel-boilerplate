<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function (Request $request) {
    $episodes = collect(config('podcast.episodes'));
    $search = $request->string('search')->trim()->lower()->toString();

    if (filled($search)) {
        $episodes = $episodes->filter(function (array $episode) use ($search): bool {
            return str($episode['title'])->lower()->contains($search)
                || str($episode['summary'])->lower()->contains($search)
                || str($episode['category'])->lower()->contains($search)
                || str($episode['guest'])->lower()->contains($search);
        });
    }

    return view('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'episodes' => $episodes->values()->all(),
        'search' => $search,
    ]);
})->name('home');

Route::get('episodes/{episode}', function (string $episode) {
    $episodeDetails = collect(config('podcast.episodes'))->firstWhere('slug', $episode);

    abort_if($episodeDetails === null, 404);

    return view('podcasts.show', [
        'episode' => $episodeDetails,
        'relatedEpisodes' => collect(config('podcast.episodes'))
            ->reject(fn (array $podcastEpisode): bool => $podcastEpisode['slug'] === $episodeDetails['slug'])
            ->take(2)
            ->values()
            ->all(),
    ]);
})->name('episodes.show');

Route::view('live', 'podcasts.live')->name('podcasts.live');

Route::get('podcast.xml', function () {
    $episodes = collect(config('podcast.episodes'));

    return response()
        ->view('podcasts.feed', ['episodes' => $episodes->all()])
        ->header('Content-Type', 'application/rss+xml');
})->name('podcasts.feed');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
});

require __DIR__.'/settings.php';
