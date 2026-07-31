<?php
/**
 * ESE Latam theme — bootstrap
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('ESE_LATAM_VERSION', '0.1.0');
define('ESE_LATAM_DIR', get_template_directory());
define('ESE_LATAM_URI', get_template_directory_uri());

// Modo dev: si existe .vite-dev en la raíz del theme, se usa el server de Vite.
// En producción, se leen los assets compilados desde dist/.vite/manifest.json.
define('ESE_LATAM_IS_DEV', file_exists(ESE_LATAM_DIR . '/.vite-dev'));
define('ESE_LATAM_VITE_SERVER', 'http://localhost:5173');

require_once ESE_LATAM_DIR . '/inc/setup.php';
require_once ESE_LATAM_DIR . '/inc/enqueue.php';
