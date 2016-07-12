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

    protected static $fullsize;
    protected static $thumbs;

    protected static $imgs_dir;
    protected static $thumbs_dir;

    public static function init($value)
    {
        self::$dir_name   = $value;

        self::$imgs_dir   = JPATH_BASE . '/images/' . $value;
        self::$thumbs_dir = JPATH_BASE . '/images/' . $value . '/thumbs';

        self::$fullsize   = glob(self::$imgs_dir.'/*.{jpg,gif,png}', GLOB_BRACE);
        self::$thumbs     = glob(self::$thumbs_dir.'/*.{jpg,gif,png}', GLOB_BRACE);
    }

	/**
	 * Retrieve a list of images
	 *
     * @return  array
     *
	 * @since   1.0
	 */
    public static function getImages()
    {
        // Checks if there is any image in the directory
        $images = self::_checkImages();

        if ($images === null)
            return null;

        $images = array(
            'fullsize' => glob('images/' . self::$dir_name .'/*.{jpg,gif,png}', GLOB_BRACE),
            'thumbs'   => glob('images/' . self::$dir_name . '/thumbs/*.{jpg,gif,png}', GLOB_BRACE)
        );

        return $images;
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
        $num_imgs = self::_checkImages();

        if ($num_imgs === null)
            return null;

        $num_thumbs = count(self::$thumbs);
        $thumb_size = $params->get('size');
        $diff       = false;

        if ($num_imgs !== $num_thumbs)
        {
            self::_destroyThumbs();
        }

        if ($diff === false)
        {
            $handle = explode('_', basename(self::$thumbs[0]));

            if (strpos($handle[1], $thumb_size) === false)
            {
                $diff = self::_destroyThumbs();
            }
        }

        if ($diff !== false)
        {
            foreach (self::$fullsize as $key => $image)
            {
                // 1. Pega as dimensões da imagem de origem
                list($origem_x, $origem_y) = getimagesize($image);

                if (is_numeric($origem_x))
                {
                    // 2. Lê a imagem de origem
                    $img_origem = imagecreatefromjpeg($image);

                    // 3. Escolhe a largura maior, e se baseando nela mesma, gera a largura menor
                    if ($origem_x >= $origem_y)
                    {
                        // A largura será a do thumbnail
                        $final_x = $thumb_size;

                        // A altura é calculada
                        $final_y = floor($thumb_size * $origem_y / $origem_x);

                        // Colar no x = 0
                        $f_x = 0;

                        // Centralizar a imagem no meio y do thumbnail
                        $f_y = round(($thumb_size / 2) - ($final_y / 2));
                    }
                    else // Se a altura for maior ou igual à largura
                    {
                        // Calcula a largura
                        $final_x = floor($thumb_size * $origem_x / $origem_y);

                        // A altura será a do thumbnail
                        $final_y = $thumb_size;

                        // Centraliza a imagem no meio x do thumbnail
                        $f_x = round(($thumb_size / 2) - ($final_x / 2));
                        $f_y = 0; // Colar no y = 0
                    }

                    // 4. cria a imagem final para o thumbnail
                    $img_final = imageCreatetruecolor($thumb_size, $thumb_size);

                    // 5. Define a cor do fundo (branco)
                    imagefill($img_final, 0, 0, imagecolorallocate($img_final, 255, 255, 255));

                    // 6. Copia a imagem original para dentro do thumbnail
                    imagecopyresampled($img_final, $img_origem, $f_x, $f_y, 0, 0, $final_x, $final_y, $origem_x, $origem_y);

                    #gerando a a miniatura da imagem

                    //get the name of photo
                    $old_name   = basename($image);
                    $new_name   = self::_cleanName($old_name);
                    $thumb_name = 'thumb_' . $thumb_size . 'x' . $thumb_size . '_' . $new_name;

                    rename(self::$imgs_dir.'/'.$old_name, self::$imgs_dir.'/'.$new_name);

                    #o 3º argumento é a qualidade da miniatura de 0 a 100
                    // 7. Salva o thumbnail
                    imagejpeg($img_final, self::$thumbs_dir . '/' . $thumb_name, 80);
                    imagedestroy($img_final);
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
        $path       = self::_checkPath();
        $count_imgs = count(self::$fullsize);

        if ($path === null || $count_imgs === 0)
            return null;

        return $count_imgs;
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
