<?php
/**
 * Template de resultados de busca.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main" class="content-wrap" role="main">
    <header>
        <h1 class="section-title">
            <?php
            printf(
                esc_html__('Resultados para: %s', 'quimbanda-jp'),
                '<span>' . esc_html(get_search_query()) . '</span>'
            );
            ?>
        </h1>
    </header>

    <?php if (have_posts()) : ?>
        <div class="updates-grid">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
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
                <h2><?php esc_html_e('Nenhum resultado encontrado', 'quimbanda-jp'); ?></h2>
                <p><?php esc_html_e('Tente outro termo de busca.', 'quimbanda-jp'); ?></p>
            </div>
        </article>
    <?php endif; ?>
</main>

<?php get_footer();
