<?php
/**
 * Template para páginas.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main" class="content-wrap" role="main">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card post-featured'); ?>>
                <header class="post-content">
                    <h1><?php the_title(); ?></h1>
                </header>
                <div class="post-content">
                    <?php the_content(); ?>
                    <?php wp_link_pages(); ?>
                </div>
            </article>

            <?php
            if (comments_open() || get_comments_number()) {
                comments_template();
            }
            ?>
        <?php endwhile; ?>
    <?php else : ?>
        <article class="post-card">
            <div class="post-content">
                <h2><?php esc_html_e('Página não encontrada', 'quimbanda-jp'); ?></h2>
            </div>
        </article>
    <?php endif; ?>
</main>

<?php get_footer();
