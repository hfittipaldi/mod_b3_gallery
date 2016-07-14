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

jimport('joomla.filter.filteroutput');

/**
 * Helper for mod_b3_gallery
 *
 * @package     Joomla.Site
 * @subpackage  mod_b3_gallery
 * @since       1.0
 */
class ModB3GalleryHelper
{
    protected static $dir_name;
    protected static $extensions;

    protected static $fullsize;
    protected static $thumbs;

    protected static $imgs_dir;
    protected static $thumbs_dir;

    public static function init($value)
    {
        self::$dir_name   = $value;
        self::$extensions = '/*.{jpg,jpeg,gif,png}';

        self::$imgs_dir   = JPATH_BASE . '/images/' . $value;
        self::$thumbs_dir = JPATH_BASE . '/images/' . $value . '/thumbs';

        self::$fullsize   = glob(self::$imgs_dir . self::$extensions, GLOB_BRACE);
        self::$thumbs     = glob(self::$thumbs_dir . self::$extensions, GLOB_BRACE);
    }

	/**
	 * Retrieve the list of images
	 *
     * @return  array
     *
	 * @since   1.0
	 */
    public static function getImages()
    {
        // Checks if there is any image in the directory
        return self::_checkImages();
    }

    /**
     * Create the images thumbs
     *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
     *
     * @return  mixed
     *
     * @since   1.0
     */
    public static function createThumbs($params)
    {
        // Checks if there is any image in the directory
        $imgs = self::_checkImages();

        if ($imgs === null)
            return null;

        $num_imgs   = count($imgs['fullsize']);
        $num_thumbs = count($imgs['thumbs']);
        $diff       = false;

        if ($num_imgs !== $num_thumbs)
        {
            self::_destroyThumbs();
        }

        if ($diff === false)
        {
            $handle = explode('_', basename($imgs['thumbs'][0]));

            if (strpos($handle[1], $thumb_size) === false)
            {
                $diff = self::_destroyThumbs();
            }
        }

        if ($diff !== false)
        {
            foreach (self::$fullsize as $key => $image)
            {
                list($origem_x, $origem_y) = getimagesize($image);

                // Check if the file is really an image
                if (is_numeric($origem_x))
                {
                    $img_origem = imagecreatefromjpeg($image);

                    // Get the name of image and renamed it
                    $old_name   = basename($image);
                    $thumb_name   = self::_cleanName($old_name);
                    rename(self::$imgs_dir.'/'.$old_name, self::$imgs_dir.'/'.$new_name);

                    $filename = self::$thumbs_dir . '/' . $thumb_name;

                    $thumb_size = $params->get('size', 150);

                    // Get the image dimensions
                    $width = imagesx($img_origem);
                    $height = imagesy($img_origem);

                    $original_aspect = $width / $height;
                    $thumb_aspect = 1;


                    if ($original_aspect >= $thumb_aspect)
                    {
                       // If image is wider than thumbnail (in aspect ratio sense)
                       $new_height = $thumb_size;
                       $new_width  = $width / ($height / $thumb_size);
                    }
                    else
                    {
                       // If the thumbnail is wider than the image
                       $new_width  = $thumb_size;
                       $new_height = $height / ($width / $thumb_size);
                    }
                    $thumb = imagecreatetruecolor($thumb_size, $thumb_size);

                    // Resize and crop
                    imagecopyresampled($thumb,
                                       $img_origem,
                                       0 - ($new_width - $thumb_size) / 2, // Center the image horizontally
                                       0 - ($new_height - $thumb_size) / 2, // Center the image vertically
                                       0, 0,
                                       $new_width, $new_height,
                                       $width, $height);
                    imagejpeg($thumb, $filename, 80);
                }
            }
        }

        return true;
    }

    /**
     * Checks if the selected directory exists
     *
     * @return  mixed
     *
     * @since   1.0
     */
    private static function _checkPath()
    {
        if (!is_dir(self::$imgs_dir))
            return null;

        if (!is_dir(self::$thumbs_dir))
        {
            mkdir(self::$thumbs_dir);
        }

        return true;
    }

    /**
     * Checks if there is any image in the directory
     *
     * @return  mixed
     *
     * @since   1.0
     */
    private static function _checkImages()
    {
        $path          = self::_checkPath();
        $fullsize_imgs = glob('images/' . self::$dir_name . self::$extensions, GLOB_BRACE);
        $count_imgs    = count($fullsize_imgs);

        foreach ($fullsize_imgs as $image)
        {
            list($width, $height) = getimagesize($image);
            if (is_numeric($width))
                $fullsize[] = $image;
        }
        $images = array(
            'fullsize' => $fullsize,
            'thumbs'   => glob('images/' . self::$dir_name . '/thumbs' . self::$extensions, GLOB_BRACE)
        );

        if ($path === null || $count_imgs === 0 || count($fullsize) === 0)
            return null;

        return $images;
    }

    /**
     * Destroy any image in the directory
     *
     * @return  mixed
     *
     * @since   1.0
     */
    private static function _destroyThumbs()
    {
        array_map('unlink', self::$thumbs);

        return true;
    }

    /**
     * Clean the name of the image
     *
     * @param   string  $file  filename to sanitize
     *
     * @return  string
     *
     * @since   1.0
     */
    private static function _cleanName($file)
    {
        $info = pathinfo($file);
        $file_name =  basename($file, '.' . $info['extension']);

        return JFilterOutput::stringURLSafe($file_name).'.'.$info['extension'];
    }
}
