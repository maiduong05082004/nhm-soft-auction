<?php

return [
    'direction' => 'ltr',
    'max_content_width' => '4xl', // Giảm độ rộng để giống Word hơn
    'disable_stylesheet' => false,
    'disable_link_as_button' => false,

    /*
    |--------------------------------------------------------------------------
    | Profiles - Cấu hình giống Microsoft Word
    |--------------------------------------------------------------------------
    */
    'profiles' => [
        'default' => [
            // Formatting group
            'bold', 'italic', 'underline', 'strike', 'superscript', 'subscript', '|',
            
            // Font styling
            'color', 'highlight', 'lead', 'small', '|',
            
            // Paragraph alignment
            'align-left', 'align-center', 'align-right', 'align-justify', '|',
            
            // Headers and structure
            'heading', 'blockquote', 'hr', '|',
            
            // Lists
            'bullet-list', 'ordered-list', 'checked-list', '|',
            
            // Insert elements
            'link', 'media', 'table', '|',
            
            // Advanced features
            'oembed', 'grid-builder', 'details', '|',
            
            // Code and source
            'code', 'code-block', 'blocks', 'source'
        ],
        
        // Profile đơn giản giống Word Online
        'word_simple' => [
            'bold', 'italic', 'underline', 'color', 'highlight', '|',
            'align-left', 'align-center', 'align-right', '|',
            'heading', 'bullet-list', 'ordered-list', '|',
            'link', 'media', 'table'
        ],
        
        // Profile tối thiểu
        'minimal' => ['bold', 'italic', 'underline', 'link', 'bullet-list', 'ordered-list'],
        'none' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */
    'media_action' => FilamentTiptapEditor\Actions\MediaAction::class,
    'edit_media_action' => FilamentTiptapEditor\Actions\EditMediaAction::class,
    'link_action' => FilamentTiptapEditor\Actions\LinkAction::class,
    'grid_builder_action' => FilamentTiptapEditor\Actions\GridBuilderAction::class,
    'oembed_action' => FilamentTiptapEditor\Actions\OEmbedAction::class,

    /*
    |--------------------------------------------------------------------------
    | Output format
    |--------------------------------------------------------------------------
    */
    'output' => FilamentTiptapEditor\Enums\TiptapOutput::Html,

    /*
    |--------------------------------------------------------------------------
    | Media Uploader - Cấu hình upload file
    |--------------------------------------------------------------------------
    */
    'accepted_file_types' => [
        // Images
        'image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/gif',
        // Documents (giống Word)
        'application/pdf', 
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ],
    'disk' => 'public',
    'directory' => 'uploads/editor',
    'visibility' => 'public',
    'preserve_file_names' => true, // Giữ tên file gốc
    'max_file_size' => 10240, // 10MB
    'min_file_size' => 0,
    'image_resize_mode' => 'force',
    'image_crop_aspect_ratio' => null,
    'image_resize_target_width' => 800,
    'image_resize_target_height' => 600,
    'use_relative_paths' => true,

    /*
    |--------------------------------------------------------------------------
    | Menus - Cấu hình menu giống Word
    |--------------------------------------------------------------------------
    */
    'disable_floating_menus' => false,
    'disable_bubble_menus' => false,
    'disable_toolbar_menus' => false,

    // Menu bubble khi select text (giống Word)
    'bubble_menu_tools' => [
        'bold', 'italic', 'underline', 'strike', 
        'superscript', 'subscript', 'color', 'highlight', 'link'
    ],
    
    // Menu floating khi cursor ở dòng trống
    'floating_menu_tools' => [
        'heading', 'bullet-list', 'ordered-list', 'blockquote',
        'media', 'table', 'hr', 'code-block'
    ],

    /*
    |--------------------------------------------------------------------------
    | Extensions
    |--------------------------------------------------------------------------
    */
    'extensions_script' => null,
    'extensions_styles' => null,
    'extensions' => [],

    /*
    |--------------------------------------------------------------------------
    | Preset Colors - Màu sắc giống Word
    |--------------------------------------------------------------------------
    */
    'preset_colors' => [
        // Standard colors
        'black' => '#000000',
        'red' => '#FF0000',
        'green' => '#008000',
        'blue' => '#0000FF',
        'yellow' => '#FFFF00',
        'cyan' => '#00FFFF',
        'magenta' => '#FF00FF',
        'white' => '#FFFFFF',
        
        // Theme colors
        'dark_blue' => '#1F4E79',
        'light_blue' => '#4F81BD',
        'dark_red' => '#953734',
        'light_red' => '#D99694',
        'dark_green' => '#548235',
        'light_green' => '#A9D18E',
        'dark_yellow' => '#BF8F00',
        'light_yellow' => '#FFD966',
        'orange' => '#C65911',
        'light_orange' => '#F4B183',
        'purple' => '#7030A0',
        'light_purple' => '#B19CD9',
    ],

    /*
    |--------------------------------------------------------------------------
    | Protocols
    |--------------------------------------------------------------------------
    */
    'link_protocols' => [
        'http', 'https', 'ftp', 'mailto', 'tel', 'sms'
    ],
];