<?php
/**
 * B3 Gallery Module
 *
 * @package     Joomla.Site
 * @subpackage  mod_b3_gallery
 *
 * @author      Hugo Fittipaldi <hugo.fittipaldi@gmail.com>
 * @copyright   Copyright (C) 2019 Hugo Fittipaldi. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 * @link        https://github.com/hfittipaldi/mod_b3_gallery
 */

// No direct access
defined('_JEXEC') or die;

// Include the related items functions only once
JLoader::register('B3GalleryHelper', __DIR__ . '/helper.php');

JHtml::_('stylesheet', 'mod_b3_gallery/b3_gallery.css', array('relative' => true));
JHtml::_('bootstrap.framework');
JHtml::_('script', 'mod_b3_gallery/b3_gallery.js', array('relative' => true));


/* Module */
$module_id = $module->id;
$mod_title = htmlspecialchars($module->title, ENT_COMPAT, 'UTF-8');

/* Params */
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
$size    = (int) $params->get('size', 260);
$counter = (bool) $params->get('counter', true);

/* Carousel modal params */
if ($params->get('autoslide', 1)) {
    $interval = (int) $params->get('interval', 5000);
    $interval = $interval !== 5000 ? ' data-interval="' . $interval . '"' : '';
    $interval = ' data-ride="carousel"' . $interval;
    $pause    = (int) $params->get('pause') !== 1 ? ' data-pause="false"' : '';
}
$transition = (int) $params->get('transition', 0);
$transition = $transition !== 0 ? ' carousel-fade' : '';
if ($controls = (int) $params->get('controls', 1)) {
    $keyboard = (int) $params->get('keyboard') !== 1 ? ' data-keyboard="false"' : '';
}
$wrap = (int) $params->get('wrap') !== 1 ? ' data-wrap="false"' : '';

$version  = '';
$item     = 'carousel-';
$ctrlNext = 'carousel-control-next';
$ctrlPrev = 'carousel-control-prev';
$spanNext = 'carousel-control-next-icon';
$spanPrev = 'carousel-control-prev-icon';
if ($params->get('version') === '3.x') {
    $version  = ' b3';
    $item     = '';
    $ctrlNext = 'right carousel-control';
    $ctrlPrev = 'left carousel-control';
    $spanNext = 'glyphicon glyphicon-chevron-right';
    $spanPrev = 'glyphicon glyphicon-chevron-left';
}

$gallery = B3GalleryHelper::getGallery($params, $module_id);

require JModuleHelper::getLayoutPath('mod_b3_gallery', $params->get('layout', 'default'));
