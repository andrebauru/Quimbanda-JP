<?php
/**
 * Rodapé do tema Quimbanda-JP.
 */

if (!defined('ABSPATH')) {
    exit;
}

$bio      = get_theme_mod('qjp_footer_bio', '');
$address  = get_theme_mod('qjp_footer_address', '');
$phone    = get_theme_mod('qjp_footer_phone', '');
$hours    = get_theme_mod('qjp_footer_hours', '');
$wa_link  = function_exists('qjp_get_whatsapp_link') ? qjp_get_whatsapp_link() : '';

$latest_post = get_posts([
    'posts_per_page' => 1,
    'post_status'    => 'publish',
]);
?>

<section class="footer-widgets" aria-label="<?php esc_attr_e('Informações do rodapé', 'quimbanda-jp'); ?>">
    <div class="footer-grid">
        <aside class="footer-block footer-bio" aria-labelledby="footer-bio-title">
            <h3 id="footer-bio-title"><?php esc_html_e('BIO', 'quimbanda-jp'); ?></h3>
            <p><?php echo $bio ? esc_html($bio) : esc_html__('Adicione sua biografia no Customizer.', 'quimbanda-jp'); ?></p>
        </aside>

        <aside class="footer-block footer-info" aria-labelledby="footer-info-title">
            <h3 id="footer-info-title"><?php esc_html_e('Info', 'quimbanda-jp'); ?></h3>
            <ul>
                <li><strong><?php esc_html_e('Endereço:', 'quimbanda-jp'); ?></strong> <?php echo $address ? esc_html($address) : '-'; ?></li>
                <li>
                    <strong><?php esc_html_e('Telefone:', 'quimbanda-jp'); ?></strong>
                    <?php
                    if (!empty($phone)) {
                        $phone_href = preg_replace('/\D+/', '', $phone);
                        echo '<a href="tel:' . esc_attr($phone_href) . '">' . esc_html($phone) . '</a>';
                    } else {
                        echo '-';
                    }
                    ?>
                </li>
                <li><strong><?php esc_html_e('Horário:', 'quimbanda-jp'); ?></strong> <?php echo $hours ? esc_html($hours) : '-'; ?></li>
            </ul>
        </aside>

        <aside class="footer-block footer-latest" aria-labelledby="footer-latest-title">
            <h3 id="footer-latest-title"><?php esc_html_e('Latest', 'quimbanda-jp'); ?></h3>
            <?php if (!empty($latest_post)) : ?>
                <?php
                $post = $latest_post[0];
                setup_postdata($post);
                ?>
                <h4><a href="<?php echo esc_url(get_permalink($post)); ?>"><?php echo esc_html(get_the_title($post)); ?></a></h4>
                <p><?php echo esc_html(wp_trim_words(get_the_excerpt($post), 18)); ?></p>
                <a class="read-more" href="<?php echo esc_url(get_permalink($post)); ?>"><?php esc_html_e('Leia Mais...', 'quimbanda-jp'); ?></a>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php esc_html_e('Ainda não há posts publicados.', 'quimbanda-jp'); ?></p>
            <?php endif; ?>
        </aside>
    </div>
</section>

<?php if (function_exists('qjp_is_wpforms_active') && qjp_is_wpforms_active()) : ?>
    <section class="content-wrap" aria-labelledby="qjp-contact-title">
        <h2 id="qjp-contact-title"><?php esc_html_e('Contato', 'quimbanda-jp'); ?></h2>
        <?php if (is_active_sidebar('qjp-contact-form')) : ?>
            <?php dynamic_sidebar('qjp-contact-form'); ?>
        <?php else : ?>
            <p><?php esc_html_e('WPForms ativo. Adicione o formulário na área de widgets "Área de Contato (WPForms)".', 'quimbanda-jp'); ?></p>
        <?php endif; ?>
    </section>
<?php endif; ?>

<footer class="site-footer" role="contentinfo">
    <p>
        &copy; <?php echo esc_html(date_i18n('Y')); ?> <?php bloginfo('name'); ?>.
        <?php esc_html_e('Todos os direitos reservados.', 'quimbanda-jp'); ?>
        <?php esc_html_e('Desenvolvido por', 'quimbanda-jp'); ?>
        <a href="https://andretsc.dev" target="_blank" rel="noopener noreferrer">Andre Silva</a>.
    </p>
</footer>

<?php if (!empty($wa_link)) : ?>
    <a class="whatsapp-float" href="<?php echo esc_url($wa_link); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Fale conosco no WhatsApp', 'quimbanda-jp'); ?>">
        WhatsApp
    </a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
