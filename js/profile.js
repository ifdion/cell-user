(function ($) {
	"use strict";
	$(function () {
		// show hide on init
		if ($('[data-show-on]').length > 0) {
			$('[data-show-on]').each(function(index){
				var trigger = $(this).attr('data-show-on');
				var trigger_object = $('[name="'+trigger+'"]');
				trigger_object.addClass('trigger');
				if (trigger_object.is(":checked")) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		}

		// show hide on change
		$('.trigger').live('change',function(){
			var target = $(this).attr('name');
			var target_object = $('[data-show-on="'+target+'"]');
			if ($(this).is(":checked")) {
				target_object.show();
			} else {
				target_object.hide();
			}
		});
	});
}(jQuery));