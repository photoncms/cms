$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.formComponents build');
    
    proton.formComponents.build();
});

proton.formComponents = {
	build: function () {
		// Initiate Select2 Plugin
		!$('#testFunction').select2 || proton.formComponents.select2();

		// Date Time Picker
		!$('#testFunction').datetimepicker || proton.formComponents.datetimepicker();

		// Input Fields
		!$('input').length || proton.formComponents.inputFields();

		// Masked Input
		!$('#testFunction').mask || proton.formComponents.maskedInput();

		// Autotab Input
		!$('#testFunction').autotab_magic || proton.formComponents.autotab();

		// Text Areas
		!$('#basic-text-area').length || proton.formComponents.textAreas();
		
		//tinymce
		!$('.tinymce').length || proton.formComponents.tinymce();

		!$('#testFunction').uniform || $('input[type="radio"], input[type="checkbox"]').uniform();

		!verboseBuild || console.log('            proton.formComponents build DONE');
	},
	select2 : function () {
		!verboseBuild || console.log('            proton.formComponents.select2()');
		
        var opts=$("#source").html(), opts2="<option></option>"+opts;
        $("select.populate").each(function() { var e=$(this); e.html(e.hasClass("placeholder")?opts2:opts); });

        $('.select2').select2({ placeholder: "Select a State", maximumSelectionSize: 6 });
    	$('.select2-bootstrap-prepend :checkbox').on( "click", function() {
    		$(this).parents('.form-group').find('select').select2("enable", this.checked );
    	});
	},
	datetimepicker : function () {
		!verboseBuild || console.log('            proton.formComponents.datetimepicker()');

		$('.datetimepicker-default').datetimepicker();
		$('.datetimepicker-month').datetimepicker({
			format : 'yyyy-mm-dd',
			minView : 'month',
			maxView : 'month'
		});
		$('.datetimepicker-range').datetimepicker({
			format : 'yyyy-mm-dd',
			minView : 'month',
			maxView : 'month',
			autoclose : true
		});
	},
	inputFields : function () {
		!verboseBuild || console.log('            proton.formComponents.inputFields()');

		!$('#testFunction').charCount || $('.input-counter').charCount({
		    allowed: 30,
		    warning: 20,
		    counterText: 'Characters left: ',
		    cssWarning: 'text-warning',
		    cssExceeded: 'text-danger',
		    css: 'character-counter'
		});
	},
	maskedInput : function () {
		!verboseBuild || console.log('            proton.formComponents.maskedInput()');

		$.mask.definitions['^']='[1-9]';
		$('#inputDate').mask('99/99/9999');
		$('#inputPhone').mask('(999) 999-9999');
		$('#inputPhoneExit').mask("(999) 999-9999? x99999");
		$('#inputIntPhone').mask("+33 (999) (999-9999)");
		$('#inputTaxID').mask("99-9999999");
		$('#inputSSN').mask("999-99-9999");
		$('#inputProductKey').mask("a*-999-a999");
		$('#inputEyeScript').mask("~9.99 ~9.99 999");
		$('#inputPurchaseOrder').mask("aaa-999-***");
		$('#inputPercent').mask("99%");
		$('#inputCharacterRanges').mask("^9%");
		$('#inputMaskEscaping').mask("99-999 ?a");
	},
	autotab : function () {
		!verboseBuild || console.log('            proton.formComponents.autotab()');

		$('.inputPhone').autotab({format: 'numeric'});
		$('.inputSsn').autotab({format: 'numeric'});
		$('.inputText').autotab({format: 'text'});
		$('.inputAlpha').autotab({format: 'alpha'});
		$('.inputUpper').autotab({format: 'alphanumeric', uppercase: true});
		$('.inputRegex').autotab({format: 'custom', pattern: '[^0-9\.]'});
	},
	textAreas : function () {
		!verboseBuild || console.log('            proton.formComponents.textAreas()');
		
		$(".auto-resize").keyup(function(){
		    autoGrowField($(this).get(0)); 
		});
		function autoGrowField(f, max) {
		    /* Default max height */
		    max = (typeof max == 'undefined') ? 1000 : max;
		    /* Don't let it grow over the max height */
		    if (f.scrollHeight > max) {
		        /* Add the scrollbar back and bail */
		        if (f.style.overflowY != 'scroll') {
		            f.style.overflowY = 'scroll';
		        }
		        return;
		    }
		    /* Make sure element does not have scroll bar to prevent jumpy-ness */
		    if (f.style.overflowY != 'hidden') {
		        f.style.overflowY = 'hidden';
		    }
		    /* Now adjust the height */
		    var scrollH = f.scrollHeight;
		    // console.log(scrollH);
		    if( scrollH > f.style.height.replace(/[^0-9]/g,'') ){
		        f.style.height = scrollH+20+'px';
		    }
		}

		$("#text-area-character-counter").charCount({
		    counterText: 'Characters left: ',
		    cssWarning: 'text-warning',
		    cssExceeded: 'text-danger',
		    css: 'character-counter'
		});

		$("#text-area-word-counter").textareaCounter();

		ltIE9 || $('#wysiwyg').summernote({
			height: 150,
			toolbar: [
			    //['style', ['style']], // no style button
			    ['style', ['bold', 'italic', 'underline', 'clear']],
			    ['fontsize', ['fontsize']],
			    ['color', ['color']],
			    ['para', ['ul', 'ol', 'paragraph']],
			    ['height', ['height']],
			    //['insert', ['picture', 'link']], // no insert buttons
			    //['table', ['table']], // no table button
			    //['help', ['help']] //no help button
			]
		});

		ltIE9 || $('#wysiwyg-full').summernote({
			height: 150
		});
	},
	tinymce: function () {
		!verboseBuild || console.log('            proton.formComponents.tinymce()');
		
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