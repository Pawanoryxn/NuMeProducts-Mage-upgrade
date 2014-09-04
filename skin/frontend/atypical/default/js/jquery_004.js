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