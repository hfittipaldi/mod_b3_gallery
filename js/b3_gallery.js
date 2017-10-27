/*jslint browser: true*/
/*global jQuery, window, document*/
function viewport() {
    'use strict';
    var e = window,
        a = 'inner';
    if (!window.hasOwnProperty('innerWidth')) {
        a = 'client';
        e = document.documentElement || document.body;
    }
    return { width: e[a + 'Width'], height: e[a + 'Height'] };
}

function resizeModal(id, tam, first) {
    'use strict';
    var win_size   = viewport(),
        view       = win_size.width,
        modal      = jQuery('#galleryModal-' + id),
        modal_body = modal.find('.modal-body'),
        pad_top    = parseInt(modal_body.css('padding-top'), 0),
        pad_right  = parseInt(modal_body.css('padding-right'), 0),
        pad_bottom = parseInt(modal_body.css('padding-bottom'), 0),
        pad_left   = parseInt(modal_body.css('padding-left'), 0),
        nWidth     = tam[0] + pad_left + pad_right + 2 + 'px',
        nHeight    = tam[1] + pad_top + pad_bottom + 'px';

    if (view > 767) {
        if (first === true) {
            modal.find('.modal-dialog').css({width: nWidth});
            modal_body.css({height: nHeight});
        } else {
            modal.find('.modal-dialog').animate({width: nWidth}, 400);
            modal_body.animate({height: nHeight}, 400);
        }
    }
}

function getItemIndex(index, id, imgs_dimensions, subtitles) {
    'use strict';
    var carousel = jQuery('#carousel-' + id);

    resizeModal(id, imgs_dimensions[index], true);

    carousel.on('slid.bs.carousel', function () {
        var index = jQuery(carousel).find('figure.active').index(),
            currentIndex = index + 1,
            tam = imgs_dimensions[index];

        jQuery('#counter-' + id).text(currentIndex);
        jQuery('#caption-' + id).text(subtitles[index]);

        resizeModal(id, tam);
    });
}
