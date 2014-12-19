var albumSwitchHandler = function(context) {
    
    context = context || $('body');
    
    var albumId = $('select#File_album_id', context).val();
    if(albumId !=='NULL' && albumId != '') {
        $('select#File_permission', context).prop('disabled', 'disabled');
        $('small.hint-permission', context).show();
    } else {
        $('select#File_permission', context).prop('disabled', false);
        $('small.hint-permission', context).hide();
    }
};

$(function() {
    albumSwitchHandler();

    $('body').on('change', 'select#File_album_id', function(e) {
        var el = $(this),
            container = $(el.parents('div.gitem-edit-panel').get(0));
            
        albumSwitchHandler(container);
    });
});