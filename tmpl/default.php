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

// No direct access
defined('_JEXEC') or die;
?>
<div id="b3Gallery-<?php echo $module_id; ?>" class="b3Gallery<?php echo $row; ?>">

<?php
if ($gallery !== null) :

    $captions = array();
    foreach ($gallery as $key => $image)
    {
        array_push($captions, $image->caption);
    }

    $k = 0;
    foreach ($gallery as $image) :
?>
        <div data-target="#carousel-<?php echo $module_id; ?>" data-slide-to="<?php echo $k; ?>" class="b3Gallery-item <?php echo $cols; ?>">
            <a href="#galleryModal-<?php echo $module_id; ?>" class="thumbnail" data-toggle="modal" data-item-id="item-<?php echo $module_id .'-' . $k; ?>">
                <img src="<?php echo $image->thumb; ?>" alt="<?php echo $image->caption; ?>" title="<?php echo $image->caption; ?>" />
            </a>
        </div>
<?php
        ++$k;
    endforeach;
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
                            $k = 0;
                            foreach ($gallery as $image) :
                                $file = $image->image;
                                list($width, $height) = getimagesize($file);
                                $imgs_width[] = $width;
                            ?>
                            <figure class="item-<?php echo $module_id . '-' . $k;?> item<?php echo $k==0 ? ' active' : ''; ?>">
                                <img src="<?php echo $file; ?>" alt="<?php echo $image->caption; ?>" />

                                <?php if ($image->caption !== '' && $counter === false) : ?>
                                <figcaption class="carousel-caption">
                                    <?php echo $image->caption; ?>
                                </figcaption>
                                <?php endif; ?>
                            </figure>
                            <?php
                                ++$k;
                            endforeach; ?>
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

                <?php if ($counter !== false) : ?>
                <div class="modal-footer">
                    <div class="pull-right"><span id="counter-<?php echo $module_id; ?>">1</span> / <?php echo count(get_object_vars($gallery)); ?></div>
                    <div id="caption-<?php echo $module_id; ?>" class="caption"><?php echo $captions[0]; ?></div>
                </div>
                <?php endif; ?>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        jQuery('.b3Gallery-item').find('a').on('click', function() {
            getItemIndex( <?php echo $module_id; ?>, [ <?php echo implode(', ', $imgs_width); ?> ], [ '<?php echo implode("', '", $captions); ?>' ] );
        });
    </script>

<?php else : ?>
    <div class="alert alert-danger" role="alert">
        <a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a>
        <h4 class="alert-heading">Error</h4>
        <p>There is no images in the gallery</p>
    </div>
<?php endif; ?>
    <div class="clearfix"></div>
</div>
