<!doctype html>
<?php
  session_cache_expire(30);
  session_start();

  $_SESSION['disable_animations'] = isset($_SESSION['disable_animations']) ? 0 : 0;
?>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <script>window.disableAnimations = <?php echo $_SESSION['disable_animations']; ?></script>
  </head>

  <body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <?php do_action('get_header'); ?>

    <div id="app">
      <?php echo view(app('sage.view'), app('sage.data'))->render(); ?>
    </div>

    <?php do_action('get_footer'); ?>
    <?php wp_footer(); ?>
  </body>
</html>
