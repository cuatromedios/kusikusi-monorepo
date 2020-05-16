<?php

return [
    'presets' => [
        'preview' => [
            'quality' => 95,
            'width' => 1200,
            'height' =>  1200,
            'background' => 'crop',
            'alignment' => 'center',
            'scale' => 'cover',
            'format' => 'jpg',
            'effects' => []
        ],
        'thumb' => [
            'quality' => 80,
            'width' => 320,
            'height' =>  320,
            'background' => 'crop',
            'alignment' => 'center',
            'scale' => 'cover',
            'format' => 'jpg',
            'effects' => []
        ]
    ],
    'formats' => [
        'webImages' => ['jpeg', 'jpg', 'png', 'gif'],
        'images' => ['jpeg', 'jpg', 'png', 'gif', 'tif', 'tiff', 'iff', 'bmp', 'psd'],
        'audios' => ['mp3', 'wav', 'aiff', 'aac', 'oga', 'pcm', 'flac'],
        'webAudios' => ['mp3', 'oga'],
        'videos' => ['mov', 'mp4', 'qt', 'avi', 'mpe', 'mpeg', 'ogg', 'm4p', 'm4v', 'flv', 'wmv'],
        'webVideos' => ['webm', 'mp4', 'ogg', 'm4p', 'm4v'],
        'documents' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'htm', 'html', 'txt', 'rtf', 'csv', 'pps', 'ppsx', 'odf', 'key', 'pages', 'numbers'],
    ]
];
