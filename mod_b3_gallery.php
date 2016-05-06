<?php
/**
 * @package     Joomla.site
 * @subpackage  mod_b3_gallery
 *
 * @author      Hugo Fittipaldi <hugo.fittipaldi@gmail.com>
 * @copyright   Copyright (C) 2016 Hugo Fittipaldi. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

//No Direct Access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$doc = JFactory::getDocument();

/* Module */
$module_id = $module->id;
$mod_title = $module->title;

/* Params */
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$dir_name        = $params->get('folder');
$size            = $params->get('size', 150);

/* Modal params */
$autoslide       = (int) $params->get('autoslide', 1);
$interval        = (int) $params->get('interval', 5000);
$transition      = (int) $params->get('transition', 0);

if ($transition !== 0)
{
    $transition = ' carousel-fade';
    $doc->addStyleSheet(JURI::base() . '/media/mod_b3_gallery/css/b3_gallery.css');
}
else
{
    $transition = '';
}

$interval        = $interval !== 5000 ? ' data-interval="' . $interval . '"' : '';
$interval        = $autoslide !== 0 ? $interval : ' data-interval="false"';

$indicators      = (int) $params->get('indicators', 1);
$controls        = (int) $params->get('controls', 1);

$pause           = (int) $params->get('pause') !== 1 ? ' data-pause="false"' : '';
$wrap            = (int) $params->get('wrap') !== 1 ? ' data-wrap="false"' : '';
$keyboard        = (int) $params->get('keyboard') !== 1 ? ' data-keyboard="false"' : '';

$init = ModB3GalleryHelper::init($dir_name);

if ($params->get('thumbnail') == 1)
    ModB3GalleryHelper::createThumbs($params);

$images = ModB3GalleryHelper::getImages();

require JModuleHelper::getLayoutPath('mod_b3_gallery', $params->get('layout', 'default'));
