<?php

return [
    'langs' => ['en'],
    "static_generation" => "lazy", // (TBD) lazy | eager | none Lazy: The entities wait to be called to get processed. Eager: Entities views get processed on save. None: No cache.
    'static_storage' => [
        'drive' => 'public',
        'folder' => ''
    ]
];
