<?php
/**
 * B3 Gallery Module
 *
 * @package     Joomla.Site
 * @subpackage  mod_b3_gallery
 *
 * @author      Hugo Fittipaldi <hugo.fittipaldi@gmail.com>
 * @copyright   Copyright (C) 2016 Hugo Fittipaldi. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 * @link        https://github.com/hfittipaldi/mod_b3_gallery
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base() . '/media/mod_b3_gallery/css/b3_gallery.css');
$doc->addScript(JURI::base() . 'media/mod_b3_gallery/js/b3_gallery.js');

/* Module */
$module_id = $module->id;
$mod_title = $module->title;

/* Params */
$size      = (int) $params->get('size', 150);
$bootstrap = (int) $params->get('bootstrap', 1);
$columns   = (string) $params->get('columns', '');
$counter   = (bool) $params->get('counter', true);

$row  = '';
$cols = 'pull-left';
if ($bootstrap === 1)
{
    $row  =  ' row';
    $cols = $columns !== '' ? $columns : 'col-xs-6 col-sm-4 col-md-3';
}

/* Modal params */
$autoslide  = (int) $params->get('autoslide', 1);
$interval   = (int) $params->get('interval', 5000);

$transition = (int) $params->get('transition', 0);
$transition = $transition !== 0 ? ' carousel-fade' : '';

$interval   = $interval !== 5000 ? ' data-interval="' . $interval . '"' : '';
$interval   = $autoslide !== 0 ? $interval : ' data-interval="false"';

$controls   = (int) $params->get('controls', 1);

$pause      = (int) $params->get('pause') !== 1 ? ' data-pause="false"' : '';
$wrap       = (int) $params->get('wrap') !== 1 ? ' data-wrap="false"' : '';
$keyboard   = (int) $params->get('keyboard') !== 1 ? ' data-keyboard="false"' : '';

$init = modB3GalleryHelper::init($params->get('images'));

$images = modB3GalleryHelper::getImages($params);

if ($params->get('thumbnail') == 1)
    $thumbnails = modB3GalleryHelper::createThumbs($params);

require JModuleHelper::getLayoutPath('mod_b3_gallery', $params->get('layout', 'default'));
