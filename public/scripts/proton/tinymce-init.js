$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.tinymce build');
    
    proton.tinymce.build();
});

proton.tinymce = {
	build: function () {
		tinymce.init({
			selector: "#tinymce-basic"
		});

		tinymce.init({
			selector: "#tinymce-no-fileman",
		    plugins: [
		        "advlist autolink lists link image charmap print preview anchor",
		        "searchreplace visualblocks code fullscreen",
		        "insertdatetime media table contextmenu paste"
		    ],
		    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	}
}