<x-layouts::app :title="__('Podcast Studio')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="rounded-3xl border border-cyan-200/70 bg-gradient-to-br from-cyan-50 to-white p-6 shadow-sm dark:border-cyan-400/20 dark:from-cyan-950/40 dark:to-zinc-900">
            <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
                <div>
                    <flux:badge color="cyan">{{ __('Podcast studio') }}</flux:badge>
                    <flux:heading size="xl" class="mt-4">{{ __('Broadcast command center') }}</flux:heading>
                    <flux:text class="mt-2 max-w-2xl">{{ __('Plan episodes, monitor your live room, and publish replays from one operational dashboard.') }}</flux:text>
                </div>
                <div class="flex flex-wrap gap-3">
                    <flux:button :href="route('home')" wire:navigate>{{ __('Public site') }}</flux:button>
                    <flux:button variant="primary" :href="route('podcasts.live')" wire:navigate>{{ __('Live room') }}</flux:button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-zinc-900">
                <flux:text>{{ __('Published episodes') }}</flux:text>
                <flux:heading size="xl" class="mt-2">{{ count(config('podcast.episodes')) }}</flux:heading>
            </div>
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-zinc-900">
                <flux:text>{{ __('Next broadcast') }}</flux:text>
                <flux:heading size="lg" class="mt-2">{{ config('podcast.live.next_show') }}</flux:heading>
            </div>
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-zinc-900">
                <flux:text>{{ __('Distribution') }}</flux:text>
                <flux:heading size="lg" class="mt-2">{{ __('RSS + live + on-demand') }}</flux:heading>
            </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Production queue') }}</flux:heading>
            <div class="mt-5 grid gap-3">
                @foreach (config('podcast.episodes') as $episode)
                    <div class="flex flex-col justify-between gap-3 rounded-xl border border-neutral-200 p-4 dark:border-neutral-700 sm:flex-row sm:items-center">
                        <div>
                            <flux:text class="text-sm">{{ __('Episode :number · :category', ['number' => $episode['number'], 'category' => $episode['category']]) }}</flux:text>
                            <flux:heading>{{ $episode['title'] }}</flux:heading>
                        </div>
                        <flux:button size="sm" :href="route('episodes.show', $episode['slug'])" wire:navigate>{{ __('Review') }}</flux:button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts::app>
