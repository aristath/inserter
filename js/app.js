/* global inserterAppEl, inserterAppData */
var Inserter = {

	/**
	 * A collection of methods for posts.
	 *
	 * @since 1.0.0
	 */
	Post: {

		/**
		 * Render a post.
		 *
		 * @since 1.0.0
		 * @param {Object} args - The arguments.
		 * @param {int}    args.id - The post-ID.
		 * @param {Array}  args - An array of the views we want to render.
		 * @param {Object} args - The arguments we need to render the view.
		 * @param {string} args.template - The underscode.js template to use.
		 * @param {string} args.element - The DOM element we'll use to render the template.
		 * @param {string} args.watch - The attribute that can trigger a view re-render on change.
		 * @returns {void}
		 */
		render: function( args ) {
			var self = this,
				post = new wp.api.models.Post( { id: args.id } ),
				view,
				postView;

			post.fetch().done( function() {
				view     = Backbone.View.extend( Inserter.viewArgs( { template: args.template } ) );
				postView = new view( { model: post } );

				// Get additional data.
				if ( args.data ) {
					_.each( args.data, function( val, key ) {
						post.set( { key: val } );
					} );
				}

				// Get author data.
				post.getAuthorUser().done( function( author ) {
					post.set( { author: author.attributes } );
				} );

				// Get Featured Media data.
				post.getFeaturedMedia().done( function( image ) {
					post.set( { featured_media: image.attributes } );
				} );

				// Add the HTML.
				jQuery( args.element ).html( postView.render().el );

				// Listen for changes to the post object and re-render the view.
				if ( args.watch ) {
					_.each( args.watch, function( watch ) {

						postView.listenTo( post, 'change:' + watch, function() {
							jQuery( args.element ).html( postView.render().el );
						} );
					} );
				}
			} );
		}
	},

	/**
	 * Get the view args.
	 *
	 * @since 1.0.0
	 * @param {Object} args - The arguments.
	 * @param {string} args.template - The underscore.js template.
	 * @returns {Object}
	 */
	viewArgs: function( args ) {
		return {
			template: wp.template( args.template ),
			render: function() {
				this.el.innerHTML = this.template( this.model.toJSON() );
				return this;
			}
		};
	}
};

var template;
_.each( inserterAppData, function( data, id ) {
	if ( data.inserterDataType && 'REST' === data.inserterDataType ) {
		if ( data.id ) {
			Inserter.Post.render( {
				id: data.id,
				template: id,
				element: inserterAppEl[ id ],
				watch: [ 'featured_media', 'author' ],
				data: data
			} );
		}
		return;
	}
	template = wp.template( id );
	jQuery( inserterAppEl[ id ] ).html( template( data ) );
} );
