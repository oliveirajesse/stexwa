<?php
return [
    'id' => 'whatsapp',
    'name' => 'Whatsapp',
    'author' => 'Stackcode',
    'author_uri' => 'https://stackposts.com',
    'version' => '1.0',
    'desc' => '',
    'icon' => 'fab fa-whatsapp',
    'color' => '#25D366',
    'menu' => [
        'tab' => 2,
    	'position' => 10000,
    	'name' => 'Whatsapp',
    ],
    'pricing_menu' => [
        "title" => "Whatsapp features",
        "main_permission" => "whatsapp_enable",
        "sub_menu" => [
            [
                "text" => [
                    "Unlimited Messages",
                    "%s message/month"
                ],
                "check" => true,
                "permission" => "whatsapp_message_per_day",
            ],
            [
                "text" => "Chat",
                "permission" => "whatsapp_chat",
            ],
            [
                "text" => "Autoresponder",
                "permission" => "whatsapp_autoresponder",
            ],
            [
                "text" => "Bulk messaging",
                "permission" => "whatsapp_bulk",
            ],
            [
                "text" => "Chatbot",
                "permission" => "whatsapp_chatbot",
            ],
            [
                "text" => "Chat with media",
                "permission" => "whatsapp_chat_media",
            ],
            [
                "text" => "Autoresponder with media",
                "permission" => "whatsapp_autoresponder_media",
            ],
            [
                "text" => "Bulk messaging with media",
                "permission" => "whatsapp_bulk_media",
            ],
            [
                "text" => "Chatbot with media",
                "permission" => "whatsapp_chatbot_media",
            ]
        ]
    ],   
    'css' => [
         'assets/plugins/magicsuggest/magicsuggest-min.css',
         'assets/plugins/tagsinput/tagsinput.css',
         'assets/fonts/remixicon/remixicon.css',
         'assets/css/whatsapp.css'
    ],
    'js' => [
        'assets/plugins/socket.io/socket.io.min.js',
        'assets/plugins/magicsuggest/magicsuggest-min.js',
        'assets/plugins/tagsinput/tagsinput.js',
        'assets/js/whatsapp.js',
    ]
];