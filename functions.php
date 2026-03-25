<?php
/**
 * Funções do tema Quimbanda-JP.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configurações iniciais do tema.
 */
function qjp_theme_setup()
{
    load_theme_textdomain('quimbanda-jp', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 80,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    register_nav_menus([
        'primary' => __('Menu Principal', 'quimbanda-jp'),
    ]);
}
add_action('after_setup_theme', 'qjp_theme_setup');

/**
 * Enfileira estilos do tema.
 */
function qjp_enqueue_assets()
{
    wp_enqueue_style('qjp-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'qjp_enqueue_assets');

/**
 * Define largura de conteúdo.
 */
function qjp_content_width()
{
    $GLOBALS['content_width'] = apply_filters('qjp_content_width', 1200);
}
add_action('after_setup_theme', 'qjp_content_width', 0);

/**
 * Registra área de widget para formulário de contato.
 */
function qjp_register_sidebars()
{
    register_sidebar([
        'name'          => __('Área de Contato (WPForms)', 'quimbanda-jp'),
        'id'            => 'qjp-contact-form',
        'description'   => __('Adicione aqui o widget/shortcode do formulário de contato.', 'quimbanda-jp'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'qjp_register_sidebars');

/**
 * Sanitiza texto de bio (máximo 500 caracteres).
 */
function qjp_sanitize_bio($value)
{
    $value = sanitize_textarea_field($value);
    if (mb_strlen($value) > 500) {
        $value = mb_substr($value, 0, 500);
    }

    return $value;
}

/**
 * Registra opções no Customizer.
 */
function qjp_customize_register($wp_customize)
{
    // Seção de cores.
    $wp_customize->add_section('qjp_colors_section', [
        'title'    => __('Quimbanda-JP: Cores', 'quimbanda-jp'),
        'priority' => 30,
    ]);

    $wp_customize->add_setting('qjp_background_color', [
        'default'           => '#121212',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'qjp_background_color_control', [
        'label'    => __('Cor de Fundo', 'quimbanda-jp'),
        'section'  => 'qjp_colors_section',
        'settings' => 'qjp_background_color',
    ]));

    $wp_customize->add_setting('qjp_text_color', [
        'default'           => '#E0E0E0',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'qjp_text_color_control', [
        'label'    => __('Cor de Texto', 'quimbanda-jp'),
        'section'  => 'qjp_colors_section',
        'settings' => 'qjp_text_color',
    ]));

    $wp_customize->add_setting('qjp_accent_color', [
        'default'           => '#8B0000',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'qjp_accent_color_control', [
        'label'    => __('Cor de Destaque', 'quimbanda-jp'),
        'section'  => 'qjp_colors_section',
        'settings' => 'qjp_accent_color',
    ]));

    // Seção de contato.
    $wp_customize->add_section('qjp_contact_section', [
        'title'    => __('Quimbanda-JP: Contato', 'quimbanda-jp'),
        'priority' => 31,
    ]);

    $wp_customize->add_setting('qjp_whatsapp_number', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('qjp_whatsapp_number_control', [
        'label'       => __('Número do WhatsApp (com DDI)', 'quimbanda-jp'),
        'description' => __('Exemplo: 5511999999999', 'quimbanda-jp'),
        'type'        => 'text',
        'section'     => 'qjp_contact_section',
        'settings'    => 'qjp_whatsapp_number',
    ]);

    // Seção do rodapé.
    $wp_customize->add_section('qjp_footer_section', [
        'title'    => __('Quimbanda-JP: Rodapé', 'quimbanda-jp'),
        'priority' => 32,
    ]);

    $wp_customize->add_setting('qjp_footer_bio', [
        'default'           => '',
        'sanitize_callback' => 'qjp_sanitize_bio',
    ]);

    $wp_customize->add_control('qjp_footer_bio_control', [
        'label'       => __('BIO (até 500 caracteres)', 'quimbanda-jp'),
        'type'        => 'textarea',
        'section'     => 'qjp_footer_section',
        'settings'    => 'qjp_footer_bio',
    ]);

    $wp_customize->add_setting('qjp_footer_address', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('qjp_footer_address_control', [
        'label'    => __('Endereço', 'quimbanda-jp'),
        'type'     => 'text',
        'section'  => 'qjp_footer_section',
        'settings' => 'qjp_footer_address',
    ]);

    $wp_customize->add_setting('qjp_footer_phone', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('qjp_footer_phone_control', [
        'label'    => __('Telefone', 'quimbanda-jp'),
        'type'     => 'text',
        'section'  => 'qjp_footer_section',
        'settings' => 'qjp_footer_phone',
    ]);

    $wp_customize->add_setting('qjp_footer_hours', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('qjp_footer_hours_control', [
        'label'    => __('Horário de Funcionamento', 'quimbanda-jp'),
        'type'     => 'text',
        'section'  => 'qjp_footer_section',
        'settings' => 'qjp_footer_hours',
    ]);
}
add_action('customize_register', 'qjp_customize_register');

/**
 * Injeta variáveis CSS com base no Customizer.
 */
function qjp_customizer_css_variables()
{
    $bg     = get_theme_mod('qjp_background_color', '#121212');
    $text   = get_theme_mod('qjp_text_color', '#E0E0E0');
    $accent = get_theme_mod('qjp_accent_color', '#8B0000');

    $css = ":root{--qjp-bg: {$bg}; --qjp-text: {$text}; --qjp-accent: {$accent};}";
    wp_add_inline_style('qjp-style', $css);
}
add_action('wp_enqueue_scripts', 'qjp_customizer_css_variables', 20);

/**
 * Retorna o link do WhatsApp formatado.
 */
function qjp_get_whatsapp_link()
{
    $number = get_theme_mod('qjp_whatsapp_number', '');
    $number = preg_replace('/\D+/', '', (string) $number);

    if (empty($number)) {
        return '';
    }

    return 'https://wa.me/' . $number;
}

/**
 * Adiciona item de WhatsApp ao menu principal quando preenchido.
 */
function qjp_add_whatsapp_to_menu($items, $args)
{
    if (!isset($args->theme_location) || 'primary' !== $args->theme_location) {
        return $items;
    }

    $wa_link = qjp_get_whatsapp_link();
    if (empty($wa_link)) {
        return $items;
    }

    $items .= '<li class="menu-item menu-item-whatsapp desktop-whatsapp"><a href="' . esc_url($wa_link) . '" target="_blank" rel="noopener noreferrer">WhatsApp</a></li>';

    return $items;
}
add_filter('wp_nav_menu_items', 'qjp_add_whatsapp_to_menu', 10, 2);

/**
 * Verifica se WPForms está ativo.
 */
function qjp_is_wpforms_active()
{
    return class_exists('WPForms') || function_exists('wpforms');
}
