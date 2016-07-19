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
    protected static $extensions = '/*.{jpg,jpeg,gif,png}';

    protected static $thumbs;

    protected static $imgs_dir;
    protected static $thumbs_dir;

    public static function init($data)
    {
        self::$dir_name   = self::_getDirName($data);

        self::$imgs_dir   = JPATH_BASE . '/images/' . self::$dir_name;
        self::$thumbs_dir = JPATH_BASE . '/images/' . self::$dir_name . '/thumbs';

        self::$thumbs     = glob(self::$thumbs_dir . self::$extensions, GLOB_BRACE);
    }

    /**
     * Retrieve the list of images
     *
     * @return  array
     *
     * @since   1.0
     */
    public static function getImages($params)
    {
        // Checks if there is any image in the directory
        return self::_groupByKey($params);
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
            $diff = self::_destroyThumbs();
        }

        if ($diff !== false)
        {
            $images = glob(self::$imgs_dir . self::$extensions, GLOB_BRACE);
            foreach ($images as $key => $image)
            {
                list($origem_x, $origem_y) = getimagesize($image);

                // Check if the file is really an image
                if (is_numeric($origem_x))
                {
                    $img_origem = imagecreatefromjpeg($image);

                    // Get the name of image and rename it
                    $img_old_name = basename($image);
                    $thumb_name   = self::_cleanName($img_old_name);
                    rename(self::$imgs_dir.'/'.$img_old_name, self::$imgs_dir.'/'.$thumb_name);

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
     * Group an object by key
     *
     * @param   array  $json An object containing the item data
     *
     * @return  mixed
     *
     * @since   1.0
     */
    private function _groupByKey($params)
    {
        $imagesJSON = self::_getJSON($params->get('images'));
        if ($imagesJSON !== null)
        {
            $result = array();
            foreach ($imagesJSON as $i => $sub)
            {
                foreach ($sub as $k => $v)
                {
                    $result[$k][$i] = $v;
                }
            }
            $return = self::_columnsList($result);

            if ($return !== null)
                return $return;
        }

        return null;
    }

    /**
     * Retrieves the data in JSON format
     *
     * @param   array  $data An object containing the item data
     *
     * @return  mixed
     *
     * @since   1.0
     */
    private function _getJSON($data)
    {
        $result = json_decode($data, true);

        if (version_compare(phpversion(), '5.6', '<'))
        {
            $result = call_user_func_array('json_decode', func_get_args());
        }

        if (json_last_error() === JSON_ERROR_NONE)
            return $result;

        return null;
    }

    /**
     * Retrieves the list of columns
     *
     * @param   array  $data An object containing the item data
     *
     * @return  mixed
     *
     * @since   1.0
     */
    private function _columnsList($data)
    {
        foreach ($data as $key => $row)
        {
            // Get the image name, clean it and rename it
            $img_old_name = basename($row['image']);
            $img_new_name = self::_cleanName($img_old_name);
            rename(self::$imgs_dir.'/'.$img_old_name, self::$imgs_dir.'/'.$img_new_name);

            // Split the image path
            $handle = explode('/', $row['image']);

            // Pop the image name from array
            $last = array_pop($handle);

            // Duplicate $handle to build the new path
            $handle1 = $handle;

            // Recriate the new image path
            array_push($handle1, $img_new_name);
            $img = implode('/', $handle1);

            // Create the thumb image path
            array_push($handle, 'thumbs' , $img_new_name);
            $thumb = implode('/', $handle);

            // Only for ordering porpouses
            $image[$key]    = $row['image'];
            $ordering[$key] = $row['ordering'];

            // Assign the new pathes
            $data[$key]['thumb'] = $thumb;
            $data[$key]['image'] = $img;
        }

        // Ordena os dados com ordering ascendente, image ascendente
        // adiciona $data como o último parâmetro, para ordenar pela chave comum
        array_multisort($ordering, SORT_ASC, $image, SORT_ASC, $data);

        return $data;
    }

    /**
     * Get the image directory
     *
     * @return  string
     *
     * @since   1.0
     */
    private static function _getDirName($data)
    {
        $handle = self::_getJSON($data);

        // Check if $handle is an array or an object
        if (!is_array($handle)) {
            $pieces = explode('/', $handle->image[0]);
        } else {
            $pieces = explode('/', $handle['image'][0]);
        }

        $first = array_shift($pieces);
        $last = array_pop($pieces);

        $dir_name = implode('/', $pieces);

        return $dir_name;
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
