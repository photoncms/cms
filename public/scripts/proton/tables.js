$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.tables build');
    
    proton.tables.build();
});

proton.tables = {
	build: function () {
		// Data Tables
		$('#tableSortable').dataTable(); 
		$('.dataTables_wrapper').find('input, select').addClass('form-control');
		$('.dataTables_wrapper').find('input').attr('placeholder', 'Quick Search');

		$('.dataTables_wrapper select').select2();
	}
}