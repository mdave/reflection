// blog.js
// -------
// Main JavaScript for the Reflection theme. Actual reflection is done using
// Christophe Beyls' reflection.js for mootools script:
//   http://www.digitalia.be/software/reflectionjs-for-mootools

var Site = {
	init: function(options) {
		this.options = $extend({
			panelDuration: 600,
			panelTransition: Fx.Transitions.Cubic.easeInOut,
			panelOpacity: 0.87,
			reflectionHeight: 0.15,
			reflectionOpacity: 0.5,
			faderDuration: 600
		}, options || {});
		
		// Set up initial variables.
		this.panels       = $$('#titlebits a.panel');
		this.panelIds     = new Array();
		this.panelSlide   = new Array();
		this.panelDisplay = -1;
		this.panelClick   = this.clickPanel.bind(this);
		this.mainImage    = $('mainimage');
		this.nextPostLink = $('nextPostLink');
		this.prevPostLink = $('prevPostLink');
		this.loadingImage = 0;
		
		// For the next/previous links, place an image at the right position
		// and remove the default background image put in by the CSS. Add nice
		// transition effect for when a user mouses over/leaves.
		$$('#overPrevLink', '#overNextLink').each(function(el, i){
			el.setStyle('background-image', 'none');
			var img = new Element('img', {
				id:     el.id+"Img", 
				src:    this.templateDir+'/images/'+(el.id=='overPrevLink'?'prevlabel.gif':'nextlabel.gif'),
				styles: {top:"30%", position:'absolute', border:0}
			}).injectInside(el.id);
			if (el.id == 'overNextLink') img.setStyle('right', 0);
			var fx = new Fx.Tween(el.id+"Img", {property:"opacity", duration:200, wait:false});
			fx.set(0);
			el.addEvent('mouseover',  function(e){ fx.start(1); });
			el.addEvent('mouseleave', function(e){ fx.start(0); });
		}.bind(this));
		
		// If the next post link is set, then we're on the home page, so set up
		// all of the elements required for image transition and add click events
		// to the various links. Also add the keyboard listener for keydowns.
		if (this.nextPostLink) {
			this.imageFader = new Fx.Elements($$('#panel_overlay', '#reflectionholder', '#topcontent', '#mainimage', '#footer', '#imageholder'), {
				duration:   this.options.faderDuration,
				onComplete: this.resizeComplete.bind(this)
			});
			
			$('nextPostLink').addEvent('click', this.nextPost.bind(this));
			$('overNextLink').addEvent('click', this.nextPost.bind(this));
			$('prevPostLink').addEvent('click', this.prevPost.bind(this));
			$('overPrevLink').addEvent('click', this.prevPost.bind(this));
		}
		
		// Create reflection effect.
		this.doReflection();
		
		// Now set up panels. For each panel link that is on the toolbar, we
		// create a panel slider which fades the panel into the main display.
		this.panels.each(function(link, i) {
			var id = "panel_"+link.id, p = $(id);
			
			this.panelSlide[i] = new Fx.Morph(id, {
				duration:  this.options.panelDuration,
				transition:this.options.panelTransition,
				onComplete:function() {
					this.panelDisplay = this.panelDisplay == -1 ? i : -1;
				}.bind(this)
			});
			
			// If the panel has the bottomPanel class, then slide this in from
			// the bottom instead of the top.
			if (p.hasClass('bottomPanel'))
				this.panelSlide[i].set({'opacity': 0, 'bottom':-p.getSize().y+"px"});
			else
				this.panelSlide[i].set({'opacity': 0, 'top':   -p.getSize().y+"px"});
			
			p.setStyle('visibility', 'visible');
			
			// Finally, set up the link which brings the panel in.
			link.addEvent('click', function(e){
				e = new Event(e);
				e.stop();
				this.panelClick(i);
			}.bind(this));
		}.bind(this));
	},
	
	nextPost: function(e) {
		e = new Event(e);
		e.stop();
		this.ajaxRequest(1);
	},
	
	prevPost: function(e) {
		e = new Event(e);
		e.stop();
		this.ajaxRequest(0);
	},
	
	ajaxRequest: function(dir) {
		this.postID = dir ? this.nextPostID : this.prevPostID;
		
		if (this.postID == 0 || this.loadingImage > 0)
			return;
		
		this.panels.each(function(el,i){
			this.slideIn(i);
			this.panelDisplay = -1;
		}.bind(this));
	
		this.loadingImage = 1;
		this.fadeOut();
	},
	
	fadeOut: function() {
		$('panel_overlay').setStyles({width:this.mainImage.width, height:this.mainImage.height, opacity:0, display:'block'});
	
		this.imageFader.start({
			0: {'opacity': [0,1]},
			1: {'opacity': [1,0]},
			4: {'opacity': [1,0]}
		});
	},
	
	fadeIn: function() {
		this.doReflection();
		this.imageFader.start({
			0: {'opacity': [1,0]},
			1: {'opacity': [0,1]},
			4: {'opacity': [0,1]}
		});
	},
	
	ajaxRefresh: function(req) {
		this.imageinfo      = req;
		this.preload        = new Image();
		this.preload.onload = this.loadComplete.bind(this);
		this.preload.src    = this.imageinfo.image_uri;
		this.nextPostID     = this.imageinfo.next_post;
		this.prevPostID     = this.imageinfo.prev_post;
		
		this.nextPostLink.innerHTML = this.nextPostID == 0 ? '' : '&raquo;';
		this.prevPostLink.innerHTML = this.prevPostID == 0 ? '' : '&laquo;';
		$('overNextLink').setStyle('display', this.nextPostID == 0 ? 'none' : 'block');
		$('overPrevLink').setStyle('display', this.prevPostID == 0 ? 'none' : 'block');
		this.nextPostLink.href = $('overNextLink').href = this.imageinfo.next_post_perm;
		this.prevPostLink.href = $('overPrevLink').href = this.imageinfo.prev_post_perm;
		
		$('comment').innerHTML    = this.imageinfo.comment_count + " comment" + (this.imageinfo.comment_count != 1 ? "s" : "");
		$('comment').href         = this.imageinfo.permalink + '#comments';
		$('texttitle').innerHTML  = '<a href="' + this.imageinfo.permalink + '">' + this.imageinfo.post_title + '</a><span id="inlinedate">' + this.imageinfo.post_date + '</span>';
		$('panel_exif').innerHTML = this.imageinfo.exif;
		$('panel_info').innerHTML = this.imageinfo.post_content;
		
		this.panels.each(function(link, i) {
			var id = "panel_"+link.id, p = $(id);
			if (p.hasClass('bottomPanel'))
				this.panelSlide[i].set({'opacity': 0, 'bottom':-p.getSize().y+"px"});
			else
				this.panelSlide[i].set({'opacity': 0, 'top':   -p.getSize().y+"px"});
		}.bind(this));
	},
	
	loadComplete: function() {
		this.mainImage.width  = Math.min(this.mainImage.width,  this.preload.width);
		this.mainImage.height = Math.min(this.mainImage.height, this.preload.height);
		this.imageFader.start({
			0: {width: this.preload.width, height: this.preload.height},
			2: {width: this.preload.width                             },
			5: {width: this.preload.width, height: this.preload.height}
		});
   	 },
   	 
	resizeComplete: function() {
		this.loadingImage++;
		switch(this.loadingImage) {
		case 2:
			var jsonRequest = new Request.JSON({
				method:'get',
				url:this.templateDir+'/ajax_blog.php',
				onComplete:this.ajaxRefresh.bind(this)
			}).send('id='+this.postID);
			$('imageholder').setStyle('overflow', 'visible');
			break;
		case 3:
			this.mainImage.width  = this.preload.width;
			this.mainImage.height = this.preload.height;
			this.mainImage.src    = this.preload.src;
			
			this.preload.onload = new Class();
			this.preload = null;

			$('imageholder').setStyle('overflow', 'hidden');
			this.fadeIn();
			break;
		case 4:
			$('panel_overlay').setStyle('display', 'none');
			this.loadingImage = 0;
			break;
		}
	},
	
	clickPanel: function(i) {
		if (this.panelDisplay==i) {
			this.slideIn(i);
		} else if (this.panelDisplay==-1) {
			this.slideOut(i)
		} else if (this.panelDisplay!=i) {
			this.slideIn(this.panelDisplay);
			this.slideOut(i);
		}
	},
	
	slideOut: function(i) {
		var p = $("panel_"+this.panels[i].id);
		if (p.hasClass("bottomPanel"))
			this.panelSlide[i].start({'opacity':this.options.panelOpacity, 'bottom':"0px"});
		else
			this.panelSlide[i].start({'opacity':this.options.panelOpacity, 'top':   "0px"});
	},
	
	slideIn: function(i) {
		var p = $("panel_"+this.panels[i].id);
		if (p.hasClass("bottomPanel"))
			this.panelSlide[i].start({'opacity':0, 'bottom':-p.getSize().y+"px"});
		else
			this.panelSlide[i].start({'opacity':0, 'top':   -p.getSize().y+"px"});
	},
	
	// doReflection: sets up reflection effect. Adaptation of Christophe Beyls' 
	//			   reflection.js mootools script.
	doReflection: function() {
		var canvasHeight = Math.floor(this.mainImage.height * this.options.reflectionHeight);
		
		if (this.canvas) this.canvas.dispose();
		
		if (Browser.Engine.trident){
			this.canvas = new Element('img', {'src': this.mainImage.src, 'styles': {
				'width': this.mainImage.width,
				'marginBottom': -this.mainImage.height+canvasHeight,
				'filter': 'flipv progid:DXImageTransform.Microsoft.Alpha(opacity='+(this.options.reflectionOpacity*100)+', style=1, finishOpacity=0, startx=0, starty=0, finishx=0, finishy='+(this.options.reflectionHeight*100)+')'
				}});
		} else {
			this.canvas = new Element('canvas', {'styles': {'width': this.mainImage.width, 'height': canvasHeight}});
			if (!this.canvas.getContext) return;
		}

		$('reflectionholder').setStyles({'width': this.mainImage.width, 'marginLeft': 'auto', 'marginRight': 'auto'});
		this.canvas.injectInside($('reflectionholder'));
		if (Browser.Engine.trident) return;
		
		var context = this.canvas.setProperties({'width': this.mainImage.width, 'height': canvasHeight}).getContext('2d');
		context.save();
		context.translate(0, this.mainImage.height-1);
		context.scale(1, -1);
		context.drawImage(this.mainImage, 0, 0, this.mainImage.width, this.mainImage.height);
		context.restore();
		context.globalCompositeOperation = 'destination-out';
		var gradient = context.createLinearGradient(0, 0, 0, canvasHeight);
		gradient.addColorStop(0, 'rgba(255, 255, 255, '+(1-this.options.reflectionOpacity)+')');
		gradient.addColorStop(1, 'rgba(255, 255, 255, 1.0)');
		context.fillStyle = gradient;
		context.rect(0, 0, this.mainImage.width, canvasHeight);
		context.fill();
	}
};
