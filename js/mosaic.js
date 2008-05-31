var Mosaic = {
	init: function() {
		var images = $$('img.mosaic');
		
		images.each(function(el,i){
			var fx = new Fx.Style(el, 'opacity', {duration:200, wait:false});
			fx.set(0.75);
			el.addEvent('mouseenter', function(){
				fx.start(1);
			});
			el.addEvent('mouseleave', function(){
				fx.start(0.75);
			});
		});
	}
};

window.addEvent('domready', Mosaic.init);
