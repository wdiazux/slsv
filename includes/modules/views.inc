<?php

/**
 * @file
 * Provides views theme override functions for Slsv themes.
 */

/**
 * Display a view as a table style.
 */
function slsv_preprocess_views_view_table(&$vars) {
  $vars['classes_array'][] = 'table';
}

function slsv_preprocess_views_view_grid(&$vars) {
  $vars['class'] .= ' table';
}
