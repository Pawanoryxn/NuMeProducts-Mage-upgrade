jQuery(document).ready(function($) {

    jQuery = $.noConflict();

    var url = window.location.href;

    $('ul.nav li a').filter(function() {
	    return this.href == url;
	}).addClass('active');

	$('ul#main-menu li a').filter(function() {
	    return this.href == url;
	}).addClass('active');

});

$(function (){
        $mage = jQuery.noConflict();
})();

function resetTimer(){
	clearTimeout(timeOut);
        console.log('time reset');
	timeOut = setTimeout(function(){ doRedirect();}, 300000);
};


function doRedirect(){
	window.location = "http://numeproducts.com/checkout/cart/";
};