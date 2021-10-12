<?php
/**
 * An arbitrary template for the AL Inpsyde plugin
 */
require_once(ABSPATH . '/wp-load.php');
?>
<html class="no-js" <?php language_attributes(); ?>>

    <head>

        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="profile" href="https://gmpg.org/xfn/11">
        <title>AL Inpsyde - Users Table</title>
        <?php wp_head(); ?>

    </head>

    <body class="altest-inpsyde">
        <main id="site-content" role="main">

            <div class="al-entry-content">
                <h1>AL USERS</h1>
                <h2>Custom HTML template from the plugin with WP loaded</h2>

                <?php echo do_shortcode('[al_users_table]'); ?>

            </div>
        </main>
    </body>
</html>


