/*
 * SLSV - Plugins options
 *
 */

;(function ($, Drupal, window, document, undefined) {

    $(document).ready(function() {

        // Fix Drupal toolbar when jQuery is 1.8.2
        if(jQuery && jQuery.fn.jquery == "1.8.2") {
            jQuery.fn._outerHeight = jQuery.fn.outerHeight;
            jQuery.fn.outerHeight = function() {
                return jQuery(this)._outerHeight(false);
            };
        }


        // Tooltips
        if( $.fn.tooltip() ) {
	    $('[rel="tooltip"]').tooltip();
	}


	// Accordion
	$( '.accordion' ).accordion();


	// Flexslider
	if( $.fn.flexslider ) {
	    $('.slider-wrap .slider').waitForImages(function() {
		$( this ).flexslider({
                    animation: 'slide', 
		    slideshowSpeed: 4000
		    //directionNav: false
		});
	    });

	    $( '.portfolio .slider' ).waitForImages(function() {
		$( this ).flexslider({
		    controlNav: false
		});
	    });

	}


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

        var $dropdowns = $('li.dropdown'); // Specifying the element is faster for older browsers
        /**
         * Mouse events
         *
         * @description Mimic hoverIntent plugin by waiting for the mouse to 'settle' within the target before triggering
         */
        $dropdowns
            .on('mouseover', function() { // Mouseenter (used with .hover()) does not trigger when user enters from outside document window
                var $this = $(this);
                if ($this.prop('hoverTimeout')) {
                    $this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
                }
                $this.prop('hoverIntent', setTimeout(function() {
                    $this.addClass('open');
                }, 250));
            })
            .on('mouseleave', function() {
                var $this = $(this);
                if ($this.prop('hoverIntent')) {
                    $this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
                }
                $this.prop('hoverTimeout', setTimeout(function() {
                    $this.removeClass('open');
                }, 250));
            });
        /**
         * Touch events
         *
         * @description Support click to open if we're dealing with a touchscreen
         */
        if ('ontouchstart' in document.documentElement) {
            $dropdowns.each(function() {
                var $this = $(this);
                this.addEventListener('touchstart', function(e) {
                    if (e.touches.length === 1) {
                        // Prevent touch events within dropdown bubbling down to document
                        e.stopPropagation();
                        // Toggle hover
                        if (!$this.hasClass('open')) {
                            // Prevent link on first touch
                            if (e.target === this || e.target.parentNode === this) {
                                e.preventDefault();
                            }
                            // Hide other open dropdowns
                            $dropdowns.removeClass('open');
                            $this.addClass('open');

                            // Hide dropdown on touch outside
                            document.addEventListener('touchstart', closeDropdown = function(e) {
                                e.stopPropagation();

                                $this.removeClass('open');
                                document.removeEventListener('touchstart', closeDropdown);
                            });
                        }
                    }
                }, false);
            });
        }
    });

}) (jQuery, Drupal, this, this.document);
