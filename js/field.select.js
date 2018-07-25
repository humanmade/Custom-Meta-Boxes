var cmbSelectInit = function() {

	jQuery( '.cmb_select' ).each( function() {

		var el = jQuery( this );
		var fieldID = el.attr( 'data-field-id' ); // JS Friendly ID

		// If fieldID is set
		// If fieldID options exist
		// If Element is not hidden template field.
		// If elemnt has not already been initialized.
		if ( fieldID && window[fieldID] && el.is( ':visible' ) && ! el.hasClass( 'select2-added' ) ) {

			// Get options for this field.
			var SelectData = window[fieldID];
			options = SelectData.options;

			if ( typeof SelectData.ajax_url !== 'undefined' && typeof SelectData.ajax_data !== 'undefined' ) {

				options.ajax = {
					url: SelectData.ajax_url,
					type: 'POST',
					dataType: 'json',
					delay: 250,
					data: function( params ) {
						SelectData.ajax_data.query.s = params.term;
						SelectData.ajax_data.query.paged = params.page;
						return SelectData.ajax_data;
					},
					processResults: function( data, params ) {
						var postsPerPage = SelectData.ajax_data.query.posts_per_page = ( 'posts_per_page' in SelectData.ajax_data.query ) ? SelectData.ajax_data.query.posts_per_page : ( 'showposts' in SelectData.ajax_data.query ) ? SelectData.ajax_data.query.showposts : 10;
						return {
							results: data.posts,
							pagination: {
								more: ( params.page * postsPerPage ) < data.total
							}
						};
					}
				}
			}

			el.addClass( 'select2-added' ).select2( options );

		}

	})

};

// Hook this in for all the required fields.
CMB.addCallbackForInit( cmbSelectInit );
CMB.addCallbackForClonedField( 'CMB_Select', cmbSelectInit );
CMB.addCallbackForClonedField( 'CMB_Post_Select', cmbSelectInit );
CMB.addCallbackForClonedField( 'CMB_Taxonomy', cmbSelectInit );
