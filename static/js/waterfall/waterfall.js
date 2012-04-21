$(document).ready(function() {
	$('#waterfall').imagesLoaded(function(){
		$('#waterfall').masonry({
		  itemSelector: '.post-box',
		  isAnimated: true,
		  animationOptions: {
			easing: 'linear',
			duration: 400,
			queue: true
		  }
		});
	});
});