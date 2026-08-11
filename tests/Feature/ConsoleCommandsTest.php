<?php

use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    foreach ([
        base_path('.env.master.test'),
        base_path('.env.target.test'),
        base_path('VERSION.test'),
        base_path('version.test.json'),
        base_path('favicon-source.svg'),
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
        base_path('favicon-source.svg'),
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

test('favicon command generates a default svg favicon and manifest', function (): void {
    $this->artisan('app:make:favicon', [
        '--output' => 'favicon-test',
        '--png' => true,
        '--manifest' => true,
    ])->assertSuccessful();

    expect(File::exists(public_path('favicon-test/favicon.svg')))->toBeTrue()
        ->and(File::get(public_path('favicon-test/favicon.svg')))->toContain('<svg')
        ->and(File::json(public_path('favicon-test/site.webmanifest'))['icons'][0])
        ->toMatchArray([
            'src' => '/favicon-test/favicon.svg',
            'sizes' => 'any',
            'type' => 'image/svg+xml',
        ]);
});

test('favicon command copies an svg source from a relative path', function (): void {
    File::put(base_path('favicon-source.svg'), '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

    $this->artisan('app:favicon', [
        'source' => 'favicon-source.svg',
        '--output' => 'favicon-test',
    ])->assertSuccessful();

    expect(File::get(public_path('favicon-test/favicon.svg')))->toContain('http://www.w3.org/2000/svg');
});

test('favicon command fails when the source image is missing', function (): void {
    $this->artisan('app:favicon', [
        'source' => 'missing-favicon.svg',
        '--output' => 'favicon-test',
    ])->assertFailed();

    expect(File::exists(public_path('favicon-test/favicon.svg')))->toBeFalse();
});

test('favicon command requires at least one valid size', function (): void {
    $this->artisan('app:favicon', [
        '--output' => 'favicon-test',
        '--sizes' => '0,-4,2048',
    ])->assertFailed();

    expect(File::exists(public_path('favicon-test')))->toBeFalse();
});
