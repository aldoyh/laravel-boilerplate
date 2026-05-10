<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class SyncEnvironmentVariablesCommand extends Command
{
    protected $signature = 'env:sync
                            {--master=.env.example : Environment file that defines the expected keys}
                            {--target=* : Environment file to check or update; may be passed multiple times}
                            {--dry-run : Show changes without writing files}
                            {--validate-only : Only validate files and return a non-zero exit code when keys are missing}
                            {--fix : Legacy compatibility option; env:sync fixes missing keys by default}
                            {--report : Legacy compatibility option; reports are printed to the console}
                            {--select-master : Legacy compatibility option}
                            {--interactive : Legacy compatibility option}
                            {--skip-validation : Legacy compatibility option}
                            {--include-comments : Legacy compatibility option}
                            {--s|select : Legacy compatibility option}';

    protected $aliases = [
        'env:validate',
        'env:sync-vars',
        'app:env:sync',
        'app:env:syncc',
        'app:sync-env-command',
    ];

    protected $description = 'Validate and fill missing environment keys from a master .env file.';

    public function handle(): int
    {
        $masterPath = $this->environmentPath($this->masterOption());

        if (! File::exists($masterPath)) {
            $this->error('Master environment file was not found: '.$masterPath);

            return self::FAILURE;
        }

        $masterVariables = $this->parseEnvironmentFile($masterPath);
        $targets = $this->targetFiles($masterPath);

        if ($targets->isEmpty()) {
            $this->warn('No target environment files were found.');

            return self::SUCCESS;
        }

        $hasMissingKeys = false;

        foreach ($targets as $targetPath) {
            $targetVariables = $this->parseEnvironmentFile($targetPath);
            $missingVariables = array_diff_key($masterVariables, $targetVariables);

            $this->line(basename($targetPath).': '.count($missingVariables).' missing key(s)');

            if ($missingVariables === []) {
                continue;
            }

            $hasMissingKeys = true;

            foreach (array_keys($missingVariables) as $key) {
                $this->line('  - '.$key);
            }

            if (! $this->option('validate-only') && ! $this->option('dry-run')) {
                $this->appendMissingVariables($targetPath, $missingVariables);
                $this->info('  Updated '.basename($targetPath));
            }
        }

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN - no files were changed.');
        }

        if ($this->option('validate-only') && $hasMissingKeys) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function masterOption(): string
    {
        if ($this->option('select-master')) {
            return '.env.example';
        }

        return (string) $this->option('master');
    }

    /**
     * @return array<string, string>
     */
    private function parseEnvironmentFile(string $path): array
    {
        $variables = [];

        foreach (explode("\n", File::get($path)) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $variables[trim($key)] = trim($value);
        }

        return $variables;
    }

    /**
     * @return Collection<int, string>
     */
    private function targetFiles(string $masterPath): Collection
    {
        $targets = collect($this->option('target'))
            ->filter(fn (?string $target): bool => filled($target))
            ->map(fn (string $target): string => $this->environmentPath($target));

        if ($targets->isEmpty()) {
            $targets = collect([
                '.env',
                '.env.local',
                '.env.testing',
                '.env.development',
                '.env.staging',
                '.env.production',
            ])->map(fn (string $target): string => $this->environmentPath($target));
        }

        return $targets
            ->filter(fn (string $target): bool => $target !== $masterPath && File::exists($target))
            ->unique()
            ->values();
    }

    private function environmentPath(string $path): string
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);
    }

    /**
     * @param  array<string, string>  $missingVariables
     */
    private function appendMissingVariables(string $targetPath, array $missingVariables): void
    {
        $content = rtrim(File::get($targetPath));
        $lines = ['', '# Added from .env sync'];

        foreach ($missingVariables as $key => $value) {
            $lines[] = $key.'='.$value;
        }

        File::put($targetPath, $content.PHP_EOL.implode(PHP_EOL, $lines).PHP_EOL);
    }
}
