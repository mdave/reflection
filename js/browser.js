var Browse = {
  init: function(options) {
    // Hook up each link in both year and tag clouds to tagClick.
    $$('#tagCloud a').each(function(l, i) {
      l.addEvent('click', function(e) {
	e = new Event(e);
	e.stop();
	this.tagClick(l, 'tag');
      }.bind(this));
    }.bind(this));

    $$('#catCloud a').each(function(l, i) {
      l.addEvent('click', function(e) {
        e = new Event(e);
        e.stop();
        this.tagClick(l, 'cat');
      }.bind(this));
    }.bind(this));

    $$('#yearCloud a').each(function(l, i) {
      l.addEvent('click', function(e) {
	e = new Event(e);
	e.stop();
	this.tagClick(l, 'year');
      }.bind(this));
    }.bind(this));

    if (this.initialPosts && typeof this.initialPosts[0].comment_count != 'undefined') {
      $$('.mosaic').each(function(el, i) {
        el.store('tip:title', this.initialPosts[i].post_title);
        el.store('tip:text',  this.initialPosts[i].post_date+"<br />"+this.initialPosts[i].comment_count+" "+(this.initialPosts[i].comment_count == 1 ? "comment" : "comments"));
      }.bind(this));
      
      var tipz = new Tips('.mosaic', { className: 'tipz', hideDelay: 50, showDelay: 50 });
      tipz.addEvents({
  	  'show': function(tip) { tip.fade(0.8); },
	  'hide': function(tip) { tip.fade(0.0); }
      });
    }

    this.progCount = -1;
    this.progFx    = new Fx.Elements($$('#tagPics', '#tagContainer', '#tagProgress'), {
      'link': 'chain',
      'onComplete': function() { this.progCount += this.progCount == 3 ? -4 : 1; }.bind(this)
    });

    // Make tagProgress visible but with zero opacity (allows us to calculate height).
    $('tagProgress').setStyles({'opacity': 0, 'visibility': 'visible'});
  },

  tagClick: function(l, type) {
    // If we are currently in animation then clicking does nothing.
    if (this.progCount > -1)
      return;

    if (type == "tag")
      ident = /tag-link-(\d+)/.exec(l.get('class'))[1];
    else if (type == "cat")
      ident = /cat-item-(\d+)/.exec(l.getParent().get('class'))[1];
    else
      ident = l.innerHTML;
    
    // Set up heights for a smooth transition.
    $('tagPics').setStyle('height', $('tagPics').scrollHeight);
    $('tagContainer').setStyles({
      'overflow': 'hidden',
      'height': $('tagContainer').scrollHeight
    });

    // Highlight this link as the current one.
    var cur = $$('a.current');
    if (cur.length > 0)
      cur[0].removeClass('current');
    l.addClass('current');

    // Fade out pictures, then fade in the timer.
    this.progCount = 0;
    fx = this.progFx;
    fx.start({
      '1': { 'opacity': 0 }
    }).start({
      '2': { 'opacity': 1 }
    });

    // Send off a JSON Request to the server for the list of images associated with this tag/year.
    var jsonRequest = new Request.JSON({
      method:'get',
      url:this.templateDir+'/ajax_browse.php',
      onComplete:this.tagRefresh.bind(this)
    }).send(type+'='+ident);
  },

  tagRefresh: function(data) {
    // Create asset manager to grab all images from the server.
    srcArray = new Array();
    for (i = 0; i < data.length; i++)
      srcArray[i] = data[i].image_uri;
    this.preload = new Asset.images(srcArray, { onComplete: this.preloadFinish.bind(this) });

    // Store data associated with each image using Mootools element storage.
    for (i = 0; i < data.length; i++)
      this.preload[i].store('imgData', data[i]);
  },

  preloadFinish: function() {
    if (this.progCount == 0) {
      setTimeout(this.preloadFinish.bind(this), 500);
      return;
    }

    // Remove all images from container
    $$('.mosaic').each(function(e, i) { e.destroy(); });

    // Create image element and insert into container.
    var tagFlag = typeof this.preload[0].retrieve('imgData').comment_count != 'undefined';
    
    for (i = 0; i < this.preload.length; i++) {
      tmpdata = this.preload[i].retrieve('imgData');
      link    = new Element('a', { 'href': tmpdata.permalink });
      this.preload[i].set('class', 'mosaic');
      this.preload[i].injectInside(link);

      if (tagFlag) {
        this.preload[i].store('tip:title', tmpdata.post_title);
        this.preload[i].store('tip:text', tmpdata.post_date+"<br />"+tmpdata.comment_count+" "+(tmpdata.comment_count == 1 ? "comment" : "comments"));
      }
      link.injectInside($('tagContainer'));
    }

    if (tagFlag) {
      // Set up tooltips
      var tipz = new Tips('.mosaic', { className: 'tipz', hideDelay: 50, showDelay: 50 });
      tipz.addEvents({
	'show': function(tip) { tip.fade(0.8); },
	'hide': function(tip) { tip.fade(0.0); }
      });
    }
    
    $('tagContainer').setStyles({'height': 'auto', 'overflow': 'auto'});

    // Change height whilst fading out progress indicator, then fade in images.
    this.progFx.start({
      '0': { 'height':  $('tagContainer').scrollHeight },
      '2': { 'opacity': 0 }
    }).start({
      '1': { 'opacity': 1 }
    });
  }
};
