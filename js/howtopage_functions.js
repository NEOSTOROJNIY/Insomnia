$(function() {



	$('#manualdata_menu li').click(function() {

			$('#manualdata_menu li').each(function() {
				$(this).removeClass('active');
			});

			$(this).addClass('active');

			switch(this.id) {

				case 'mu_testing':
					showManualContent( function() { $('#manual_user_testing').fadeIn(150); } );
					break;

				case 'mu_reporting':
					showManualContent( function() { $('#manual_user_reporting').fadeIn(150); } );
					break;

				case 'mu_statistics':
					showManualContent( function() { $('#manual_user_statistics').fadeIn(150); } );
					break;

				case 'mt_xmlstructure':
					showManualContent( function() { $('#manual_technical_xmlstructure').fadeIn(150); } );
					break;

				case 'ms_sqlinjection':
					showManualContent( function() { $('#manual_specification_sqlinjection').fadeIn(150); } );
					break;

				case 'ms_xss':
					showManualContent( function() { $('#manual_specification_xss').fadeIn(150); } );
					break;

				default:
					break;
			}
	});

});

function showManualContent(fn) {
	$('#manualdata_content div:not(:hidden)').each(function() {
		$(this).fadeOut(150, fn);
	});
}