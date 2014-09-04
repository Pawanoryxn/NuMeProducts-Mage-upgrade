jQuery(document).ready(function(){
	var v = jQuery("#onepagecheckout_orderform").validate({
		//errorClass: "error",
		onkeyup: function (element, event) {
			if (event.which === 9 && this.elementValue(element) === "") {
				return;
			} else {
				this.element(element);
			}
		},
		errorPlacement: function(error, element) {
			var container = jQuery('<div />');
			container.addClass('invalid-tooltip');
			error.insertAfter(element);
			error.wrap(container);
			//jQuery("<div class='errorImage'></div>").insertAfter(error);
		}
	});
	jQuery("#billing\\:firstname").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "please enter a first name"
			}
		});
	});
	jQuery("#billing\\:lastname").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
	jQuery("#billing\\:street1").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
	jQuery("#billing\\:country_id").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
	jQuery("#billing\\:region_id").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
	jQuery("#billing\\:postcode").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
	jQuery("#billing\\:city").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
	jQuery("#billing\\:telephone").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
	jQuery("#billing\\:email").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
	jQuery("#paypal_direct_cc_type").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
		jQuery("#paypal_direct_cc_number").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
		jQuery("#paypal_direct_expiration").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
		jQuery("#paypal_direct_expiration_yr").each(function() {
		jQuery(this).rules('add', {
				required: true,
				messages: {
				required:  "*required"
			}
		});
	});
	jQuery(".continue").click(function() {
		if (v.form()) {
			jQuery('.flexslider').flexslider(2);
		}
	});
});