<article class="node-<?php print $node->nid; ?> <?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <?php if ($title_prefix || $title_suffix || $display_submitted || !$page && $title): ?>
    <header>
      <?php print render($title_prefix); ?>
      <?php if (!$page && $title): ?>
        <h3<?php print $title_attributes; ?>>
          <a href="<?php print $node_url; ?>">
            <?php print $title; ?>
          </a>
        </h3>
      <?php endif; ?>
      <?php print render($title_suffix); ?>

      <?php if ($display_submitted): ?>
        <ul class="entry-meta">
          <?php print $submitted; ?>
        </ul>
      <?php endif; ?>

    </header>
  <?php endif; ?>

  <?php
 	if ($teaser) {
		unset($content['links']['comment']['#links']['comment-add']);
		unset($content['links']['comment']['#links']['comment-comments']);
		unset($content['links']['comment']['#links']['comment_forbidden']);
		unset($content['links']['comment']['#links']['comment-new-comments']);
	}

    // We hide the comments and links now so that we can render them later.
    hide($content['comments']);
    hide($content['links']);
	hide($content['field_tags']);
    print render($content);
  ?>

  <footer>
    <?php print render($content['links']); ?></p>
  </footer>

  <?php print render($content['comments']); ?>

</article>
