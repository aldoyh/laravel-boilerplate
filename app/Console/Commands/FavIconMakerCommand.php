<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FavIconMakerCommand extends Command
{
    protected $signature = 'app:favicon
                            {source? : Optional source image path. SVG files are copied; raster files require the GD extension for PNG output.}
                            {--output=favicon : Directory under public/ where generated assets are written}
                            {--sizes=16,32,180,192,512 : Comma-separated PNG sizes to generate when GD is available}
                            {--manifest : Generate a web manifest alongside the favicon assets}
                            {--dry-run : Show planned files without writing them}
                            {--png : Legacy compatibility option; PNG files are generated automatically for raster sources}';

    protected $aliases = [
        'app:favicon:make',
        'app:make:favicon',
        'app:auto-create-fav-icon-command',
    ];

    protected $description = 'Generate boilerplate favicon assets from a source image or a default SVG.';

    public function handle(): int
    {
        $outputPath = trim((string) $this->option('output'), '/');

        if ($outputPath === '') {
            $this->error('The output directory cannot be empty.');

            return self::FAILURE;
        }

        $outputDirectory = public_path($outputPath);
        $source = $this->resolveSource($this->argument('source'));

        try {
            $sizes = $this->sizes();
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        if (filled($this->argument('source')) && $source === null) {
            $this->error('Source image not found: '.$this->argument('source'));

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->line('DRY RUN - no files will be written.');
            $this->displayPlan($outputDirectory, $source, $sizes);

            return self::SUCCESS;
        }

        File::ensureDirectoryExists($outputDirectory);

        if ($source === null) {
            $this->writeDefaultSvg($outputDirectory);
            $this->info('Generated default SVG favicon.');
        } elseif ($this->isSvg($source)) {
            File::copy($source, $outputDirectory.'/favicon.svg');
            $this->info('Copied SVG favicon source.');
        } else {
            if (! extension_loaded('gd')) {
                $this->error('The GD extension is required to generate PNG favicons from raster images.');

                return self::FAILURE;
            }

            $image = $this->loadRasterImage($source);

            if ($image === null) {
                $this->error('Unsupported or unreadable image source: '.$source);

                return self::FAILURE;
            }

            foreach ($sizes as $size) {
                if (! $this->writePng($image, $size, $outputDirectory."/favicon-{$size}x{$size}.png")) {
                    imagedestroy($image);
                    $this->error("Unable to write favicon-{$size}x{$size}.png.");

                    return self::FAILURE;
                }
            }

            imagedestroy($image);
            $this->info('Generated PNG favicon assets.');
        }

        if ($this->option('manifest')) {
            $this->writeManifest($outputDirectory, $outputPath, $source, $sizes);
            $this->info('Generated web manifest.');
        }

        $this->line('Favicon assets are available in public/'.$outputPath.'.');

        return self::SUCCESS;
    }

    private function resolveSource(?string $source): ?string
    {
        if (filled($source)) {
            $path = Str::startsWith($source, DIRECTORY_SEPARATOR) ? $source : base_path($source);

            return File::exists($path) ? $path : null;
        }

        foreach (['logo.svg', 'logo.png', 'logo.jpg', 'logo.jpeg', 'icon.svg', 'icon.png'] as $candidate) {
            $path = public_path($candidate);

            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * @return array<int, int>
     */
    private function sizes(): array
    {
        return collect(explode(',', (string) $this->option('sizes')))
            ->map(fn (string $size): int => (int) trim($size))
            ->filter(fn (int $size): bool => $size > 0 && $size <= 1024)
            ->unique()
            ->values()
            ->whenEmpty(fn (): never => throw new InvalidArgumentException('Provide at least one valid favicon size between 1 and 1024 pixels.'))
            ->all();
    }

    /**
     * @param  array<int, int>  $sizes
     */
    private function displayPlan(string $outputDirectory, ?string $source, array $sizes): void
    {
        $this->line('Source: '.($source ?? 'default generated SVG'));
        $this->line('Output: '.$outputDirectory);

        if ($source === null || $this->isSvg($source)) {
            $this->line('Would write: favicon.svg');
        } else {
            foreach ($sizes as $size) {
                $this->line("Would write: favicon-{$size}x{$size}.png");
            }
        }

        if ($this->option('manifest')) {
            $this->line('Would write: site.webmanifest');
        }
    }

    private function isSvg(string $path): bool
    {
        return Str::lower(pathinfo($path, PATHINFO_EXTENSION)) === 'svg';
    }

    private function writeDefaultSvg(string $outputDirectory): void
    {
        $appName = (string) config('app.name', 'Laravel');
        $initials = Str::of($appName)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');

        $label = e($initials !== '' ? $initials : 'L');

        File::put($outputDirectory.'/favicon.svg', <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" role="img" aria-label="{$label}">
    <rect width="64" height="64" rx="14" fill="#f97316"/>
    <text x="50%" y="54%" text-anchor="middle" dominant-baseline="middle" font-family="Arial, sans-serif" font-size="28" font-weight="700" fill="#ffffff">{$label}</text>
</svg>
SVG);
    }

    /**
     * @return resource|null
     */
    private function loadRasterImage(string $source): mixed
    {
        $extension = Str::lower(pathinfo($source, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($source) ?: null,
            'png' => imagecreatefrompng($source) ?: null,
            'webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($source) ?: null : null,
            default => null,
        };
    }

    /**
     * @param  resource  $image
     */
    private function writePng(mixed $image, int $size, string $path): bool
    {
        $canvas = imagecreatetruecolor($size, $size);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, $size, $size, $transparent);

        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);
        $scale = min($size / $sourceWidth, $size / $sourceHeight);
        $targetWidth = (int) round($sourceWidth * $scale);
        $targetHeight = (int) round($sourceHeight * $scale);
        $targetX = (int) floor(($size - $targetWidth) / 2);
        $targetY = (int) floor(($size - $targetHeight) / 2);

        imagecopyresampled($canvas, $image, $targetX, $targetY, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);
        $written = imagepng($canvas, $path);
        imagedestroy($canvas);

        return $written;
    }

    /**
     * @param  array<int, int>  $sizes
     */
    private function writeManifest(string $outputDirectory, string $outputPath, ?string $source, array $sizes): void
    {
        $icons = $source === null || $this->isSvg($source)
            ? [
                ['src' => "/{$outputPath}/favicon.svg", 'sizes' => 'any', 'type' => 'image/svg+xml'],
            ]
            : collect($sizes)
                ->map(fn (int $size): array => [
                    'src' => "/{$outputPath}/favicon-{$size}x{$size}.png",
                    'sizes' => "{$size}x{$size}",
                    'type' => 'image/png',
                ])
                ->values()
                ->all();

        $manifest = [
            'name' => config('app.name', 'Laravel'),
            'short_name' => config('app.name', 'Laravel'),
            'icons' => $icons,
            'theme_color' => '#ffffff',
            'background_color' => '#ffffff',
            'display' => 'standalone',
        ];

        File::put($outputDirectory.'/site.webmanifest', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
    }
}
