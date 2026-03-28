<?php
/**
 * Template Name: Página Axé
 * Template Post Type: page
 *
 * Estrutura semântica com foco em SEO e compatibilidade com plugins.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main" class="content-wrap" role="main">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <?php
            $post_id = get_the_ID();

            $qjp_split_lines = static function ($value) {
                $value = (string) $value;
                if ('' === trim($value)) {
                    return [];
                }

                $value = str_replace(["\r\n", "\r"], "\n", $value);
                $lines = array_filter(array_map('trim', explode("\n", $value)));

                return array_values($lines);
            };

            $qjp_collect_items = static function ($meta_key) use ($post_id, $qjp_split_lines) {
                $items = get_post_meta($post_id, $meta_key, false);

                if (empty($items)) {
                    $single = get_post_meta($post_id, $meta_key, true);
                    return $qjp_split_lines($single);
                }

                $normalized = [];
                foreach ($items as $item) {
                    if (is_scalar($item)) {
                        $normalized = array_merge($normalized, $qjp_split_lines((string) $item));
                    }
                }

                return array_values(array_unique(array_filter($normalized)));
            };

            $trabalhos = $qjp_collect_items('qjp_trabalhos');
            $consultas = $qjp_collect_items('qjp_consultas');
            $enderecos = $qjp_collect_items('qjp_enderecos');
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card post-featured qjp-axe-page'); ?> itemscope itemtype="https://schema.org/LocalBusiness">
                <header class="post-content qjp-axe-header">
                    <h1 itemprop="name"><?php the_title(); ?></h1>
                    <p class="post-meta">
                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" itemprop="datePublished"><?php echo esc_html(get_the_date()); ?></time>
                    </p>
                </header>

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

                <section class="post-content qjp-axe-content" aria-labelledby="qjp-axe-sobre-title">
                    <h2 id="qjp-axe-sobre-title"><?php esc_html_e('Sobre o Terreiro', 'quimbanda-jp'); ?></h2>
                    <div itemprop="description">
                        <?php the_content(); ?>
                    </div>
                </section>

                <section class="post-content qjp-axe-section" aria-labelledby="qjp-trabalhos-title">
                    <h2 id="qjp-trabalhos-title"><?php esc_html_e('Trabalhos', 'quimbanda-jp'); ?></h2>
                    <?php if (!empty($trabalhos)) : ?>
                        <?php foreach ($trabalhos as $trabalho) : ?>
                            <article class="qjp-axe-item">
                                <h3><?php echo esc_html($trabalho); ?></h3>
                            </article>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p><?php esc_html_e('Adicione o campo personalizado qjp_trabalhos (um item por linha).', 'quimbanda-jp'); ?></p>
                    <?php endif; ?>
                </section>

                <section class="post-content qjp-axe-section" aria-labelledby="qjp-consultas-title">
                    <h2 id="qjp-consultas-title"><?php esc_html_e('Consultas', 'quimbanda-jp'); ?></h2>
                    <?php if (!empty($consultas)) : ?>
                        <?php foreach ($consultas as $consulta) : ?>
                            <article class="qjp-axe-item">
                                <h3><?php echo esc_html($consulta); ?></h3>
                            </article>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p><?php esc_html_e('Adicione o campo personalizado qjp_consultas (um item por linha).', 'quimbanda-jp'); ?></p>
                    <?php endif; ?>
                </section>

                <section class="post-content qjp-axe-section" aria-labelledby="qjp-enderecos-title">
                    <h2 id="qjp-enderecos-title"><?php esc_html_e('Endereços', 'quimbanda-jp'); ?></h2>
                    <?php if (!empty($enderecos)) : ?>
                        <?php foreach ($enderecos as $endereco) : ?>
                            <article class="qjp-axe-item" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                                <h3><?php echo esc_html($endereco); ?></h3>
                            </article>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p><?php esc_html_e('Adicione o campo personalizado qjp_enderecos (um item por linha).', 'quimbanda-jp'); ?></p>
                    <?php endif; ?>
                </section>
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
