<?php
/**
 * Template de comentários.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (post_password_required()) {
    return;
}
?>

<section id="comments" class="post-card" aria-label="<?php esc_attr_e('Comentários', 'quimbanda-jp'); ?>">
    <div class="post-content">
        <?php if (have_comments()) : ?>
            <h2>
                <?php
                printf(
                    esc_html(_nx('Um comentário', '%1$s comentários', get_comments_number(), 'comentários', 'quimbanda-jp')),
                    number_format_i18n(get_comments_number())
                );
                ?>
            </h2>

            <ol class="comment-list">
                <?php
                wp_list_comments([
                    'style'      => 'ol',
                    'short_ping' => true,
                ]);
                ?>
            </ol>

            <?php the_comments_pagination(); ?>
        <?php endif; ?>

        <?php if (!comments_open() && get_comments_number()) : ?>
            <p><?php esc_html_e('Comentários encerrados.', 'quimbanda-jp'); ?></p>
        <?php endif; ?>

        <?php comment_form(); ?>
    </div>
</section>
