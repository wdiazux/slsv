/*
 * SLSV - Plugins options
 *
 */  

;(function( $, window, document, undefined ) {

    $(document).ready(function() {


        // Tooltips
        if( $.fn.tooltip() ) {
	    $('[rel="tooltip"]').tooltip();
	}

	// Accordion
	$( '.accordion' ).accordion();

	// Tweets Widget
	if( $.fn.tweet ) {
	    $('.tweet').tweet({
                avatar_size: 32,
                count: 2,
                query: "#slsv",
                loading_text: "Cargando tuits..."
	    });
	}

        // Flickr Feed Widget
        if( $.fn.jflickrfeed ) {
	    $('.flickr-stream ul').jflickrfeed({
	        qstrings: {
		    id: '77307054@N00', tags:'slsv' 
	        }, 
	        limit: 6,
	        itemTemplate:
                '<li>' +
                    '<a href="{{link}}" title="{{title}}" target="_blank">' +
                        '<img src="{{image_s}}" alt="{{title}}" />' +
                    '</a>' +
                '</li>'
	    });
        }

    });


    /*
     * MAIN MENU
     * (submenu slide and setting up of a select box on small screen)
     */
    (function() {

        // ul to select
        var $mainMenu = $('#mainMenu').children('ul');
        var optionsList = '<option value="" selected>Navegar...</option>';
        $mainMenu.find('li').each(function() {
            var $this   = $(this),
            $anchor = $this.children('a'),
            depth   = $this.parents('ul').length - 1,
            indent  = '';

            if( depth ) {
                while( depth > 0 ) {
                    indent += ' - ';
                    depth--;
                }
            }

            optionsList += '<option value="' + $anchor.attr('href') + '">' + indent + ' ' + $anchor.text() + '</option>';
        }).end().after('<select class="selectpicker responsive-nav" data-size="auto">' + optionsList + '</select>');

        $('.responsive-nav').on('change', function() {
            window.location = $(this).val();
        }); 
    })();

}) (jQuery, window, document);
