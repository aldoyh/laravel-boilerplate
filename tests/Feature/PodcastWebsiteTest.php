<?php

use Illuminate\Support\Facades\Config;

it('renders the podcast hosting home page with playable episodes', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Host polished podcasts and broadcast live shows')
        ->assertSee('Building an Independent Audio Brand')
        ->assertSee('The Live Show Production Stack')
        ->assertSee('audio', false)
        ->assertSee(route('podcasts.feed'));
});

it('filters episodes by query string', function () {
    $this->get(route('home', ['search' => 'growth']))
        ->assertOk()
        ->assertSee('Turning Listeners Into Members')
        ->assertDontSee('The Live Show Production Stack');
});

it('renders a dedicated episode page with chapters and related episodes', function () {
    $episode = Config::get('podcast.episodes.0');

    $this->get(route('episodes.show', $episode['slug']))
        ->assertOk()
        ->assertSee($episode['title'])
        ->assertSee('Chapter markers')
        ->assertSee($episode['chapters'][0])
        ->assertSee('Keep listening');
});

it('renders the live broadcast room and queues listener questions', function () {
    $this->get(route('podcasts.live', ['question' => 'How do I sponsor the show?']))
        ->assertOk()
        ->assertSee('Live room')
        ->assertSee('Audience player ready')
        ->assertSee('Question queued for the producer')
        ->assertSee('How do I sponsor the show?');
});

it('publishes an rss feed for podcast apps', function () {
    $this->get(route('podcasts.feed'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/rss+xml')
        ->assertSee('<rss version="2.0">', false)
        ->assertSee('<enclosure', false)
        ->assertSee('Building an Independent Audio Brand');
});
