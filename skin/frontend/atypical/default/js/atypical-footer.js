jQuery(document).ready(function() {
	jQuery("#nav-main").delay(500).fadeIn('slow');
});

jQuery(window).scroll(function(){
		if (jQuery(this).scrollTop() > 515) {
			jQuery('#socail-block').addClass('socail-block-fix');
		} else {
			jQuery('#socail-block').removeClass('socail-block-fix');
		}
});

jQuery(document).ready(function(){
	jQuery(".menu-item-block").click(function(){
		jQuery(".mailmenu-rightsite").toggle();
	});
});

jQuery(document).ready(function(){
	//jQuery(".sl-slider-wrapper").hide();
	jQuery(".slidingDiv").hide();
	jQuery(".show_hide").show();
	jQuery('.show_hide').click(function(){
		if(jQuery('.flexslider-blank').hasClass('isUp')){
			jQuery('.flexslider-blank').animate({height: "360px"}, 800);
			jQuery('.flexslider-blank').removeClass('isUp');
		} else {
			jQuery('.flexslider-blank').animate({height: "0px"}, 800);
			jQuery('.flexslider-blank').addClass('isUp');
		}
		jQuery(".slidingDiv").slideToggle(800);
		jQuery(".sl-slider-wrapper").slideToggle(800);
	});
	jQuery('.shop-parent').click(function(){
		 jQuery('.shop-parent ul').toggle();
	});
	jQuery('.account-parent').click(function(){
		 jQuery('.account-parent ul').toggle();
	});
});

(function(jQuery) {

	jQuery.fn.glowNav = function(options) {

		options = jQuery.extend({  
			extra : 70,
			overlap : 42,  
			speed : 600,  
			reset : 1000,  
			//color : '#0b2b61',  
			easing : 'easeOutExpo'  
		}, options); 
		
		return this.each(function() {
				
			var menu = jQuery(this),
				currentPageItem = jQuery('.current-menu-item', menu),
				glow,
				reset;
				
			jQuery('<li id="glow"></li>').css({
				width : currentPageItem.outerWidth() + options.extra,
				height : currentPageItem.outerHeight() + options.overlap,
				//left : currentPageItem.position().left - options.extra / 2,
				//top : currentPageItem.position().top - options.overlap / 2,
				//backgroundColor : options.color
			}).appendTo(this);
			
			glow = jQuery('#glow', menu);
			 
			jQuery('li:not(#glow)', menu).hover(function() {
				// mouse over
				clearTimeout(reset);
				glow.animate(
					{
						left : jQuery(this).position().left - options.extra / 2,
						width : jQuery(this).width() + options.extra
					},
					{
						duration : options.speed, 
						easing : options.easing,
						queue : false
					}
				);
			}, function() {
				//mouse out				
				reset = setTimeout(function() {
					glow.animate({
						width : currentPageItem.outerWidth() + options.extra,
						left : currentPageItem.position().left - options.extra / 2
					}, options.speed)
				}, options.reset);
						
			});
			
		});   // end each
		
	};
	
})(jQuery);

jQuery('.menu').glowNav();