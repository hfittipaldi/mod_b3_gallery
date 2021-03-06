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

/**
 * Helper for mod_b3_gallery
 *
 * @since       1.0
 */
class B3GalleryHelper
{
    /**
     * Retrieve the list of images
     *
     * @param   \Joomla\Registry\Registry  &$params  module parameters
     * @param  integer $module_id Module id
     *
     * @return  array
     *
     * @since   1.0
     */
    public static function getGallery(&$params, $module_id)
    {
        $gallery_params = $params->get('gallery');

        $new_params = self::checkThumbs($params, $module_id);
        if ($new_params !== null) {
            $gallery_params = $new_params;
        }

        return $gallery_params;
    }

    /**
     * Convert the image name to alternative text
     *
     * @param  string $file Image name
     *
     * @return string Alternative text
     *
     * since   2.1
     */
    public static function getAltText($file)
    {
        $info     = pathinfo($file);
        $filename = $info['filename'];

        $arr = ['-', '_', '.'];
        $altText = str_replace($arr, ' ', $filename);

        return $altText;
    }

    /**
     * Checks if there is any image in the directory
     *
     * @private
     *
     * @param  json    $params    module parameters
     * @param  integer $module_id Module id
     *
     * @return mixed Returns an array of images or null
     *
     * @since 2.0
     */
    protected static function checkThumbs($params, $module_id)
    {
        $gallery        = array();
        $gallery_params = $params->get('gallery');
        $thumb_size     = $params->get('size', 260);
        $create_thumb   = false;

        foreach ($gallery_params as $key => $image) {
            $gallery[$key]['image']   = $image->image;
            $gallery[$key]['thumb']   = $image->thumb;
            $gallery[$key]['caption'] = $image->caption;

            if (empty($image->thumb) === false) {
                list($width) = getimagesize($image->thumb);
                if ($width != $thumb_size) {
                    unlink($image->thumb);
                    $gallery[$key]['thumb'] = self::createThumb($image, $thumb_size);
                    $create_thumb = true;
                }
            } else {
                $gallery[$key]['thumb'] = self::createThumb($image, $thumb_size);
                $create_thumb = true;
            }
        }

        return $create_thumb ? self::setParams($params, $gallery, $module_id) : null;
    }

    /**
     * Create the image thumbnail
     *
     * @param   \Joomla\Registry\Registry  $image parameters
     * @param   integer  Thumb size
     *
     * @return  string Filename path
     *
     * @since   1.0
     */
    protected static function createThumb($image, $thumb_size)
    {
        $fullsize_img = $image->image;
        $thumbs_dir   = self::getThumbPath($fullsize_img);

        list($width, $height) = getimagesize($fullsize_img);


        // Create the image
        $type = exif_imagetype($fullsize_img);
        if ($type === 2) { // IMAGETYPE_JPEG
            $img_origem = imagecreatefromjpeg($fullsize_img);
        } elseif ($type === 3) { // IMAGETYPE_PNG
            $img_origem = imagecreatefrompng($fullsize_img);
        }

        // Set the thumbnail
        $thumb_name = basename($fullsize_img);
        $filename = $thumbs_dir . '/' . $thumb_name;

        // Get the image dimensions
        $width  = imagesx($img_origem);
        $height = imagesy($img_origem);

        $original_aspect = $width / $height;
        $thumb_aspect = 1;


        if ($original_aspect >= $thumb_aspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $thumb_size;
            $new_width  = $width / ($height / $thumb_size);
        } else {
            // If the thumbnail is wider than the image
            $new_width  = $thumb_size;
            $new_height = $height / ($width / $thumb_size);
        }
        $thumb = imagecreatetruecolor($thumb_size, $thumb_size);

        // Resize and crop
        imagecopyresampled(
            $thumb,
            $img_origem,
            0 - ($new_width - $thumb_size) / 2, // Center the image horizontally
            0 - ($new_height - $thumb_size) / 2, // Center the image vertically
            0,
            0,
            $new_width,
            $new_height,
            $width,
            $height
        );

        if ($type === 2) {
            imagejpeg($thumb, $filename, 80);
        } elseif ($type === 3) {
            imagepng($thumb, $filename);
        }

        return $filename;
    }

    /**
     * Update the database with new module parameters
     *
     * @param   \Joomla\Registry\Registry  &$params  module parameters
     * @param   array  $gallery  Gallery parameters
     * @param   integer  $module_id  Module id
     *
     * @return  mixed
     *
     * @since   2.0
     */
    protected static function setParams($params, $gallery, $module_id)
    {
        $return = null;

        if (count($gallery) > 0) {
            $result['gallery']          = $gallery;
            $result['version']          = $params->get('version');
            $result['size']             = $params->get('size');
            $result['counter']          = $params->get('counter');
            $result['autoslide']        = $params->get('autoslide');
            $result['transition']       = $params->get('transition');
            $result['interval']         = $params->get('interval');
            $result['controls']         = $params->get('controls');
            $result['pause']            = $params->get('pause');
            $result['wrap']             = $params->get('wrap');
            $result['keyboard']         = $params->get('keyboard');
            $result['layout']           = $params->get('layout');
            $result['moduleclass_sfx']  = $params->get('moduleclass_sfx');
            $result['cache']            = $params->get('cache');
            $result['cache_time']       = $params->get('cache_time');
            $result['cachemode']        = $params->get('cachemode');
            $result['module_tag']       = $params->get('module_tag');
            $result['bootstrap_size']   = $params->get('bootstrap_size');
            $result['header_tag']       = $params->get('header_tag');
            $result['header_class']     = $params->get('header_class');
            $result['style']            = $params->get('style');

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->update($db->quoteName('#__modules'))
                ->set($db->quoteName('params') . ' = ' . $db->quote(json_encode($result)))
                ->where($db->quoteName('id') . ' = ' . $module_id);

            if ($db->setQuery($query)->execute()) {
                $return = json_decode(json_encode($gallery), false);
            }
        }

        return $return;
    }

    /**
     * Checks if the selected directory exists
     *
     * @return  string
     *
     * @since   2.0
     */
    protected static function getImagePath($path)
    {
        $pieces = explode('/', $path);
        $image  = array_pop($pieces);

        $dir_name = implode('/', $pieces);

        return $dir_name;
    }

    /**
     * Checks if the selected directory exists
     *
     * @return  string
     *
     * @since   2.0
     */
    protected static function getThumbPath($path)
    {
        $thumbs_dir = self::getImagePath($path) . '/thumbs';

        if (!is_dir($thumbs_dir)) {
            mkdir($thumbs_dir);
        }

        return $thumbs_dir;
    }
}
