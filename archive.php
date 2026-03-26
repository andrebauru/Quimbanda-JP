<?php
/**
 * Template para arquivos.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main" class="content-wrap" role="main">
    <header>
        <h1 class="section-title"><?php the_archive_title(); ?></h1>
        <?php the_archive_description('<div class="post-meta">', '</div>'); ?>
    </header>

    <?php if (have_posts()) : ?>
        <div class="updates-grid">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                            <?php the_post_thumbnail('medium_large', [
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            ]); ?>
                        </a>
                    <?php endif; ?>
                    <div class="post-content">
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p class="post-meta"><?php echo esc_html(get_the_date()); ?></p>
                        <?php the_excerpt(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <nav class="pagination" aria-label="<?php esc_attr_e('Paginação', 'quimbanda-jp'); ?>">
            <?php the_posts_pagination(); ?>
        </nav>
    <?php else : ?>
        <article class="post-card">
            <div class="post-content">
                <h2><?php esc_html_e('Nenhum conteúdo encontrado', 'quimbanda-jp'); ?></h2>
            </div>
        </article>
    <?php endif; ?>
</main>

<?php get_footer();
