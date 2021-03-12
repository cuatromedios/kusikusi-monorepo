<?php

return [
  'presets' => [
    'icon' =>   [
      'quality' => 100,
      'width' => 240,
      'height' =>  240,
      'crop' => true,
      'alignment' => 'center',
      'scale' => 'cover', 
      'format' => 'png',
      'effects' => []
    ],
    'thumb' =>   [
      'quality' => 100,
      'width' => 480,
      'height' =>  480,
      'background' => 'transparent',
      'alignment' => 'center',
      'scale' => 'contain',
      'format' => 'png',
      'effects' => []
    ],
    'photo' =>   [
      'quality' => 95,
      'width' => 1200,
      'height' =>  1200,
      'crop' => true,
      'background' => '#ffffff',
      'alignment' => 'center',
      'scale' => 'cover',
      'format' => 'jpg',
      'effects' => []
    ]
],
  'formats' => [
    'webImages' => ['jpeg', 'jpg', 'png', 'gif', 'svg'],
    'webBitmapImages' => ['jpeg', 'jpg', 'png', 'gif'],
    'images' => ['jpeg', 'jpg', 'png', 'gif', 'tif', 'tiff', 'iff', 'bmp', 'psd', 'svg'],
    'audios' => ['mp3', 'wav', 'aiff', 'aac', 'oga', 'pcm', 'flac'],
    'webAudios' => ['mp3', 'oga'],
    'videos' => ['mov', 'mp4', 'qt', 'avi', 'mpe', 'mpeg', 'ogg', 'm4p', 'm4v', 'flv', 'wmv'],
    'webVideos' => ['webm', 'mp4', 'ogg', 'm4p', 'm4v'],
    'documents' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'htm', 'html', 'txt', 'rtf', 'csv', 'pps', 'ppsx', 'odf', 'key', 'pages', 'numbers'],
  ]
];
