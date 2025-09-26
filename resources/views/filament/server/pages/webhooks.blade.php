<x-filament-panels::page>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Server Webhooks</h2>
            <p class="text-gray-600 mb-4">
                Configure webhooks to receive notifications when server events occur. 
                You can listen to events like file changes, server power actions, and more.
            </p>
            
            <div class="flex gap-4">
                <a href="{{ \App\Filament\Server\Resources\ServerWebhooks\ServerWebhookResource::getUrl('index', panel: 'server') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                    Manage Webhooks
                </a>
                <a href="{{ \App\Filament\Server\Resources\ServerWebhooks\ServerWebhookResource::getUrl('create', panel: 'server') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                    Create Webhook
                </a>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-blue-800 font-semibold mb-2">Available Server Events</h3>
            <div class="grid grid-cols-2 gap-2 text-sm text-blue-700">
                <div>
                    <strong>File Operations:</strong>
                    <ul class="ml-4 mt-1">
                        <li>• File Write</li>
                        <li>• File Read</li>
                        <li>• File Create</li>
                        <li>• File Delete</li>
                    </ul>
                </div>
                <div>
                    <strong>Power Actions:</strong>
                    <ul class="ml-4 mt-1">
                        <li>• Server Start</li>
                        <li>• Server Stop</li>
                        <li>• Server Restart</li>
                        <li>• Server Kill</li>
                    </ul>
                </div>
                <div>
                    <strong>Settings:</strong>
                    <ul class="ml-4 mt-1">
                        <li>• Server Rename</li>
                        <li>• Description Change</li>
                        <li>• Startup Edit</li>
                    </ul>
                </div>
                <div>
                    <strong>Resources:</strong>
                    <ul class="ml-4 mt-1">
                        <li>• Backup Create/Delete</li>
                        <li>• Database Create/Delete</li>
                        <li>• Allocation Changes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
