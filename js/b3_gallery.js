;
function getItemIndex(id, img_width) {
    var carousel = jQuery('#carousel-' + id);

    resizeModal(id, img_width[0]+'px');
    carousel.on('slid.bs.carousel', function() {
        var index = jQuery(carousel).find('figure.active').index(),
            currentIndex = index + 1,
            tam = img_width[index] + 'px';

        jQuery('#counter-'+id).text(currentIndex);

        resizeModal(id, tam);
    });
}

function resizeModal(id, tam) {
    var win_size = viewport(),
        view = win_size['width'];
    if (view > 767) {
        jQuery('#galleryModal-' + id).find('.modal-dialog').css({
            width: tam
        });
    }
}

function viewport() {
    var e = window, a = 'inner';
    if (!('innerWidth' in window )) {
        a = 'client';
        e = document.documentElement || document.body;
    }
    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
}
