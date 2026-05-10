<?php

use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    foreach ([
        base_path('.env.master.test'),
        base_path('.env.target.test'),
        base_path('VERSION.test'),
        base_path('version.test.json'),
    ] as $path) {
        File::delete($path);
    }

    File::deleteDirectory(public_path('favicon-test'));
});

afterEach(function (): void {
    foreach ([
        base_path('.env.master.test'),
        base_path('.env.target.test'),
        base_path('VERSION.test'),
        base_path('version.test.json'),
    ] as $path) {
        File::delete($path);
    }

    File::deleteDirectory(public_path('favicon-test'));
});

test('environment sync appends missing variables from the master file', function (): void {
    File::put(base_path('.env.master.test'), "APP_NAME=Laravel\nAPP_ENV=local\nQUEUE_CONNECTION=database\n");
    File::put(base_path('.env.target.test'), "APP_NAME=Example\n");

    $this->artisan('env:validate', [
        '--master' => '.env.master.test',
        '--fix' => true,
        '--target' => ['.env.target.test'],
    ])->assertSuccessful();

    expect(File::get(base_path('.env.target.test')))
        ->toContain('APP_NAME=Example')
        ->toContain('APP_ENV=local')
        ->toContain('QUEUE_CONNECTION=database');
});

test('environment validation fails when target files are missing keys', function (): void {
    File::put(base_path('.env.master.test'), "APP_NAME=Laravel\nAPP_ENV=local\n");
    File::put(base_path('.env.target.test'), "APP_NAME=Example\n");

    $this->artisan('env:validate', [
        '--master' => '.env.master.test',
        '--fix' => true,
        '--target' => ['.env.target.test'],
        '--validate-only' => true,
    ])->assertFailed();
});

test('version command bumps and synchronizes version files', function (): void {
    File::put(base_path('VERSION.test'), '1.2.3-4');

    $this->artisan('app:version:bump', [
        'action' => 'patch',
        '--file' => 'VERSION.test',
        '--json' => 'version.test.json',
    ])->assertSuccessful();

    expect(trim(File::get(base_path('VERSION.test'))))->toBe('1.2.4-0')
        ->and(File::json(base_path('version.test.json')))
        ->toMatchArray([
            'version' => '1.2.4',
            'build' => 0,
        ]);
});

test('favicon command generates a default svg favicon', function (): void {
    $this->artisan('app:make:favicon', [
        '--output' => 'favicon-test',
        '--png' => true,
    ])->assertSuccessful();

    expect(File::exists(public_path('favicon-test/favicon.svg')))->toBeTrue()
        ->and(File::get(public_path('favicon-test/favicon.svg')))->toContain('<svg');
});
