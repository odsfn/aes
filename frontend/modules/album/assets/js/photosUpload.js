function handleUploaded(file, data, response) {
    var respData = $.parseJSON(data);
            
    if(!response && respData.success)
        throw new Error("Upload failed");
    
    $('#uploaded-photos-container').append(respData.html);
}

$(function() {
    $('#uploaded-photos-container')
        .on('click', '.photo-edit-panel button[type="submit"]', function(event) {
            event.preventDefault();
            var el = $(this),
                container = $(el.parents('div.photo-edit-panel').get(0)),
                form = $(el.parents('form.photo-edit-form').get(0)),
                submitUrl = form.attr('action');

            container.mask('Please wait...');
            $.ajax({
                type: "POST",
                url: submitUrl,
                data: form.serialize(),
                success: function(response) {
                    if(!response || typeof(response) !== 'object' || !response.hasOwnProperty('success'))
                        throw new Error("Saving failed");
                    
                    container.unmask();
                    container.replaceWith(response.html);
                },
                dataType: 'json'
            });
        })
        .on('click', '.photo-edit-panel a.photo-delete', function(event) {
            event.preventDefault();
            var el = $(this),
                container = $(el.parents('div.photo-edit-panel').get(0));
                
            container.mask('Please wait...');
            $.ajax({
                type: 'POST',
                url: el.attr('href'),
                success: function(response) {
                    if(response.success)
                        container.fadeOut(function() {
                            container.unmask();
                            container.remove();
                        });
                    else
                        throw new Error("Deletion failed");
                },
                dataType: 'json'
            });
        });
});