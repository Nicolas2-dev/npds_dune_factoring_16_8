<?php


global $surlignage, $font_size, $auteur, $groupe;

$tmp .= "
    toolbar : 'image | npds_img npds_gperso npds_gmns npds_gupl',
    setup: function (ed) {
        ed.options.register('tiny_mce_groupe', {
            processor: 'string',
            default: '&groupe=" . $groupe . "'
        });
        ed.on('keydown',function(e) {
            // faisons une 'static' en javascript
            if ( typeof this.counter == 'undefined' ) this.counter = 0;

            // On capte les touches de directions
            if (e.keyCode >= 37 && e.keyCode <= 40) {
                this.counter=0;
                return true;
            }
            // On capte la touche backspace
            if ((e.keyCode == 8) || (e.keyCode == 13)) {
                this.counter=0;
                return true;
            }
            if (this.counter==0) {
                tinymce.activeEditor.formatter.register('wspadformat', {
                inline     : 'span',
                styles     : {'background-color' : '$surlignage', 'font-size' : '$font_size'},
                classes    : '$auteur'
                });
                tinymce.activeEditor.formatter.apply('wspadformat');
                this.counter=1;
            }
        });

        // déplacement dans le RTE via la sourie
        ed.on('mousedown',function(ed, e) {
            this.counter=0;
        });
    },";
