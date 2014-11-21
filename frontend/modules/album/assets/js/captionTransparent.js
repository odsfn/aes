$(function() {  
    $('body')
        .on('mouseenter', '.thumbnails.gallery > li', function(e) {
            $(this).find('.caption-hidable').slideDown(250);
        })
        .on('mouseleave', '.thumbnails.gallery > li', function(e) {
            $(this).find('.caption-hidable').slideUp(250);
        });
});