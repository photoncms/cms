$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.imageGallery build');
    
    proton.imageGallery.build();
});

proton.imageGallery = {
	build: function () {
		// Initiate imageGallery events
		proton.imageGallery.events();

	    Dropzone.options.imageGalleryDropzone = false; // Prevent Dropzone from auto discovering this element
	    var dropZoneTemplate = $.get('./gallery-dropzone-template.html', function(template) {
	    	proton.imageGallery.makeDropzone(template); // Make Dropzone after loading template html
	    })
	    .fail(function() {
	        alert( "Image Gallery Error: could not load gallery html template" );
	    });

		!verboseBuild || console.log('            proton.imageGallery build DONE');

	},
	events: function () {
		!verboseBuild || console.log('            proton.imageGallery binding events');

		$('.gallery-uploader').on('click', '.add', function(event) {
			event.preventDefault();
			$(this).fadeOut(75, function () {
				$(this).parents('.gallery-uploader').toggleClass('active');
				$(this).siblings('.add').fadeIn(150);
			});
		});

		$('.gallery-uploader').on('click', '.remove-item', function(event) {
			event.preventDefault();
			$(this).parents('.dz-preview').fadeOut(250, function () {
				$(this).remove();
			});
		});

		$('.gallery-uploader').on('click', '.trash-item, .remove-cancel', function(event) {
			event.preventDefault();
			$(this).parents('.dz-preview').find('.controls').fadeToggle('150');
		});
		
		$('.gallery-uploader').on('click', '.edit-item', function(event) {
			event.preventDefault();
			$imagePreview = $(this).parents('.dz-preview');
			$('.editing-item').removeClass('editing-item');
			$imagePreview.addClass('editing-item');
			$('#edit-image-modal .modal-thumbnail').attr({
				src: $imagePreview.find('.dz-details img').attr('src')
			});
			$('#edit-image-modal #image-caption').val($imagePreview.find('.dz-filename span').text());
			$('#edit-image-modal').modal();
		});
		$('#edit-image-modal').on('click', '.btn-success', function(event) {
			$('.editing-item .dz-filename span').text($('#edit-image-modal #image-caption').val());
		});
	},
	makeDropzone: function (template) {
		!verboseBuild || console.log('            proton.imageGallery.makeDropzone()');
		
		$('#imageGalleryDropzone').dropzone({
			paramName: "file", // The name that will be used to transfer the file
			maxFilesize: 2, // MB
			acceptedFiles: '.jpg,.jpeg,.png,.gif',
			uploadMultiple: true,
			previewsContainer: '.gallery-container',
			previewTemplate: template,
			init: function() {
				this.on("addedfile", function(file) { 
					// alert("Added file."); 
				});
			}
		});
	}
}