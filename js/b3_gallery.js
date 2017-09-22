/*jslint browser: true*/
/*global $, jQuery*/
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

function resizeModal(id, tam) {
    'use strict';
    var win_size = viewport(),
        view = win_size.width,
        size = tam + 32 + 'px';
    if (view > 767) {
        jQuery('#galleryModal-' + id).find('.modal-dialog').css({
            width: size
        });
    }
}

function getItemIndex(id, imgs_width, subtitles) {
    'use strict';
    var carousel = jQuery('#carousel-' + id);
    resizeModal(id, imgs_width[0]);
    carousel.on('slid.bs.carousel', function () {
        var index = jQuery(carousel).find('figure.active').index(),
            currentIndex = index + 1,
            tam = imgs_width[index];

        jQuery('#counter-' + id).text(currentIndex);
        jQuery('#caption-' + id).text(subtitles[index]);

        resizeModal(id, tam);
    });
}
