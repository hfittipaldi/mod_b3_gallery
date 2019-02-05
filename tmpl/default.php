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
?>
<section id="b3Gallery-<?php echo $module_id; ?>" class="b3Gallery<?php echo $version; ?>">

<?php
if ($gallery !== null) {
    $k = 0;
    foreach ($gallery as $image) {
        $image->caption = htmlspecialchars($image->caption, ENT_COMPAT, 'UTF-8');
        $alt = substr($image->caption, 0, 120);
        if ($alt === '') {
            $alt = B3GalleryHelper::getAltText($image->image);
        }
        $image->alt = $alt;
?>
        <div data-target="#carousel-<?php echo $module_id; ?>" data-slide-to="<?php echo $k; ?>" class="b3Gallery-item">
            <a href="#galleryModal-<?php echo $module_id; ?>" data-toggle="modal" data-item-id="item-<?php echo $module_id .'-' . $k; ?>">
                <img src="<?php echo $image->thumb; ?>" alt="<?php echo $image->alt; ?>" title="<?php echo $image->caption; ?>" width="<?php echo $size; ?>" height="<?php echo $size; ?>" />
            </a>
        </div>
<?php
        $k++;
    }

    $labelledby = str_replace(' ', '_', $mod_title);
?>

    <div id="galleryModal-<?php echo $module_id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="<?php echo $labelledby; ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="<?php echo $labelledby; ?>"><?php echo $mod_title; ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="carousel-<?php echo $module_id; ?>" class="carousel slide<?php echo $transition; ?>"<?php echo $interval . $pause . $wrap . $keyboard; ?>>
                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            <?php
                            $k = 0;
                            foreach ($gallery as $image) {
                                $file = $image->image;
                                list($width, $height) = getimagesize($file);
                                $imgs_dimensions[$k] = '[' . $width . ', ' . $height . ']';

                            ?>
                            <figure class="<?php echo $item; ?>item item-<?php echo $module_id . '-' . $k . ($k==0 ? ' active' : ''); ?>">
                                <img src="<?php echo $file; ?>" alt="<?php echo $image->alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />

                                <figcaption>
                                    <?php if ($image->caption !== '') { ?>
                                    <div class="caption">
                                        <?php echo $image->caption; ?>
                                    </div>
                                    <?php } ?>

                                    <?php if ($counter !== false) { ?>
                                    <div class="counter">
                                        <?php echo $k + 1; ?> / <?php echo count(get_object_vars($gallery)); ?>
                                    </div>
                                    <?php } ?>
                                </figcaption>
                            </figure>
                            <?php
                                $k++;
                            } //endforeach ?>
                        </div>

                        <?php if ($controls === 1) { ?>
                        <!-- Controls -->
                        <a class="<?php echo $ctrlPrev; ?>" href="#carousel-<?php echo $module_id; ?>" role="button" data-slide="prev">
                            <span class="<?php echo $spanPrev; ?>" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="<?php echo $ctrlNext; ?>" href="#carousel-<?php echo $module_id; ?>" role="button" data-slide="next">
                            <span class="<?php echo $spanNext; ?>" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                        <?php } ?>

                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        jQuery('.b3Gallery-item').find('a').on('click', function() {
            getItemIndex(jQuery(this).parent().attr('data-slide-to'), <?php echo $module_id; ?>, [ <?php echo implode(', ', $imgs_dimensions); ?> ]);
        });
    </script>
<?php } else { ?>

    <div class="alert alert-warning" role="alert">
        <?php echo JText::_(MOD_B3_GALLERY_WARNING_NO_IMAGES); ?>
    </div>
<?php } ?>
</section>
