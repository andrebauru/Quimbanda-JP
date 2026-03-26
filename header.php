<?php
/**
 * Cabeçalho do tema Quimbanda-JP.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner">
    <div class="header-inner">
        <div class="branding" itemscope itemtype="https://schema.org/Organization">
            <div class="site-logo" aria-hidden="true">
                <?php
                if (function_exists('the_custom_logo') && has_custom_logo()) {
                    the_custom_logo();
                }
                ?>
            </div>
            <h1 class="site-title" itemprop="name">
                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
            </h1>
        </div>

        <nav class="main-nav" role="navigation" aria-label="<?php esc_attr_e('Menu principal', 'quimbanda-jp'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'fallback_cb'    => 'wp_page_menu',
            ]);
            ?>
        </nav>
    </div>
</header>
