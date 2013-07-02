/*
 * SLSV - Plugins options
 *
 */

(function ($, Drupal, window, document, undefined) {

    $(document).ready(function() {

        // Fix Drupal toolbar when jQuery is 1.8.2
        if(window.jQuery && window.jQuery.fn.jquery == '1.8.2') {
            jQuery.fn._outerHeight = jQuery.fn.outerHeight;
            jQuery.fn.outerHeight = function() {
                return jQuery(this)._outerHeight(false);
                return lastDisplaced.offset().top+lastDisplaced.outerHeight().val(23);
            };
        }


        // Tooltips
        if( $.fn.tooltip() ) {
	    $('a[data-toggle="tooltip"]').tooltip();
	}

        // Popover
        if( $.fn.popover() ) {
            $("a[data-toggle=popover]").popover()
                .click(function(e) {
                    e.preventDefault();
                });
        }


        // Responsive toolbar height
        var $toolbar = $('#toolbar');

        if ( $toolbar.length ){
            // Change height dinamically
            var $window = $(window);

            function checkWidth() {
                var $windowsize = $window.width();
                var $bodyPadding = 0;

                if ($windowsize > 1105) {
                    $bodyPadding = 73;
                }
                else if ($windowsize < 960) {
                    $bodyPadding = 0;
	        }
                else {
                    $bodyPadding = 65;
                };

                var $newPadding = $toolbar.outerHeight()+$bodyPadding;
                $('body').css('padding-top', $newPadding);
                $('.navbar-fixed-top').css('top',$toolbar.outerHeight());
            }
            // Execute on load
            checkWidth();
            // Bind event listener
            $(window).resize(checkWidth);
        }


        // Dropdowns
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


        /*
         * Admin menu
         * (submenu slide and setting up of a select box on small screen)
         */
        (function() {

            // ul to select
            var $mainMenu = $('.toolbar-menu').children('#toolbar-menu');
            var optionsList = '<option value="" selected>Barra de Administración...</option>';
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

    });

})(jQuery, Drupal, this, this.document);
