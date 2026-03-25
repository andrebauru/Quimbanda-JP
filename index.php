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
    <?php
    // Exibir o post mais recente completo
    $qjp_latest_id = 0;
    $qjp_latest = new WP_Query([
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    ]);
    if ($qjp_latest->have_posts()) :
        while ($qjp_latest->have_posts()) : $qjp_latest->the_post();
            $qjp_latest_id = get_the_ID();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card post-featured'); ?> itemscope itemtype="https://schema.org/BlogPosting">
                <header>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large', [
                                'loading'       => 'eager',
                                'fetchpriority' => 'high',
                                'decoding'      => 'async',
                                'itemprop'      => 'image',
                            ]); ?>
                        </div>
                    <?php endif; ?>
                    <h2 itemprop="headline"><?php the_title(); ?></h2>
                    <p class="post-meta">
                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" itemprop="datePublished"><?php echo esc_html(get_the_date()); ?></time>
                    </p>
                </header>
                <div class="post-content" itemprop="articleBody">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile;
        wp_reset_postdata();
    endif;
    ?>

    <section aria-labelledby="atualizacoes-title">
        <header>
            <h2 id="atualizacoes-title" class="section-title"><?php esc_html_e('Atualizações', 'quimbanda-jp'); ?></h2>
        </header>

        <?php
        // Exibir os demais posts (exceto o mais recente)
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $qjp_grid = new WP_Query([
            'posts_per_page' => get_option('posts_per_page'),
            'post_status'    => 'publish',
            'paged'          => $paged,
            'post__not_in'   => $qjp_latest_id ? [$qjp_latest_id] : [],
        ]);
        if ($qjp_grid->have_posts()) : ?>
            <div class="updates-grid">
                <?php while ($qjp_grid->have_posts()) : $qjp_grid->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?> itemscope itemtype="https://schema.org/BlogPosting">
                        <header>
                            <?php if (has_post_thumbnail()) : ?>
                                <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php the_post_thumbnail('medium_large', [
                                        'loading'       => 'lazy',
                                        'fetchpriority' => 'auto',
                                        'decoding'      => 'async',
                                        'itemprop'      => 'image',
                                    ]); ?>
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
                <?php
                // Paginação para o grid
                $big = 999999999;
                echo paginate_links([
                    'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format'    => '?paged=%#%',
                    'current'   => max(1, get_query_var('paged')),
                    'total'     => $qjp_grid->max_num_pages,
                ]);
                ?>
            </nav>
            <?php wp_reset_postdata(); ?>
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
