
jQuery(document).ready(function($) {
    if( stella_cmb_ids ){
        stella_cmb_ids = JSON.parse( stella_cmb_ids );

        function showElements( passedCode ) {
            for( var code in stella_cmb_ids ) {
                if( code == passedCode ){
                    for( var i in stella_cmb_ids[code] ){
                        // show elements if lang match with passed code and metabox checked in screen options
                        if( $( '#' + stella_cmb_ids['default'][i] + '-hide').is(':checked') ){
                            $( '#' + stella_cmb_ids[code][i] ).css( 'display', 'block' );
                        }
                    }
                }else{ // hide otherwise
                    for( var i in stella_cmb_ids[code] ){
                        $( '#' + stella_cmb_ids[code][i] ).css( 'display', 'none' );
                    }
                }
            }
        }

        function onTabClick( e ){
            var tabHref = $(e.target).attr('href');
            if( typeof tabHref === "undefined" || tabHref === "#default-lang-editor" ){
                showElements( 'default' );
            }else{
                var code = tabHref.substr( tabHref.length - 2, 2 );
                showElements( code );
            }
        }

        function hideScreenOptionsElements(){
            for( var code in stella_cmb_ids ) {
                if( code != 'default'){
                    for( var i in stella_cmb_ids[code] ){
                        $( 'label[for=' + stella_cmb_ids[code][i] + '-hide]' ).css('display','none');
                    }
                }
            }

        }

        function updateScreenOptionsElements(){
            for( var i in stella_cmb_ids['default'] ) {
                $('#' + stella_cmb_ids['default'][i] + '-hide').click( function(e){
                    if($( '#' + stella_cmb_ids['default'][i] + '-hide').is(':checked')){
                       $( '#' + stella_cmb_ids['default'][i]).css('display','block');
                    }else{
                       $( '#' + stella_cmb_ids['default'][i]).css('display','none');
                    }
                });
            }

        }

        $('.ui-tabs-anchor').click( onTabClick );
        showElements( 'default' );
        hideScreenOptionsElements();
        updateScreenOptionsElements();
    }
});

