<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => 'Live broadcast'])
        <meta name="description" content="{{ config('podcast.live.description') }}">
    </head>
    <body class="min-h-screen bg-slate-950 text-white antialiased">
        <main class="mx-auto grid max-w-7xl gap-10 px-6 py-10 lg:grid-cols-[0.9fr_1.1fr] lg:px-8 lg:py-20">
            <section>
                <a href="{{ route('home') }}" class="text-sm font-semibold text-cyan-300 hover:text-cyan-200">← Back home</a>
                <p class="mt-10 inline-flex rounded-full bg-rose-500 px-4 py-2 text-sm font-bold uppercase tracking-wide">Live room</p>
                <h1 class="mt-5 text-5xl font-black tracking-tight sm:text-7xl">{{ config('podcast.live.title') }}</h1>
                <p class="mt-6 text-lg leading-8 text-slate-300">{{ config('podcast.live.description') }}</p>
                <div class="mt-8 rounded-3xl border border-cyan-300/20 bg-cyan-300/10 p-5">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-200">Next show</p>
                    <p class="mt-2 text-2xl font-bold">{{ config('podcast.live.next_show') }}</p>
                </div>
            </section>

            <section class="rounded-[2rem] border border-white/10 bg-white/[0.06] p-6 shadow-2xl shadow-slate-950/30">
                <div class="aspect-video rounded-[1.5rem] bg-slate-900 p-6">
                    <div class="flex h-full flex-col justify-between rounded-[1rem] border border-white/10 bg-[radial-gradient(circle_at_center,_rgba(34,211,238,0.22),_transparent_24rem)] p-6">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-rose-500 px-3 py-1 text-xs font-bold uppercase tracking-wide">Broadcasting</span>
                            <span class="text-sm text-slate-300">Stream URL: {{ config('podcast.live.stream_url') }}</span>
                        </div>
                        <div class="text-center">
                            <div class="mx-auto grid size-24 place-items-center rounded-full bg-cyan-400 text-4xl text-slate-950 shadow-xl shadow-cyan-400/30">▶</div>
                            <p class="mt-5 text-2xl font-bold">Audience player ready</p>
                            <p class="mt-2 text-sm text-slate-300">Connect your encoder to publish audio and video to the live room.</p>
                        </div>
                        <div class="grid gap-3 text-sm sm:grid-cols-3">
                            <span class="rounded-xl bg-white/10 p-3">Low-latency player</span>
                            <span class="rounded-xl bg-white/10 p-3">Moderated Q&A</span>
                            <span class="rounded-xl bg-white/10 p-3">Replay capture</span>
                        </div>
                    </div>
                </div>

                <form class="mt-6 grid gap-3 rounded-2xl bg-slate-950/60 p-5" method="GET" action="{{ route('podcasts.live') }}">
                    <label for="question" class="font-semibold">Send a listener question</label>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <input id="question" name="question" value="{{ request('question') }}" placeholder="Ask the host anything" class="min-w-0 flex-1 rounded-full border border-white/10 bg-white/10 px-5 py-3 text-sm text-white placeholder:text-slate-400 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40">
                        <button class="rounded-full bg-cyan-400 px-5 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-300">Submit</button>
                    </div>
                    @if (request()->filled('question'))
                        <p class="rounded-2xl bg-emerald-400/10 p-3 text-sm font-semibold text-emerald-200">Question queued for the producer: “{{ request('question') }}”</p>
                    @endif
                </form>
            </section>
        </main>
    </body>
</html>
