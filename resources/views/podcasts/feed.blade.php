{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<rss version="2.0">
    <channel>
        <title>{{ config('podcast.name') }}</title>
        <link>{{ route('home') }}</link>
        <description>{{ config('podcast.description') }}</description>
        <language>en-us</language>
        <managingEditor>{{ config('podcast.email') }} ({{ config('podcast.host') }})</managingEditor>
        @foreach ($episodes as $episode)
            <item>
                <title>{{ $episode['title'] }}</title>
                <link>{{ route('episodes.show', $episode['slug']) }}</link>
                <guid>{{ route('episodes.show', $episode['slug']) }}</guid>
                <description>{{ $episode['summary'] }}</description>
                <pubDate>{{ \Illuminate\Support\Carbon::parse($episode['published_at'])->toRfc2822String() }}</pubDate>
                <enclosure url="{{ $episode['audio_url'] }}" length="0" type="audio/mpeg" />
            </item>
        @endforeach
    </channel>
</rss>
