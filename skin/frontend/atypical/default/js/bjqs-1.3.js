/*
 * Basic jQuery Slider plug-in v.1.3
 *
 * http://www.basic-slider.com
 *
 * Authored by John Cobb
 * http://www.johncobb.name
 * @john0514
 *
 * Copyright 2011, John Cobb
 * License: GNU General Public License, version 3 (GPL-3.0)
 * http://www.opensource.org/licenses/gpl-3.0.html
 *
 */

;(function(jQuery) {

    "use strict";

    jQuery.fn.bjqs = function(o) {
        
        // slider default settings
        var defaults        = {

            // w + h to enforce consistency
            width           : 700,
            height          : 300,

            // transition valuess
            animtype        : 'fade',
            animduration    : 450,      // length of transition
            animspeed       : 4000,     // delay between transitions
            automatic       : true,     // enable/disable automatic slide rotation

            // control and marker configuration
            showcontrols    : true,     // enable/disable next + previous UI elements
            centercontrols  : true,     // vertically center controls
            nexttext        : 'Next',   // text/html inside next UI element
            prevtext        : 'Prev',   // text/html inside previous UI element
            showmarkers     : true,     // enable/disable individual slide UI markers
            centermarkers   : true,     // horizontally center markers

            // interaction values
            keyboardnav     : true,     // enable/disable keyboard navigation
            hoverpause      : true,     // enable/disable pause slides on hover

            // presentational options
            usecaptions     : true,     // enable/disable captions using img title attribute
            randomstart     : false,     // start from a random slide
            responsive      : false     // enable responsive behaviour

        };

        // create settings from defauls and user options
        var settings        = jQuery.extend({}, defaults, o);

        // slider elements
        var jQuerywrapper        = this,
            jQueryslider         = jQuerywrapper.find('ul.bjqs'),
            jQueryslides         = jQueryslider.children('li'),

            // control elements
            jQueryc_wrapper      = null,
            jQueryc_fwd          = null,
            jQueryc_prev         = null,

            // marker elements
            jQuerym_wrapper      = null,
            jQuerym_markers      = null,

            // elements for slide animation
            jQuerycanvas         = null,
            jQueryclone_first    = null,
            jQueryclone_last     = null;

        // state management object
        var state           = {
            slidecount      : jQueryslides.length,   // total number of slides
            animating       : false,            // bool: is transition is progress
            paused          : false,            // bool: is the slider paused
            currentslide    : 1,                // current slide being viewed (not 0 based)
            nextslide       : 0,                // slide to view next (not 0 based)
            currentindex    : 0,                // current slide being viewed (0 based)
            nextindex       : 0,                // slide to view next (0 based)
            interval        : null              // interval for automatic rotation
        };

        var responsive      = {
            width           : null,
            height          : null,
            ratio           : null
        };

        // helpful variables
        var vars            = {
            fwd             : 'forward',
            prev            : 'previous'
        };
            
        // run through options and initialise settings
        var init = function() {

            // differentiate slider li from content li
            jQueryslides.addClass('bjqs-slide');

            // conf dimensions, responsive or static
            if( settings.responsive ){
                conf_responsive();
            }
            else{
                conf_static();
            }

            // configurations only avaliable if more than 1 slide
            if( state.slidecount > 1 ){

                // enable random start
                if (settings.randomstart){
                    conf_random();
                }

                // create and show controls
                if( settings.showcontrols ){
                    conf_controls();
                }

                // create and show markers
                if( settings.showmarkers ){
                    conf_markers();
                }

                // enable slidenumboard navigation
                if( settings.keyboardnav ){
                    conf_keynav();
                }

                // enable pause on hover
                if (settings.hoverpause && settings.automatic){
                    conf_hoverpause();
                }

                // conf slide animation
                if (settings.animtype === 'slide'){
                    conf_slide();
                }

            } else {
                // Stop automatic animation, because we only have one slide! 
                settings.automatic = false;
            }

            if(settings.usecaptions){
                conf_captions();
            }

            // TODO: need to accomodate random start for slide transition setting
            if(settings.animtype === 'slide' && !settings.randomstart){
                state.currentindex = 1;
                state.currentslide = 2;
            }

            // slide components are hidden by default, show them now
            jQueryslider.show();
            jQueryslides.eq(state.currentindex).show();

            // Finally, if automatic is set to true, kick off the interval
            if(settings.automatic){
                state.interval = setInterval(function () {
                    go(vars.fwd, false);
                }, settings.animspeed);
            }

        };

        var conf_responsive = function() {

            responsive.width    = jQuerywrapper.outerWidth();
            responsive.ratio    = responsive.width/settings.width,
            responsive.height   = settings.height * responsive.ratio;

            if(settings.animtype === 'fade'){

                // initial setup
                jQueryslides.css({
                    'height'        : settings.height,
                    'width'         : '100%'
                });
                jQueryslides.children('img').css({
                    'height'        : settings.height,
                    'width'         : '100%'
                });
                jQueryslider.css({
                    'height'        : settings.height,
                    'width'         : '100%'
                });
                jQuerywrapper.css({
                    'height'        : settings.height,
                    'max-width'     : settings.width,
                    'position'      : 'relative'
                });

                if(responsive.width < settings.width){

                    jQueryslides.css({
                        'height'        : responsive.height
                    });
                    jQueryslides.children('img').css({
                        'height'        : responsive.height
                    });
                    jQueryslider.css({
                        'height'        : responsive.height
                    });
                    jQuerywrapper.css({
                        'height'        : responsive.height
                    });

                }

                jQuery(window).resize(function() {

                    // calculate and update dimensions
                    responsive.width    = jQuerywrapper.outerWidth();
                    responsive.ratio    = responsive.width/settings.width,
                    responsive.height   = settings.height * responsive.ratio;

                    jQueryslides.css({
                        'height'        : responsive.height
                    });
                    jQueryslides.children('img').css({
                        'height'        : responsive.height
                    });
                    jQueryslider.css({
                        'height'        : responsive.height
                    });
                    jQuerywrapper.css({
                        'height'        : responsive.height
                    });

                });

            }

            if(settings.animtype === 'slide'){

                // initial setup
                jQueryslides.css({
                    'height'        : settings.height,
                    'width'         : settings.width
                });
                jQueryslides.children('img').css({
                    'height'        : settings.height,
                    'width'         : settings.width
                });
                jQueryslider.css({
                    'height'        : settings.height,
                    'width'         : settings.width * settings.slidecount
                });
                jQuerywrapper.css({
                    'height'        : settings.height,
                    'max-width'     : settings.width,
                    'position'      : 'relative'
                });

                if(responsive.width < settings.width){

                    jQueryslides.css({
                        'height'        : responsive.height
                    });
                    jQueryslides.children('img').css({
                        'height'        : responsive.height
                    });
                    jQueryslider.css({
                        'height'        : responsive.height
                    });
                    jQuerywrapper.css({
                        'height'        : responsive.height
                    });

                }

                jQuery(window).resize(function() {

                    // calculate and update dimensions
                    responsive.width    = jQuerywrapper.outerWidth(),
                    responsive.ratio    = responsive.width/settings.width,
                    responsive.height   = settings.height * responsive.ratio;

                    jQueryslides.css({
                        'height'        : responsive.height,
                        'width'         : responsive.width
                    });
                    jQueryslides.children('img').css({
                        'height'        : responsive.height,
                        'width'         : responsive.width
                    });
                    jQueryslider.css({
                        'height'        : responsive.height,
                        'width'         : responsive.width * settings.slidecount
                    });
                    jQuerywrapper.css({
                        'height'        : responsive.height
                    });
                    jQuerycanvas.css({
                        'height'        : responsive.height,
                        'width'         : responsive.width
                    });

                    resize_complete(function(){
                        go(false,state.currentslide);
                    }, 200, "some unique string");

                });

            }

        };

        var resize_complete = (function () {
            
            var timers = {};
            
            return function (callback, ms, uniqueId) {
                if (!uniqueId) {
                    uniqueId = "Don't call this twice without a uniqueId";
                }
                if (timers[uniqueId]) {
                    clearTimeout (timers[uniqueId]);
                }
                timers[uniqueId] = setTimeout(callback, ms);
            };

        })();

        // enforce fixed sizing on slides, slider and wrapper
        var conf_static = function() {

            jQueryslides.css({
                'height'    : settings.height,
                'width'     : settings.width
            });
            jQueryslider.css({
                'height'    : settings.height,
                'width'     : settings.width
            });
            jQuerywrapper.css({
                'height'    : settings.height,
                'width'     : settings.width,
                'position'  : 'relative'
            });

        };

        var conf_slide = function() {

            // create two extra elements which are clones of the first and last slides
            jQueryclone_first    = jQueryslides.eq(0).clone();
            jQueryclone_last     = jQueryslides.eq(state.slidecount-1).clone();

            // add them to the DOM where we need them
            jQueryclone_first.attr({'data-clone' : 'last', 'data-slide' : 0}).appendTo(jQueryslider).show();
            jQueryclone_last.attr({'data-clone' : 'first', 'data-slide' : 0}).prependTo(jQueryslider).show();

            // update the elements object
            jQueryslides             = jQueryslider.children('li');
            state.slidecount    = jQueryslides.length;

            // create a 'canvas' element which is neccessary for the slide animation to work
            jQuerycanvas = jQuery('<div class="bjqs-wrapper"></div>');

            // if the slider is responsive && the calculated width is less than the max width
            if(settings.responsive && (responsive.width < settings.width)){

                jQuerycanvas.css({
                    'width'     : responsive.width,
                    'height'    : responsive.height,
                    'overflow'  : 'hidden',
                    'position'  : 'relative'
                });

                // update the dimensions to the slider to accomodate all the slides side by side
                jQueryslider.css({
                    'width'     : responsive.width * (state.slidecount + 2),
                    'left'      : -responsive.width * state.currentslide
                });

            }
            else {

                jQuerycanvas.css({
                    'width'     : settings.width,
                    'height'    : settings.height,
                    'overflow'  : 'hidden',
                    'position'  : 'relative'
                });

                // update the dimensions to the slider to accomodate all the slides side by side
                jQueryslider.css({
                    'width'     : settings.width * (state.slidecount + 2),
                    'left'      : -settings.width * state.currentslide
                });

            }

            // add some inline styles which will align our slides for left-right sliding
            jQueryslides.css({
                'float'         : 'left',
                'position'      : 'relative',
                'display'       : 'list-item'
            });

            // 'everything.. in it's right place'
            jQuerycanvas.prependTo(jQuerywrapper);
            jQueryslider.appendTo(jQuerycanvas);

        };

        var conf_controls = function() {

            // create the elements for the controls
            jQueryc_wrapper  = jQuery('<ul class="bjqs-controls"></ul>');
            jQueryc_fwd      = jQuery('<li class="bjqs-next"><a href="#" data-direction="'+ vars.fwd +'">' + settings.nexttext + '</a></li>');
            jQueryc_prev     = jQuery('<li class="bjqs-prev"><a href="#" data-direction="'+ vars.prev +'">' + settings.prevtext + '</a></li>');

            // bind click events
            jQueryc_wrapper.on('click','a',function(e){

                e.preventDefault();
                var direction = jQuery(this).attr('data-direction');

                if(!state.animating){

                    if(direction === vars.fwd){
                        go(vars.fwd,false);
                    }

                    if(direction === vars.prev){
                        go(vars.prev,false);
                    }

                }

            });

            // put 'em all together
            jQueryc_prev.appendTo(jQueryc_wrapper);
            jQueryc_fwd.appendTo(jQueryc_wrapper);
            jQueryc_wrapper.appendTo(jQuerywrapper);

            // vertically center the controls
            if (settings.centercontrols) {

                jQueryc_wrapper.addClass('v-centered');

                // calculate offset % for vertical positioning
                var offset_px   = (jQuerywrapper.height() - jQueryc_fwd.children('a').outerHeight()) / 2,
                    ratio       = (offset_px / settings.height) * 100,
                    offset      = ratio + '%';

                jQueryc_fwd.find('a').css('top', offset);
                jQueryc_prev.find('a').css('top', offset);

            }

        };

        var conf_markers = function() {

            // create a wrapper for our markers
            jQuerym_wrapper = jQuery('<ol class="bjqs-markers"></ol>');

            // for every slide, create a marker
            jQuery.each(jQueryslides, function(key, slide){

                var slidenum    = key + 1,
                    gotoslide   = key + 1;
                
                if(settings.animtype === 'slide'){
                    // + 2 to account for clones
                    gotoslide = key + 2;
                }

                var marker = jQuery('<li><a href="#">'+ slidenum +'</a></li>');

                // set the first marker to be active
                if(slidenum === state.currentslide){ marker.addClass('active-marker'); }

                // bind the click event
                marker.on('click','a',function(e){
                    e.preventDefault();
                    if(!state.animating && state.currentslide !== gotoslide){
                        go(false,gotoslide);
                    }
                });

                // add the marker to the wrapper
                marker.appendTo(jQuerym_wrapper);

            });

            jQuerym_wrapper.appendTo(jQuerywrapper);
            jQuerym_markers = jQuerym_wrapper.find('li');

            // center the markers
            if (settings.centermarkers) {
                jQuerym_wrapper.addClass('h-centered');
                var offset = (settings.width - jQuerym_wrapper.width()) / 2;
                jQuerym_wrapper.css('left', offset);
            }

        };

        var conf_keynav = function() {

            jQuery(document).keyup(function (event) {

                if (!state.paused) {
                    clearInterval(state.interval);
                    state.paused = true;
                }

                if (!state.animating) {
                    if (event.keyCode === 39) {
                        event.preventDefault();
                        go(vars.fwd, false);
                    } else if (event.keyCode === 37) {
                        event.preventDefault();
                        go(vars.prev, false);
                    }
                }

                if (state.paused && settings.automatic) {
                    state.interval = setInterval(function () {
                        go(vars.fwd);
                    }, settings.animspeed);
                    state.paused = false;
                }

            });

        };

        var conf_hoverpause = function() {

            jQuerywrapper.hover(function () {
                if (!state.paused) {
                    clearInterval(state.interval);
                    state.paused = true;
                }
            }, function () {
                if (state.paused) {
                    state.interval = setInterval(function () {
                        go(vars.fwd, false);
                    }, settings.animspeed);
                    state.paused = false;
                }
            });

        };

        var conf_captions = function() {

            jQuery.each(jQueryslides, function (key, slide) {

                var caption = jQuery(slide).children('img:first-child').attr('title');

                // Account for images wrapped in links
                if(!caption){
                    caption = jQuery(slide).children('a').find('img:first-child').attr('title');
                }

                if (caption) {
                    caption = jQuery('<p class="bjqs-caption">' + caption + '</p>');
                    caption.appendTo(jQuery(slide));
                }

            });

        };

        var conf_random = function() {

            var rand            = Math.floor(Math.random() * state.slidecount) + 1;
            state.currentslide  = rand;
            state.currentindex  = rand-1;

        };

        var set_next = function(direction) {

            if(direction === vars.fwd){
                
                if(jQueryslides.eq(state.currentindex).next().length){
                    state.nextindex = state.currentindex + 1;
                    state.nextslide = state.currentslide + 1;
                }
                else{
                    state.nextindex = 0;
                    state.nextslide = 1;
                }

            }
            else{

                if(jQueryslides.eq(state.currentindex).prev().length){
                    state.nextindex = state.currentindex - 1;
                    state.nextslide = state.currentslide - 1;
                }
                else{
                    state.nextindex = state.slidecount - 1;
                    state.nextslide = state.slidecount;
                }

            }

        };

        var go = function(direction, position) {

            // only if we're not already doing things
            if(!state.animating){

                // doing things
                state.animating = true;

                if(position){
                    state.nextslide = position;
                    state.nextindex = position-1;
                }
                else{
                    set_next(direction);
                }

                // fade animation
                if(settings.animtype === 'fade'){

                    if(settings.showmarkers){
                        jQuerym_markers.removeClass('active-marker');
                        jQuerym_markers.eq(state.nextindex).addClass('active-marker');
                    }

                    // fade out current
                    jQueryslides.eq(state.currentindex).fadeOut(settings.animduration);
                    // fade in next
                    jQueryslides.eq(state.nextindex).fadeIn(settings.animduration, function(){

                        // update state variables
                        state.animating = false;
                        state.currentslide = state.nextslide;
                        state.currentindex = state.nextindex;

                    });

                }

                // slide animation
                if(settings.animtype === 'slide'){

                    if(settings.showmarkers){
                        
                        var markerindex = state.nextindex-1;

                        if(markerindex === state.slidecount-2){
                            markerindex = 0;
                        }
                        else if(markerindex === -1){
                            markerindex = state.slidecount-3;
                        }

                        jQuerym_markers.removeClass('active-marker');
                        jQuerym_markers.eq(markerindex).addClass('active-marker');
                    }

                    // if the slider is responsive && the calculated width is less than the max width
                    if(settings.responsive && ( responsive.width < settings.width ) ){
                        state.slidewidth = responsive.width;
                    }
                    else{
                        state.slidewidth = settings.width;
                    }

                    jQueryslider.animate({'left': -state.nextindex * state.slidewidth }, settings.animduration, function(){

                        state.currentslide = state.nextslide;
                        state.currentindex = state.nextindex;

                        // is the current slide a clone?
                        if(jQueryslides.eq(state.currentindex).attr('data-clone') === 'last'){

                            // affirmative, at the last slide (clone of first)
                            jQueryslider.css({'left': -state.slidewidth });
                            state.currentslide = 2;
                            state.currentindex = 1;

                        }
                        else if(jQueryslides.eq(state.currentindex).attr('data-clone') === 'first'){

                            // affirmative, at the fist slide (clone of last)
                            jQueryslider.css({'left': -state.slidewidth *(state.slidecount - 2)});
                            state.currentslide = state.slidecount - 1;
                            state.currentindex = state.slidecount - 2;

                        }

                        state.animating = false;

                    });

                }

            }

        };

        // lets get the party started :)
        init();

    };

})(jQuery);
