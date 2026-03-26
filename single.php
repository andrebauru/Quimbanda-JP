<?php
/**
 * Template para post individual.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main" class="content-wrap" role="main">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card post-featured'); ?> itemscope itemtype="https://schema.org/BlogPosting">
                <header class="post-content">
                    <h1 itemprop="headline"><?php the_title(); ?></h1>
                    <p class="post-meta">
                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" itemprop="datePublished"><?php echo esc_html(get_the_date()); ?></time>
                    </p>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('large', [
                            'loading'  => 'eager',
                            'decoding' => 'async',
                            'itemprop' => 'image',
                        ]); ?>
                    </div>
                <?php endif; ?>

                <div class="post-content" itemprop="articleBody">
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
                <h2><?php esc_html_e('Conteúdo não encontrado', 'quimbanda-jp'); ?></h2>
            </div>
        </article>
    <?php endif; ?>
</main>

<?php get_footer();
