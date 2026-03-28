<?php
/**
 * Registro de padrões de bloco Gutenberg do tema.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registra categoria e padrões de bloco para páginas de Axé.
 */
function qjp_register_block_patterns()
{
    if (!function_exists('register_block_pattern') || !function_exists('register_block_pattern_category')) {
        return;
    }

    register_block_pattern_category('qjp-axe', [
        'label' => __('Quimbanda-JP Axé', 'quimbanda-jp'),
    ]);

    register_block_pattern(
        'quimbanda-jp/galeria-rituais',
        [
            'title'       => __('Galeria de Rituais e Entidades', 'quimbanda-jp'),
            'description' => __('Bloco pronto para exibir imagens de rituais, trabalhos e entidades.', 'quimbanda-jp'),
            'categories'  => ['qjp-axe'],
            'content'     => "<!-- wp:group {\"className\":\"qjp-axe-gallery\"} -->\n<div class=\"wp-block-group qjp-axe-gallery\">\n<!-- wp:heading {\"level\":2} -->\n<h2>Galeria de Rituais</h2>\n<!-- /wp:heading -->\n\n<!-- wp:gallery {\"linkTo\":\"media\",\"sizeSlug\":\"large\",\"columns\":3} -->\n<figure class=\"wp-block-gallery has-nested-images columns-3 is-cropped\">\n<!-- wp:image {\"sizeSlug\":\"large\"} -->\n<figure class=\"wp-block-image size-large\"><img alt=\"Trabalho espiritual\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:image {\"sizeSlug\":\"large\"} -->\n<figure class=\"wp-block-image size-large\"><img alt=\"Gira espiritual\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:image {\"sizeSlug\":\"large\"} -->\n<figure class=\"wp-block-image size-large\"><img alt=\"Entidades de luz\"/></figure>\n<!-- /wp:image -->\n</figure>\n<!-- /wp:gallery -->\n</div>\n<!-- /wp:group -->",
        ]
    );
}
add_action('init', 'qjp_register_block_patterns');
