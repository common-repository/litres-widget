( function() {
    tinymce.PluginManager.add( 'litres_button', function( editor, url ) {

        // Add a button that opens a window
        editor.addButton( 'litres_button_button_key', {
			title : 'LitRes Widget',
            text: false,
			image : url + '/litres.png',
            
            onclick: function() {
                // Open window
                editor.windowManager.open( {
                    title: 'LitRes Widget',
                    body: [
					{type: 'textbox', name: 'author', label: 'Автор книги:', 'minWidth': 380},
                    {type: 'textbox', name: 'title', label: 'Название книги:', 'minWidth': 380},
					],
                    onsubmit: function( e ) {
                        // Insert content when the window form is submitted
                        editor.insertContent('[litres author=\'' + e.data.author + '\'' + ' title=\'' + e.data.title + '\']');
                    }

                } );
            }

        } );

    } );

} )();