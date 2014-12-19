function handleUploaded(file, data, response) {
    var respData = $.parseJSON(data);
            
    if(!response && respData.success)
        throw new Error("Upload failed");
    
    $('#uploaded-gitems-container').append(respData.html);
}

$(function() {
    $('#uploaded-gitems-container')
        .on('click', '.gitem-edit-panel button[type="submit"]', function(event) {
            event.preventDefault();
            var el = $(this),
                container = $(el.parents('div.gitem-edit-panel').get(0)),
                form = $(el.parents('form.gitem-edit-form').get(0)),
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
        .on('click', '.gitem-edit-panel a.gitem-delete', function(event) {
            event.preventDefault();
            var el = $(this),
                container = $(el.parents('div.gitem-edit-panel').get(0));
                
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
        })
        .on('click', '.gitem-edit-panel .rotate', function(event) {
            event.preventDefault();
            var el = $(this),
                direction = el.data('direction'),
                container = $(el.parents('div.gitem-edit-panel').get(0));
            
            container.mask('Please wait...');
            $.ajax({
                type: 'POST',
                url: el.attr('href'),
                success: function(response) {
                    if(response.success) {
                        var img = $('.left-col img', container),
                                newSrc = img.attr('src').replace(/(\?_dc=[\d\.]+)?$/, '?_dc=' + Math.random());
                        
                        img.attr('src', newSrc);
                        container.unmask();
                    } else
                        throw new Error("Rotation failed");
                },
                dataType: 'json'
            });
        });
});