<?php
/**
 * An arbitrary PAGE template for the AL Inpsyde plugin
 */
get_header();
?>

<div class="al-entry-content">
    <h1>AL USERS</h1>
    <h2>Custom template from the plugin with active theme's header/footer</h2>

    <?php echo do_shortcode('[al_users_table]'); ?>

</div>

<?php get_footer(); ?>


