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
    return;
}

function resizeModal(id, tam) {
    jQuery('#galleryModal-' + id).find('.modal-dialog').css({
        width: tam
    });
}
