/* MAIN MENU (submenu slide and setting up of a select box on small screen)*/
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
