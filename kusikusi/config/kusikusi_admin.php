<?php
/*
 * This is general configuration needed for the CMS.
 *
 * WARNING !!!!!
 *
 * THIS INFORMATION IS FOR PUBLIC ACCESS!!! DO NOT SET HERE SENSITIVE INFORMATION LIKE API KEYS OR PASSWORDS
 *
 */

/*
 * Return the configuration structure
 * Icons are the name of the Material Design Icons https://material.io/tools/icons/?style=baseline
 */

return [
    "langs" => ["en"], // The first lang will be the default each time the entity is loaded
    "app_url" => env('APP_URL', 'http://127.0.0.1:8000'), // Very important to change this in your .env file or here. This will be used for canonical urls and social share assets
    "static_generation" => "lazy", // (TBD) lazy | eager | none Lazy: The entities wait to be called to get processed. Eager: Entities views get processed on save. None: No cache.
    "page_size" => 25, // Default page size if not defined in the call
    "token_expiration_in_seconds" => 0, // Seconds to the token to be expired or 0
    "short_id_length" => 10, // Change if you database is going to be veeeery big. Maximum 16.
    "title" => 'Kusikusi CMS',
    "copy_original_media_to_static" => true,
    "include_in_sitemap" => ['home', 'page', 'section'],
    "models" => [
        "home" => [
            "icon" => "home",
            "name" => "models.home",
            "views" => ["home", "home2"],
            "form" => [
                [
                    "label" => "contents.contents",
                    "components" => [
                        ["component" => "nq-input", "value" => "contents.title", "label" => "contents.title", "props" => ["size" => "xl"], "rules" => [["required"]]],
                        ["component" => "html-editor", "value" => "contents.welcome", "label" => "contents.description", "props" => []],
                        ["component" => "slug", "value" => "contents.slug", "label" => "contents.slug"]
                    ],
                ],
                [
                    "label" => "contents.children",
                    "components" => [
                        ["component" => "children", "props" => ["models" => ["section", "page"], "order_by" => "contents.title", "tags" => ["menu", "footer"]]]
                    ],
                ],
                [
                    "label" => "contents.media",
                    "components" => [
                        ["component" => "media", "props" => ["allowed" => [ "images" ], "tags" => ["hero", "social", "favicon", "logo"]]]
                    ],
                ]
            ]
        ],
        "section" => [
            "icon" => "folder",
            "name" => "models.section",
            "form" => [
                [
                    "label" => "contents.contents",
                    "components" => [
                        ["component" => "nq-input", "value" => "contents.title", "label" => "contents.title", "props" => ["size" => "xl"], "rules" => [["required"]]],
                        ["component" => "nq-input", "value" => "contents.description", "label" => "contents.description"],
                        ["component" => "slug", "value" => "contents.slug", "label" => "contents.slug"]
                    ],
                ],
                [
                    "label" => "contents.children",
                    "components" => [
                        ["component" => "children", "props" => ["models" => ["page"]]]
                    ],
                ]
            ]
        ],
        "page" => [
            "icon" => "description",
            "name" => "models.page",
            "form" => [
                [
                    "label" => "contents.contents",
                    "components" => [
                        ["component" => "nq-input", "value" => "contents.title", "label" => "contents.title", "props" => ["size" => "xl"], "rules" => [["required"]]],
                        ["component" => "nq-input", "value" => "contents.description", "label" => "contents.description"],
                        ["component" => "html-editor", "value" => "contents.body", "label" => "contents.body"],
                        ["component" => "slug", "value" => "contents.slug", "label" => "contents.slug"]
                    ],
                ],
                [
                    "label" => "contents.media",
                    "components" => [
                        ["component" => "media", "props" => ["allowed" => [ "webImages", "webVideos", 'xhr', 'jpg' ], "tags" => ["icon", "social", "gallery"]]]
                    ],
                ]
            ]
        ],
        "medium" => [
            "icon" => "insert_drive_file",
            "name" => "models.medium",
            "form" => [
                [
                    "label" => "contents.contents",
                    "components" => [
                        ["component" => "nq-input", "value" => "contents.title", "label" => "contents.title", "props" => ["size" => "xl"], "rules" => [["required"]]]
                    ],
                ]
            ]
        ],
        "website" => [
            "icon" => "language",
            "name" => "models.website",
            "views" => ["website"],
            "form" => [
                [
                    "label" => "contents.contents",
                    "components" => [
                        ["component" => "nq-input", "value" => "contents.title", "label" => "contents.title", "props" => ["size" => "xl"], "rules" => [["required"]]],
                        ["component" => "nq-input", "value" => "properties.theme_color", "label" => "Theme color"],
                        ["component" => "nq-input", "value" => "properties.background_color", "label" => "Background color"]
                    ],
                ],
                [
                    "label" => "contents.media",
                    "components" => [
                        ["component" => "media", "props" => ["allowed" => [ "images" ], "tags" => ["social", "favicon"]]]
                    ],
                ]
            ]
        ],
        "menus-container" => [
            "icon" => "list",
            "name" => "menus.title",
            "views" => ["menu-container"],
            "editable" => false,
            "form" => [
                [
                    "label" => "menus.title",
                    "components" => [
                        ["component" => "children", "props" => ["models" => ["menu"], "order_by" => "contents.title", "tags" => []]]
                    ]
                ]
            ]
        ],
        "menu" => [
            "icon" => "list",
            "name" => "menus.menu",
            "views" => ["menu"],
            "form" => [
                [
                    "label" => "contents.contents",
                    "components" => [
                        ["component" => "nq-input", "value" => "properties.title", "label" => "contents.name", "props" => ["size" => "xl"], "rules" => [["required"]]]
                    ],
                ],
                [
                    "label" => "menus.items",
                    "components" => [
                        ["component" => "relations", "props" => ['kind' => 'menu', 'from_entity_id' => 'home', 'list' => 'descendants', 'models' => ['medium'], 'tags' => ['emphasize']]]
                    ]
                ]
            ]
        ]
    ]
];
