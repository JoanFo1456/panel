<?php

use App\Enums\EggFormat;
use App\Extensions\Features\FeatureService;
use App\Services\Eggs\Sharing\EggExporterService;
use App\Services\Eggs\Sharing\EggImporterService;

it('registers the windows feature', function () {
    $feature = $this->app->make(FeatureService::class)->get('windows');

    expect($feature->getId())->toBe('windows')
        ->and($feature->getListeners())->toBe([]);
});

it('recognises windows eggs by their feature or install script entrypoint', function (array $attributes, bool $windows) {
    expect(getBungeecordEgg()->forceFill($attributes)->isWindowsEgg())->toBe($windows);
})->with([
    'linux egg' => [['features' => ['eula'], 'script_entry' => 'bash'], false],
    'windows feature' => [['features' => ['eula', 'windows'], 'script_entry' => 'bash'], true],
    'powershell.exe' => [['features' => [], 'script_entry' => 'powershell.exe'], true],
    'pwsh.exe' => [['features' => [], 'script_entry' => 'pwsh.exe'], true],
    'powershell path' => [['features' => [], 'script_entry' => 'C:\Windows\System32\WindowsPowerShell\v1.0\POWERSHELL.EXE'], true],
    'pwsh path' => [['features' => [], 'script_entry' => '/usr/bin/pwsh'], true],
]);

it('keeps the windows feature and PowerShell install script through an egg export', function (EggFormat $format) {
    $egg = cloneEggAndVariables(getBungeecordEgg());
    $egg->update([
        'features' => ['eula', 'windows'],
        'script_install' => 'Write-Output "main"',
        'script_container' => 'mcr.microsoft.com/powershell:nanoserver-ltsc2022',
        'script_entry' => 'pwsh.exe',
    ]);

    $exported = $this->app->make(EggExporterService::class)->handle($egg->id, $format);

    // Importing the export of an existing uuid updates that egg, so remove it first.
    $egg->delete();
    $imported = $this->app->make(EggImporterService::class)->fromContent($exported, $format);

    expect($imported->features)->toBe(['eula', 'windows'])
        ->and($imported->script_entry)->toBe('pwsh.exe')
        ->and($imported->script_container)->toBe('mcr.microsoft.com/powershell:nanoserver-ltsc2022')
        ->and($imported->isWindowsEgg())->toBeTrue();
})->with([EggFormat::JSON, EggFormat::YAML]);
