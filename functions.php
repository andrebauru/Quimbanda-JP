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
        'height'      => 120,
        'width'       => 120,
        'flex-height' => true,
        'flex-width'  => true,
        'unlink-homepage-logo' => false,
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
 * Sanitiza texto de bio (máximo 500 caracteres).
 */
function qjp_sanitize_bio($value)
{
    $value = sanitize_textarea_field($value);

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($value) > 500) {
            $value = mb_substr($value, 0, 500);
        }
    } elseif (strlen($value) > 500) {
        $value = substr($value, 0, 500);
    }

    return $value;
}

/**
 * Sanitiza checkbox.
 */
function qjp_sanitize_checkbox($value)
{
    return (bool) $value;
}

/**
 * Sanitiza tipo de mídia de fundo.
 */
function qjp_sanitize_background_media_type($value)
{
    $allowed = ['none', 'image', 'video'];
    return in_array($value, $allowed, true) ? $value : 'none';
}

/**
 * Registra opções no Customizer.
 */
function qjp_customize_register($wp_customize)
{
    // Seção de background.
    $wp_customize->add_section('qjp_background_section', [
        'title'       => __('Quimbanda-JP: Background', 'quimbanda-jp'),
        'priority'    => 29,
        'description' => __('Selecione imagem ou vídeo para o fundo. Atenção: GIF e MP4 podem deixar o carregamento mais pesado.', 'quimbanda-jp'),
    ]);

    $wp_customize->add_setting('qjp_background_media_type', [
        'default'           => 'none',
        'sanitize_callback' => 'qjp_sanitize_background_media_type',
    ]);

    $wp_customize->add_control('qjp_background_media_type_control', [
        'label'       => __('Tipo de background', 'quimbanda-jp'),
        'description' => __('Use PNG para melhor performance. GIF e MP4 aumentam o peso da página.', 'quimbanda-jp'),
        'type'        => 'select',
        'choices'     => [
            'none'  => __('Nenhum (cor padrão)', 'quimbanda-jp'),
            'image' => __('Imagem (PNG/GIF)', 'quimbanda-jp'),
            'video' => __('Vídeo (MP4)', 'quimbanda-jp'),
        ],
        'section'     => 'qjp_background_section',
        'settings'    => 'qjp_background_media_type',
    ]);

    $wp_customize->add_setting('qjp_background_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'qjp_background_image_control', [
        'label'       => __('Imagem de fundo (PNG/GIF)', 'quimbanda-jp'),
        'description' => __('Você pode usar arquivos de exemplo em /Assets/background ou enviar sua própria imagem.', 'quimbanda-jp'),
        'section'     => 'qjp_background_section',
        'settings'    => 'qjp_background_image',
    ]));

    $wp_customize->add_setting('qjp_background_video', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'qjp_background_video_control', [
        'label'       => __('Vídeo de fundo (MP4)', 'quimbanda-jp'),
        'description' => __('O vídeo será reproduzido em loop. Recomendado usar arquivo curto e comprimido.', 'quimbanda-jp'),
        'section'     => 'qjp_background_section',
        'settings'    => 'qjp_background_video',
        'mime_type'   => 'video',
    ]));

    // Seção de cores.
    $wp_customize->add_section('qjp_colors_section', [
        'title'    => __('Quimbanda-JP: Cores', 'quimbanda-jp'),
        'priority' => 30,
    ]);
    // Cor dos blocos (cards, footer-blocks)
    $wp_customize->add_setting('qjp_block_color', [
        'default'           => '#1a1a1a',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'qjp_block_color_control', [
        'label'    => __('Cor dos Blocos', 'quimbanda-jp'),
        'section'  => 'qjp_colors_section',
        'settings' => 'qjp_block_color',
    ]));

    // Cor do texto dos blocos
    $wp_customize->add_setting('qjp_block_text_color', [
        'default'           => '#E0E0E0',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'qjp_block_text_color_control', [
        'label'    => __('Cor do Texto dos Blocos', 'quimbanda-jp'),
        'section'  => 'qjp_colors_section',
        'settings' => 'qjp_block_text_color',
    ]));

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

    $wp_customize->add_setting('qjp_text_outline_color', [
        'default'           => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'qjp_text_outline_color_control', [
        'label'    => __('Cor do Contorno do Texto', 'quimbanda-jp'),
        'section'  => 'qjp_colors_section',
        'settings' => 'qjp_text_outline_color',
    ]));

    $wp_customize->add_setting('qjp_text_outline_size', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('qjp_text_outline_size_control', [
        'label'       => __('Espessura do Contorno (px)', 'quimbanda-jp'),
        'description' => __('Use 0 para desativar.', 'quimbanda-jp'),
        'type'        => 'number',
        'input_attrs' => [
            'min'  => 0,
            'max'  => 4,
            'step' => 1,
        ],
        'section'     => 'qjp_colors_section',
        'settings'    => 'qjp_text_outline_size',
    ]);

    $wp_customize->add_setting('qjp_global_text_outline', [
        'default'           => false,
        'sanitize_callback' => 'qjp_sanitize_checkbox',
    ]);

    $wp_customize->add_control('qjp_global_text_outline_control', [
        'label'       => __('Aplicar contorno global nos textos', 'quimbanda-jp'),
        'description' => __('Ativa o contorno em títulos, links e textos do tema.', 'quimbanda-jp'),
        'type'        => 'checkbox',
        'section'     => 'qjp_colors_section',
        'settings'    => 'qjp_global_text_outline',
    ]);

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

    $block      = get_theme_mod('qjp_block_color', '#1a1a1a');
    $block_text = get_theme_mod('qjp_block_text_color', '#E0E0E0');
    $outline    = get_theme_mod('qjp_text_outline_color', '#000000');
    $outline_px = absint(get_theme_mod('qjp_text_outline_size', 0));
    $bg_type    = get_theme_mod('qjp_background_media_type', 'none');
    $bg_image   = get_theme_mod('qjp_background_image', '');

    $css = ":root{--qjp-bg: {$bg}; --qjp-text: {$text}; --qjp-accent: {$accent}; --qjp-block: {$block}; --qjp-block-text: {$block_text}; --qjp-text-outline-color: {$outline}; --qjp-text-outline-size: {$outline_px}px;}";

    if ('image' === $bg_type && !empty($bg_image)) {
        $css .= "body{background-image:url('" . esc_url_raw($bg_image) . "');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed;}";
    }

    wp_add_inline_style('qjp-style', $css);
}
add_action('wp_enqueue_scripts', 'qjp_customizer_css_variables', 20);

/**
 * Adiciona classe de contorno global no body.
 */
function qjp_body_classes($classes)
{
    if (get_theme_mod('qjp_global_text_outline', false)) {
        $classes[] = 'qjp-global-outline';
    }

    $bg_type = get_theme_mod('qjp_background_media_type', 'none');
    if ('video' === $bg_type) {
        $classes[] = 'qjp-has-bg-video';
    }

    return $classes;
}
add_filter('body_class', 'qjp_body_classes');

/**
 * Renderiza vídeo de background (MP4 em loop).
 */
function qjp_render_background_video()
{
    $bg_type  = get_theme_mod('qjp_background_media_type', 'none');
    $video_url = get_theme_mod('qjp_background_video', '');

    if ('video' !== $bg_type || empty($video_url)) {
        return;
    }

    $path = wp_parse_url($video_url, PHP_URL_PATH);
    $ext  = pathinfo((string) $path, PATHINFO_EXTENSION);
    if ('mp4' !== strtolower((string) $ext)) {
        return;
    }
    ?>
    <video class="qjp-bg-video" autoplay muted loop playsinline preload="metadata" aria-hidden="true">
        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
    </video>
    <div class="qjp-bg-overlay" aria-hidden="true"></div>
    <?php
}
add_action('wp_body_open', 'qjp_render_background_video', 5);

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
    if (!is_object($args) || empty($args->theme_location) || 'primary' !== $args->theme_location) {
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
 * =============================
 * Atualização segura via GitHub
 * =============================
 */

if (!defined('QJP_GITHUB_REPO')) {
    define('QJP_GITHUB_REPO', 'andrebauru/Quimbanda-JP');
}

if (!defined('QJP_GITHUB_API_LATEST')) {
    define('QJP_GITHUB_API_LATEST', 'https://api.github.com/repos/' . QJP_GITHUB_REPO . '/releases/latest');
}

if (!defined('QJP_GITHUB_API_TAGS')) {
    define('QJP_GITHUB_API_TAGS', 'https://api.github.com/repos/' . QJP_GITHUB_REPO . '/tags');
}

/**
 * Adiciona intervalo semanal se necessário.
 */
function qjp_add_weekly_cron_schedule($schedules)
{
    if (!isset($schedules['weekly'])) {
        $schedules['weekly'] = [
            'interval' => 7 * DAY_IN_SECONDS,
            'display'  => __('Uma vez por semana', 'quimbanda-jp'),
        ];
    }

    return $schedules;
}
add_filter('cron_schedules', 'qjp_add_weekly_cron_schedule');

/**
 * Registra agendamento semanal para update.
 */
function qjp_schedule_weekly_update_check()
{
    if (!wp_next_scheduled('qjp_weekly_update_check')) {
        wp_schedule_event(time() + HOUR_IN_SECONDS, 'weekly', 'qjp_weekly_update_check');
    }
}
add_action('after_switch_theme', 'qjp_schedule_weekly_update_check');
add_action('init', 'qjp_schedule_weekly_update_check');

/**
 * Remove agendamento ao trocar tema.
 */
function qjp_unschedule_weekly_update_check()
{
    $timestamp = wp_next_scheduled('qjp_weekly_update_check');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'qjp_weekly_update_check');
    }
}
add_action('switch_theme', 'qjp_unschedule_weekly_update_check');

/**
 * Checa atualização semanal e aplica update seguro automaticamente.
 */
function qjp_weekly_update_worker()
{
    qjp_check_theme_update();
    $update_info = get_option('qjp_theme_update_info', []);

    if (!empty($update_info['has_update'])) {
        qjp_perform_safe_theme_update(false);
    }
}
add_action('qjp_weekly_update_check', 'qjp_weekly_update_worker');

/**
 * Consulta GitHub para verificar se existe versão mais nova.
 */
function qjp_check_theme_update()
{
    $theme           = wp_get_theme();
    $current_version = (string) $theme->get('Version');

    $response = wp_remote_get(QJP_GITHUB_API_LATEST, [
        'timeout' => 15,
        'headers' => [
            'Accept'     => 'application/vnd.github+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url('/'),
        ],
    ]);

    $tag_name       = '';
    $remote_version = '';
    $package_url    = '';
    $release_url    = 'https://github.com/' . QJP_GITHUB_REPO;

    if (!is_wp_error($response) && 200 === (int) wp_remote_retrieve_response_code($response)) {
        $body = json_decode((string) wp_remote_retrieve_body($response), true);
        if (!empty($body) && is_array($body) && !empty($body['tag_name'])) {
            $tag_name       = (string) $body['tag_name'];
            $remote_version = ltrim($tag_name, 'vV');
            $package_url    = !empty($body['zipball_url']) ? (string) $body['zipball_url'] : '';
            $release_url    = !empty($body['html_url']) ? (string) $body['html_url'] : $release_url;
        }
    }

    // Fallback: usa última tag quando não houver release publicado.
    if ('' === $tag_name || '' === $package_url) {
        $tags_response = wp_remote_get(QJP_GITHUB_API_TAGS, [
            'timeout' => 15,
            'headers' => [
                'Accept'     => 'application/vnd.github+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url('/'),
            ],
        ]);

        if (is_wp_error($tags_response) || 200 !== (int) wp_remote_retrieve_response_code($tags_response)) {
            return;
        }

        $tags_body = json_decode((string) wp_remote_retrieve_body($tags_response), true);
        if (empty($tags_body) || !is_array($tags_body) || empty($tags_body[0]['name'])) {
            return;
        }

        $tag_name       = (string) $tags_body[0]['name'];
        $remote_version = ltrim($tag_name, 'vV');
        $package_url    = 'https://github.com/' . QJP_GITHUB_REPO . '/archive/refs/tags/' . rawurlencode($tag_name) . '.zip';
        $release_url    = 'https://github.com/' . QJP_GITHUB_REPO . '/tags';
    }

    $has_update = version_compare($remote_version, $current_version, '>');

    update_option('qjp_theme_update_info', [
        'checked_at'      => time(),
        'current_version' => $current_version,
        'remote_version'  => $remote_version,
        'tag_name'        => $tag_name,
        'package_url'     => esc_url_raw($package_url),
        'release_url'     => esc_url_raw($release_url),
        'has_update'      => $has_update,
    ], false);
}

/**
 * Copia diretório recursivamente.
 */
function qjp_recursive_copy($source, $destination)
{
    if (!file_exists($source)) {
        return false;
    }

    if (is_file($source)) {
        return copy($source, $destination);
    }

    if (!file_exists($destination)) {
        wp_mkdir_p($destination);
    }

    $items = scandir($source);
    if (false === $items) {
        return false;
    }

    foreach ($items as $item) {
        if ('.' === $item || '..' === $item) {
            continue;
        }

        $from = trailingslashit($source) . $item;
        $to   = trailingslashit($destination) . $item;

        if (is_dir($from)) {
            if (!qjp_recursive_copy($from, $to)) {
                return false;
            }
        } else {
            if (!copy($from, $to)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Remove diretório recursivamente.
 */
function qjp_recursive_delete($path)
{
    if (!file_exists($path)) {
        return true;
    }

    if (is_file($path) || is_link($path)) {
        return @unlink($path);
    }

    $items = scandir($path);
    if (false === $items) {
        return false;
    }

    foreach ($items as $item) {
        if ('.' === $item || '..' === $item) {
            continue;
        }

        if (!qjp_recursive_delete(trailingslashit($path) . $item)) {
            return false;
        }
    }

    return @rmdir($path);
}

/**
 * Executa atualização segura do tema.
 */
function qjp_perform_safe_theme_update($manual = false)
{
    $update_info = get_option('qjp_theme_update_info', []);
    if (empty($update_info['has_update']) || empty($update_info['package_url'])) {
        update_option('qjp_theme_update_notice', [
            'type'    => 'info',
            'message' => __('Nenhuma atualização disponível no momento.', 'quimbanda-jp'),
        ], false);
        return false;
    }

    $theme       = wp_get_theme();
    $stylesheet  = $theme->get_stylesheet();
    $theme_dir   = trailingslashit(get_theme_root()) . $stylesheet;
    $backup_root = trailingslashit(WP_CONTENT_DIR) . 'theme-backups/quimbanda-jp';
    $backup_dir  = trailingslashit($backup_root) . $stylesheet . '-backup-' . $theme->get('Version') . '-' . gmdate('YmdHis');

    wp_mkdir_p($backup_root);

    if (!qjp_recursive_copy($theme_dir, $backup_dir)) {
        update_option('qjp_theme_update_notice', [
            'type'    => 'error',
            'message' => __('Falha ao criar backup antes da atualização.', 'quimbanda-jp'),
        ], false);
        return false;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/misc.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    $updates = get_site_transient('update_themes');
    if (!is_object($updates)) {
        $updates = new stdClass();
    }
    if (!isset($updates->response) || !is_array($updates->response)) {
        $updates->response = [];
    }

    $updates->response[$stylesheet] = [
        'theme'       => $stylesheet,
        'new_version' => (string) $update_info['remote_version'],
        'url'         => !empty($update_info['release_url']) ? (string) $update_info['release_url'] : 'https://github.com/' . QJP_GITHUB_REPO,
        'package'     => (string) $update_info['package_url'],
    ];
    set_site_transient('update_themes', $updates);

    $upgrader = new Theme_Upgrader(new Automatic_Upgrader_Skin());
    $result   = $upgrader->upgrade($stylesheet, ['clear_update_cache' => true]);

    if (is_wp_error($result) || false === $result) {
        qjp_recursive_delete($theme_dir);
        qjp_recursive_copy($backup_dir, $theme_dir);

        update_option('qjp_theme_update_notice', [
            'type'    => 'error',
            'message' => __('Atualização falhou e o tema foi restaurado automaticamente (rollback).', 'quimbanda-jp'),
        ], false);
        return false;
    }

    // Checagem rápida do site após update.
    $health = wp_remote_get(home_url('/'), ['timeout' => 10]);
    if (is_wp_error($health) || (int) wp_remote_retrieve_response_code($health) >= 500) {
        qjp_recursive_delete($theme_dir);
        qjp_recursive_copy($backup_dir, $theme_dir);

        update_option('qjp_theme_update_notice', [
            'type'    => 'error',
            'message' => __('A nova versão apresentou problema e foi feito downgrade automático.', 'quimbanda-jp'),
        ], false);
        return false;
    }

    // Marca que não há update pendente após sucesso.
    $update_info['has_update'] = false;
    update_option('qjp_theme_update_info', $update_info, false);

    update_option('qjp_theme_update_notice', [
        'type'    => 'success',
        'message' => __('Tema atualizado com sucesso para a versão mais recente.', 'quimbanda-jp'),
    ], false);

    if ($manual) {
        wp_clean_themes_cache(true);
    }

    return true;
}

/**
 * Ação manual: atualizar agora com backup.
 */
function qjp_handle_manual_update_request()
{
    if (!current_user_can('update_themes')) {
        wp_die(esc_html__('Permissão negada.', 'quimbanda-jp'));
    }

    check_admin_referer('qjp_run_theme_update');

    qjp_check_theme_update();
    qjp_perform_safe_theme_update(true);

    wp_safe_redirect(admin_url('themes.php?page=qjp-theme-updates'));
    exit;
}
add_action('admin_post_qjp_run_theme_update', 'qjp_handle_manual_update_request');

/**
 * Menu simples de updates no painel.
 */
function qjp_register_updates_submenu()
{
    add_theme_page(
        __('Atualizações do Tema', 'quimbanda-jp'),
        __('Atualizações do Tema', 'quimbanda-jp'),
        'update_themes',
        'qjp-theme-updates',
        'qjp_render_updates_page'
    );
}
add_action('admin_menu', 'qjp_register_updates_submenu');

/**
 * Tela de atualização do tema.
 */
function qjp_render_updates_page()
{
    if (!current_user_can('update_themes')) {
        return;
    }

    qjp_check_theme_update();
    $update_info = get_option('qjp_theme_update_info', []);
    $theme       = wp_get_theme();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Atualizações do Quimbanda-JP', 'quimbanda-jp'); ?></h1>
        <p>
            <strong><?php esc_html_e('Versão instalada:', 'quimbanda-jp'); ?></strong>
            <?php echo esc_html($theme->get('Version')); ?>
        </p>
        <p>
            <strong><?php esc_html_e('Última versão no GitHub:', 'quimbanda-jp'); ?></strong>
            <?php echo esc_html(!empty($update_info['remote_version']) ? $update_info['remote_version'] : __('não encontrada', 'quimbanda-jp')); ?>
        </p>

        <?php if (!empty($update_info['has_update'])) : ?>
            <p>
                <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=qjp_run_theme_update'), 'qjp_run_theme_update')); ?>" class="button button-primary">
                    <?php esc_html_e('Atualizar agora (com backup automático)', 'quimbanda-jp'); ?>
                </a>
            </p>
        <?php else : ?>
            <p><?php esc_html_e('Seu tema já está atualizado.', 'quimbanda-jp'); ?></p>
        <?php endif; ?>

        <?php if (!empty($update_info['release_url'])) : ?>
            <p>
                <a href="<?php echo esc_url($update_info['release_url']); ?>" target="_blank" rel="noopener noreferrer" class="button">
                    <?php esc_html_e('Ver release no GitHub', 'quimbanda-jp'); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Aviso admin sobre resultado de update.
 */
function qjp_theme_update_admin_notice()
{
    if (!current_user_can('update_themes')) {
        return;
    }

    $update_info = get_option('qjp_theme_update_info', []);
    if (!empty($update_info['has_update']) && !empty($update_info['remote_version'])) {
        $update_url = wp_nonce_url(admin_url('admin-post.php?action=qjp_run_theme_update'), 'qjp_run_theme_update');
        echo '<div class="notice notice-warning"><p>';
        echo esc_html__('Há uma nova versão do tema Quimbanda-JP disponível:', 'quimbanda-jp') . ' <strong>' . esc_html($update_info['remote_version']) . '</strong>. ';
        echo '<a class="button button-primary" href="' . esc_url($update_url) . '">' . esc_html__('Atualizar com backup automático', 'quimbanda-jp') . '</a>';
        echo '</p></div>';
    }

    $notice = get_option('qjp_theme_update_notice', []);
    if (empty($notice['message'])) {
        return;
    }

    $class = 'notice-info';
    if (!empty($notice['type']) && 'error' === $notice['type']) {
        $class = 'notice-error';
    } elseif (!empty($notice['type']) && 'success' === $notice['type']) {
        $class = 'notice-success';
    }

    echo '<div class="notice ' . esc_attr($class) . ' is-dismissible"><p>' . esc_html($notice['message']) . '</p></div>';

    delete_option('qjp_theme_update_notice');
}
add_action('admin_notices', 'qjp_theme_update_admin_notice');

