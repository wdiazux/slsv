<?php
/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see http://drupal.org/node/1728246
 */
?>
<section id="<?php print $block_html_id; ?>" class="<?php print $classes; ?> block"<?php print $attributes; ?>>

  <?php print render($title_prefix); ?>
  <?php if ($title): ?>
    <h3<?php print $title_attributes; ?>><?php print $title; ?></h3>
    <div class="divider"><span></span></div>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <?php print $content; ?>

</section>
