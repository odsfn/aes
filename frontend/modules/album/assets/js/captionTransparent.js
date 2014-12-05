$(function() {
    $('body')
        .on('mouseenter', '.thumbnails.gallery > li .caption-transparent', function(e) {
            var caption = $(this).find('.caption-hidable');
            if (!caption.data('state') || caption.data('state') == 'closed') {
                caption.data('state', 'opening');
                caption.slideDown(250, function(){
                    caption.data('state', 'opened');
                });
            }
        })
        .on('mouseleave', '.thumbnails.gallery > li .caption-transparent', function(e) {
            var caption = $(this).find('.caption-hidable');
            if (caption.data('state') == 'opened' || caption.data('state') == 'opening') {
                caption.data('state', 'closing');
                caption.slideUp(250, function(){
                    caption.data('state', 'closed');
                });
            }
        });
});