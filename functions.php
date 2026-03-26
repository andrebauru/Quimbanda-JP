<?php
/**
 * Funções do tema Quimbanda-JP.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * =============================
 * Sistema de Debug e Logging
 * =============================
 */

if (!defined('QJP_DEBUG_LOG_FILE')) {
    define('QJP_DEBUG_LOG_FILE', WP_CONTENT_DIR . '/qjp-theme-debug.log');
}

if (!defined('QJP_ENABLE_DEBUG')) {
    define('QJP_ENABLE_DEBUG', defined('WP_DEBUG') && WP_DEBUG);
}

/**
 * Registra erro/evento no arquivo de log do tema.
 *
 * @param string $message Mensagem a registrar.
 * @param string $level   Nível (info, warning, error).
 * @param mixed  $data    Dados adicionais para debug (opcional).
 */
function qjp_log($message = '', $level = 'info', $data = null)
{
    if (!QJP_ENABLE_DEBUG) {
        return;
    }

    $log_entry = sprintf(
        "[%s] [%s] %s\n",
        gmdate('Y-m-d H:i:s'),
        strtoupper($level),
        $message
    );

    if (null !== $data) {
        $log_entry .= "Data: " . wp_json_encode($data) . "\n";
    }

    $log_entry .= "---\n";

    // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_operations_file_put_contents
    @file_put_contents(QJP_DEBUG_LOG_FILE, $log_entry, FILE_APPEND);
}

/**
 * Limpa o arquivo de log.
 */
function qjp_clear_debug_log()
{
    if (!current_user_can('manage_options')) {
        return false;
    }

    if (file_exists(QJP_DEBUG_LOG_FILE)) {
        // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
        @unlink(QJP_DEBUG_LOG_FILE);
    }

    return true;
}

/**
 * Retorna conteúdo do log para exibição no painel.
 *
 * @return string Conteúdo do arquivo de log.
 */
function qjp_get_debug_log()
{
    if (!current_user_can('manage_options')) {
        return '';
    }

    if (!file_exists(QJP_DEBUG_LOG_FILE)) {
        return __('Nenhum evento registrado no log.', 'quimbanda-jp');
    }

    // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_operations_file_get_contents
    $content = @file_get_contents(QJP_DEBUG_LOG_FILE);

    if (false === $content) {
        return __('Erro ao ler o arquivo de log.', 'quimbanda-jp');
    }

    // Limita a última 500 linhas para melhor performance
    $lines = explode("\n", $content);
    $lines = array_slice($lines, -500);

    return implode("\n", $lines);
}

/**
 * Hook para registrar erros de plugins/temas conflitantes.
 */
function qjp_check_plugin_conflicts()
{
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $all_plugins = get_plugins();
    $active_plugins = get_option('active_plugins', []);

    qjp_log('Plugins ativos detectados', 'info', [
        'total' => count($active_plugins),
        'plugins' => array_keys(array_intersect_key($all_plugins, array_flip($active_plugins))),
    ]);
}
add_action('admin_init', 'qjp_check_plugin_conflicts', 100);

/**
 * =============================
 * Sanitização de Output para REST API
 * =============================
 *
 * Estas funções garantem que nenhum output não intencional
 * seja gerado durante chamadas de REST API, o que causaria
 * "JSON inválido" em respostas.
 */

/**
 * Detecta se estamos em uma requisição REST API.
 *
 * @return bool True se for REST API.
 */
function qjp_is_rest_request()
{
    // Método 1: Verificar definido (disponível em WP 4.4+)
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return true;
    }

    // Método 2: Verificar $_SERVER REQUEST_URI
    // phpcs:ignore WordPress.Security.ValidatedInput.InputNotSanitized
    if (!empty($_SERVER['REQUEST_URI'])) {
        // phpcs:ignore WordPress.Security.ValidatedInput.InputNotSanitized
        $request_uri = wp_unslash($_SERVER['REQUEST_URI']);
        if (false !== strpos($request_uri, '/wp-json/')) {
            return true;
        }
    }

    return false;
}

/**
 * Registra tentativa de output durante requisição REST.
 *
 * Útil para debug de plugins que geram output indesejado.
 */
function qjp_log_rest_output()
{
    if (!qjp_is_rest_request()) {
        return;
    }

    qjp_log('Detectada requisição REST API', 'info', [
        'rest_request' => defined('REST_REQUEST') ? REST_REQUEST : false,
        // phpcs:ignore WordPress.Security.ValidatedInput.InputNotSanitized
        'request_uri' => !empty($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : 'unknown',
    ]);
}
add_action('init', 'qjp_log_rest_output', 5);

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
    // Registra tentativa de adição de item ao menu
    qjp_log('Adicionando WhatsApp ao menu', 'info', [
        'theme_location' => isset($args->theme_location) ? $args->theme_location : 'unknown',
    ]);

    // Validação defensiva: verifica se $args é um objeto
    if (!is_object($args)) {
        qjp_log('args não é objeto no filtro wp_nav_menu_items', 'warning', ['args_type' => gettype($args)]);
        return $items;
    }

    // Verifica localização do menu
    if (empty($args->theme_location) || 'primary' !== $args->theme_location) {
        return $items;
    }

    // Obtém link do WhatsApp
    $wa_link = qjp_get_whatsapp_link();
    if (empty($wa_link)) {
        return $items;
    }

    // Construção segura do HTML
    $whatsapp_item = sprintf(
        '<li class="menu-item menu-item-whatsapp desktop-whatsapp"><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></li>',
        esc_url($wa_link),
        esc_html__('WhatsApp', 'quimbanda-jp')
    );

    // Concatenação segura
    $items = $items . $whatsapp_item;

    qjp_log('WhatsApp adicionado ao menu com sucesso', 'info');

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
 * Menu de debug/ferramentas do tema no painel.
 */
function qjp_register_debug_submenu()
{
    if (!QJP_ENABLE_DEBUG || !current_user_can('manage_options')) {
        return;
    }

    add_theme_page(
        __('Quimbanda-JP: Debug & Logs', 'quimbanda-jp'),
        __('Debug & Logs', 'quimbanda-jp'),
        'manage_options',
        'qjp-debug-logs',
        'qjp_render_debug_page'
    );
}
add_action('admin_menu', 'qjp_register_debug_submenu');

/**
 * Processa ação de limpeza de log.
 */
function qjp_handle_debug_actions()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Missing
    if (!empty($_POST['qjp_clear_log'])) {
        check_admin_referer('qjp_debug_nonce');
        qjp_clear_debug_log();
        wp_safe_redirect(admin_url('themes.php?page=qjp-debug-logs&msg=cleared'));
        exit;
    }
}
add_action('admin_init', 'qjp_handle_debug_actions');

/**
 * Tela de debug do tema.
 */
function qjp_render_debug_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $msg = !empty($_GET['msg']) ? sanitize_text_field(wp_unslash($_GET['msg'])) : '';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Quimbanda-JP: Debug & Logs', 'quimbanda-jp'); ?></h1>
        <p><?php esc_html_e('Visualize e gerencie os arquivos de log do tema.', 'quimbanda-jp'); ?></p>

        <?php if ('cleared' === $msg) : ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Log limpado com sucesso!', 'quimbanda-jp'); ?></p>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-top: 20px;">
            <h2><?php esc_html_e('Arquivo de Log', 'quimbanda-jp'); ?></h2>
            <p>
                <strong><?php esc_html_e('Localização:', 'quimbanda-jp'); ?></strong>
                <code><?php echo esc_html(QJP_DEBUG_LOG_FILE); ?></code>
            </p>

            <form method="post" action="">
                <?php wp_nonce_field('qjp_debug_nonce'); ?>
                <button type="submit" name="qjp_clear_log" class="button button-secondary" onclick="return confirm('<?php esc_attr_e('Tem certeza que deseja limpar o log?', 'quimbanda-jp'); ?>')">
                    <?php esc_html_e('Limpar Log', 'quimbanda-jp'); ?>
                </button>
            </form>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h2><?php esc_html_e('Conteúdo do Log', 'quimbanda-jp'); ?></h2>
            <pre style="background: #f1f1f1; padding: 15px; border-radius: 5px; max-height: 600px; overflow-y: auto; font-size: 12px;">
<?php echo esc_html(qjp_get_debug_log()); ?>
            </pre>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h2><?php esc_html_e('Informações do Sistema', 'quimbanda-jp'); ?></h2>
            <ul>
                <li><strong><?php esc_html_e('Versão do WordPress:', 'quimbanda-jp'); ?></strong> <?php echo esc_html(get_bloginfo('version')); ?></li>
                <li><strong><?php esc_html_e('Versão do PHP:', 'quimbanda-jp'); ?></strong> <?php echo esc_html(PHP_VERSION); ?></li>
                <li><strong><?php esc_html_e('Versão do Tema:', 'quimbanda-jp'); ?></strong> <?php echo esc_html(wp_get_theme()->get('Version')); ?></li>
                <li><strong><?php esc_html_e('Debug Ativado:', 'quimbanda-jp'); ?></strong> <?php echo QJP_ENABLE_DEBUG ? esc_html__('Sim', 'quimbanda-jp') : esc_html__('Não', 'quimbanda-jp'); ?></li>
                <li><strong><?php esc_html_e('Modo de Debug do WordPress:', 'quimbanda-jp'); ?></strong> <?php echo defined('WP_DEBUG') && WP_DEBUG ? esc_html__('Ativado', 'quimbanda-jp') : esc_html__('Desativado', 'quimbanda-jp'); ?></li>
            </ul>
        </div>

        <div class="card" style="margin-top: 20px; background: #fff3cd; border-color: #ffc107; border-left: 4px solid #ffc107;">
            <h3><?php esc_html_e('⚠️ Ativar Debug', 'quimbanda-jp'); ?></h3>
            <p><?php esc_html_e('Para ativar o debug completo do tema, adicione ao wp-config.php:', 'quimbanda-jp'); ?></p>
            <code style="display: block; background: #f1f1f1; padding: 10px; border-radius: 3px; margin-top: 10px;">
define( 'WP_DEBUG', true );
            </code>
        </div>
    </div>
    <?php
}

/**
 * Registro de Hooks adicionado seguro para evitar erros de REST API.
 */
add_action('admin_notices', 'qjp_theme_update_admin_notice');

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

    qjp_log('Verificando avisos de atualização', 'info');

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

