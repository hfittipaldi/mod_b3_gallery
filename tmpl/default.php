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
?>
<div id="b3Gallery-<?php echo $module_id; ?>" class="b3Gallery">

<?php
if ($images !== null) :
    $files     = array();
    $thumbs    = array();
    $subtitles = array();
    foreach ($images as $key => $image)
    {
        array_push($thumbs, $image['thumb']);
        array_push($files, $image['image']);
        array_push($subtitles, $image['subtitle']);
    }

    foreach ($thumbs as $k => $thumb)
    {
?>
        <div data-target="#carousel-<?php echo $module_id; ?>" data-slide-to="<?php echo $k; ?>" class="b3Gallery-item pull-left">
            <a href="#galleryModal-<?php echo $module_id; ?>" class="thumbnail" data-toggle="modal" data-item-id="item-<?php echo $module_id .'-' . $k; ?>">
                <img src="<?php echo $thumb; ?>" alt="<?php echo $subtitles[$k]; ?>" />
            </a>
        </div>
<?php
    }
?>

    <div id="galleryModal-<?php echo $module_id; ?>" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php echo $mod_title; ?></h4>
                </div>
                <div class="modal-body">
                    <div id="carousel-<?php echo $module_id; ?>" class="carousel slide<?php echo $transition; ?>" data-ride="carousel"<?php echo $interval . $pause . $wrap . $keyboard; ?>>
                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            <?php
                                $array = '[';
                            foreach ($files as $k => $file) :
                                list($width, $height) = getimagesize($file);
                                if ($k > 0) $array .= ',';
                                $array .= $width;
                            ?>
                            <figure class="item-<?php echo $module_id . '-' . $k;?> item<?php echo $k==0 ? ' active' : ''; ?>">
                                <img src="<?php echo $file; ?>" alt="<?php echo $subtitles[$k]; ?>" />

                                <?php if ($subtitles[$k] !== '') : ?>
                                <figcaption class="carousel-caption">
                                    <?php echo $subtitles[$k]; ?>
                                </figcaption>
                                <?php endif; ?>
                            </figure>
                            <?php endforeach; ?>
                            <?php $array .= ']'; ?>
                        </div>

                        <?php if ($controls === 1) : ?>
                        <!-- Controls -->
                        <a class="left carousel-control" href="#carousel-<?php echo $module_id; ?>" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control" href="#carousel-<?php echo $module_id; ?>" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">

                    <div class="pull-left"><span id="counter-<?php echo $module_id; ?>">1</span> / <?php echo count($images); ?></div>

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        jQuery('.b3Gallery-item').find('a').on('click', function() {
            getItemIndex(<?php echo $module_id; ?>);
        });

        function getItemIndex(id) {
            var carousel = jQuery('#carousel-' + id),
                width = <?php echo $array; ?>;

            resizeModal(<?php echo $module_id; ?>, width[0]+'px');
            carousel.on('slid.bs.carousel', function() {
                var index = jQuery(carousel).find('figure.active').index(),
                    currentIndex = index + 1,
                    tam = width[index] + 'px';

                jQuery('#counter-'+id).text(currentIndex);

                resizeModal(<?php echo $module_id; ?>, tam);
            });
        }

        function resizeModal(id, w) {
            jQuery('#galleryModal-' + id).find('.modal-dialog').css({
                width: w
            });
        }
    </script>

<?php else : ?>
    <div class="alert alert-danger" role="alert">
        <a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a>
        <h4 class="alert-heading">Error</h4>
        <p>The directory <b><?php echo $dir_name; ?></b> does not exits or there is no images in the <b><?php echo $dir_name; ?></b> directory</p>
    </div>
<?php endif; ?>

    <div class="clearfix"></div>
</div>
