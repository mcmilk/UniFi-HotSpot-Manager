<?php
/**
 * theme and language switching (auth not needed)
 */
if (isset($_GET['theme'])) {
  $theme = $_GET['theme'];
  $_SESSION['theme'] = $theme;
}

if (isset($_SESSION['theme'])) {
  $theme = $_SESSION['theme'];
}

if ($theme === 'bootstrap') {
  $css_url = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css';
} else {
  $css_url = 'https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/' . trim($theme) . '/bootstrap.min.css';
}

/**
 * setup i18n variables
 * 1) object $i18n
 * 2) array $languages
 */
$i18n = null;
$languages = array();
$_SESSION['lang'] = i18n_init();
?>