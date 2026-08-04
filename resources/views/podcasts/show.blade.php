<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $episode['title']])
        <meta name="description" content="{{ $episode['summary'] }}">
    </head>
    <body class="min-h-screen bg-slate-950 text-white antialiased">
        <main class="mx-auto max-w-5xl px-6 py-10 lg:px-8">
            <a href="{{ route('home') }}#episodes" class="text-sm font-semibold text-cyan-300 hover:text-cyan-200">← Back to episodes</a>

            <article class="mt-8 rounded-[2rem] border border-white/10 bg-white/[0.06] p-6 shadow-2xl shadow-slate-950/30 md:p-10">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-300">Episode {{ $episode['number'] }} · {{ $episode['category'] }}</p>
                <h1 class="mt-4 text-4xl font-black tracking-tight sm:text-6xl">{{ $episode['title'] }}</h1>
                <p class="mt-5 text-lg leading-8 text-slate-300">{{ $episode['description'] }}</p>

                <dl class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl bg-white/5 p-4">
                        <dt class="text-sm text-slate-400">Guest</dt>
                        <dd class="mt-1 font-bold">{{ $episode['guest'] }}</dd>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4">
                        <dt class="text-sm text-slate-400">Published</dt>
                        <dd class="mt-1 font-bold">{{ \Illuminate\Support\Carbon::parse($episode['published_at'])->format('F j, Y') }}</dd>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4">
                        <dt class="text-sm text-slate-400">Runtime</dt>
                        <dd class="mt-1 font-bold">{{ $episode['duration'] }}</dd>
                    </div>
                </dl>

                <audio class="mt-8 w-full" controls preload="metadata" src="{{ $episode['audio_url'] }}"></audio>

                <section class="mt-10">
                    <h2 class="text-2xl font-bold">Chapter markers</h2>
                    <ol class="mt-4 grid gap-3">
                        @foreach ($episode['chapters'] as $chapter)
                            <li class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">{{ $loop->iteration }}. {{ $chapter }}</li>
                        @endforeach
                    </ol>
                </section>
            </article>

            <section class="mt-12">
                <h2 class="text-2xl font-bold">Keep listening</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    @foreach ($relatedEpisodes as $relatedEpisode)
                        <a href="{{ route('episodes.show', $relatedEpisode['slug']) }}" class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 hover:bg-white/10">
                            <span class="text-sm text-cyan-300">Episode {{ $relatedEpisode['number'] }}</span>
                            <strong class="mt-2 block">{{ $relatedEpisode['title'] }}</strong>
                        </a>
                    @endforeach
                </div>
            </section>
        </main>
    </body>
</html>
