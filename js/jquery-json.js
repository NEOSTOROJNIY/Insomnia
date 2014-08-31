/*
 * @author			Emanuel Vitzthum
 * @copyright		© 2012 jQuery SDK v1.4
 * @info			http://www.jquerysdk.com
 *
 * @license			Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) and GPL (http://www.gnu.org/licenses/gpl.html)
 *
 * @plugin			jQuery.json
 */

(function( jQuery, undefined ){

	var support = jQuery.support.JSON = false,
		// expose fromJSON according to the API standard: .toTYPE and .fromTYPE
		fromJSON = function(){
			jQuery.fromJSON = jQuery.parseJSON;
		};

	try{
		support = !!(JSON.stringify("{}"));

		jQuery.plugin( "jQuery.json", function( $, undefined ){
			$.toJSON = function( mixed_val ){
				return JSON.stringify(mixed_val);
			};
			fromJSON();
		});
	}
	catch( error ){
		jQuery.plugin( "jQuery.json", "jQuery.json.fix", fromJSON);
	}

})(jQuery);