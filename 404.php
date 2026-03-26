<?php
/**
 * Template 404.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main" class="content-wrap" role="main">
    <article class="post-card post-featured">
        <div class="post-content">
            <h1><?php esc_html_e('404 - Página não encontrada', 'quimbanda-jp'); ?></h1>
            <p><?php esc_html_e('O conteúdo que você procurou não existe ou foi movido.', 'quimbanda-jp'); ?></p>
            <?php get_search_form(); ?>
        </div>
    </article>
</main>

<?php get_footer();
