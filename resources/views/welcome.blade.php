<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head', ['title' => config('podcast.name')])
        <meta name="description" content="{{ config('podcast.description') }}">
    </head>
    <body class="min-h-screen bg-slate-950 text-white antialiased">
        <div class="absolute inset-x-0 top-0 -z-10 h-96 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.35),_transparent_32rem),radial-gradient(circle_at_top_right,_rgba(168,85,247,0.28),_transparent_28rem)]"></div>

        <header class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold tracking-tight">
                <span class="grid size-11 place-items-center rounded-2xl bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-400/30">🎙️</span>
                <span>{{ config('podcast.name') }}</span>
            </a>

            <nav class="hidden items-center gap-8 text-sm font-medium text-slate-300 md:flex">
                <a href="#episodes" class="hover:text-white">Episodes</a>
                <a href="{{ route('podcasts.live') }}" class="hover:text-white">Live broadcast</a>
                <a href="{{ route('podcasts.feed') }}" class="hover:text-white">RSS feed</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('teams.index') }}" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950 shadow-sm hover:bg-cyan-100">Manage studio</a>
                @else
                    <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-slate-300 hover:text-white sm:inline">Log in</a>
                    @if ($canRegister)
                        <a href="{{ route('register') }}" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950 shadow-sm hover:bg-cyan-100">Start hosting</a>
                    @endif
                @endauth
            </div>
        </header>

        <main>
            <section class="mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-24">
                <div>
                    <p class="mb-5 inline-flex rounded-full border border-cyan-300/30 bg-cyan-300/10 px-4 py-2 text-sm font-medium text-cyan-200">{{ config('podcast.live.status') }}</p>
                    <h1 class="max-w-4xl text-5xl font-black tracking-tight text-white sm:text-7xl">Host polished podcasts and broadcast live shows from one studio.</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">{{ config('podcast.tagline') }}</p>
                    <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                        <a href="#episodes" class="rounded-full bg-cyan-400 px-6 py-3 text-center text-sm font-bold text-slate-950 shadow-xl shadow-cyan-400/20 hover:bg-cyan-300">Browse episodes</a>
                        <a href="{{ route('podcasts.live') }}" class="rounded-full border border-white/15 px-6 py-3 text-center text-sm font-bold text-white hover:bg-white/10">Join live room</a>
                    </div>
                </div>

                <aside class="rounded-[2rem] border border-white/10 bg-white/10 p-5 shadow-2xl shadow-cyan-950/40 backdrop-blur">
                    <div class="rounded-[1.5rem] bg-slate-950/90 p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-300">Now playing</p>
                                <h2 class="mt-3 text-2xl font-bold">{{ config('podcast.episodes.0.title') }}</h2>
                            </div>
                            <span class="rounded-full bg-rose-500 px-3 py-1 text-xs font-bold uppercase tracking-wide">On air</span>
                        </div>
                        <div class="mt-8 flex h-28 items-end gap-2" aria-hidden="true">
                            @foreach ([35, 72, 44, 90, 58, 76, 48, 88, 63, 100, 54, 81, 46, 69, 39, 74] as $barHeight)
                                <span class="flex-1 rounded-t-full bg-gradient-to-t from-cyan-500 to-fuchsia-400" style="height: {{ $barHeight }}%"></span>
                            @endforeach
                        </div>
                        <audio class="mt-8 w-full" controls preload="metadata" src="{{ config('podcast.episodes.0.audio_url') }}">
                            Your browser does not support the audio element.
                        </audio>
                        <dl class="mt-6 grid grid-cols-3 gap-3 text-center">
                            <div class="rounded-2xl bg-white/5 p-3">
                                <dt class="text-xs text-slate-400">Episodes</dt>
                                <dd class="text-xl font-bold">{{ count(config('podcast.episodes')) }}</dd>
                            </div>
                            <div class="rounded-2xl bg-white/5 p-3">
                                <dt class="text-xs text-slate-400">Formats</dt>
                                <dd class="text-xl font-bold">Live + VOD</dd>
                            </div>
                            <div class="rounded-2xl bg-white/5 p-3">
                                <dt class="text-xs text-slate-400">Host</dt>
                                <dd class="text-xl font-bold">{{ config('podcast.host') }}</dd>
                            </div>
                        </dl>
                    </div>
                </aside>
            </section>

            <section class="border-y border-white/10 bg-white/[0.03] py-10">
                <div class="mx-auto grid max-w-7xl gap-4 px-6 sm:grid-cols-3 lg:px-8">
                    @foreach (['Unlimited public episode pages', 'Embeddable HTML5 players', 'RSS feed for podcast apps'] as $feature)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-sm font-semibold text-slate-200">{{ $feature }}</div>
                    @endforeach
                </div>
            </section>

            <section id="episodes" class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-300">Episode library</p>
                        <h2 class="mt-3 text-4xl font-black tracking-tight">Latest broadcasts</h2>
                    </div>
                    <form action="{{ route('home') }}" method="GET" class="flex w-full gap-3 md:max-w-md">
                        <label for="search" class="sr-only">Search episodes</label>
                        <input id="search" name="search" value="{{ $search }}" placeholder="Search category, guest, or topic" class="min-w-0 flex-1 rounded-full border border-white/10 bg-white/10 px-5 py-3 text-sm text-white placeholder:text-slate-400 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40">
                        <button class="rounded-full bg-white px-5 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-100">Search</button>
                    </form>
                </div>

                <div class="mt-10 grid gap-6 lg:grid-cols-3">
                    @forelse ($episodes as $episode)
                        <article class="flex flex-col rounded-[1.75rem] border border-white/10 bg-white/[0.06] p-6 shadow-xl shadow-slate-950/20">
                            <div class="flex items-center justify-between gap-4 text-sm text-slate-400">
                                <span>Episode {{ $episode['number'] }}</span>
                                <span>{{ $episode['duration'] }}</span>
                            </div>
                            <h3 class="mt-4 text-2xl font-bold">{{ $episode['title'] }}</h3>
                            <p class="mt-3 flex-1 text-sm leading-6 text-slate-300">{{ $episode['summary'] }}</p>
                            <div class="mt-6 flex flex-wrap gap-2 text-xs font-semibold">
                                <span class="rounded-full bg-cyan-400/15 px-3 py-1 text-cyan-200">{{ $episode['category'] }}</span>
                                <span class="rounded-full bg-white/10 px-3 py-1 text-slate-200">Guest: {{ $episode['guest'] }}</span>
                            </div>
                            <audio class="mt-6 w-full" controls preload="metadata" src="{{ $episode['audio_url'] }}"></audio>
                            <a href="{{ route('episodes.show', $episode['slug']) }}" class="mt-6 rounded-full border border-white/15 px-4 py-2 text-center text-sm font-bold text-white hover:bg-white/10">Open episode page</a>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-10 text-center lg:col-span-3">
                            <h3 class="text-2xl font-bold">No episodes matched your search.</h3>
                            <p class="mt-2 text-slate-300">Try searching for strategy, growth, broadcasting, or a guest name.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </body>
</html>
