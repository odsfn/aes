/**
 * This function is very usefull when you are loading yii-form or widget by ajax
 * request. 
 * 
 * This function acts in the same way as jQuery.load. It will remove head tags, 
 * and scripts that already were loaded from response.
 * 
 * @see jQuery.load
 * 
 * @todo
 *  - Move head tags to the head if any ( link first of all )
 *  - Provide settigns for scripts and css files that should be include any way
 *  - Process scripts even when *selector* is set
 */
jQuery.fn.smartLoad = function( url, params, callback ) {
	if ( typeof url !== "string" && _load ) {
		return _load.apply( this, arguments );
	}

	var selector, type, response,
		self = this,
		off = url.indexOf(" ");

	if ( off >= 0 ) {
		selector = jQuery.trim( url.slice( off ) );
		url = url.slice( 0, off );
	}

	// If it's a function
	if ( jQuery.isFunction( params ) ) {

		// We assume that it's the callback
		callback = params;
		params = undefined;

	// Otherwise, build a param string
	} else if ( params && typeof params === "object" ) {
		type = "POST";
	}

	// If we have elements to modify, make the request
	if ( self.length > 0 ) {
		jQuery.ajax({
			url: url,

			// if "type" variable is undefined, then "GET" method will be used
			type: type,
			dataType: "html",
			data: params
		}).done(function( responseText ) {

			// Save response for use in complete callback
			response = arguments;

                        var loaded = jQuery.parseHTML( responseText, true);
                        //remove scripts that already have been loaded
                        $.each(loaded, function(index, item) {
                            var $item = $(item);
                            
                            var tagName = $item.prop('tagName');
                            if(!tagName) return;
                            tagName = tagName.toLowerCase();
                            
                            if (tagName === 'script') {

                                var src = $item.attr('src');
                                if (!src) {
                                    return;
                                }

                                if( $(document).find('script[src="' + src + '"]').length > 0 )
                                    loaded.splice(index, 1);
                                
                            } else if (tagName === 'link' || tagName === 'meta') {
                                loaded.splice(index, 1);
                            }
                            
                        });

			self.html( selector ?

				// If a selector was specified, locate the right elements in a dummy div
				// Exclude scripts to avoid IE 'Permission Denied' errors
				jQuery("<div>").append( jQuery.parseHTML( responseText ) ).find( selector ) :

				// Otherwise use the full result
				loaded );

		}).complete( callback && function( jqXHR, status ) {
			self.each( callback, response || [ jqXHR.responseText, status, jqXHR ] );
		});
	}

	return this;
};