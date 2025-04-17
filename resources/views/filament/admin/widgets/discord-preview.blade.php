<x-filament-widgets::widget>
    @assets
    <link rel="stylesheet" href="{{ asset('/css/filament/admin/discord-preview.css') }}">
    @endassets
    <x-filament::section>
    <div class="container mx-auto p-4 w-full max-w-full">
        <div class="bg-gray-800 p-4 rounded-lg shadow-lg w-full max-w-full">
            <div class="flex items-start mb-4 sender">
                <div class="relative" style="width: 44px; min-width: 44px; height: 44px; margin-right: 12px;">
                    @if($avatar = data_get($sender, 'avatar'))
                        <img class="w-full h-full rounded-full object-cover absolute top-0 left-0 z-10 avatar"
                            src="{{ $avatar }}" 
                            alt="Avatar">
                    @endif
                    @if($decoration = data_get($sender, 'decoration'))
                        <img src="{{ $decoration }}"
                            class="decoration-overlay absolute decoration"
                            aria-hidden="true"
                            alt="Discord Decoration">
                    @endif
                </div>

                <!-- Name -->
                <div class="flex flex-col flex-grow">
                    <div class="flex items-center space-x-2">
                        <h1 class="font-bold text-white name">{{ data_get($sender, 'name') }}</h1>
                        @if(!data_get($sender, 'human'))
                            <span class="text-white text-xs rounded-md tag">app</span>
                        @endif
                        <span class="timestamp text-xs">{{ $getTime }}</span>
                    </div>

                    <!-- Message Content -->
                    @if(filled($content))
                        <p class="text-gray-300 break-words">{!! nl2br($content) !!}</p>
                    @endif

                    <!-- Embeds -->
                    @foreach($embeds as $embed)
                        @php
                            $name = data_get($embed, 'author.name');
                            $author_url = data_get($embed, 'author.url');
                            $author_icon_url = data_get($embed, 'author.icon_url');

                            $url = data_get($embed, 'url');

                            $title = data_get($embed, 'title');
                            $description = data_get($embed, 'description');

                            $fields = data_get($embed, 'fields');

                            $image = data_get($embed, 'image.url');
                            $thumbnail = data_get($embed, 'thumbnail.url');

                            $footer_icon_url = data_get($embed, 'footer.icon_url');
                            $footer_text = data_get($embed, 'footer.text');
                            $footer_timestamp = data_get($embed, 'timestamp');

                            $link = fn ($ref, $child) => $ref ? sprintf('<a href="%s" target="_blank" class="link">%s</a>', $ref, $child) : $child;
                        @endphp
                        <div class="p-3 mt-3 rounded-lg w-full max-w-full embed relative" style="border-color: #{{ dechex(data_get($embed, 'color')) }};">
                            @if($name || $author_icon_url || $thumbnail)
                                <div class="flex items-start mb-0 relative" style="height: auto; overflow: visible;">
                                    <div class="flex items-center">
                                        @if($author_icon_url)
                                            <img src="{{ $author_icon_url }}" alt="Author Avatar" class="w-8 h-8 rounded-full mr-2 object-cover avatar">
                                        @endif
                                        @if($name)
                                            {!! $link($author_url, <<<HTML
                                                <h2 class="font-bold text-lg whitespace-nowrap">$name</h2>
                                            HTML) !!}
                                        @endif
                                    </div>
                                    @if($thumbnail)
                                        <img src="{{ $thumbnail }}" alt="Embed Thumbnail" class="thumbnail rounded-lg" style="width: 64px; height: 64px; object-fit: cover; position: absolute; top: 50%; right: 0; transform: translateY(-15%);">
                                    @endif
                                </div>
                            @endif

                            @if($title)
                                <div style="overflow: visible; white-space: nowrap; width: 100%;">
                                    {!! $link($url, <<<HTML
                                        <h3 class="font-bold text-lg break-words mb-0" style="margin-bottom: 0;">$title</h3>
                                    HTML) !!}
                                </div>
                            @endif

                            @if($description)
                                <p class="break-words text-gray-300 mt-0" style="margin-top: 0;">{!! nl2br($description) !!}</p>
                            @endif

                            @if($fields)
                                @foreach($fields as $field)
                                    <div class="mt-2">
                                        <strong class="break-words">{{ data_get($field, 'name') }}</strong>
                                        <br />
                                        <span class="break-words">{{ data_get($field, 'value') }}</span>
                                    </div>
                                @endforeach
                            @endif

                            @if($image)
                                <img src="{{ $image }}" alt="Embed Image" class="object-contain mt-3 w-full">
                            @endif

                            @if($footer_text || $footer_timestamp)
                                <div class="flex items-center text-sm mt-4 mb-2">
                                    @if($footer_icon_url)
                                        <img src="{{ $footer_icon_url }}" alt="Footer Icon" class="w-5 h-5 rounded-full mr-2 object-cover">
                                    @endif
                                    @if($footer_text)
                                        <p class="break-words">{!! nl2br($footer_text) !!}</p>
                                    @endif
                                    @if($footer_timestamp)
                                        <span class="ml-2 text-gray-400">{{ $footer_timestamp }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    </x-filament::section>
</x-filament-widgets::widget>
