jQuery(document).ready(function() {

	/* MAIN MENU */
	jQuery('#main-menu > li:has(ul.sub-menu)').addClass('parent');
	jQuery('ul.sub-menu > li:has(ul.sub-menu) > a').addClass('parent');

	jQuery('#menu-toggle').click(function() {
		jQuery('#main-menu').slideToggle(300);
		return false;
	});

	jQuery(window).resize(function() {
		if (jQuery(window).width() > 700) {
			jQuery('#main-menu').removeAttr('style');
		}
	});

});