$(function() {
    $('.thumbnails.gallery > li').hover(
        function() {
            $(this).find('.caption-hidable').slideDown(250);
        },
        function() {
            $(this).find('.caption-hidable').slideUp(250);
        }
    );
});