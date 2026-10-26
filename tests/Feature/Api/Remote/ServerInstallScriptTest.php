<?php

it('returns the install script of the egg regardless of a requested platform', function (string $query) {
    $server = createServerModel();
    $server->egg->update([
        'script_install' => 'Write-Output main',
        'script_container' => 'mcr.microsoft.com/powershell:nanoserver-ltsc2022',
        'script_entry' => 'pwsh.exe',
    ]);

    $node = $server->node;

    $this->withHeader('Authorization', "Bearer $node->daemon_token_id." . $node->daemon_token)
        ->getJson("/api/remote/servers/$server->uuid/install$query")
        ->assertOk()
        ->assertExactJson([
            'container_image' => 'mcr.microsoft.com/powershell:nanoserver-ltsc2022',
            'entrypoint' => 'pwsh.exe',
            'script' => 'Write-Output main',
        ]);
})->with(['no platform' => [''], 'linux' => ['?platform=linux'], 'windows' => ['?platform=windows']]);
