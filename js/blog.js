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
		
		this.panelIds = new Array(), this.panelSlide = new Array(), this.panelOpacity = new Array();
		this.panels = $$('#titlebits a.panel');
		this.mainImage = $('mainimage');
		this.panelClick = this.clickPanel.bind(this);
		this.loadingImage = 0;
		this.panelInTrans = this.panelDisplay = -1;
		
		$$('#overPrevLink', '#overNextLink').each(function(el, i){
			el.setStyle('background-image', 'none');
			var img = new Element('img', {
				id: el.id+"Img", 
				src:this.templateDir+'/images/'+(el.id=='overPrevLink'?'prevlabel.gif':'nextlabel.gif'),
				styles:{top:"30%",position:'absolute',border:0}
			}).injectInside(el.id);
			if (el.id == 'overNextLink') img.setStyle('right', 0);
			var fx = new Fx.Style(el.id+"Img", 'opacity', {duration:200, wait:false});
			fx.set(0);
			el.addEvent('mouseover', function(e){ fx.start(1); });
			el.addEvent('mouseleave', function(e){ fx.start(0); });
		}.bind(this));
		
		if ($('nextPostLink')) {
			this.imageFader = new Fx.Elements($$('#theoverlay_panel', '#reflectionholder', '#topcontent', '#mainimage'), {
				duration: this.options.faderDuration,
				onComplete: this.resizeComplete.bind(this)
			});
			
			document.addEvent('keydown', this.keyboardListener.bindAsEventListener(this));		
			$('nextPostLink').addEvent('click', this.nextPost.bind(this));
			$('overNextLink').addEvent('click', this.nextPost.bind(this));
			$('prevPostLink').addEvent('click', this.prevPost.bind(this));
			$('overPrevLink').addEvent('click', this.prevPost.bind(this));
		}
		
		this.setPanels();
		this.doReflection();
		
		this.panels.each(function(link, i) {
			var id = link.id+"_panel", p = $(link.id+"_panel");
			$(link.id+"_holder").setStyle('display', 'block');
			this.panelSlide[i] = new Fx.Slide(id, {
				duration:this.options.panelDuration,
				transition:this.options.panelTransition,
				onComplete:function() {
					this.panelInTrans = -1;
					this.panelDisplay = this.panelDisplay == -1 ? i : -1;
				}.bind(this)
			}).hide();
			this.panelOpacity[i] = new Fx.Style(id, 'opacity', { duration: 600 });
			link.addEvent('click', function(e){
				e = new Event(e);
				e.stop();
				this.panelClick(i);
			}.bind(this));
		}.bind(this));
	},
	
	fadeOut: function() {
		$('theoverlay_panel').setStyles({width:this.mainImage.width, height:this.mainImage.height, opacity:0});
		$('theoverlay_holder').setStyle('display', 'block');
		
		this.imageFader.start({
			0: {'opacity': [0,1]},
			1: {'opacity': [1,0]}
		});
	},
	
	fadeIn: function() {
		this.doReflection();
		this.imageFader.start({
			0: {'opacity': [1,0]},
			1: {'opacity': [0,1]}
		});
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
			this.panelSlide[i].hide();
			this.panelOpacity[i].set(0);
			this.panelInTrans = this.panelDisplay = -1;
		}.bind(this));
		
		this.loadingImage = 1;
		this.fadeOut();
	},
	
	ajaxRefresh: function(req) {
		eval('this.imageinfo='+req+';');
		
		this.preload = new Image();
		this.preload.onload = this.loadComplete.bind(this);
		this.preload.src = this.imageinfo.image_uri;
		
		this.nextPostID = this.imageinfo.next_post;
		this.prevPostID = this.imageinfo.prev_post;
		
		$('nextPostLink').innerHTML = this.nextPostID == 0 ? '' : '&raquo;';
		$('prevPostLink').innerHTML = this.prevPostID == 0 ? '' : '&laquo;';
		$('overNextLink').setStyle('display', this.nextPostID == 0 ? 'none' : 'block');
		$('overPrevLink').setStyle('display', this.prevPostID == 0 ? 'none' : 'block');
		
		$('nextPostLink').href = $('overNextLink').href = this.imageinfo.next_post_perm;
		$('prevPostLink').href = $('overPrevLink').href = this.imageinfo.prev_post_perm;
		
		$('comment').innerHTML = this.imageinfo.comment_count + " comment" + (this.imageinfo.comment_count != 1 ? "s" : "");
		$('comment').href = this.imageinfo.permalink + '#comments';
		
		$('texttitle').innerHTML = '<a href="' + this.imageinfo.permalink + '">' + this.imageinfo.post_title + '</a><span id="inlinedate">' + this.imageinfo.post_date + '</span>';
		
		$('exif_panel').innerHTML = this.imageinfo.exif;
		$('info_panel').innerHTML = this.imageinfo.post_content;
	},

	loadComplete: function() {
		this.mainImage.width = Math.min(this.mainImage.width, this.preload.width);
		this.mainImage.height = Math.min(this.mainImage.height, this.preload.height);
		this.imageFader.start({
			0: {width: this.preload.width, height: this.preload.height},
			2: {width: this.preload.width}
		});
	},
	
	resizeComplete: function() {
		this.loadingImage++;
		switch(this.loadingImage) {
		case 2:
			var myAjax = new Ajax(this.templateDir+'/ajax_request.php?id='+this.postID, {
				method:'get',
				onComplete:this.ajaxRefresh.bind(this)
			}).request();
			break;
		case 3:
			this.mainImage.width = this.preload.width;
			this.mainImage.height = this.preload.height;
			this.mainImage.src = this.preload.src;
			
			this.preload.onload = Class.empty;
			this.preload = null;
			
			this.setPanels();
			this.fadeIn();
			break;
		case 4:
			$('theoverlay_holder').setStyle('display', 'none');
			this.loadingImage = 0;
			break;
		}
	},
	
	setPanels: function() {
		//$('exif_panel').setStyles({ height: this.mainImage.height });
	},
	
	clickPanel: function(i) {
		if (this.panelInTrans==-1 && this.panelDisplay==i) {
			this.panelSlide[i].slideOut();
			this.panelOpacity[i].start(this.options.panelOpacity,0);
			this.panelInTrans = i;
		} else if (this.panelInTrans==-1 && this.panelDisplay==-1) {
			this.panelSlide[i].slideIn();
			this.panelOpacity[i].start(0,this.options.panelOpacity);
			this.panelInTrans = i;
		} else if (this.panelDisplay!=i && this.panelInTrans==-1) {
			this.panelSlide[this.panelDisplay].slideOut().chain(function(){
				this.panelSlide[i].slideIn();
				this.panelOpacity[i].start(0,this.options.panelOpacity);
				this.panelInTrans=i;
			}.bind(this));
			this.panelOpacity[this.panelDisplay].start(0);
			this.panelInTrans=this.panelDisplay;
		} else if (this.panelDisplay!=i && this.panelInTrans!=-1) {
			this.panelSlide[this.panelInTrans].stop();
			this.panelOpacity[this.panelInTrans].stop();
			this.panelDisplay=this.panelInTrans;
			this.panelSlide[this.panelInTrans].slideOut().chain(function(){
				this.panelSlide[i].slideIn();
				this.panelOpacity[i].start(0,this.options.panelOpacity);
				this.panelInTrans=i;
			}.bind(this));
			this.panelOpacity[this.panelInTrans].start(0);
		}
	},
	
	keyboardListener: function(e) {
		switch (e.keyCode) {
			case 78: case 110: this.ajaxRequest(1); break;
			case 80: case 112: this.ajaxRequest(0); break;
			case 69: case 101: this.panelClick(0); break;
			case 73: case 105: this.panelClick(1); break;
		}
	},
	
	doReflection: function() {
		var canvasHeight = Math.floor(this.mainImage.height * this.options.reflectionHeight);
		
		if (this.canvas) this.canvas.remove();
		
		if (window.ie){
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
		if (window.ie) return;

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
