<?php

// Provide < PHP 5.3 support for the __DIR__ constant.
if (!defined('__DIR__')) {
  define('__DIR__', dirname(__FILE__));
}
require_once __DIR__ . '/includes/slsv.inc';
require_once __DIR__ . '/includes/theme.inc';
require_once __DIR__ . '/includes/pager.inc';
require_once __DIR__ . '/includes/form.inc';
require_once __DIR__ . '/includes/menu.inc';

// Load module specific files in the modules directory.
$includes = file_scan_directory(__DIR__ . '/includes/modules', '/\.inc$/');
foreach ($includes as $include) {
  if (module_exists($include->name)) {
    require_once $include->uri;
  }    
}

// Auto-rebuild the theme registry during theme development.
if (theme_get_setting('slsv_rebuild_registry') && !defined('MAINTENANCE_MODE')) {
  // Rebuild .info data.
  system_rebuild_theme_data();
  // Rebuild theme registry.
  drupal_theme_rebuild();
}

/**
 * Implements HOOK_theme().
 */
function slsv_theme(&$existing, $type, $theme, $path) {
  include_once './' . drupal_get_path('theme', 'slsv') . '/includes/template.theme-registry.inc';
  return _slsv_theme($existing, $type, $theme, $path);
}

/**
 * Return a themed breadcrumb trail.
 *
 * @param $variables
 *   - title: An optional string to be used as a navigational heading to give
 *     context for breadcrumb links to screen-reader users.
 *   - title_attributes_array: Array of HTML attributes for the title. It is
 *     flattened into a string within the theme function.
 *   - breadcrumb: An array containing the breadcrumb links.
 * @return
 *   A string containing the breadcrumb output.
 */
function slsv_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  $output = '';

  // Determine if we are to display the breadcrumb.
  $show_breadcrumb = theme_get_setting('slsv_breadcrumb');
  if ($show_breadcrumb == 'yes' || $show_breadcrumb == 'admin' && arg(0) == 'admin') {

    // Optionally get rid of the homepage link.
    $show_breadcrumb_home = theme_get_setting('slsv_breadcrumb_home');
    if (!$show_breadcrumb_home) {
      array_shift($breadcrumb);
    }

    // Return the breadcrumb with separators.
    if (!empty($breadcrumb)) {
      $breadcrumb_separator = theme_get_setting('slsv_breadcrumb_separator');
      $trailing_separator = $title = '';
      if (theme_get_setting('slsv_breadcrumb_title')) {
        $item = menu_get_item();
        if (!empty($item['tab_parent'])) {
          // If we are on a non-default tab, use the tab's title.
          $breadcrumb[] = check_plain($item['title']);
        }
        else {
          $breadcrumb[] = drupal_get_title();
        }
      }
      elseif (theme_get_setting('slsv_breadcrumb_trailing')) {
        $trailing_separator = $breadcrumb_separator;
      }

      // Provide a navigational heading to give context for breadcrumb links to
      // screen-reader users.
      if (empty($variables['title'])) {
        $variables['title'] = t('You are here');
      }
      // Unless overridden by a preprocess function, make the heading invisible.
      if (!isset($variables['title_attributes_array']['class'])) {
        $variables['title_attributes_array']['class'][] = 'element-invisible';
      }

      // Build the breadcrumb trail.
      $output = '<nav class="container">';
      $output .= '<h2' . drupal_attributes($variables['title_attributes_array']) . '>' . $variables['title'] . '</h2>';
      $output .= '<ul class="breadcrumb"><li>' . implode('<span class="divider">' . $breadcrumb_separator . '</span></li><li class="active">' , $breadcrumb) . $trailing_separator . '</li></ul>';
      $output .= '</nav>';
    }
  }

  return $output;
}

/**
 * Override or insert variables into the html template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered. This is usually "html", but can
 *   also be "maintenance_page" since slsv_preprocess_maintenance_page() calls
 *   this function to have consistent variables.
 */
function slsv_preprocess_html(&$variables, $hook) {
  // Add variables and paths needed for HTML5 and responsive support.
  $variables['base_path'] = base_path();
  $variables['path_to_slsv'] = drupal_get_path('theme', 'slsv');

  // Attributes for html element.
  $variables['html_attributes_array'] = array(
    'lang' => $variables['language']->language,
    'dir' => $variables['language']->dir,
  );

  // Send X-UA-Compatible HTTP header to force IE to use the most recent
  // rendering engine or use Chrome's frame rendering engine if available.
  // This also prevents the IE compatibility mode button to appear when using
  // conditional classes on the html tag.
  if (is_null(drupal_get_http_header('X-UA-Compatible'))) {
    drupal_add_http_header('X-UA-Compatible', 'IE=edge,chrome=1');
  }

  $variables['skip_link_anchor'] = theme_get_setting('slsv_skip_link_anchor');
  $variables['skip_link_text'] = theme_get_setting('slsv_skip_link_text');

  // Return early, so the maintenance page does not call any of the code below.
  if ($hook != 'html') {
    return;
  }

  // Serialize RDF Namespaces into an RDFa 1.1 prefix attribute.
  if ($variables['rdf_namespaces']) {
    $prefixes = array();
    foreach (explode("\n  ", ltrim($variables['rdf_namespaces'])) as $namespace) {
      // Remove xlmns: and ending quote and fix prefix formatting.
      $prefixes[] = str_replace('="', ': ', substr($namespace, 6, -1));
    }
    $variables['rdf_namespaces'] = ' prefix="' . implode(' ', $prefixes) . '"';
  }

  // Add Ubuntu fonts from Google
  drupal_add_css('http://fonts.googleapis.com/css?family=Ubuntu:500', array('type' => 'external'));

  // Classes for body element. Allows advanced theming based on context
  // (home page, node of certain type, etc.)
  if (!$variables['is_front']) {
    // Add unique class for each page.
    $path = drupal_get_path_alias($_GET['q']);
    // Add unique class for each website section.
    list($section, ) = explode('/', $path, 2);
    $arg = explode('/', $_GET['q']);
    if ($arg[0] == 'node' && isset($arg[1])) {
      if ($arg[1] == 'add') {
        $section = 'node-add';
      }
      elseif (isset($arg[2]) && is_numeric($arg[1]) && ($arg[2] == 'edit' || $arg[2] == 'delete')) {
        $section = 'node-' . $arg[2];
      }
    }
    $variables['classes_array'][] = drupal_html_class('section-' . $section);
  }
  if (theme_get_setting('slsv_wireframes')) {
    $variables['classes_array'][] = 'with-wireframes'; // Optionally add the wireframes style.
  }
  // Store the menu item since it has some useful information.
  $variables['menu_item'] = menu_get_item();
  if ($variables['menu_item']) {
    switch ($variables['menu_item']['page_callback']) {
      case 'views_page':
        // Is this a Views page?
        $variables['classes_array'][] = 'page-views';
        break;
      case 'page_manager_blog':
      case 'page_manager_blog_user':
      case 'page_manager_contact_site':
      case 'page_manager_contact_user':
      case 'page_manager_node_add':
      case 'page_manager_node_edit':
      case 'page_manager_node_view_page':
      case 'page_manager_page_execute':
      case 'page_manager_poll':
      case 'page_manager_search_page':
      case 'page_manager_term_view_page':
      case 'page_manager_user_edit_page':
      case 'page_manager_user_view_page':
        // Is this a Panels page?
        $variables['classes_array'][] = 'page-panels';
        break;
	  }
  }
}

/**
 * Override or insert variables into the html templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */
function slsv_process_html(&$variables, $hook) {
  // Flatten out html_attributes.
  $variables['html_attributes'] = drupal_attributes($variables['html_attributes_array']);
}

/**
 * Override or insert variables in the html_tag theme function.
 */
function slsv_process_html_tag(&$variables) {
	$tag = &$variables['element'];

	if ($tag['#tag'] == 'style' || $tag['#tag'] == 'script') {
    // Remove redundant type attribute and CDATA comments.
		unset($tag['#attributes']['type'], $tag['#value_prefix'], $tag['#value_suffix']);

    // Remove media="all" but leave others unaffected.
		if (isset($tag['#attributes']['media']) && $tag['#attributes']['media'] === 'all') {
			unset($tag['#attributes']['media']);
		}
	}
}

/**
 * Implement hook_html_head_alter().
 */
function slsv_html_head_alter(&$head) {
  // Simplify the meta tag for character encoding.
	if (isset($head['system_meta_content_type']['#attributes']['content'])) {
		$head['system_meta_content_type']['#attributes'] = array('charset' => str_replace('text/html; charset=', '', $head['system_meta_content_type']['#attributes']['content']));
	}
}

/**
 * Override or insert variables into the page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
function slsv_preprocess_page(&$variables, $hook) {
  // Primary nav
	$variables['primary_nav'] = FALSE;
	if ($variables['main_menu']) {
    // Build links
		$variables['primary_nav'] = menu_tree(variable_get('menu_main_links_source', 'main-menu'));
    // Provide default theme wrapper function
		$variables['primary_nav']['#theme_wrappers'] = array('menu_tree__primary');
	}

  // Secondary nav
	$variables['secondary_nav'] = FALSE;
	if ($variables['secondary_menu']) {
    // Build links
		$variables['secondary_nav'] = menu_tree(variable_get('menu_secondary_links_source', 'user-menu'));
    // Provide default theme wrapper function
		$variables['secondary_nav']['#theme_wrappers'] = array('menu_tree__secondary');
	}
}

/**
 * Slsv theme wrapper function for the primary menu links
 */
function slsv_menu_tree__primary(&$variables) {
	return '<ul class="menu nav">' . $variables['tree'] . '</ul>';
}

/**
 * Slsv theme wrapper function for the secondary menu links
 */
function slsv_menu_tree__secondary(&$variables) {
	return '<ul class="menu nav pull-right">' . $variables['tree'] . '</ul>';
}

/**
 * Returns HTML for a single local action link.
 *
 * This function overrides theme_menu_local_action() to add the icons that ship
 * with Slsv to the action links.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: A render element containing:
 *     - #link: A menu link array with "title", "href", "localized_options", and
 *       "icon" keys. If "icon" is not passed, it defaults to "plus-sign".
 *
 * @ingroup themeable
 *
 * @see theme_menu_local_action().
 */
function slsv_menu_local_action($variables) {
  $link = $variables['element']['#link'];

  // Build the icon rendering element.
  if (empty($link['icon'])) {
    $link['icon'] = 'plus-sign';
  }
  $icon = '<i class="' . drupal_clean_css_identifier('icon-' . $link['icon']) . '"></i>';

  // Format the action link.
  $output = '<li>';
  if (isset($link['href'])) {
    $options = isset($link['localized_options']) ? $link['localized_options'] : array();

    // If the title is not HTML, sanitize it.
    if (empty($link['localized_options']['html'])) {
      $link['title'] = check_plain($link['title']);
    }

    // Force HTML so we can add the icon rendering element.
    $options['html'] = TRUE;
    $output .= l($icon . $link['title'], $link['href'], $options);
  }
  elseif (!empty($link['localized_options']['html'])) {
    $output .= $icon . $link['title'];
  }
  else {
    $output .= $icon . check_plain($link['title']);
  }
  $output .= "</li>\n";

  return $output;
}

/**
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
function slsv_preprocess_maintenance_page(&$variables, $hook) {
	slsv_preprocess_html($variables, $hook);
  // There's nothing maintenance-related in slsv_preprocess_page(). Yet.
  //slsv_preprocess_page($variables, $hook);
}

/**
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
function slsv_process_maintenance_page(&$variables, $hook) {
	slsv_process_html($variables, $hook);
// Ensure default regions get a variable. Theme authors often forget to remove
// a deleted region's variable in maintenance-page.tpl.
	foreach (array('header', 'navigation', 'highlighted', 'help', 'content', 'sidebar_first', 'sidebar_second', 'footer', 'bottom') as $region) {
		if (!isset($variables[$region])) {
			$variables[$region] = '';
		}
	}
}

/**
 * Override or insert variables into the node templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
function slsv_preprocess_node(&$variables, $hook) {
  // Add $unpublished variable.
  $variables['unpublished'] = (!$variables['status']) ? TRUE : FALSE;

  // Add pubdate to submitted variable.
  $variables['date'] = format_date($variables['node']->created, 'custom', 'M j, Y');
  $variables['pubdate'] = '<time pubdate datetime="' . format_date($variables['node']->created, 'custom', 'c') . '">' . $variables['date'] . '</time>';
  if ($variables['display_submitted']) {

    // Publication date
    $submitted = '<li><i class="icon-calendar"></i>';
    $submitted .= $variables['pubdate'] . '</li>';
  
    // Username
    $submitted .= '<li><i class="icon-user"></i>';
    $submitted .= $variables['name'] . '</li>';

    // Comments
    $nid = $variables['node']->nid;
    $comments = l(format_plural($variables['node']->comment_count, '1 ' . t('comment'), '@count ' . t('comments')),
                'node/' . $nid,
                array('fragment' => 'comments'));
    $submitted .= '<li><i class="icon-comments"></i>';
    $submitted .= $comments . '</li>';

    // Tags
    $tags = render($variables['content']['field_tags']);
    if (!empty($variables['node']->field_tags)) {
      $submitted .= '<li><i class="icon-tag"></i>';
      $submitted .= $tags . '</li>';
    }

    $variables['submitted'] = $submitted;

  }

  // Add icon to comment forbidden
  if (!user_access('post comments') && !$variables['teaser'])  {
	  $variables['content']['links']['node']['#links']['comment_forbidden']['title'] = '<i class="icon-caret-right"></i> ' . theme('comment_post_forbidden', array('node' => $variables['node']));
	  $variables['content']['links']['node']['#links']['comment_forbidden']['html'] = TRUE;
  }

  // Add class to readmore link
  if (isset($variables['content']['links']['node']['#links']['node-readmore'])) {
	  $variables['content']['links']['node']['#links']['node-readmore']['attributes']['class'] = 'btn btn-small';
	  $variables['content']['links']['node']['#links']['node-readmore']['title'] = t('Read more<span class="element-invisible"> about @title</span>') . ' <i class="icon-double-angle-right"></i>';
  }

  // Add icon to link comment-add
  if (isset($variables['content']['links']['comment']['#links']['comment-add'])) {
	  $variables['content']['links']['comment']['#links']['comment-add']['title'] = '<i class="icon-plus-sign"></i> ' . t('Add new comment'); 
	  $variables['content']['links']['comment']['#links']['comment-add']['html'] = TRUE;
  }

  // Add a class for the view mode.
  if (!$variables['teaser']) {
    $variables['classes_array'][] = 'view-mode-' . $variables['view_mode'];
  }

  // Add a class to show node is authored by current user.
  if ($variables['uid'] && $variables['uid'] == $GLOBALS['user']->uid) {
    $variables['classes_array'][] = 'node-by-viewer';
  }

  $variables['title_attributes_array']['class'][] = 'node-title';
}

/**
 * Override or insert variables into the comment templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
function slsv_preprocess_comment(&$variables, $hook) {
  // If comment subjects are disabled, don't display them.
  if (variable_get('comment_subject_field_' . $variables['node']->type, 1) == 0) {
    $variables['title'] = '';
  }

  // New format for created date comment
  $variables['created'] = format_date($variables['comment']->created, 'custom', 'M j, Y');

  // Add icon to comment forbidden
  if (isset($variables['content']['links']['comment']['#links']['comment_forbidden'])) {
    $variables['content']['links']['comment']['#links']['comment_forbidden']['title'] = '<i class="icon-caret-right"></i> ' . theme('comment_post_forbidden', array('node' => $variables['node']));
  }

  // Add icon to reply link
  if (isset($variables['content']['links']['comment']['#links']['comment-reply'])) {
    $variables['content']['links']['comment']['#links']['comment-reply']['title'] = '<i class="icon-reply"></i> ' . t('reply');
  }

  // Add icon to delete link
  if (isset($variables['content']['links']['comment']['#links']['comment-delete'])) {
    $variables['content']['links']['comment']['#links']['comment-delete']['title'] = '<i class="icon-trash"></i> ' . t('delete');
  }

  // Add icon to edit link
  if (isset($variables['content']['links']['comment']['#links']['comment-edit'])) {
    $variables['content']['links']['comment']['#links']['comment-edit']['title'] = '<i class="icon-pencil"></i> ' . t('edit');
  }

  // Zebra striping.
  if ($variables['id'] == 1) {
    $variables['classes_array'][] = 'first';
  }
  if ($variables['id'] == $variables['node']->comment_count) {
    $variables['classes_array'][] = 'last';
  }
  $variables['classes_array'][] = $variables['zebra'];

  $variables['title_attributes_array']['class'][] = 'comment-title';
}

/**
 * Preprocess variables for region.tpl.php
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("region" in this case.)
 */
function slsv_preprocess_region(&$variables, $hook) {
  // Sidebar regions get some extra classes and a common template suggestion.
  if (strpos($variables['region'], 'sidebar_') === 0) {
    $variables['classes_array'][] = 'column';
    $variables['classes_array'][] = 'sidebar';
    // Allow a region-specific template to override Slsv's region--sidebar.
    array_unshift($variables['theme_hook_suggestions'], 'region__sidebar');
  }
  // Use a template with no wrapper for the content region.
  elseif ($variables['region'] == 'content') {
    // Allow a region-specific template to override Slsv's region--no-wrapper.
    array_unshift($variables['theme_hook_suggestions'], 'region__no_wrapper');
  }
  // Add a SMACSS-style class for header region.
  elseif ($variables['region'] == 'header') {
    array_unshift($variables['classes_array'], 'header--region');
  }
}


/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
function slsv_preprocess_block(&$variables, $hook) {
  // Use a template with no wrapper for the page's main content.
  if ($variables['block_html_id'] == 'block-system-main') {
    $variables['theme_hook_suggestions'][] = 'block__no_wrapper';
  }

  // Classes describing the position of the block within the region.
  if ($variables['block_id'] == 1) {
    $variables['classes_array'][] = 'first';
  }
  // The last_in_region property is set in slsv_page_alter().
  if (isset($variables['block']->last_in_region)) {
    $variables['classes_array'][] = 'last';
  }
  $variables['classes_array'][] = $variables['block_zebra'];

  $variables['title_attributes_array']['class'][] = 'block--title';
  $variables['title_attributes_array']['class'][] = 'block-title';

  // Add Aria Roles via attributes.
  switch ($variables['block']->module) {
    case 'system':
      switch ($variables['block']->delta) {
        case 'main':
          // Note: the "main" role goes in the page.tpl, not here.
          break;
        case 'help':
        case 'powered-by':
          $variables['attributes_array']['role'] = 'complementary';
          break;
        default:
          // Any other "system" block is a menu block.
          $variables['attributes_array']['role'] = 'navigation';
          break;
      }
      break;
    case 'menu':
    case 'menu_block':
    case 'blog':
    case 'book':
    case 'comment':
    case 'forum':
    case 'shortcut':
    case 'statistics':
      $variables['attributes_array']['role'] = 'navigation';
      break;
    case 'search':
      $variables['attributes_array']['role'] = 'search';
      break;
    case 'help':
    case 'aggregator':
    case 'locale':
    case 'poll':
    case 'profile':
      $variables['attributes_array']['role'] = 'complementary';
      break;
    case 'node':
      switch ($variables['block']->delta) {
        case 'syndicate':
          $variables['attributes_array']['role'] = 'complementary';
          break;
        case 'recent':
          $variables['attributes_array']['role'] = 'navigation';
          break;
      }
      break;
    case 'user':
      switch ($variables['block']->delta) {
        case 'login':
          $variables['attributes_array']['role'] = 'form';
          break;
        case 'new':
        case 'online':
          $variables['attributes_array']['role'] = 'complementary';
          break;
      }
      break;
  }
}

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
function slsv_process_block(&$variables, $hook) {
// Drupal 7 should use a $title variable instead of $block->subject.
	$variables['title'] = $variables['block']->subject;
}

/**
 * Implements hook_page_alter().
 *
 * Look for the last block in the region. This is impossible to determine from
 * within a preprocess_block function.
 *
 * @param $page
 *   Nested array of renderable elements that make up the page.
 */
function slsv_page_alter(&$page) {
  // Look in each visible region for blocks.
  foreach (system_region_list($GLOBALS['theme'], REGIONS_VISIBLE) as $region => $name) {
    if (!empty($page[$region])) {
      // Find the last block in the region.
      $blocks = array_reverse(element_children($page[$region]));
      while ($blocks && !isset($page[$region][$blocks[0]]['#block'])) {
        array_shift($blocks);
      }
      if ($blocks) {
        $page[$region][$blocks[0]]['#block']->last_in_region = TRUE;
      }
    }
  }
}

/**
 * Implements hook_form_BASE_FORM_ID_alter().
 *
 * Prevent user-facing field styling from screwing up node edit forms by
 * renaming the classes on the node edit form's field wrappers.
 */
function slsv_form_node_form_alter(&$form, &$form_state, $form_id) {
  // Remove if #1245218 is backported to D7 core.
  foreach (array_keys($form) as $item) {
    if (strpos($item, 'field_') === 0) {
      if (!empty($form[$item]['#attributes']['class'])) {
        foreach ($form[$item]['#attributes']['class'] as &$class) {
          if (strpos($class, 'field-type-') === 0 || strpos($class, 'field-name-') === 0) {
            // Make the class different from that used in theme_field().
            $class = 'form-' . $class;
          }
        }
      }
    }
  }
}


/**
 * Returns HTML for status and/or error messages, grouped by type.
 */
function slsv_status_messages($variables) {
  $display = $variables['display'];
  $output = '';

  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
    'info' => t('Informative message'),
  );

  // Map Drupal message types to their corresponding Slsv classes.
  // @see http://twitter.github.com/slsv/components.html#alerts
  $status_class = array(
	  'status' => 'success',
	  'error' => 'error',
	  'warning' => 'warning',
    // Not supported, but in theory a module could send any type of message.
    // @see drupal_set_message()
    // @see theme_status_messages()
	  'info' => 'info',
  );
  foreach (drupal_get_messages($display) as $type => $messages) {
    $class = (isset($status_class[$type])) ? ' alert-' . $status_class[$type] : '';
    $output .= "<div class=\"alert alert-block$class\">\n";
    $output .= " <a class=\"close\" data-dismiss=\"alert\" href=\"#\">&times;</a>\n";

    if (!empty($status_heading[$type])) {
      $output .= '<h4 class="element-invisible">' . $status_heading[$type] . "</h4>\n";
    }

    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= $messages[0];
    }

    $output .= "</div>\n";
  }
  return $output;
}

/**
 * Returns HTML for a marker for new or updated content.
 */
function slsv_mark($variables) {
	$type = $variables['type'];

	if ($type == MARK_NEW) {
		return ' <mark class="label label-warning">' . t('new') . '</mark>';
	}
	elseif ($type == MARK_UPDATED) {
		return ' <mark class="label label-info">' . t('updated') . '</mark>';
	}
}

/**
 * Alters the default Panels render callback so it removes the panel separator.
 */
function slsv_panels_default_style_render_region($variables) {
	return implode('', $variables['panes']);
}

/**
 * Implements theme_field__field_type().
 */
function slsv_field__taxonomy_term_reference($variables) {
	$output = '';

// Render the items.
	$output .= ($variables['element']['#label_display'] == 'inline') ? '<ul class="links">' : '<ul class="links">';
	foreach ($variables['items'] as $delta => $item) {

    // Set a delimiter, in this case a comma
    $delimiter = ',';

    // If the item is the last in the array remove the comma
    if (end($variables['items']) === $item) {
      $delimiter = '';
    }

		$output .= '<li class="taxonomy-term-reference-' . $delta . '"';
		$output .= $variables['item_attributes'][$delta] . '>';
		$output .= drupal_render($item) . $delimiter . '</li>';
	}
	$output .= '</ul>';

	return $output;
}
