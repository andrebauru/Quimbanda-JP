<?php
/**
 * Sidebar principal.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="footer-widgets" role="complementary" aria-label="<?php esc_attr_e('Barra lateral', 'quimbanda-jp'); ?>">
    <div class="footer-grid">
        <?php dynamic_sidebar('sidebar-1'); ?>
    </div>
</aside>
