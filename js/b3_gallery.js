/*jslint browser: true*/
/*global jQuery*/

function resizeModal(id, tam, first) {
    'use strict';
    var modal      = jQuery('#galleryModal-' + id),
        modal_body = modal.find('.modal-body'),
        pad_right  = parseInt(modal_body.css('padding-right'), 0),
        pad_left   = parseInt(modal_body.css('padding-left'), 0),
        nWidth     = tam[0] + pad_left + pad_right + 2,
        carousel   = jQuery('#carousel-' + id),
        active     = carousel.find('figure.active');

    if (first === true) {
        modal.on('shown.bs.modal', function () {
            var imgHeight = active.find('img').height();
            modal.find('a[data-slide]').height(imgHeight);
        }).find('.modal-dialog').css({width: nWidth + 'px'});
    } else {
        modal.find('.modal-dialog').animate({width: nWidth + 'px'}, 400, function () {
            var imgHeight = active.find('img').height();
            modal.find('a[data-slide]').height(imgHeight);
        });
    }

    modal.focus(function () {
        carousel.find('a[data-slide="next"]').focus();
    });
}

function getItemIndex(index, id, imgs_dimensions) {
    'use strict';
    var carousel = jQuery('#carousel-' + id);

    resizeModal(id, imgs_dimensions[index], true);

    carousel.on('slid.bs.carousel', function () {
        var index = carousel.find('figure.active').index();

        resizeModal(id, imgs_dimensions[index]);
    });
}
