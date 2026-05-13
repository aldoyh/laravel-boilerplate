<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

class BumpVersion extends Command
{
    protected $signature = 'app:version
                            {action=show : Action to run: show, bump, or sync}
                            {type=build : Bump type for the bump action: major, minor, patch, or build}
                            {--file=VERSION : Plain-text version file path}
                            {--json=version.json : JSON metadata file path}
                            {--update-env : Update APP_VERSION and APP_BUILD in existing .env files}
                            {--dry-run : Show changes without writing files}
                            {--show : Legacy compatibility option for app:version:bump}';

    protected $aliases = [
        'app:version:bump',
        'app:version:sync',
        'app:version:sync-version',
    ];

    protected $description = 'Show, bump, and synchronize boilerplate application version files.';

    public function handle(): int
    {
        $action = (string) $this->argument('action');
        $type = (string) $this->argument('type');

        if ($this->option('show')) {
            $action = 'show';
        }

        if (in_array($action, ['major', 'minor', 'patch', 'build'], true)) {
            $type = $action;
            $action = 'bump';
        }

        $version = $this->readVersion($this->versionFilePath());

        try {
            $version = match ($action) {
                'show' => $version,
                'bump' => $this->bumpVersion($version, $type),
                'sync' => $version,
                default => throw new InvalidArgumentException('Supported actions are show, bump, and sync.'),
            };
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->displayVersion($version);

        if ($action === 'show') {
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN - no files were changed.');

            return self::SUCCESS;
        }

        $this->writeVersionFile($this->versionFilePath(), $version);
        $this->writeVersionJson($this->versionJsonPath(), $version);

        if ($this->option('update-env')) {
            $this->updateEnvironmentFiles($version);
        }

        $this->info('Version files synchronized.');

        return self::SUCCESS;
    }

    /**
     * @return array{major: int, minor: int, patch: int, build: int}
     */
    private function readVersion(string $path): array
    {
        if (! File::exists($path)) {
            return ['major' => 0, 'minor' => 1, 'patch' => 0, 'build' => 0];
        }

        $content = trim(File::get($path));

        if (! preg_match('/^(\d+)\.(\d+)\.(\d+)(?:-(\d+))?$/', $content, $matches)) {
            $this->warn('Invalid VERSION content detected; using 0.1.0-0.');

            return ['major' => 0, 'minor' => 1, 'patch' => 0, 'build' => 0];
        }

        return [
            'major' => (int) $matches[1],
            'minor' => (int) $matches[2],
            'patch' => (int) $matches[3],
            'build' => isset($matches[4]) ? (int) $matches[4] : 0,
        ];
    }

    /**
     * @param  array{major: int, minor: int, patch: int, build: int}  $version
     * @return array{major: int, minor: int, patch: int, build: int}
     */
    private function bumpVersion(array $version, string $type): array
    {
        return match ($type) {
            'major' => ['major' => $version['major'] + 1, 'minor' => 0, 'patch' => 0, 'build' => 0],
            'minor' => ['major' => $version['major'], 'minor' => $version['minor'] + 1, 'patch' => 0, 'build' => 0],
            'patch' => ['major' => $version['major'], 'minor' => $version['minor'], 'patch' => $version['patch'] + 1, 'build' => 0],
            'build' => ['major' => $version['major'], 'minor' => $version['minor'], 'patch' => $version['patch'], 'build' => $version['build'] + 1],
            default => throw new InvalidArgumentException('Supported bump types are major, minor, patch, and build.'),
        };
    }

    /**
     * @param  array{major: int, minor: int, patch: int, build: int}  $version
     */
    private function displayVersion(array $version): void
    {
        $this->table(['Field', 'Value'], [
            ['Version', $this->formatVersion($version)],
            ['Base Version', $this->formatBaseVersion($version)],
            ['Build', (string) $version['build']],
        ]);
    }

    /**
     * @param  array{major: int, minor: int, patch: int, build: int}  $version
     */
    private function writeVersionFile(string $path, array $version): void
    {
        File::put($path, $this->formatVersion($version).PHP_EOL);
    }

    /**
     * @param  array{major: int, minor: int, patch: int, build: int}  $version
     */
    private function writeVersionJson(string $path, array $version): void
    {
        $payload = File::exists($path) ? json_decode(File::get($path), true) : [];
        $payload = is_array($payload) ? $payload : [];
        $payload['version'] = $this->formatBaseVersion($version);
        $payload['build'] = $version['build'];
        $payload['updated_at'] = now()->toIso8601String();

        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
    }

    /**
     * @param  array{major: int, minor: int, patch: int, build: int}  $version
     */
    private function updateEnvironmentFiles(array $version): void
    {
        foreach (['.env', '.env.example'] as $file) {
            $path = base_path($file);

            if (! File::exists($path)) {
                continue;
            }

            $content = File::get($path);
            $content = $this->setEnvironmentValue($content, 'APP_VERSION', $this->formatBaseVersion($version));
            $content = $this->setEnvironmentValue($content, 'APP_BUILD', (string) $version['build']);

            File::put($path, $content);
        }
    }

    private function setEnvironmentValue(string $content, string $key, string $value): string
    {
        if (preg_match('/^'.preg_quote($key, '/').'=.*/m', $content)) {
            return preg_replace('/^'.preg_quote($key, '/').'=.*/m', $key.'='.$value, $content) ?? $content;
        }

        return rtrim($content).PHP_EOL.$key.'='.$value.PHP_EOL;
    }

    private function versionFilePath(): string
    {
        return base_path((string) $this->option('file'));
    }

    private function versionJsonPath(): string
    {
        return base_path((string) $this->option('json'));
    }

    /**
     * @param  array{major: int, minor: int, patch: int, build: int}  $version
     */
    private function formatVersion(array $version): string
    {
        return $this->formatBaseVersion($version).'-'.$version['build'];
    }

    /**
     * @param  array{major: int, minor: int, patch: int, build: int}  $version
     */
    private function formatBaseVersion(array $version): string
    {
        return $version['major'].'.'.$version['minor'].'.'.$version['patch'];
    }
}
