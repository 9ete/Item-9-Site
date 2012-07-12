/*!
 *
 * Copyright 2011, Pete Lower
 * jQuery Code used on personal site
 * http://www.petelower.com
 *
 * Creation Date: Mon Dec 29 
 */

 $(document).ready(function()
{
	//test function to enable user to hide the test message from below
	$("p.test").click(function()
		{
			$(this).hide();
		});
		
	//output a test message to the screen for testing purposes between the ""
	$("p.test").html("");
	
	//force all off-site links to load in new window, while allowing on-site links to use the same window
	$("a").attr("target", function()
		{
			if(this.host == location.host) return "_self"
			else return "_blank";
		});
		
	$("a").attr("rel", function()
		{
			if(this.host == location.host) return "friend"
			else return "nofollow";
		});	
	  //setInterval (rotateImage, 4500);



	//Speed of the slideshow
	var speed = 4000;
	
	//You have to specify width and height in #slider CSS properties
	//After that, the following script will set the width and height accordingly
	$('#mask-gallery, #gallery li').width($('#slider').width());	
	$('#gallery').width($('#slider').width() * $('#gallery li').length);
	$('#mask-gallery, #gallery li, #mask-excerpt, #excerpt li').height($('#slider').height());
	
	//Assign a timer, so it will run periodically
	var run = setInterval('newsscoller(0)', speed);	
	
	$('#gallery li:first, #excerpt li:first').addClass('selected');

	//Pause the slidershow with clearInterval
	$('#btn-pause').click(function () {
		clearInterval(run);
		return false;
	});

	//Continue the slideshow with setInterval
	$('#btn-play').click(function () {
		run = setInterval('newsscoller(0)', speed);	
		return false;
	});
	
	//Next Slide by calling the function
	$('#btn-next').click(function () {
		newsscoller(0);	
		return false;
	});	

	//Previous slide by passing prev=1
	$('#btn-prev').click(function () {
		newsscoller(1);	
		return false;
	});	
	
	//Mouse over, pause it, on mouse out, resume the slider show
	$('#slider').hover(
	
		function() {
			clearInterval(run);
		}, 
		function() {
			run = setInterval('newsscoller(0)', speed);	
		}
	); 	
	
});


function newsscoller(prev) {

	//Get the current selected item (with selected class), if none was found, get the first item
	var current_image = $('#gallery li.selected').length ? $('#gallery li.selected') : $('#gallery li:first');
	var current_excerpt = $('#excerpt li.selected').length ? $('#excerpt li.selected') : $('#excerpt li:first');

	//if prev is set to 1 (previous item)
	if (prev) {
		
		//Get previous sibling
		var next_image = (current_image.prev().length) ? current_image.prev() : $('#gallery li:last');
		var next_excerpt = (current_excerpt.prev().length) ? current_excerpt.prev() : $('#excerpt li:last');
	
	//if prev is set to 0 (next item)
	} else {
		
		//Get next sibling
		var next_image = (current_image.next().length) ? current_image.next() : $('#gallery li:first');
		var next_excerpt = (current_excerpt.next().length) ? current_excerpt.next() : $('#excerpt li:first');
	}

	//clear the selected class
	$('#excerpt li, #gallery li').removeClass('selected');
	
	//reassign the selected class to current items
	next_image.addClass('selected');
	next_excerpt.addClass('selected');

	//Scroll the items
	$('#mask-gallery').scrollTo(next_image, 800);		
	$('#mask-excerpt').scrollTo(next_excerpt, 800);					
	
}

 
 
 
/*
 
//Upcomging shows rotater 
var images = new Array ('item-9-mad-hatters-iowa-city-band-yacht-club-St-Patricks-2012.jpg', 'images/item9Logo.jpg', 'images/item9altLogo.jpg','images/iowa-city-band-rock-music-item-9-mad-hatters.JPG','images/iowa-city-band-rock-music-live-entertainment.jpg');
var index = 1;

function rotateImage()
{
  $('#myImage').fadeOut('fast', function()
  {
    $(this).attr('src', images[index]);
 
    $(this).fadeIn('fast', function()
    {
      if (index == images.length-1)
      {
        index = 0;
      }
      else
      {
        index++;
      }
    });
  });
}



//HTML 5 SLIDER
$(window).load(function(){
	
	// We are listening to the window.load event, so we can be sure
	// that the images in the slideshow are loaded properly.


	// Testing wether the current browser supports the canvas element:
	var supportCanvas = 'getContext' in document.createElement('canvas');

	// The canvas manipulations of the images are CPU intensive,
	// this is why we are using setTimeout to make them asynchronous
	// and improve the responsiveness of the page.

	var slides = $('#slideshow li'),
		current = 0,
		slideshow = {width:0,height:0};

	setTimeout(function(){
		
		window.console && window.console.time && console.time('Generated In');
		
		if(supportCanvas){
			$('#slideshow img').each(function(){

				if(!slideshow.width){
					// Taking the dimensions of the first image:
					slideshow.width = this.width;
					slideshow.height = this.height;
				}
				
				// Rendering the modified versions of the images:
				createCanvasOverlay(this);
			});
		}
		
		window.console && window.console.timeEnd && console.timeEnd('Generated In');
		
		$('#slideshow .arrow').click(function(){
			var li			= slides.eq(current),
				canvas		= li.find('canvas'),
				nextIndex	= 0;

			// Depending on whether this is the next or previous
			// arrow, calculate the index of the next slide accordingly.
			
			if($(this).hasClass('next')){
				nextIndex = current >= slides.length-1 ? 0 : current+1;
			}
			else {
				nextIndex = current <= 0 ? slides.length-1 : current-1;
			}

			var next = slides.eq(nextIndex);
			
			if(supportCanvas){

				// This browser supports canvas, fade it into view:

				canvas.fadeIn(function(){
					
					// Show the next slide below the current one:
					next.show();
					current = nextIndex;
					
					// Fade the current slide out of view:
					li.fadeOut(function(){
						li.removeClass('slideActive');
						canvas.hide();
						next.addClass('slideActive');
					});
				});
			}
			else {
				
				// This browser does not support canvas.
				// Use the plain version of the slideshow.
				
				current=nextIndex;
				next.addClass('slideActive').show();
				li.removeClass('slideActive').hide();
			}
		});
		
	},100);

	// This function takes an image and renders
	// a version of it similar to the Overlay blending
	// mode in Photoshop.
	
	function createCanvasOverlay(image){

		var canvas			= document.createElement('canvas'),
			canvasContext	= canvas.getContext("2d");
		
		// Make it the same size as the image
		canvas.width = slideshow.width;
		canvas.height = slideshow.height;
		
		// Drawing the default version of the image on the canvas:
		canvasContext.drawImage(image,0,0);
		

		// Taking the image data and storing it in the imageData array:
		var imageData	= canvasContext.getImageData(0,0,canvas.width,canvas.height),
			data		= imageData.data;
		
		// Loop through all the pixels in the imageData array, and modify
		// the red, green, and blue color values.
		
		for(var i = 0,z=data.length;i<z;i++){
			
			// The values for red, green and blue are consecutive elements
			// in the imageData array. We modify the three of them at once:
			
			data[i] = ((data[i] < 128) ? (2*data[i]*data[i] / 255) : (255 - 2 * (255 - data[i]) * (255 - data[i]) / 255));
			data[++i] = ((data[i] < 128) ? (2*data[i]*data[i] / 255) : (255 - 2 * (255 - data[i]) * (255 - data[i]) / 255));
			data[++i] = ((data[i] < 128) ? (2*data[i]*data[i] / 255) : (255 - 2 * (255 - data[i]) * (255 - data[i]) / 255));
			
			// After the RGB elements is the alpha value, but we leave it the same.
			++i;
		}
		
		// Putting the modified imageData back to the canvas.
		canvasContext.putImageData(imageData,0,0);
		
		// Inserting the canvas in the DOM, before the image:
		image.parentNode.insertBefore(canvas,image);
	}
	
});



<!-- <script type="text/javascript">
$(document).ready(function() {
    $('.about_slideshow').cycle({
		fx: 'fade' // choose your transition type, ex: fade, scrollUp, shuffle, etc...
	});
	 $('#about_pete').cycle({
		fx:'fade' ,
		next: '#about_pete' 
		
	 })
	 
	 $('#about_max').cycle({
		fx:'fade' ,
		next: '#about_max' ,
		
		
	 });
	 $('#about_bryks').cycle({
		fx:'fade' ,
		next: '#about_bryks' ,
		
		
	 });
	  $('#about_amo').cycle({
		fx:'fade' ,
		next: '#about_amo' ,
		
		
	 });
	 $('#about_bob').cycle({
		fx:'fade' ,
		next: '#about_bob' ,

	 });
});
</script> -->

*/
