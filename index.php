<?php
/**
 * Template principal do tema Quimbanda-JP.
 */

if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>

<main id="main" class="content-wrap" role="main">
    <section aria-labelledby="atualizacoes-title">
        <header>
            <h2 id="atualizacoes-title" class="section-title"><?php esc_html_e('Atualizações', 'quimbanda-jp'); ?></h2>
        </header>

        <?php if (have_posts()) : ?>
            <div class="updates-grid">
                <?php
                $qjp_post_index = 0;
                while (have_posts()) :
                    the_post();
                    $qjp_post_index++;
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?> itemscope itemtype="https://schema.org/BlogPosting">
                        <header>
                            <?php if (has_post_thumbnail()) : ?>
                                <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php
                                    if (1 === $qjp_post_index) {
                                        the_post_thumbnail('large', [
                                            'loading'       => 'eager',
                                            'fetchpriority' => 'high',
                                            'decoding'      => 'async',
                                            'itemprop'      => 'image',
                                        ]);
                                    } else {
                                        the_post_thumbnail('medium_large', [
                                            'loading'       => 'lazy',
                                            'fetchpriority' => 'auto',
                                            'decoding'      => 'async',
                                            'itemprop'      => 'image',
                                        ]);
                                    }
                                    ?>
                                </a>
                            <?php endif; ?>
                        </header>

                        <div class="post-content">
                            <h3 itemprop="headline">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <p class="post-meta">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" itemprop="datePublished"><?php echo esc_html(get_the_date()); ?></time>
                            </p>

                            <div itemprop="description">
                                <?php the_excerpt(); ?>
                            </div>
                        </div>

                        <footer class="post-content">
                            <a class="read-more" href="<?php the_permalink(); ?>"><?php esc_html_e('Leia Mais...', 'quimbanda-jp'); ?></a>
                        </footer>
                    </article>
                <?php endwhile; ?>
            </div>

            <nav class="pagination" aria-label="<?php esc_attr_e('Paginação', 'quimbanda-jp'); ?>">
                <?php the_posts_pagination(); ?>
            </nav>
        <?php else : ?>
            <article class="post-card">
                <div class="post-content">
                    <h3><?php esc_html_e('Nenhuma atualização encontrada', 'quimbanda-jp'); ?></h3>
                    <p><?php esc_html_e('Publique conteúdos para exibir nesta área.', 'quimbanda-jp'); ?></p>
                </div>
            </article>
        <?php endif; ?>
    </section>
</main>

<?php get_footer();
