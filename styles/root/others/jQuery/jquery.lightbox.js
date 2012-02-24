/**
 * jQuery Lightbox Plugin (balupton edition) - Lightboxes for jQuery
 * Copyright (C) 2008 Benjamin Arthur Lupton
 * http://jquery.com/plugins/project/jquerylightbox_bal
 *
 * This file is part of jQuery Lightbox (balupton edition).
 * 
 * jQuery Lightbox (balupton edition) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * jQuery Lightbox (balupton edition) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with jQuery Lightbox (balupton edition).  If not, see <http://www.gnu.org/licenses/>.
 *
 * @name jquery_lightbox: jquery.lightbox.js
 * @package jQuery Lightbox Plugin (balupton edition)
 * @version 1.2.1-final
 * @date August 1, 2008
 * @category jQuery plugin
 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
 * @copyright (c) 2008 Benjamin Arthur Lupton {@link http://www.balupton.com}
 * @license GNU Affero General Public License - {@link http://www.gnu.org/licenses/agpl.html}
 * @example Visit {@link http://jquery.com/plugins/project/jquerylightbox_bal} for more information.
 */

// Start of our jQuery Plugin
(function($)
{	// Create our Plugin function, with $ as the argument (we pass the jQuery object over later)
	// More info: http://docs.jquery.com/Plugins/Authoring#Custom_Alias
	
	// Avoid IE6, It's time to upgrade
	if ( navigator.userAgent.indexOf('MSIE 6') >= 0 )
	{	// Is IE6
		// Include Upgrade Message
		var headEl = document.getElementsByTagName('head')[0];
		var scriptEl = document.createElement('script');
		scriptEl.type = 'text/javascript';
		scriptEl.src = 'http://www.savethedevelopers.org/say.no.to.ie.6.js';
		headEl.appendChild(scriptEl);
		// Kill Lightbox
		return null;
	}
	
	// Debug
	$.log = $.log || function ( options )
	{	// Can we debug? - Do we have firebug
		var con = null;
		if ( typeof console !== 'undefined' && typeof $.log !== 'undefined' )
		{	con = console;	}
		else if ( typeof window.console !== 'undefined' && typeof window.$.log !== 'undefined')
		{	con = window.console;	}
		
		// Do the log
		if ( con )
		{	// Do we support arguments?
			if ( typeof arguments !== 'undefined' && arguments.length > 1)
			{	con.log(arguments);	return arguments;	}
			else
			{	con.log(options);	return options;		}
		}
	};
	
	// Pre-Req
	$.params_to_json = $.params_to_json || function ( params )
	{	// Turns a params string or url into an array of params
		// Adjust
		params = String(params);
		// Remove url if need be
		params = params.substring(params.indexOf('?')+1);
		// params = params.substring(params.indexOf('#')+1);
		// Change + to %20, the %20 is fixed up later with the decode
		params = params.replace(/\+/g, '%20');
		// Do we have JSON string
		if ( params.substring(0,1) === '{' && params.substring(params.length-1) === '}' )
		{	// We have a JSON string
			return eval(decodeURIComponent(params));
		}
		// We have a params string
		params = params.split(/\&|\&amp\;/);
		var json = {};
		// We have params
		for ( var i = 0, n = params.length; i < n; ++i )
		{
			// Adjust
			var param = params[i] || null;
			if ( param === null ) { continue; }
			param = param.split('=');
			if ( param === null ) { continue; }
			// ^ We now have "var=blah" into ["var","blah"]
			
			// Get
			var key = param[0] || null;
			if ( key === null ) { continue; }
			if ( typeof param[1] === 'undefined' ) { continue; }
			var value = param[1];
			// ^ We now have the parts
			
			// Fix
			key = decodeURIComponent(key);
			value = decodeURIComponent(value);
			try {
			    // value can be converted
			    value = eval(value);
			} catch ( e ) {
			    // value is a normal string
			}
			
			// Set
			// console.log({'key':key,'value':value}, split);
			var keys = key.split('.');
			if ( keys.length === 1 )
			{	// Simple
				json[key] = value;
			}
			else
			{	// Advanced
				var path = '';
				for ( ii in keys )
				{	//
					key = keys[ii];
					path += '.'+key;
					eval('json'+path+' = json'+path+' || {}');
				}
				eval('json'+path+' = value');
			}
			// ^ We now have the parts added to your JSON object
		}
		return json;
	};
	
	// Declare our class
	$.LightboxClass = function ( )
	{	// This is the handler for our constructor
		this.construct();
	};

	// Extend jQuery elements for Lightbox
	$.fn.lightbox = function ( options )
	{	// Init a el for Lightbox
		// Eg. $('#gallery a').lightbox();
		
		// If need be: Instantiate $.LightboxClass to $.Lightbox
		$.Lightbox = $.Lightbox || new $.LightboxClass();
		
		// Establish options
		options = $.extend({start:false,events:true} /* default options */, options);
		
		// Get group
		var group = $(this);
		
		// Events?
		if ( options.events )
		{	// Add events
			$(group).unbind().click(function(){
				// Get obj
				var obj = $(this);
				// Get rel
				// var rel = $(obj).attr('rel');
				// Init group
				if ( !$.Lightbox.init($(obj)[0], group) )
				{	return false;	}
				// Display lightbox
				if ( !$.Lightbox.start() )
				{	return false;	}
				// Cancel href
				return false;
			});
			// Add style
			$(group).addClass('lightbox-enabled');
		}
		
		// Start?
		if ( options.start )
		{	// Start
			// Get obj
			var obj = $(this);
			// Get rel
			// var rel = $(obj).attr('rel');
			// Init group
			if ( !$.Lightbox.init($(obj)[0], group) )
			{	return this;	}
			// Display lightbox
			if ( !$.Lightbox.start() )
			{	return this;	}
		}
		
		// And chain
		return this;
	};
	
	// Define our class
	$.extend($.LightboxClass.prototype,
	{	// Our LightboxClass definition
		
		// -----------------
		// Everyting to do with images
		
		images: {
			
			// -----------------
			// Variables
			
			// Our array of images
			list:[], /* [ {
				src: 'url to image',
				link: 'a link to a page',
				title: 'title of the image',
				name: 'name of the image',
				description: 'description of the image'
			} ], */
			
			// The current active image
			image: false,
			
			// -----------------
			// Functions
			
			prev: function ( image )
			{	// Get previous image
				
				// Get previous from current?
				if ( typeof image === 'undefined' )
				{	image = this.active();
					if ( !image ) { return image; }
				}
				
				// Is there a previous?
				if ( this.first(image) )
				{	return false;	}
				
				// Get the previous
				return this.get(image.index-1);
			},
			
			next: function ( image )
			{	// Get next image
				
				// Get next from current?
				if ( typeof image === 'undefined' )
				{	image = this.active();
					if ( !image ) { return image; }
				}
				
				// Is there a next?
				if ( this.last(image) )
				{	return false;	}
				
				// Get the next
				return this.get(image.index+1);
			},
			
			first: function ( image )
			{	//
				// Get the first image?
				if ( typeof image === 'undefined' )
				{	return this.get(0);	}
				
				// Are we the first?
				return image.index === 0;
			},
			
			last: function ( image )
			{	//
				// Get the last image?
				if ( typeof image === 'undefined' )
				{	return this.get(this.size()-1);	}
				
				// Are we the last?
				return image.index === this.size()-1;
			},
		
			single: function ( )
			{	// Are we only one
				return this.size() === 1;
			},
			
			size: function ( )
			{	// How many images do we have
				return this.list.length;
			},
			
			empty: function ( )
			{	// Are we empty
				return this.size() === 0;
			},
			
			clear: function ( )
			{	// Clear image arrray
				this.list = [];
				this.image = false;
			},
		
			active: function ( image )
			{	// Set or get the active image
				
				// Get the active image?
				if ( typeof image === 'undefined' )
				{	return this.image;	}
				
				// Set the ative image
				
				// Make sure image exists
				image = this.get(image);
				if ( !image ) { return image; }
				
				// Make it the active
				this.image = image;
				
				// Done
				return true;
			},
		
			add: function ( obj )
			{
				// Do we need to recurse?
				if ( obj[0] )
				{	// We have a lot of images
					for ( var i = 0; i < obj.length; i++ )
					{	this.add(obj[i]);	}
					return true;
				}
				
				// Default image
				
				// Try and create a image
				var image = this.create(obj);
				if ( !image ) { return image; }
				
				// Set image index
				image.index = this.size();
				
				// Push image
				this.list.push(image);
				
				// Success
				return true;
			},
			
			create: function ( obj )
			{	// Create image
				
				// Define
				var image = { // default
					src:	'',
					title:	'Untitled',
					description:	'',
					name:	'',
					index:	-1,
					image:	true
				};
				
				// Create
				if ( obj.image )
				{	// Already a image, so copy over values
					image.src = obj.src || image.src;
					image.title = obj.title || image.title;
					image.description = obj.description || image.description;
					image.name = obj.name || image.name;
					image.index = obj.index || image.index;
				}
				else if ( obj.tagName )
				{	// We are an element
					obj = $(obj);
					if ( obj.attr('src') || obj.attr('href') )
					{
						image.src = obj.attr('src') || obj.attr('href');
						image.title = obj.attr('title') || obj.attr('alt') || image.title;
						image.name = obj.attr('name') || '';
						// Extract description from title
						var s = image.title.indexOf(': ');
						if ( s > 0 )
						{	// Description exists
							image.description = image.title.substring(s+2) || image.description;
							image.title = image.title.substring(0,s) || image.title;
						}
					}
					else
					{	// Unsupported element
						image = false;
					}
				}
				else
				{	// Unknown
					image = false;
				}
				
				if ( !image )
				{	// Error
					$.log('ERROR', 'We dont know what we have:', obj);
					return false;
				}
				
				// Success
				return image;
			},
			
			get: function ( image )
			{	// Get the active, or specified image
				
				// Establish image
				if ( typeof image === 'undefined' || image === null )
				{	// Get the active image
					return this.active();
				}
				else
				if ( typeof image === 'number' )
				{	// We have a index
					
					// Get image
					image = this.list[image] || false;
				}
				else
				{	// Create
					image = this.create(image);
					if ( !image ) { return false; }
					
					// Find
					var f = false;
					for ( var i = 0; i < this.size(); i++ )
					{
						var c = this.list[i];
						if ( c.src === image.src && c.title === image.title && c.description === image.description )
						{	f = c;	}
					}
					
					// Found?
					image = f;
				}
				
				// Determine image
				if ( !image )
				{	// Image doesn't exist
					$.log('ERROR', 'The desired image doesn\'t exist: ', image, this.list);
					return false;
				}
				
				// Return image
				return image;
			},
			
			debug: function ( )
			{
				return $.Lightbox.debug(arguments);
			}
			
		},
		
		// -----------------
		// Options
		
		constructed:		false,
		
		src:				null,		// the source location of our js file
		baseurl:			null,
		
		files: {
			// If you are doing a repack with packer (http://dean.edwards.name/packer/) then append ".packed" onto the js and css files before you pack it.
			js: {
				lightbox:	'/styles/root/others/jQuery/jquery.lightbox.js'
			},
			css: {
				lightbox:	'/styles/root/others/jQuery/jquery.lightbox.css'
			},
			images: {
				prev:		'/styles/root/others/jQuery/lightbox_img/prev.gif',
				next:		'/styles/root/others/jQuery/lightbox_img/next.gif',
				blank:		'/styles/root/others/jQuery/lightbox_img/blank.gif',
				loading:	'/styles/root/others/jQuery/lightbox_img/loading.gif'
			}
		},
		
		text: {
			// For translating
			image:		'Image',
			of:			'of',
			close:		'Close X',
			closeInfo:	'You can also click anywhere outside the image to close.',
			help: {
				close:		'Click to close',
				interact:	'Hover to interact'
			},
			about: {
				text: 	'jQuery Lightbox Plugin (balupton edition)',
				title:	'Licenced under the GNU Affero General Public License.',
				link:	'http://jquery.com/plugins/project/jquerylightbox_bal'
			}
		},
		show_linkback:	true,
		
		keys: {
			close:	'c',
			prev:	'p',
			next:	'n'
		},
		
		handlers: {
			// For custom actions
			show:	null
		},
		
		opacity:	0.9,
		padding:	null,	// if null - autodetect
		
		speed:		400,	// Duration of effect, milliseconds
		
		rel:		'lightbox',	// What to look for in the rels
		
		auto_relify:	true,	// should we automaticly do the rels?
		
		scroll_with:	false,	// should the lightbox scroll with the page?
		
		// names of the options that can be modified
		options:	['baseurl', 'files', 'text', 'show_linkback', 'keys', 'opacity', 'padding', 'speed', 'rel', 'auto_relify', 'scroll_with'],
		
		// -----------------
		// Functions
		
		construct: function ( options )
		{	// Construct our Lightbox
			
			// -------------------
			// Prepare
			
			// Initial construct
			var initial = typeof this.constructed === 'undefined' || this.constructed === false;
			this.constructed = true;
			
			// Perform domReady
			var domReady = initial;
			
			// Prepare options
			options = $.extend({}, options);
			
			// -------------------
			// Handle files
			
			// Add baseurl
			if ( initial && (typeof options.files === 'undefined') )
			{	// Load the files like default
			
				// Get the src of the first script tag that includes our js file (with or without an appendix)
				this.src = $('script[src*='+this.files.js.lightbox+']:first').attr('src');
				
				// Make sure we found ourselves
				if ( !this.src )
				{	// We didn't
					// $.log('WARNING', 'Lightbox was not able to find it\'s javascript script tag necessary for auto-inclusion.');
					// We don't work with files anymore, so don't care for domReady
					domReady = false;
				}
				else
				{	// We found ourself
				
					// The baseurl is the src up until the start of our js file
					this.baseurl = this.src.substring(0, this.src.indexOf(this.files.js.lightbox));
					
					// Apply baseurl to files
					var me = this;
					$.each(this.files, function(group, val){
						$.each(this, function(file, val){
							me.files[group][file] = me.baseurl+val;
						});
					});
					delete me;
					
					// Now as we have source, we may have more params
					options = $.extend(options, $.params_to_json(this.src));
				}
				
			}
			else
			if ( typeof options.files === 'object' )
			{	// We have custom files
				var me = this;
				$.each(options.files, function(group, val){
					$.each(this, function(file, val){
						this[file] = me.baseurl+val;
					});
				});
				delete me;
			}
			else
			{	// Don't have any files, so no need to perform domReady
				domReady = false;
			}
			
			// -------------------
			// Apply options
			
			for ( i in this.options )
			{	// Cycle through the options
				var name = this.options[i];
				if ( (typeof options[name] === 'object') && (typeof this[name] === 'object') )
				{	// We have a group like text or files
					this[name] = $.extend(this[name], options[name]);
				}
				else if ( typeof options[name] !== 'undefined' )
				{	// We have that option, so apply it
					this[name] = options[name];
				}
			}
			
			// -------------------
			// Handle our DOM
						
			if ( domReady || typeof options.files === 'object' || typeof options.text === 'object' || typeof options.show_linkback !== 'undefined' || typeof options.scroll_with !== 'undefined' )
			{	// We have reason to handle the dom
				$(function() {
					// DOM is ready, so fire our DOM handler
					$.Lightbox.domReady();
				});
			}
			
			// -------------------
			// Finish Up
			
			// All good
			return true;
		},
		
		domReady: function ( )
		{
			// -------------------
			// Append display
			
			// Include stylesheet
			var stylesheets = this.files.css;
			var bodyEl = document.getElementsByTagName($.browser.safari ? 'head' : 'body')[0];
			for ( stylesheet in stylesheets )
			{
				var linkEl = document.createElement('link');
				linkEl.type = 'text/css';
				linkEl.rel = 'stylesheet';
				linkEl.media = 'screen';
				linkEl.href = stylesheets[stylesheet];
				linkEl.id = 'lightbox-stylesheet-'+stylesheet;
				$('#'+linkEl.id).remove();
				bodyEl.appendChild(linkEl);
			}
			delete stylesheets;
			delete bodyEl;
			
			// Append markup
			$('#lightbox,#lightbox-overlay').remove();
			$('body').append('<div id="lightbox-overlay"><div id="lightbox-overlay-text">'+(this.show_linkback?'<p><span id="lightbox-overlay-text-about"><a href="#" title="'+this.text.about.title+'">'+this.text.about.text+'</a></span></p><p>&nbsp;</p>':'')+'<p><span id="lightbox-overlay-text-close">'+this.text.help.close+'</span><br/>&nbsp;<span id="lightbox-overlay-text-interact">'+this.text.help.interact+'</span></p></div></div><div id="lightbox"><div id="lightbox-imageBox"><div id="lightbox-imageContainer"><img id="lightbox-image" /><div id="lightbox-nav"><a href="#" id="lightbox-nav-btnPrev"></a><a href="#" id="lightbox-nav-btnNext"></a></div><div id="lightbox-loading"><a href="#" id="lightbox-loading-link"><img src="' + this.files.images.loading + '" /></a></div></div></div><div id="lightbox-infoBox"><div id="lightbox-infoContainer"><div id="lightbox-infoHeader"><span id="lightbox-caption"><span id="lightbox-caption-title"></span><span id="lightbox-caption-description"></span></span></div><div id="lightbox-infoFooter"><span id="lightbox-currentNumber"></span><span id="lightbox-close"><a href="#" id="lightbox-close-button" title="'+this.text.closeInfo+'">' + this.text.close + '</a></span></div><div id="lightbox-infoContainer-clear"></div></div></div></div>');
			
			// Update Boxes - for some crazy reason this has to be before the hide in safari and konqueror
			this.resizeBoxes();
			this.repositionBoxes();
			
			// Hide
			$('#lightbox,#lightbox-overlay,#lightbox-overlay-text-interact').hide();
			
			// -------------------
			// Preload Images
			
			// Cycle and preload
			$.each(this.files.images, function()
			{	// Proload the image
				var preloader = new Image();
				preloader.onload = function() {
					preloader.onload = null;
					preloader = null;
				};	preloader.src = this;
			});
			
			// -------------------
			// Apply events
			
			// If the window resizes, act appropriatly
			$(window).unbind().resize(function ()
			{	// The window has been resized
				$.Lightbox.resizeBoxes();
				$.Lightbox.repositionBoxes();
			});
			
			// If the window scrolls, act appropriatly
			if ( $.Lightbox.scroll_with )
			{	// We want to
				$(window).scroll(function ()
				{	// The window has scrolled
					$.Lightbox.repositionBoxes();
				});
			}
			
			// Prev
			$('#lightbox-nav-btnPrev').unbind().hover(function() { // over
				$(this).css({ 'background' : 'url(' + $.Lightbox.files.images.prev + ') left 45% no-repeat' });
			},function() { // out
				$(this).css({ 'background' : 'transparent url(' + $.Lightbox.files.images.blank + ') no-repeat' });
			}).click(function() {
				$.Lightbox.showImage($.Lightbox.images.prev());
				return false;
			});
					
			// Next
			$('#lightbox-nav-btnNext').unbind().hover(function() { // over
				$(this).css({ 'background' : 'url(' + $.Lightbox.files.images.next + ') right 45% no-repeat' });
			},function() { // out
				$(this).css({ 'background' : 'transparent url(' + $.Lightbox.files.images.blank + ') no-repeat' });
			}).click(function() {
				$.Lightbox.showImage($.Lightbox.images.next());
				return false;
			});
			
			// Help
			if ( this.show_linkback )
			{	// Linkback exists so add handler
				$('#lightbox-overlay-text-about a').click(function(){window.open($.Lightbox.text.about.link); return false;});
			}
			$('#lightbox-overlay-text-close').unbind().hover(
				function(){
					$('#lightbox-overlay-text-interact').fadeIn();
				},
				function(){
					$('#lightbox-overlay-text-interact').fadeOut();
				}
			);
			
			// Assign close clicks
			$('#lightbox-overlay, #lightbox, #lightbox-loading-link, #lightbox-btnClose').unbind().click(function() {
				$.Lightbox.finish();
				return false;	
			});
			
			// -------------------
			// Finish Up
			
			// Relify
			if ( $.Lightbox.auto_relify )
			{	// We want to relify, no the user
				$.Lightbox.relify();
			}
			
			// All good
			return true;
		},
		
		relify: function ( )
		{	// Create event
		
			//
			var groups = {};
			var groups_n = 0;
			var orig_rel = this.rel;
			// Create the groups
			$.each($('[@rel*='+orig_rel+']'), function(index, obj){
				// Get the group
				var rel = $(obj).attr('rel');
				// Are we really a group
				if ( rel === orig_rel )
				{	// We aren't
					rel = groups_n; // we are individual
				}
				// Does the group exist
				if ( typeof groups[rel] === 'undefined' )
				{	// Make the group
					groups[rel] = [];
					groups_n++;
				}
				// Append the image
				groups[rel].push(obj);
			});
			// Lightbox groups
			$.each(groups, function(index, group){
				$(group).lightbox();
			});
			// Done
			return true;
		},
		
		init: function ( image /*int*/, images /*[]*/ )
		{	// Init a batch of lightboxes
			
			// Establish images
			if ( typeof images === 'undefined' )
			{
				images = image;
				image = 0;
			}
			
			// Clear
			this.images.clear();
			
			// Add images
			if ( !this.images.add(images) )
			{	return false;	}
			
			// Do we need to bother
			if ( this.images.empty() )
			{	// No images
				$.log('WARNING', 'Lightbox started, but no images: ', image, images);
				return false;
			}
			
			// Set active
			if ( !this.images.active(image) )
			{	return false;	}
			
			// Done
			return true;
		},
		
		start: function ( )
		{	// Display the lightbox
			
			// Fix attention seekers
			$('embed, object, select').css('visibility', 'hidden');//.hide(); - don't use this, give it a go, find out why!
			
			// Resize the boxes appropriatly
			this.resizeBoxes();
			
			// Reposition the Boxes
			this.repositionBoxes({'speed':0});
			
			// Hide things
			$('#lightbox-infoFooter').hide(); // we hide this here because it makes the display smoother
			$('#lightbox-image,#lightbox-nav,#lightbox-nav-btnPrev,#lightbox-nav-btnNext,#lightbox-infoBox').hide();
					
			// Display the boxes
			$('#lightbox-overlay').css('opacity',this.opacity).fadeIn(400, function(){
				// Show the lightbox
				$('#lightbox').fadeIn(300);
				
				// Display first image
				if ( !$.Lightbox.showImage($.Lightbox.images.active()) )
				{	$.Lightbox.finish();	return false;	}
			});
			
			// All done
			return true;
		},
		
		finish: function ( )
		{	// Get rid of lightbox
			$('#lightbox').hide();
			$('#lightbox-overlay').fadeOut(function() { $('#lightbox-overlay').hide(); });
			// Fix attention seekers
			$('embed, object, select').css({ 'visibility' : 'visible' });//.show();
		},
		
		resizeBoxes: function ( )
		{
			// Style overlay and show it
			$('#lightbox-overlay').css({
				width:		$(document).width(),
				height:		$(document).height()
			});
		},
		
		
		repositioning:			false,	// are we currently repositioning
		reposition_failsafe:	false,	// failsafe
		repositionBoxes: function ( options )
		{
			// Prepare
			if ( $.Lightbox.repositioning )
			{	// Already here
				$.Lightbox.reposition_failsafe = true;
				return null;
			}
			$.Lightbox.repositioning = true;
			
			// Options
			options = $.extend({}, options);
			options.callback = options.callback || null;
			options.speed = options.speed || 'slow';
			
			// Get page scroll
			var pageScroll = this.getPageScroll();
			
			// Figure it out
			var nHeight = options.nHeight || parseInt($('#lightbox').height(),10) || $(document).height()/3;
			
			// Display lightbox in center
			var nTop = pageScroll.yScroll + ($(document.body).height() /*frame height*/ - nHeight) / 2.5;
			var nLeft = pageScroll.xScroll;
			
			// Animate
			var css = {
				left: nLeft,
				top: nTop
			};
			if (options.speed) {
				$('#lightbox').animate(css, 'slow', function(){
					if ( $.Lightbox.reposition_failsafe )
					{	// Fire again
						$.Lightbox.repositioning = $.Lightbox.reposition_failsafe = false;
						$.Lightbox.repositionBoxes(options);
					}
					else
					{	// Done
						$.Lightbox.repositioning = false;
						if ( options.callback )
						{	// Call the user callback
							options.callback();
						}
					}
				});
			}
			else
			{
				$('#lightbox').css(css);
				if ( $.Lightbox.reposition_failsafe )
				{	// Fire again
					$.Lightbox.repositioning = $.Lightbox.reposition_failsafe = false;
					$.Lightbox.repositionBoxes(options);
				}
				else
				{	// Done
					$.Lightbox.repositioning = false;
				}
			}
			
			// Done
			return true;
		},
		
		showImage: function ( image, options )
		{
			// Establish image
			image = this.images.get(image);
			if ( !image ) { return image; }
			
			// Establish options
			options = $.extend({step:1}, options);
			// Split up below for jsLint compliance
			var skipped_step_1 = options.step > 1 && this.images.active().src !== image.src;
			var skipped_step_2 = options.step > 2 && $('#lightbox-image').attr('src') !== image.src;
			if ( skipped_step_1 || skipped_step_2 )
			{	// Force step 1
				$.log('We wanted to skip a few steps: ', options, image, skipped_step_1, skipped_step_2);
				options.step = 1;
			}
			
			// What do we need to do
			switch ( options.step )
			{
				// ---------------------------------
				// We need to preload
				case 1:
				
					// Disable keyboard nav
					this.KeyboardNav_Disable();
					
					// Show the loading image
					$('#lightbox-loading').show();
					
					// Hide things
					$('#lightbox-image,#lightbox-nav,#lightbox-nav-btnPrev,#lightbox-nav-btnNext,#lightbox-infoBox').hide();
					
					// Remove show info events
					$('#lightbox-imageBox').unbind();
					// ^ Why? Because otherwise when the image is changing, the info pops out, not good!
					
					// Make the image the active image
					if ( !this.images.active(image) ) { return false; }
					
					// Preload the image, and continue to step 2 once loaded
					var preloader = new Image();
					preloader.onload = function() {
						$.Lightbox.showImage(null, {step:2, width:preloader.width, height:preloader.height});
						preloader.onload = null;
						preloader = null;
					};
					preloader.src = image.src;
					
					// Done
					break;
				
				
				// ---------------------------------
				// Resize the container
				case 2:
					
					// Set image src
					$('#lightbox-image').attr('src', image.src);
					
					// Establish options
					options = $.extend({width:null, height:null}, options);
					
					// Set container border (Moved here for Konqueror fix - Credits to Blueyed)
					if ( typeof this.padding === 'undefined' || this.padding === null || isNaN(this.padding) )
					{	// Autodetect
						this.padding = parseInt($('#lightbox-imageContainer').css('padding-left'), 10) || parseInt($('#lightbox-imageContainer').css('padding'), 10) || 0;
					}
					
					// Resize image box
					// i:image, c:current, n:new, d:difference
					
					// Get image dimensions
					var iWidth  = options.width;
					var iHeight = options.height;
					
					// Get current width and height
					var cWidth = $('#lightbox-imageBox').width();
					var cHeight = $('#lightbox-imageBox').height();
			
					// Get the width and height of the selected image plus the padding
					var nWidth	= (iWidth  + (this.padding * 2)); // Plus the image's width and the left and right padding value
					var nHeight	= (iHeight + (this.padding * 2)); // Plus the image's height and the left and right padding value
					
					// Diferences
					var dWidth  = cWidth  - nWidth;
					var dHeight = cHeight - nHeight;
					
					// Lets do this here because we can - NO because we know the heights here
					$('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').css({ height: iHeight + (this.padding * 2) }); 
					$('#lightbox-infoBox').css({ width: iWidth+this.padding*2 });
					
					// Reposition the Boxes
					this.repositionBoxes({'nHeight':nHeight});
					
					// Do we need to wait? (just a nice effect to counter the other
					if ( dWidth === 0 && dHeight === 0 )
					{	// We are the same size
						this.pause(this.speed/3);
						$.Lightbox.showImage(null, {step:3});
					}
					else
					{	// We are not the same size
						// Animate
						$('#lightbox-imageBox').animate({ width: nWidth, height: nHeight }, this.speed, function ( ) { $.Lightbox.showImage(null, {step:3}); } );
					}
					
					// Done
					break;
				
				
				// ---------------------------------
				// Display the image
				case 3:
					
					// Hide loading
					$('#lightbox-loading').hide();
					
					// Animate image
					$('#lightbox-image').fadeIn(500, function() {$.Lightbox.showImage(null, {step:4}); });
					
					// Start the proloading of other images
					this.preloadNeighbours();
					
					// Fire custom handler show
					if ( $.Lightbox.handlers.show !== null )
					{	// Fire it
						$.Lightbox.handlers.show(image);
					}
					
					// Done
					break;
				
				
				// ---------------------------------
				// Set image info / Set navigation
				case 4:
					
					// ---------------------------------
					// Set image info
					
					// Hide and set image info
					$('#lightbox-caption-title').html(image.title + (image.description ? ': ' : '') || 'Untitled');
					$('#lightbox-caption-description').html(image.description || '&nbsp;');
					
					// If we have a set, display image position
					if ( this.images.size() > 1 )
					{	// Display
						$('#lightbox-currentNumber').html(this.text.image + '&nbsp;' + ( image.index + 1 ) + '&nbsp;' + this.text.of + '&nbsp;' + this.images.size());
					} else
					{	// Empty
						$('#lightbox-currentNumber').html('&nbsp;');
					}
					
					// ---------------------------------
					// Info events
					
					// Apply event
					$('#lightbox-imageBox').unbind('mouseover').mouseover(function(){
						 $('#lightbox-infoBox').slideDown('fast');
					 });
					
					// Apply event
					$('#lightbox-infoBox').unbind('mouseover').mouseover(function(){
						 $('#lightbox-infoFooter').slideDown('fast');
					 });
					
					// ---------------------------------
					// Set navigation
		
					// Instead to define this configuration in CSS file, we define here. And it's need to IE. Just.
					$('#lightbox-nav-btnPrev, #lightbox-nav-btnNext').css({ 'background' : 'transparent url(' + this.files.images.blank + ') no-repeat' });
					
					// If not first, show previous button
					if ( !this.images.first(image) ) {
						// Not first, show button
						$('#lightbox-nav-btnPrev').show();
					}
					
					// If not last, show next button
					if ( !this.images.last(image) ) {
						// Not first, show button
						$('#lightbox-nav-btnNext').show();
					}
					
					// Make navigation active / show it
					$('#lightbox-nav').show();
					
					// Enable keyboard navigation
					this.KeyboardNav_Enable();
					
					// Done
					break;
					
					
				// ---------------------------------
				// Error handling
				default:
					$.log('ERROR', 'Don\'t know what to do: ',options);
					return this.showImage(image, {step:1});
					// break;
				
			}
			
			// All done
			return true;
		},
		
		preloadNeighbours: function ( )
		{	// Preload all neighbour images
			
			// Do we need to do this?
			if ( this.images.single() || this.images.empty() )
			{	return true;	}
			
			// Get active image
			var image = this.images.active();
			if ( !image ) { return image; }
			
			// Load previous
			var prev = this.images.prev(image);
			var objNext;
			if ( prev ) {
				objNext = new Image();
				objNext.src = prev.src;
			}
			
			// Load next
			var next = this.images.next(image);
			if ( next ) {
				objNext = new Image();
				objNext.src = next.src;
			}
		},
		
		// --------------------------------------------------
		// Things we don't really care about
		
		KeyboardNav_Enable: function ( ) {
			$(document).keydown(function(objEvent) {
				$.Lightbox.KeyboardNav_Action(objEvent);
			});
		},
		
		KeyboardNav_Disable: function ( ) {
			$(document).unbind();
		},
		
		KeyboardNav_Action: function ( objEvent ) {
			// Prepare
			objEvent = objEvent || window.event;
			
			// Get the keycode
			var keycode = objEvent.keyCode;
			var escapeKey = objEvent.DOM_VK_ESCAPE /* moz */ || 27;
			
			// Get key
			var key = String.fromCharCode(keycode).toLowerCase();
			
			// Close?
			if ( key === this.keys.close || keycode === escapeKey )
			{	return $.Lightbox.finish();		}
			
			// Prev?
			if ( key === this.keys.prev || keycode === 37 )
			{	// We want previous
				return $.Lightbox.showImage($.Lightbox.images.prev());
			}
			
			// Next?
			if ( key === this.keys.next || keycode === 39 )
			{	// We want next
				return $.Lightbox.showImage($.Lightbox.images.next());
			}
			
			// Unknown
			return true;
		},
		
		getPageScroll: function ( ) {
			var xScroll, yScroll;
			if (self.pageYOffset)
			{	// Some browser
				yScroll = self.pageYOffset;
				xScroll = self.pageXOffset;
			} else if (document.documentElement && document.documentElement.scrollTop)
			{	// Explorer 6 Strict
				yScroll = document.documentElement.scrollTop;
				xScroll = document.documentElement.scrollLeft;
			} else if (document.body)
			{	// All other browsers
				yScroll = document.body.scrollTop;
				xScroll = document.body.scrollLeft;	
			}
			var arrayPageScroll = {'xScroll':xScroll,'yScroll':yScroll};
			return arrayPageScroll;
		},
		
		
		pause: function ( ms ) {
			var date = new Date();
			var curDate = null;
			do { curDate = new Date(); }
			while ( curDate - date < ms);
		}
	
	}); // We have finished extending/defining our LightboxClass


	// --------------------------------------------------
	// Finish up
	
	// Instantiate
	if ( typeof $.Lightbox === 'undefined' )
	{	// 
		$.Lightbox = new $.LightboxClass();
	}

// Finished definition

})(jQuery); // We are done with our plugin, so lets call it with jQuery as the argument
