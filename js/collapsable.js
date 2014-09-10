(function($){

	function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1);
		var sURLVariables = sPageURL.split('&');
		for (var i = 0; i < sURLVariables.length; i++) {
			var sParameterName = sURLVariables[i].split('=');
			if (sParameterName[0] == sParam) {
				return sParameterName[1];
			}
		}
	}

	function getCookie(cname) {
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1);
			if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
		}
		return "";
	}

	function setCookie( cname, cvalue, exdays ) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+d.toGMTString();
		document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	var data   = {};
	var postID = getUrlParameter( 'post' );
	var cookie = getCookie( "cmb-collapsable-" + postID );

	if ( cookie ) {
		data = JSON.parse( cookie );
	}

	var Collapsable = function( fieldEl ) {

		var t = this;
		t.fieldEl = fieldEl;

		t.init = function() {

			if ( t.fieldEl.data( 'cmb-collapsable-initialized' ) ) {
				return;
			}

			t.fieldContent = t.fieldEl.find( '.cmb_metabox' );
			t.toggleButton = $('<button class="cmb-collapse-field" title="collapse field"><div class="dashicons dashicons-minus"></div></button>');
			t.fieldID      = t.fieldEl.closest( '.field' ).attr( 'id' );

			t.initTitle();

			t.fieldContent.before( t.toggleButton );
			t.toggleButton.click( t.toggleField );

			// Show/hide based on cookie data
			if ( data.hasOwnProperty( t.fieldID ) ) {
				if ( data[ t.fieldID ][ t.fieldEl.index() ] ) {
					t.fieldContent.slideToggle( 100, function() {
						t.updateStatus();
					} );
				}
			}

			t.updateStatus();

			t.fieldEl.data( 'cmb-collapsable-initialized', true );

		}

		t.initTitle = function() {

			t.collapseTitleContainer     = $('<div class="cmb-collapse-title"></div>');
			t.collapseTitle              = $('<h4></h4>');
			t.collapseTitleEditButton    = $('<button class="button button-small cmb-collapse-title-edit"><span class="dashicons dashicons-edit"></span></button>')
			t.collapseTitleEditField     = $('<input type="text"/>');
			t.collapseTitleEditFieldSave = $('<button class="button button-small cmb-collapse-title-edit-save-button">Update</button>');
			t.collapseTitleHiddenField   = t.fieldEl.find( 'input.cmb-collapsable-title' );

			// Update the title with the stored value.
			t.collapseTitle.html( t.collapseTitleHiddenField.val() );

			// Insert 'title heading' & 'edit title button' elements.
			t.collapseTitle.appendTo( t.collapseTitleContainer );
			t.collapseTitleEditButton.appendTo( t.collapseTitleContainer );

			// Insert & hide 'title edit field' & 'title edit field update button' elements.
			t.collapseTitleEditField.appendTo( t.collapseTitleContainer ).hide();
			t.collapseTitleEditFieldSave.appendTo( t.collapseTitleContainer ).hide();

			// Show/Hide title edit fields.
			// Update field with current title value.
			t.collapseTitleEditButton.click( function(e) {

				e.preventDefault();

				t.collapseTitle.hide();
				t.collapseTitleEditButton.hide();

				t.collapseTitleEditField.show();
				t.collapseTitleEditFieldSave.show();
				t.collapseTitleEditField.val( t.collapseTitle.html() );

			} );

			// Save edit title field.
			// Show/hide fields
			// Update hidden title field.
			t.collapseTitleEditFieldSave.click( function(e) {
				e.preventDefault()

				t.collapseTitle.show();
				t.collapseTitleEditButton.show();

				t.collapseTitleEditField.hide();
				t.collapseTitleEditFieldSave.hide();
				t.collapseTitle.html( t.collapseTitleEditField.val() );
				t.collapseTitleHiddenField.val( t.collapseTitle.html() );

			} );

			// Insert container.
			t.fieldContent.before( t.collapseTitleContainer );

		}

		t.toggleField = function( e ) {

			e.preventDefault();
			t.toggleButton.blur();

			t.fieldContent.slideToggle( 100, function() {
				t.updateStatus();
			} );

		};

		t.updateStatus = function() {

			if ( ! ( t.fieldID in data ) ) {
				data[t.fieldID] = {};
			}

			if ( t.fieldContent.is( ':visible' ) ) {

				t.fieldEl.removeClass( 'cmb-collapsable-closed' );
				t.fieldEl.addClass( 'cmb-collapsable-open' );
				t.toggleButton.html( '<div class="dashicons dashicons-arrow-up-alt2"></div>' );

				if ( t.fieldEl.index() in data[ t.fieldID ] ) {
					delete data[ t.fieldID ][ t.fieldEl.index() ];
				}

			} else {

				t.fieldEl.removeClass( 'cmb-collapsable-open' );
				t.fieldEl.addClass( 'cmb-collapsable-closed' );
				t.toggleButton.html( '<div class="dashicons dashicons-arrow-down-alt2"></div>' );

				data[ t.fieldID ][ t.fieldEl.index() ] = 'collapsed';

			}

			setCookie( "cmb-collapsable-" + postID, JSON.stringify( data ), 14 );

		};

		t.init();

	}

	CMB.addCallbackForInit( function() {

		var field = jQuery( '.CMB_Group_Field.repeatable.cmb-collapsable' );
		field.find( '> .field-item:not(.hidden)' ).each( function() {
			var collapsable = new Collapsable( $(this) );
		} );

	} );

	CMB.addCallbackForClonedField( 'CMB_Group_Field', function( newT ) {

		if ( ! newT.closest('.field').hasClass( 'cmb-collapsable' ) ) {
			return;
		}

		var instance = new Collapsable( newT );
		var title    = 'New Group';
		var index    = newT.find( '.cmb-collapsable-title' ).attr('name').match( /\[cmb-group-(\d+)\]/ );

		if ( index[1] ) {
			title = 'Group ' + ( parseInt( index[1] ) + 1 );
		}

		instance.collapseTitle.html( title );
		instance.collapseTitleHiddenField.val( title );

	} );

	CMB.addCallbackForDeletedField( 'CMB_Group_Field', function( field ) {

		if ( ! field.closest('.field').hasClass( 'cmb-collapsable' ) ) {
			return;
		}

		var index = field.index();
		var id    = field.closest( '.field' ).attr( 'id' );

		if ( id in data ) {
			if ( index in data[ id ] ) {
				delete data[ id ][ index ];
			}
		}

	} );


})(this.jQuery);
