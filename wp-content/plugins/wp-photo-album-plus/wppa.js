// wppa.js
//
// conatins slideshow, theme and ajax code
//
// Version 4.6.1

// Part 1: Slideshow
//
// Slide show variables and functions
// Vars. The vars that have a name that starts with an underscore is an internal var
// The vars without leading underscore are 'external' and get a value from html

// 'External' variables (public)
var wppaFullValignFit = new Array();
var wppaFullFrameDelta = new Array();
var wppaAnimationSpeed;
var wppaImageDirectory;
var wppaAutoColumnWidth = false;
var wppaThumbnailAreaDelta;
var wppaSlideShowTimeOut = 2500;
var wppaFadeInAfterFadeOut = false;
var wppaTextFrameDelta = 0;
var wppaBoxDelta = 0;
var wppaPreambule;
var wppaThumbnailPitch = new Array();
var wppaFilmStripLength = new Array();
var wppaFilmStripMargin = new Array();
var wppaFilmStripAreaDelta = new Array();
var wppaFilmShowGlue;
var wppaIsMini = new Array();
var wppa_portrait_only = new Array();
var wppaSlideShow;				// = 'Slideshow' or its translation
var wppaPhoto;					// = 'Photo' or its translation
var wppaOf;						// = 'of' or its translation
var wppaNextPhoto;				// = 'Next photo' or its translation
var wppaPreviousPhoto;			// = 'Previous photo' or its translation
var wppaNextP;
var wppaPrevP;
var wppaStart = 'Start';		// defaults
var wppaStop = 'Stop';			//
var wppaPleaseName;
var wppaPleaseEmail;
var wppaPleaseComment;
var wppaRatingOnce = true;
var wppaUserName;
var wppaBGcolorNumbar = 'transparent';
var wppaBcolorNumbar = 'transparent';
var wppaBGcolorNumbarActive = 'transparent';
var wppaBcolorNumbarActive = 'transparent';

var wppaFontFamilyNumbar = '';
var wppaFontSizeNumbar = '';
var wppaFontColorNumbar = '';
var wppaFontWeightNumbar = '';
var wppaFontFamilyNumbarActive = '';
var wppaFontSizeNumbarActive = '';
var wppaFontColorNumbarActive = '';
var wppaFontWeightNumbarActive = '';


var wppaNumbarMax = '10';
var wppaAjaxUrl = '';
var wppaNextOnCallback = false;
var wppaRatingUseAjax = false;
var wppaStarOpacity = 0.2;
var wppaTickImg = new Image(); 
var wppaClockImg = new Image();
var wppaSlideWrap = true;
var wppaLightBox = '';
var wppaEmailRequired = true;
var wppaSlideBorderWidth = 0;
var wppaSlideInitRunning = new Array();
var wppaAnimationType = 'fadeover';
var wppaSlidePause = new Array();
var wppaSlideBlank = new Array();
var wppaRatingMax = 5;
var wppaRatingDisplayType = 'graphic';
var wppaRatingPrec = 2;
var wppaFilmPageSize = new Array();

// 'Internal' variables (private)
var _wppaId = new Array();
var _wppaAvg = new Array();
var _wppaMyr = new Array();
var _wppaVRU = new Array();
var _wppaLinkUrl = new Array();
var _wppaLinkTitle = new Array();
var _wppaLinkTarget = new Array();
var _wppaCommentHtml = new Array();
var _wppaIptcHtml = new Array();
var _wppaExifHtml = new Array();
var _wppaToTheSame = false;
var _wppaSlides = new Array();
var _wppaNames = new Array();
var _wppaDsc = new Array();
var _wppaCurIdx = new Array();
var _wppaNxtIdx = new Array();
var _wppaTimeOut = new Array();
var _wppaSSRuns = new Array();
var _wppaFg = new Array();
var _wppaTP = new Array();
var _wppaIsBusy = new Array();
var _wppaFirst = new Array();
var _wppaVIP = false;
var _wppaTextDelay;
var _wppaUrl = new Array();
var _wppaLastVote = 0;
var _wppaSkipRated = new Array();
var _wppaLbTitle = new Array();
var _wppaStateCount = 0;
var _wppaDidGoto = new Array();
var _wppaTopMoc = 0;

jQuery(document).ready(function(){
	_wppaLog('ready', 0);
	if (wppaAutoColumnWidth) _wppaDoAutocol(0);
	_wppaTextDelay = wppaAnimationSpeed;
	if (wppaFadeInAfterFadeOut) _wppaTextDelay *= 2;
});

// First the external entrypoints that may be called directly from HTML
// These functions check the validity and store the users request to be executed later if busy and if applicable.

// This is an entrypoint to load the slide data
function wppaStoreSlideInfo(mocc, id, url, size, width, height, name, desc, photoid, avgrat, myrat, rateurl, linkurl, linktitle, linktarget, iwtimeout, commenthtml, iptchtml, exifhtml, lbtitle) {
	if ( ! _wppaSlides[mocc] ) {
		_wppaSlides[mocc] = new Array();
		_wppaNames[mocc] = new Array();
		_wppaDsc[mocc] = new Array();
		_wppaCurIdx[mocc] = -1;
		_wppaNxtIdx[mocc] = 0;
		if (parseInt(iwtimeout) > 0) _wppaTimeOut[mocc] = parseInt(iwtimeout);
		else _wppaTimeOut[mocc] = wppaSlideShowTimeOut;
		_wppaSSRuns[mocc] = false;
		_wppaTP[mocc] = -2;	// -2 means NO, index for _wppaStartStop otherwise
		_wppaFg[mocc] = 0;
		_wppaIsBusy[mocc] = false;
		_wppaFirst[mocc] = true;
		wppaFullValignFit[mocc] = false;
		_wppaId[mocc] = new Array();
		_wppaAvg[mocc] = new Array();
		_wppaMyr[mocc] = new Array();
		_wppaVRU[mocc] = new Array();
		wppa_portrait_only[mocc] = false;
		_wppaLinkUrl[mocc] = new Array(); // linkurl;
		_wppaLinkTitle[mocc] = new Array(); // linktitle;
		_wppaLinkTarget[mocc] = new Array();
		_wppaCommentHtml[mocc] = new Array();
		_wppaIptcHtml[mocc] = new Array();
		_wppaExifHtml[mocc] = new Array();
		_wppaUrl[mocc] = new Array();
		_wppaSkipRated[mocc] = false;
		_wppaLbTitle[mocc] = new Array();
		_wppaDidGoto[mocc] = false;
		wppaSlidePause[mocc] = false;
		_wppaTopMoc = mocc;
	}
    _wppaSlides[mocc][id] = ' src="' + url + '" alt="' + name + '" class="theimg big" ';
		// Add 'old' width and height only for non-auto
		if ( ! wppaAutoColumnWidth ) _wppaSlides[mocc][id] += 'width="' + width + '" height="' + height + '" ';
	_wppaSlides[mocc][id] += 'style="' + size + '; display:none;">';	// was block
    _wppaNames[mocc][id] = name;
    _wppaDsc[mocc][id] = desc;
	_wppaId[mocc][id] = photoid;		// reqd for rating and comment
	_wppaAvg[mocc][id] = avgrat;		// avg ratig value
	_wppaMyr[mocc][id] = myrat;		// my rating
	_wppaVRU[mocc][id] = rateurl;		// url that performs the vote and returns to the page
	_wppaLinkUrl[mocc][id] = linkurl;
	_wppaLinkTitle[mocc][id] = linktitle;
	
	if (linktarget != '') _wppaLinkTarget[mocc][id] = linktarget;
	else if (wppaSlideBlank[mocc]) _wppaLinkTarget[mocc][id] = '_blank';
	else _wppaLinkTarget[mocc][id] = '_self';
	
	_wppaCommentHtml[mocc][id] = commenthtml;
	_wppaIptcHtml[mocc][id] = iptchtml;
	_wppaExifHtml[mocc][id] = exifhtml;
	_wppaUrl[mocc][id] = url;
	_wppaLbTitle[mocc][id] = lbtitle;
}

function wppaSpeed(mocc, faster) {
	// Can change speed of slideshow only when running
	if ( _wppaSSRuns[mocc] ) {
		_wppaSpeed(mocc, faster);
	}
}

function wppaStopShow(mocc) {
	if ( _wppaSSRuns[mocc] ) {		// Stop it
		_wppaStop(mocc);
	}
}

function wppaStartStop(mocc, index) {
	// The application contains various togglers for start/stop
	// The busy flag will be reset at the end of the NextSlide procedure
	if ( _wppaIsBusy[mocc] ) {					// Busy...
		_wppaTP[mocc] = index;		// Remember there is a toggle pending
	}
	else { 										// Not busy...
		if ( _wppaSSRuns[mocc] ) {		// Stop it
			_wppaStop(mocc);
		}
		else {	// Start it
			_wppaStart(mocc, index);
		}
	}
}

function wppaBbb(mocc, where, act) {
	// Big Browsing Buttons only work when stopped
	if ( ! _wppaSSRuns[mocc] ) {
		_wppaBbb(mocc, where, act);
	}
}

function wppaRateIt(mocc, value) {
	_wppaRateIt(mocc, value);
}

function wppaPrev(mocc) {
	_wppaDidGoto[mocc] = true;
	if ( ! _wppaSSRuns[mocc] ) {
		_wppaPrev(mocc);
	}
}

function wppaPrevN(mocc, n) {
	_wppaDidGoto[mocc] = true;
	if ( ! _wppaSSRuns[mocc] ) {
		_wppaPrevN(mocc, n);
	}
}

function wppaNext(mocc) {
	_wppaDidGoto[mocc] = true;
	if ( ! _wppaSSRuns[mocc] ) {
		_wppaNext(mocc);
	}
}

function wppaNextN(mocc, n) {
	_wppaDidGoto[mocc] = true;
	if ( ! _wppaSSRuns[mocc] ) {
		_wppaNextN(mocc, n);
	}
}

function wppaFollowMe(mocc, idx) {
	if ( ! _wppaSSRuns[mocc] ) {
		_wppaFollowMe(mocc, idx);
	}
}

function wppaLeaveMe(mocc, idx) {
	if ( ! _wppaSSRuns[mocc] ) {
		_wppaLeaveMe(mocc, idx);
	}
}

function wppaGoto(mocc, idx) {
	// Goto the requested slide if the slideshow stopped
	_wppaDidGoto[mocc] = true;
	if ( ! _wppaSSRuns[mocc] ) {
		_wppaGoto(mocc, idx);
	}
}

function wppaGotoKeepState(mocc, idx) {
	// Goto the requested slide and preserve running state
	_wppaDidGoto[mocc] = true;
	_wppaGotoKeepState(mocc, idx);
}
function _wppaGotoKeepState(mocc, idx) {	
	if ( _wppaSSRuns[mocc] ) {
		_wppaGotoRunning(mocc,idx);
	}
	else {
		_wppaGoto(mocc,idx);
	}
}


function wppaGotoRunning(mocc, idx) {
	// Goto the requested slide and start running
	_wppaDidGoto[mocc] = true;
	_wppaGotoRunning(mocc, idx);
}

function wppaValidateComment(mocc) {
	return _wppaValidateComment(mocc);
}

function _wppaNextSlide(mocc, mode) {
	_wppaLog('NextSlide', mocc);

	var fg = _wppaFg[mocc];
	var bg = 1 - fg;

	// Paused??
	if ( mode == 'auto' ) {
		if ( wppaSlidePause[mocc] ) {
			jQuery('#theimg'+fg+'-'+mocc).attr("title", wppaSlidePause[mocc]);
			setTimeout('_wppaNextSlide('+mocc+', "auto")', 250);	// Retry after 250 ms.
			return;
		}
	}
	// Kill an old timed request, while stopped
	if ( ! _wppaSSRuns[mocc] && mode == 'auto' ) return; 
	// Empty slideshow?
	if ( ! _wppaSlides[mocc] ) return;
	// Do not animate single image
	if ( _wppaSlides[mocc].length < 2 && !_wppaFirst[mocc] ) return; 
	// Reset request?
	if ( ! _wppaSSRuns[mocc] && mode == 'reset' ) _wppaSSRuns[mocc] = true;

	// No longer busy voting
	_wppaVIP = false;
	
	// Set the busy flag
	_wppaIsBusy[mocc] = true;

	// Hide metadata while changing image
	if ( _wppaSSRuns[mocc] ) _wppaShowMetaData(mocc, 'hide');
	
	// Find index of next slide if in auto mode and not stop in progress
	if (_wppaSSRuns[mocc]) {
		_wppaNxtIdx[mocc] = _wppaCurIdx[mocc] + 1;
		if (_wppaNxtIdx[mocc] == _wppaSlides[mocc].length) _wppaNxtIdx[mocc] = 0;
	}

	// Set numbar backgrounds and fonts
	jQuery('[id^=wppa-numbar-' + mocc + '-]').css({	backgroundColor: wppaBGcolorNumbar, 
													borderColor: wppaBcolorNumbar,
													fontFamily: wppaFontFamilyNumbar,
													fontSize: wppaFontSizeNumbar,
													color: wppaFontColorNumbar,
													fontWeight: wppaFontWeightNumbar
													});

	jQuery("#wppa-numbar-" + mocc + "-" + _wppaNxtIdx[mocc]).css({	backgroundColor: wppaBGcolorNumbarActive, 
																	borderColor: wppaBcolorNumbarActive,
																	fontFamily: wppaFontFamilyNumbarActive,
																	fontSize: wppaFontSizeNumbarActive,
																	color: wppaFontColorNumbarActive,
																	fontWeight: wppaFontWeightNumbarActive
																	});

	// too many? all dots except current
	if (_wppaSlides[mocc].length > wppaNumbarMax) {
		jQuery('[id^=wppa-numbar-' + mocc + '-]').html(' . ');
		jQuery("#wppa-numbar-" + mocc + "-" + _wppaNxtIdx[mocc]).html(' ' + (_wppaNxtIdx[mocc]+1) + ' ');
	}
	
    // first:
    if (_wppaFirst[mocc]) {
	    if (_wppaCurIdx[mocc] != -1) {
			wppaMakeTheSlideHtml(mocc, '0', _wppaCurIdx[mocc]);
		}
		wppaMakeTheSlideHtml(mocc, '1', _wppaNxtIdx[mocc]);
	    
		// Display name, description and comments
		jQuery("#imagedesc-"+mocc).html(_wppaDsc[mocc][_wppaCurIdx[mocc]]);
		jQuery("#imagetitle-"+mocc).html(_wppaNames[mocc][_wppaCurIdx[mocc]]);
		jQuery("#comments-"+mocc).html(_wppaCommentHtml[mocc][_wppaCurIdx[mocc]]);
		jQuery("#iptc-"+mocc).html(_wppaIptcHtml[mocc][_wppaCurIdx[mocc]]);
		jQuery("#exif-"+mocc).html(_wppaExifHtml[mocc][_wppaCurIdx[mocc]]);
		
		// Display counter and arrow texts
		if (document.getElementById('counter-'+mocc)) {
			if (wppaIsMini[mocc]) {
				jQuery('#prev-arrow-'+mocc).html(wppaPrevP);
				jQuery('#next-arrow-'+mocc).html(wppaNextP);
			}
			else {
				jQuery('#prev-arrow-'+mocc).html(wppaPreviousPhoto);
				jQuery('#next-arrow-'+mocc).html(wppaNextPhoto);
			}
		}
    }
    // end first
    else {    	// load next img (backg)
		wppaMakeTheSlideHtml(mocc, bg, _wppaNxtIdx[mocc]);
    }
	
	_wppaLoadSpinner(mocc);
	
	_wppaFirst[mocc] = false;
	
	// See if the filmstrip needs wrap around before shifting to the right location
	_wppaCheckRewind(mocc);

	if (wppaAutoColumnWidth) _wppaDoAutocol(mocc);
	// Give free for a while to enable rendering of what we have done so far
	setTimeout('_wppaNextSlide_2('+mocc+')', 10);	// to be continued
}

function _wppaNextSlide_2(mocc) {
	_wppaLog('NextSlide_2', mocc);

	var fg, bg;	

	fg = _wppaFg[mocc];
	bg = 1 - fg;
	// Wait for load complete
	// If we are here as a result of an onstatechange event, the background image is no longer available and will not become complete
	if (document.getElementById('theimg'+bg+"-"+mocc)) { 
		if (!document.getElementById('theimg'+bg+"-"+mocc).complete) {
			setTimeout('_wppaNextSlide_2('+mocc+')', 100);	// Try again after 100 ms
			return;
		}
	}
	// Update lightbox
	wppaUpdateLightboxes();
	// Remove spinner
	_wppaUnloadSpinner(mocc);
	// Do autocol if required
	if (wppaAutoColumnWidth) _wppaDoAutocol(mocc);
	// Hide subtitles
	if (_wppaSSRuns[mocc] != -1) {	// not stop in progress
		if (!_wppaToTheSame) {
			_wppaShowMetaData(mocc, 'hide');
		}
	}
	// change foreground
	_wppaFg[mocc] = 1 - _wppaFg[mocc];
	fg = _wppaFg[mocc];
	bg = 1 - fg;
	setTimeout('_wppaNextSlide_3('+mocc+')', 10);
}

function _wppaNextSlide_3(mocc) {
	_wppaLog('NextSlide_3', mocc);

	var nw 		= _wppaFg[mocc];
	var ol 		= 1 - nw;
	
	var olIdx 	= _wppaCurIdx[mocc];
	var nwIdx 	= _wppaNxtIdx[mocc];
	
	var olSli	= "#theslide"+ol+"-"+mocc;
	var nwSli 	= "#theslide"+nw+"-"+mocc;
	
	var olImg	= "#theimg"+ol+"-"+mocc;
	var nwImg	= "#theimg"+nw+"-"+mocc;
	
	var w 		= parseInt(jQuery(olSli).css('width'));
	var dir 	= 'nil';

	
	if (olIdx == nwIdx) dir = 'none';
	if (olIdx == nwIdx-1) dir = 'left';
	if (olIdx == nwIdx+1) dir = 'right';
	if (olIdx == _wppaSlides[mocc].length-1 && nwIdx == 0 && wppaSlideWrap) dir = 'left';
	if (olIdx == 0 && nwIdx == _wppaSlides[mocc].length-1 && wppaSlideWrap) dir = 'right';
	// Not known yet?
	if (dir == 'nil') {
		if (olIdx < nwIdx) dir = 'left';
		else dir = 'right';
	}

	// Repair standard css
	jQuery(olSli).css({marginLeft:0, width:w});
	jQuery(nwSli).css({marginLeft:0, width:w});
	
	switch (wppaAnimationType) {
	
		case 'fadeover': 
			jQuery(olImg).fadeOut(wppaAnimationSpeed); 
			jQuery(nwImg).fadeIn(wppaAnimationSpeed, _wppaNextSlide_4(mocc)); 
			break;
		
		case 'fadeafter': 
			jQuery(olImg).fadeOut(wppaAnimationSpeed); 
			jQuery(nwImg).delay(wppaAnimationSpeed).fadeIn(wppaAnimationSpeed, _wppaNextSlide_4(mocc)); 
			break;
		
		case 'swipe':
			switch (dir) {
				case 'left':
					jQuery(olSli).animate({marginLeft:-w+"px"}, wppaAnimationSpeed, "swing");
					jQuery(nwSli).css({marginLeft:w+"px"});
					jQuery(nwImg).fadeIn(10);
					jQuery(nwSli).animate({marginLeft:0+"px"}, wppaAnimationSpeed, "swing", _wppaNextSlide_4(mocc));
					break;
				case 'right':
					jQuery(olSli).animate({marginLeft:w+"px"}, wppaAnimationSpeed, "swing");
					jQuery(nwSli).css({marginLeft:-w+"px"});
					jQuery(nwImg).fadeIn(10);
					jQuery(nwSli).animate({marginLeft:0+"px"}, wppaAnimationSpeed, "swing", _wppaNextSlide_4(mocc));
					break;
				case 'none':
					jQuery(nwImg).fadeIn(10);
					setTimeout('_wppaNextSlide_4('+mocc+')', 10);
					break;
			}
			break;
		
		case 'stackon':
			switch (dir) {
				case 'left':
					jQuery(olSli).css({zIndex:80});
					jQuery(nwSli).css({marginLeft:w+"px", zIndex:81});
					jQuery(nwImg).fadeIn(10);
					jQuery(olImg).delay(wppaAnimationSpeed).fadeOut(10);
					jQuery(nwSli).animate({marginLeft:0+"px"}, wppaAnimationSpeed, "swing", _wppaNextSlide_4(mocc));
					break;
				case 'right':
					jQuery(olSli).css({zIndex:80});
					jQuery(nwSli).css({marginLeft:-w+"px", zIndex:81});
					jQuery(nwImg).fadeIn(10);
					jQuery(olImg).delay(wppaAnimationSpeed).fadeOut(10);
					jQuery(nwSli).animate({marginLeft:0+"px"}, wppaAnimationSpeed, "swing", _wppaNextSlide_4(mocc));
					break;
				case 'none':
					jQuery(nwImg).fadeIn(10);
					setTimeout('_wppaNextSlide_4('+mocc+')', 10);
					break;
			}
			break;
			
		case 'stackoff':
			switch (dir) {
				case 'left':
					jQuery(olSli).css({marginLeft:0, zIndex:81});
					jQuery(olSli).animate({marginLeft:-w+"px"}, wppaAnimationSpeed, "swing", _wppaNextSlide_4(mocc));
					jQuery(nwSli).css({marginLeft:0, zIndex:80});
					jQuery(nwImg).fadeIn(10);
					jQuery(olImg).delay(wppaAnimationSpeed).fadeOut(10);
					break;
				case 'right':
					jQuery(olSli).css({marginLeft:0, zIndex:81});
					jQuery(olSli).animate({marginLeft:w+"px"}, wppaAnimationSpeed, "swing", _wppaNextSlide_4(mocc));
					jQuery(nwSli).css({marginLeft:0, zIndex:80});
					jQuery(nwImg).fadeIn(10);
					jQuery(olImg).delay(wppaAnimationSpeed).fadeOut(10);
					break;
				case 'none':
					jQuery(nwImg).fadeIn(10);
					setTimeout('_wppaNextSlide_4('+mocc+')', 10);
					break;
			}
			break;
			
		case 'turnover':
			switch (dir) {
				case 'left':
					jQuery(olSli).css({zIndex:81});
					jQuery(olSli).animate({width:0}, wppaAnimationSpeed, "swing");
					jQuery(olImg).animate({marginLeft:0, width:0, paddingLeft:0, paddingRight:0}, wppaAnimationSpeed, "swing", _wppaNextSlide_4(mocc));
					jQuery(nwSli).css({width:w, zIndex:80});
					jQuery(nwImg).fadeIn(10);
					jQuery(olImg).fadeOut(10);
					break;
				case 'right':
					var nwImgWid = parseInt(jQuery(nwImg).css('width'));
					var nwMarLft = parseInt(jQuery(nwImg).css('marginLeft'));
					jQuery(olSli).css({zIndex:80});
					jQuery(nwSli).css({zIndex:81, width:0});
					jQuery(nwImg).css({width:0, marginLeft:0});
					jQuery(nwImg).fadeIn(10);
					jQuery(nwSli).animate({width:w}, wppaAnimationSpeed, "swing");
					jQuery(nwImg).animate({width:nwImgWid, marginLeft:nwMarLft}, wppaAnimationSpeed, "swing", _wppaNextSlide_4(mocc));
					jQuery(olImg).delay(wppaAnimationSpeed).fadeOut(10);
					break;
				case 'none':
					jQuery(nwImg).fadeIn(10);
					setTimeout('_wppaNextSlide_4('+mocc+')', 10);
					break;
				}
			break;
			
		default:
			alert('Animation type '+wppaAnimationType+' is not supported in this version');	
			
	}
}

function _wppaNextSlide_4(mocc) {
	_wppaLog('NextSlide_4', mocc);

	var nw = _wppaFg[mocc];
	var ol = 1-nw;
	
    // Next is now current // put here for swipe
	_wppaCurIdx[mocc] = _wppaNxtIdx[mocc];

	// set height to fit if reqd
	if (wppa_portrait_only[mocc]) {
		h = jQuery('#theimg'+_wppaFg[mocc]+'-'+mocc).css('height');
		jQuery('#slide_frame-'+mocc).css('height', parseInt(h)+'px');
	}
	else if (wppaFullValignFit[mocc]) {
		h = parseInt(jQuery('#theimg'+_wppaFg[mocc]+'-'+mocc).css('height')) + wppaFullFrameDelta[mocc];
		jQuery('#slide_frame-'+mocc).css('height', h+'px');
		jQuery('.bbb-'+mocc).css('height', h+'px');
		jQuery('#slide_frame-'+mocc).css('minHeight', '0px');
	}

	// Display counter and arrow texts
	if (wppaIsMini[mocc]) {
		jQuery('#counter-'+mocc).html( (_wppaCurIdx[mocc]+1)+' / '+_wppaSlides[mocc].length );
	}
	else {
		jQuery('#counter-'+mocc).html( wppaPhoto+' '+(_wppaCurIdx[mocc]+1)+' '+wppaOf+' '+_wppaSlides[mocc].length );
	}

	// Update breadcrumb
	jQuery('#bc-pname-'+mocc).html( _wppaNames[mocc][_wppaCurIdx[mocc]] );

	// Adjust filmstrip
	var xoffset;
	xoffset = wppaFilmStripLength[mocc] / 2 - (_wppaCurIdx[mocc] + 0.5 + wppaPreambule) * wppaThumbnailPitch[mocc] - wppaFilmStripMargin[mocc];
	if (wppaFilmShowGlue) xoffset -= (wppaFilmStripMargin[mocc] * 2 + 2);	// Glue
	jQuery('#wppa-filmstrip-'+mocc).animate({marginLeft: xoffset+'px'});
	
	// Set rating mechanism
	_wppaSetRatingDisplay(mocc);
	
	// Wait for almost next slide
	setTimeout('_wppaNextSlide_5('+mocc+')', _wppaTextDelay); 
}

function _wppaNextSlide_5(mocc) {
	_wppaLog('NextSlide_5', mocc);

	// If we are going to the same slide, there is no need to hide and restore the subtitles and commentframe
	if (!_wppaToTheSame) {	
		// Restore subtitles
		jQuery('#imagedesc-'+mocc).html(_wppaDsc[mocc][_wppaCurIdx[mocc]]);
		jQuery('#imagetitle-'+mocc).html(_wppaNames[mocc][_wppaCurIdx[mocc]]);
		// Restore comments html
		jQuery("#comments-"+mocc).html(_wppaCommentHtml[mocc][_wppaCurIdx[mocc]]);
		// Restor IPTC
		jQuery("#iptc-"+mocc).html(_wppaIptcHtml[mocc][_wppaCurIdx[mocc]]);
		jQuery("#exif-"+mocc).html(_wppaExifHtml[mocc][_wppaCurIdx[mocc]]);

	}
	_wppaToTheSame = false;					// This has now been worked out

	// End of non wrapped show?
	if ( _wppaSSRuns[mocc] && ! wppaSlideWrap && ( ( _wppaCurIdx[mocc] + 1 ) == _wppaSlides[mocc].length ) ) {  
		_wppaIsBusy[mocc] = false;
		_wppaStop(mocc);	// stop
		return;
	}

	// Re-display the metadata
	_wppaShowMetaData(mocc, 'show'); 
	
	// Almost done, finalize
	if ( _wppaTP[mocc] != -2 ) {		// A Toggle pending?
		var index = _wppaTP[mocc];		// Remember the pending startstop request argument
		_wppaTP[mocc] = -2;				// Reset the pending toggle
		wppaStartStop(mocc, index);		// Do as if the toggle request happens now
	}
	else {								// No toggle pending
		wppaUpdateLightboxes(); 		// Refresh lighytbox
		// Update addthis url and title if ( ( this is non-mini ) AND ( this is the only running non-mini OR there are no running non-minis ) )
		if ( ! wppaIsMini[mocc] ) {	// This is a non-mini
			var nRuns = 0;
			var i=1;
			while (i<=_wppaTopMoc) {
				if ( typeof(_wppaSlides[i]) != 'undefined' && _wppaSSRuns[i] && ! wppaIsMini[i]) {
					nRuns++;
				}
				i++;
			}
			if ( nRuns == 0 || ( nRuns == 1 && _wppaSSRuns[mocc] ) ) {	// No running OR This is the only running
				wppaUpdateAddThisUrl(wppaGetCurrentFullUrl(mocc, _wppaCurIdx[mocc]), _wppaNames[mocc][_wppaCurIdx[mocc]]);	
				wppaPushStateSlide(mocc, _wppaCurIdx[mocc]);					// Add to history stack
			}
		}
		// If running: Wait for next slide
		if (_wppaSSRuns[mocc]) {				
			setTimeout('_wppaNextSlide('+mocc+', "auto")', _wppaTimeOut[mocc]); 
		}	
	}
	_wppaDidGoto[mocc] = false;					// Is worked out now
	_wppaIsBusy[mocc] = false;					// No longer busy
}
 
function wppaMakeTheSlideHtml(mocc, bgfg, idx) {
 
	if (_wppaLinkUrl[mocc][idx] != '') {	// Link explicitly given
		jQuery("#theslide"+bgfg+"-"+mocc).html(	'<a href="'+_wppaLinkUrl[mocc][idx]+'" target="'+_wppaLinkTarget[mocc][idx]+'" title="'+_wppaLinkTitle[mocc][idx]+'">'+
													'<img title="'+_wppaLinkTitle[mocc][idx]+'" id="theimg'+bgfg+'-'+mocc+'" '+_wppaSlides[mocc][idx]+
												'</a>');
	}
	else {
		if (wppaLightBox == '') {			// No link and no lightbox
			jQuery("#theslide"+bgfg+"-"+mocc).html('<img title="'+_wppaNames[mocc][idx]+'" id="theimg'+bgfg+'-'+mocc+'" '+_wppaSlides[mocc][idx]);
		}
		else {								// Lightbox
			var html = '';
			var i = 0;
			// Before current slide	// This does NOT work on lightbox 3 !
			if (wppaLightBox=='wppa') {
				while (i<idx) {
					html += '<a href="'+_wppaUrl[mocc][i]+'" title="'+_wppaLbTitle[mocc][i]+'" rel="'+wppaLightBox+'[slide-'+mocc+'-'+bgfg+']"></a>';
						i++;
				}
			}
			// Current slide
			html += '<a href="'+_wppaUrl[mocc][idx]+'" title="'+_wppaLbTitle[mocc][idx]+'" rel="'+wppaLightBox+'[slide-'+mocc+'-'+bgfg+']">'+
					'<img title="'+_wppaNames[mocc][idx]+'" id="theimg'+bgfg+'-'+mocc+'" '+_wppaSlides[mocc][idx]+
					'</a>';
			// After current slide // This does NOT work on lightbox 3 !
			if (wppaLightBox=='wppa') {
				i = idx + 1;
				while (i<_wppaUrl[mocc].length) {
					html += '<a href="'+_wppaUrl[mocc][i]+'" title="'+_wppaLbTitle[mocc][i]+'" rel="'+wppaLightBox+'[slide-'+mocc+'-'+bgfg+']"></a>';
					i++;
				}
			}
			jQuery("#theslide"+bgfg+"-"+mocc).html(html);
		}
	}
	// jQuery("#theimg"+bgfg+"-"+mocc).hide();	// == display:none
}

function wppaUpdateLightboxes() {

	if (typeof(myLightbox)!="undefined") myLightbox.updateImageList();	// Lightbox-3
	wppaInitOverlay();													// Native wppa lightbox
}

function _wppaNext(mocc) {
	_wppaLog('Next', mocc);

	// Check for end of non wrapped show
	if ( ! wppaSlideWrap && _wppaCurIdx[mocc] == (_wppaSlides[mocc].length -1) ) return;
	// Find next index
	_wppaNxtIdx[mocc] = _wppaCurIdx[mocc] + 1;
	if (_wppaNxtIdx[mocc] == _wppaSlides[mocc].length) _wppaNxtIdx[mocc] = 0;
	// And go!
	_wppaNextSlide(mocc, 0);
}

function _wppaNextN(mocc, n) {
	_wppaLog('NextN', mocc);

	// Check for end of non wrapped show
	if ( ! wppaSlideWrap && _wppaCurIdx[mocc] >= (_wppaSlides[mocc].length - n) ) return;
	// Find next index
	_wppaNxtIdx[mocc] = _wppaCurIdx[mocc] + n;
	while (_wppaNxtIdx[mocc] >= _wppaSlides[mocc].length) _wppaNxtIdx[mocc] -= _wppaSlides[mocc].length;
	// And go!
	_wppaNextSlide(mocc, 0);
}

function _wppaNextOnCallback(mocc) {
	_wppaLog('NextOnCallback', mocc);

	// Check for end of non wrapped show
	if ( ! wppaSlideWrap && _wppaCurIdx[mocc] == (_wppaSlides[mocc].length -1) ) return;
	// Check for skip rated after rating
	if ( _wppaSkipRated[mocc] ) {
		var now = _wppaCurIdx[mocc];
		var idx = now + 1;
		if (idx == _wppaSlides[mocc].length) idx = 0;	// wrap?
		var next = idx; // assume simple next
		if ( _wppaMyr[mocc][next] != 0 ) {		// Already rated, skip
			idx++;	// try next
			if (idx == _wppaSlides[mocc].length) idx = 0;	// wrap?
			while (idx != next && _wppaMyr[mocc][idx] != 0) {	// still rated, skip
				idx ++;	// try next
				if (idx == _wppaSlides[mocc].length) idx = 0;	// wrap?
			}	// either idx == next or not rated
			next = idx;
		}
		_wppaNxtIdx[mocc] = next;
	}
	else {	// Normal situation
		_wppaNxtIdx[mocc] = _wppaCurIdx[mocc] + 1;
		if (_wppaNxtIdx[mocc] == _wppaSlides[mocc].length) _wppaNxtIdx[mocc] = 0;
	}
	_wppaNextSlide(mocc, 0);
}

function _wppaPrev(mocc) {
	_wppaLog('Prev', mocc);
	
	// Check for begin of non wrapped show
	if ( ! wppaSlideWrap && _wppaCurIdx[mocc] == 0 ) return;
	// Find previous index
	_wppaNxtIdx[mocc] = _wppaCurIdx[mocc] - 1;
	if (_wppaNxtIdx[mocc] < 0) _wppaNxtIdx[mocc] = _wppaSlides[mocc].length - 1;
	// And go!
	_wppaNextSlide(mocc, 0);
}

function _wppaPrevN(mocc, n) {
	_wppaLog('PrevN', mocc);
	
	// Check for begin of non wrapped show
	if ( ! wppaSlideWrap && _wppaCurIdx[mocc] < n ) return;
	// Find previous index
	_wppaNxtIdx[mocc] = _wppaCurIdx[mocc] - n;
	while (_wppaNxtIdx[mocc] < 0) _wppaNxtIdx[mocc] += _wppaSlides[mocc].length;
	// And go!
	_wppaNextSlide(mocc, 0);
}

function _wppaGoto(mocc, idx) {
	_wppaLog('Goto', mocc);
	
	_wppaToTheSame = (_wppaNxtIdx[mocc] == idx);
	_wppaNxtIdx[mocc] = idx;
	_wppaNextSlide(mocc, 0);
}

function _wppaGotoRunning(mocc, idx) {
	//wait until not bussy
	if (_wppaIsBusy[mocc]) { 
		setTimeout('_wppaGotoRunning('+mocc+',' + idx + ')', 10);	// Try again after 10 ms
		return;
	}
    
	_wppaLog('GotoRunning', mocc);

	_wppaSSRuns[mocc] = false; // we don't want timed loop to occur during our work
    
	_wppaToTheSame = (_wppaNxtIdx[mocc] == idx);
	_wppaNxtIdx[mocc] = idx;
	_wppaNextSlide(mocc, "manual"); // enqueue new transition
    
	_wppaGotoContinue(mocc);
}

function _wppaGotoContinue(mocc){
	if (_wppaIsBusy[mocc]) {
		setTimeout('_wppaGotoContinue('+mocc+')', 10);	// Try again after 10 ms
		return;
	}
	setTimeout('_wppaNextSlide('+mocc+', "reset")', _wppaTimeOut[mocc] + 10); //restart slideshow after new timeout
}

function _wppaStart(mocc, idx) {
	_wppaLog('Start', mocc);
	
	if ( idx == -2 ) {	// Init at first without my rating
		var i = 0;
		idx = 0;
		_wppaSkipRated[mocc] = true;
		if ( _wppaMyr[mocc][i] != 0 ) {
			while (i < _wppaSlides[mocc].length) {
				if ( idx == 0 && _wppaMyr[mocc][i] == 0 ) idx = i;
				i++;
			}
		}
	}

	if ( idx > -1 ) {	// Init still at index idx
		jQuery('#startstop-'+mocc).html( wppaStart+' '+wppaSlideShow ); 
		jQuery('#speed0-'+mocc).css('visibility', 'hidden');
		jQuery('#speed1-'+mocc).css('visibility', 'hidden');
		_wppaNxtIdx[mocc] = idx;
		_wppaCurIdx[mocc] = idx;
		_wppaNextSlide(mocc, 0);
		_wppaShowMetaData(mocc, 'show');
	}
	else {	// idx == -1, start from where you are
		_wppaSSRuns[mocc] = true;
		_wppaNextSlide(mocc, 0);
		jQuery('#startstop-'+mocc).html( wppaStop );
		jQuery('#speed0-'+mocc).css('visibility', 'visible');
		jQuery('#speed1-'+mocc).css('visibility', 'visible');
		_wppaShowMetaData(mocc, 'hide');	
		jQuery('#bc-pname-'+mocc).html(wppaSlideShow);
	}
	
	// Both cases:
	_wppaSetRatingDisplay(mocc);
}

function _wppaStop(mocc) {
	_wppaLog('Stop', mocc);
	
    _wppaSSRuns[mocc] = false;
    jQuery('#startstop-'+mocc).html( wppaStart+' '+wppaSlideShow );  
	jQuery('#speed0-'+mocc).css('visibility', 'hidden');
	jQuery('#speed1-'+mocc).css('visibility', 'hidden');
	_wppaShowMetaData(mocc, 'show');
	jQuery('#bc-pname-'+mocc).html( _wppaNames[mocc][_wppaCurIdx[mocc]] );
}

function _wppaSpeed(mocc, faster) {
	_wppaLog('Speed', 0);
	
    if (faster) {
        if (_wppaTimeOut[mocc] > 500) _wppaTimeOut[mocc] /= 1.5;
    }
    else {
        if (_wppaTimeOut[mocc] < 60000) _wppaTimeOut[mocc] *= 1.5;
    }
}

function _wppaLoadSpinner(mocc) {
	_wppaLog('LoadSpinner', mocc);
	
	var top;
	var lft;
	var elm;
	
	var flag = true;
	
	if (document.getElementById('theimg0-'+mocc)) { 
		if (document.getElementById('theimg0-'+mocc).complete) flag = false;
	}
	if (document.getElementById('theimg1-'+mocc)) { 
		if (document.getElementById('theimg1-'+mocc).complete) flag = false;
	}
	if ( !flag ) return;	// There is an image visible: no spinner

	top = jQuery('#slide_frame-'+mocc).css('height');
	if (top > 0) {
		top = parseInt(parseInt(top/2) - 16)+'px';
	}
	else {
		top = jQuery('#slide_frame-'+mocc).css('minHeight');
		if (top > 0) {
			top = parseInt(parseInt(top/2) - 16)+'px';
		}
		else top = '150px';
	}
	lft = jQuery('#slide_frame-'+mocc).css('width');

	lft = parseInt(lft);
	if (lft > 0) {
		lft = parseInt(lft/2 - 16)+'px';
	}

	jQuery('#spinner-'+mocc).css('top',top);
	jQuery('#spinner-'+mocc).css('left',lft);
	jQuery('#spinner-'+mocc).html('<img id="spinnerimg-'+mocc+'" src="'+wppaImageDirectory+'loading.gif" style="box-shadow: none" />');
}

function _wppaUnloadSpinner(mocc) {
	_wppaLog('UnloadSpinner', mocc);
//return; // debug
	jQuery('#spinner-'+mocc).html('');
}

function _wppaDoAutocol(mocc) {
	_wppaLog('DoAutocol', mocc);
	var w;
	var h;
	if (!wppaAutoColumnWidth) return;
	
	w = document.getElementById('wppa-container-1').parentNode.clientWidth;
	
	jQuery(".wppa-container").css('width',w);
	ws = w - 2 * wppaSlideBorderWidth;

	jQuery(".theimg").css('width',ws);
	jQuery(".thumbnail-area").css('width',w - wppaThumbnailAreaDelta);
	wppaFilmStripLength[mocc] = w - wppaFilmStripAreaDelta[mocc];

	jQuery("#filmwindow-"+mocc).css('width',wppaFilmStripLength[mocc]);

	jQuery(".wppa-text-frame").css('width',w - wppaTextFrameDelta);
	jQuery(".wppa-cover-box").css('width',w - wppaBoxDelta);
	
	// See if there are slideframe images
	h = 0;

	if (document.getElementById('theimg0-'+mocc)) {
		h = document.getElementById('theimg0-'+mocc).clientHeight;
	}
	if (h == 0) {
		if (document.getElementById('theimg1-'+mocc)) {
			h = document.getElementById('theimg1-'+mocc).clientHeight;
		}
	}
	// Set slideframe height to the height found (if any)
	if (h > 0) {
		jQuery("#slide_frame-"+mocc).css('height',h);
	}
	else {		// Try again later
		setTimeout('_wppaDoAutocol('+mocc+')', wppaAnimationSpeed);
	}
}

function _wppaCheckRewind(mocc) {
	_wppaLog('CheckRewind', mocc);

	var n_images;
	var n_diff;
	var l_substrate;
	var x_marg;
	
	if (!document.getElementById('wppa-filmstrip-'+mocc)) return; // There is no filmstrip
	
	n_diff = Math.abs(_wppaCurIdx[mocc] - _wppaNxtIdx[mocc]);
	if (n_diff <= wppaFilmPageSize[mocc]) return;	// was 2
	
	var n_images = wppaFilmStripLength[mocc] / wppaThumbnailPitch[mocc];
	
	if (n_diff >= ((n_images + 1) / 2)) {
		l_substrate = wppaThumbnailPitch[mocc] * _wppaSlides[mocc].length;
		if (wppaFilmShowGlue) l_substrate += (2 + 2 * wppaFilmStripMargin[mocc]);
		
		x_marg = parseInt(jQuery('#wppa-filmstrip-'+mocc).css('margin-left'));

		if (_wppaNxtIdx[mocc] > _wppaCurIdx[mocc]) {
			x_marg -= l_substrate;
		}
		else {
			x_marg += l_substrate;
		}

		jQuery('#wppa-filmstrip-'+mocc).css('margin-left', x_marg+'px');
	}
}

function _wppaSetRatingDisplay(mocc) {
	_wppaLog('setRatingDisplay', mocc);

	var idx, avg, myr;
	if (!document.getElementById('wppa-rating-'+mocc)) return; 	// No rating bar
	
	avg = _wppaAvg[mocc][_wppaCurIdx[mocc]];
	myr = _wppaMyr[mocc][_wppaCurIdx[mocc]];
	
	if (wppaRatingDisplayType == 'graphic') {
		// Set Avg rating
		_wppaSetRd(mocc, avg, '#wppa-avg-');
		// Set My rating
		_wppaSetRd(mocc, myr, '#wppa-rate-');
	}
	else { 	// Numeric
		// Set Avg rating
		switch (wppaRatingPrec) {
			case 1:
				avg = parseInt(avg * 10 + 0.5)/10;
				break;
			case 2:
				avg = parseInt(avg * 100 + 0.5)/100;
				break;
			case 3:
				avg = parseInt(avg * 1000 + 0.5)/1000;
				break;
			case 4:
				avg = parseInt(avg * 10000 + 0.5)/10000;
				break;
		}
		jQuery('#wppa-numrate-avg-'+mocc).html(avg);
		
		// Set My rating
		if (wppaRatingOnce && myr > 0) {
			jQuery('#wppa-numrate-mine-'+mocc).html(myr);
		}
		else {
			// Format my rating
			switch (wppaRatingPrec) {
				case 1:
					myr = parseInt(myr * 10 + 0.5)/10;
					break;
				case 2:
					myr = parseInt(myr * 100 + 0.5)/100;
					break;
				case 3:
					myr = parseInt(myr * 1000 + 0.5)/1000;
					break;
				case 4:
					myr = parseInt(myr * 10000 + 0.5)/10000;
					break;
			}

			/* Selection box
			var htm = '<select id="wppa-rat-'+mocc+'" onchange="_wppaRateIt('+mocc+', this.value)">';
				if (myr<1 || parseInt(myr)<myr) htm += '<option value="0">?</option>';
			var sel = '';
				for (i=wppaRatingMax; i>0;i--) {
					if (myr == i) sel = 'selected="selected"'; else sel = '';
					htm += '<option value="'+i+'" '+sel+' >'+i+'</option>';
				}
			htm += '</select>';
			if (parseInt(myr)<myr) htm += '&nbsp;('+myr+')';
			/* End selection box */
			/* Row of numbers */
			var htm = '';
			for (i=1;i<=wppaRatingMax;i++) {
				if (myr == i) {
					htm += '<span style="cursor:pointer; font-weight:bold;" onclick="_wppaRateIt('+mocc+', '+i+')">&nbsp;'+i+'&nbsp;</span>';
				}
				else {
					if ( myr > (i-1) && myr < i ) htm += '&nbsp;('+myr+')&nbsp;';
					htm += '<span style="cursor:pointer;" onclick="_wppaRateIt('+mocc+', '+i+')" onmouseover="this.style.fontWeight=\'bold\'" onmouseout="this.style.fontWeight=\'normal\'" >&nbsp;'+i+'&nbsp;</span>';
				}
			}
			/* end row */
			jQuery('#wppa-numrate-mine-'+mocc).html(htm);
		}		
	}
}
		
function _wppaSetRd(mocc, avg, where) {
	_wppaLog('SetRd', mocc);
		
	var idx1 = parseInt(avg);
	var idx2 = idx1 + 1;
	var frac = avg - idx1;
	var opac = wppaStarOpacity + frac * (1.0 - wppaStarOpacity);
	var ilow = 1;
	var ihigh = wppaRatingMax;

	for (idx=ilow;idx<=ihigh;idx++) {
		if (where == '#wppa-rate-') {
			jQuery(where+mocc+'-'+idx).attr('src', wppaImageDirectory+'star.png');
		}
		if (idx <= idx1) {
			jQuery(where+mocc+'-'+idx).stop().fadeTo(100, 1.0);
		}
		else if (idx == idx2) {
			jQuery(where+mocc+'-'+idx).stop().fadeTo(100, opac); 
		}
		else {
			jQuery(where+mocc+'-'+idx).stop().fadeTo(100, wppaStarOpacity);
		}
	}
}

function _wppaFollowMe(mocc, idx) {
	_wppaLog('FollowMe', mocc);

	if (_wppaSSRuns[mocc]) return;				// Do not rate on a running show, what only works properly in Firefox								

	if (_wppaMyr[mocc][_wppaCurIdx[mocc]] != 0 && wppaRatingOnce) return;	// Already rated
	if (_wppaVIP) return;
	_wppaSetRd(mocc, idx, '#wppa-rate-');
}

function _wppaLeaveMe(mocc, idx) {
	_wppaLog('LeaveMe', mocc);

	if (_wppaSSRuns[mocc]) return;				// Do not rate on a running show, what only works properly in Firefox	

	if (_wppaMyr[mocc][_wppaCurIdx[mocc]] != 0 && wppaRatingOnce) return;	// Already rated
	if (_wppaVIP) return;
	_wppaSetRd(mocc, _wppaMyr[mocc][_wppaCurIdx[mocc]], '#wppa-rate-');
}

function _wppaRateIt(mocc, value) {
	_wppaLog('RateIt', mocc);
if (value == 0) return;
	var photoid = _wppaId[mocc][_wppaCurIdx[mocc]];
	var oldval  = _wppaMyr[mocc][_wppaCurIdx[mocc]];
	var url 	= _wppaVRU[mocc][_wppaCurIdx[mocc]]+'&wppa-rating='+value+'&wppa-rating-id='+photoid;
		url    += '&wppa-nonce='+jQuery('#wppa-nonce').attr('value');
	
	if (_wppaSSRuns[mocc]) return;								// Do not rate a running show								
	if (oldval != 0 && wppaRatingOnce) return;							// Already rated, and once allowed only
																			
	_wppaVIP = true;											// Keeps opacity as it is now
	_wppaLastVote = value;
	
	jQuery('#wppa-rate-'+mocc+'-'+value).attr('src', wppaTickImg.src);	// Set icon
	jQuery('#wppa-rate-'+mocc+'-'+value).stop().fadeTo(100, 1.0);		// Fade in fully
	
	// Try to create the http request object
	var xmlhttp = wppaGetXmlHttp();	// This function is in wppa-ajax.js

	if ( wppaRatingUseAjax && xmlhttp ) {								// USE AJAX
		
		// Make the Ajax url
		url = wppaAjaxUrl+'?action=wppa&wppa-action=rate&wppa-rating='+value+'&wppa-rating-id='+photoid;
		url += '&wppa-occur='+mocc+'&wppa-index='+_wppaCurIdx[mocc];
		url += '&wppa-nonce='+jQuery('#wppa-nonce').attr('value');
		
		// Setup process the result
		xmlhttp.onreadystatechange=function() 
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				var ArrValues = xmlhttp.responseText.split("||");
				
				if (ArrValues[0] == '0') {	// Error
					alert('Error Code='+ArrValues[1]+'\n\n'+ArrValues[2]);
				}
				else {
					// Store new values
					_wppaMyr[ArrValues[0]][ArrValues[2]] = ArrValues[3];
					_wppaAvg[ArrValues[0]][ArrValues[2]] = ArrValues[4];
					// Update display
					_wppaSetRatingDisplay(mocc);
					jQuery('#wppa-rate-'+mocc+'-'+value).attr('src', wppaTickImg.src);			// Set icon

					if (wppaNextOnCallback) _wppaNextOnCallback(mocc);
				}
			}
		}
		// Do the Ajax action
		xmlhttp.open('GET',url,true);
		xmlhttp.send();	
	}
	else {						// use NON-ajax method, either to setting or browser does not support ajax
		setTimeout('_wppaGo("'+url+'")', 200);	// 200 ms to display tick
	}
}

function _wppaValidateComment(mocc) {
	_wppaLog('ValidateComment', mocc);

	var photoid = _wppaId[mocc][_wppaCurIdx[mocc]];
	
	// Process name
	var name = jQuery('#wppa-comname-'+mocc).attr('value');
	if (name.length<1) {
		alert(wppaPleaseName);
		return false;
	}
	
	if ( wppaEmailRequired ) {
		// Process email address
		var email = jQuery('#wppa-comemail-'+mocc).attr('value');
		var atpos=email.indexOf("@");
		var dotpos=email.lastIndexOf(".");
		if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {
			alert(wppaPleaseEmail);
			return false;
		}
	}
	
	// Process comment
	var text = jQuery('#wppa-comment-'+mocc).attr('value');
	if (text.length<1) {
		alert(wppaPleaseComment);
		return false;
	}
	
	return true;
}

function _wppaGo(url) {
	_wppaLog('Go', 0);
	
	document.location = url;	// Go!
}

function _wppaBbb(mocc,where,act) {
	_wppaLog('Bbb', mocc);
	
	if (_wppaSSRuns[mocc]) return;
	
	var elm = '#bbb-'+mocc+'-'+where;
	switch (act) {
		case 'show':
//			jQuery(elm).stop().fadeTo(100, 0.2);
			if (where == 'l') jQuery(elm).attr('title', wppaPreviousPhoto);
			if (where == 'r') jQuery(elm).attr('title', wppaNextPhoto);
			jQuery('.bbb-'+mocc).css('cursor', 'pointer');
			break;
		case 'hide':
//			jQuery(elm).stop().fadeTo(400, 0);
			jQuery('.bbb-'+mocc).removeAttr('title');
			jQuery('.bbb-'+mocc).css('cursor', 'default');
			break;
		case 'click':
			if (where == 'l') wppaPrev(mocc);
			if (where == 'r') wppaNext(mocc);
			break;
		default:
			alert('Unimplemented instruction: '+act+' on: '+elm);
	}
}


function _wppaShowMetaData(mocc, key) {
	_wppaLog('ShowMetaData', mocc);
	
	// What to do when the slideshow is NOT running
	if ( ! _wppaSSRuns[mocc] ) {	
		if (key == 'show') {			// Show
			// Show existing comments
			jQuery('#wppa-comtable-wrap-'+mocc).css('display', 'block');
			// Show the input form table
			jQuery('#wppa-comform-wrap-'+mocc).css('display', 'block');
			// Hide the comment footer
			jQuery('#wppa-comfooter-wrap-'+mocc).css('display', 'none');
			// Fade the browse arrows in
			if ( wppaSlideWrap || ( _wppaCurIdx[mocc] != 0 ) )
				jQuery('.wppa-prev-'+mocc).fadeIn(300);
			if ( wppaSlideWrap || ( _wppaCurIdx[mocc] != (_wppaSlides[mocc].length - 1) ) )
				jQuery('.wppa-next-'+mocc).fadeIn(300);
		}
		else {							// Hide
			// Hide existing comments
			jQuery('#wppa-comtable-wrap-'+mocc).css('display', 'none');
			// Hide the input form table
			jQuery('#wppa-comform-wrap-'+mocc).css('display', 'none');
			// Hide the comment footer
			jQuery('#wppa-comfooter-wrap-'+mocc).css('display', 'block');
			// Fade the browse arrows out
//			jQuery('.wppa-prev-'+mocc).fadeOut(300);	
//			jQuery('.wppa-next-'+mocc).fadeOut(300);
		}
	}
	// What to do when the slideshow is running
	else {	// Slideshow is running
	}
	
	// What to do always, independant of slideshow is running
	if (key == 'show') {
		// Show title and description
		jQuery("#imagedesc-"+mocc).css('visibility', 'visible');
		jQuery("#imagetitle-"+mocc).css('visibility', 'visible');
		// Display counter
		jQuery("#counter-"+mocc).css('visibility', 'visible');
		// Display iptc
		jQuery("#iptccontent-"+mocc).css('visibility', 'visible'); 
		jQuery("#exifcontent-"+mocc).css('visibility', 'visible'); 
	}
	else {
		// Hide title and description
		jQuery("#imagedesc-"+mocc).css('visibility', 'hidden'); 
		jQuery("#imagetitle-"+mocc).css('visibility', 'hidden');
		// Hide counter	
		jQuery("#counter-"+mocc).css('visibility', 'hidden');
		// Fade the browse arrows out
		jQuery('.wppa-prev-'+mocc).fadeOut(300);	
		jQuery('.wppa-next-'+mocc).fadeOut(300);
		// Hide iptc
		jQuery("#iptccontent-"+mocc).css('visibility', 'hidden'); 
		jQuery("#exifcontent-"+mocc).css('visibility', 'hidden'); 
		// Hide addthis
//		jQuery(".wppa-addthis-"+mocc).css('visibility', 'hidden');
	}
}

function _wppaLog(text, mocc) {
	
	if ( ! document.getElementById('wppa-debug-'+mocc) ) return;	// Debugging off
	var elm = document.getElementById('wppa-debug-'+mocc);
	var old_html = elm.innerHTML;
	var html = '<br>[wppa js] '+mocc+' run=';
	if ( _wppaSSRuns[mocc] ) html += 'yes'; else html += 'no ';
	html += ' busy=';
	if ( _wppaIsBusy[mocc] ) html += 'yes'; else html += 'no ';
	html += ' tp=';
	if ( _wppaTP[mocc] ) html += 'yes'; else html += 'no ';
	html += ' '+text;
	
	html += old_html;
	if ( html.length > 1000 ) html = html.substr(0, 1000);
	elm.innerHTML = html;	// prepend logmessage
}


function wppaGetCurrentFullUrl(mocc, idx) {
		
var url = document.location.href;
	
	// Remove &wppa-photo=... if present.
	var temp1 = url.split("?");
	var temp2 = 'nil';
	var temp3;
	var i = 0;
	var first = true;
	if (temp1[1]) temp2 = temp1[1].split("&");
	url = temp1[0];	// everything before '?'
	if (temp2 != 'nil') {
		if (temp2.length > 0) {
			while (i<temp2.length) {
				temp3 = temp2[i].split("=");
				if (temp3[0] != "wppa-photo") {
					if (first) url += "?";
					else url += "&";
					first = false;
					url += temp2[i];
				}
				i++;
			}
		}
	}
	// Append new &wppa-photo=...
	if (first) url += "?";
	else url += "&";
	if ( wppaUsePhotoNamesInUrls ) {
		url += "wppa-photo="+_wppaNames[mocc][idx];
	}
	else {
		url += "wppa-photo="+_wppaId[mocc][idx];
	}
	
	return url;
}

// Swipe

var triggerElementID = null; 
var fingerCount = 0;
var startX = 0;
var startY = 0;
var curX = 0;
var curY = 0;
var deltaX = 0;
var deltaY = 0;
var horzDiff = 0;
var vertDiff = 0;
var minLength = 72; 
var swipeLength = 0;
var swipeAngle = null;
var swipeDirection = null;
var wppaMocc = 0;

function wppaTouchStart(event,passedName,mocc) {
	wppaMocc = mocc;
	event.preventDefault();
	fingerCount = event.touches.length;

	if ( fingerCount == 1 ) {
		startX = event.touches[0].pageX;
		startY = event.touches[0].pageY;
		triggerElementID = passedName;
	} else {
		wppaTouchCancel(event);
	}
}

function wppaTouchMove(event) {
	event.preventDefault();
	if ( event.touches.length == 1 ) {
		curX = event.touches[0].pageX;
		curY = event.touches[0].pageY;
	} else {
		wppaTouchCancel(event);
	}
}

function wppaTouchEnd(event) {
	event.preventDefault();
	if ( fingerCount == 1 && curX != 0 ) {
		swipeLength = Math.round(Math.sqrt(Math.pow(curX - startX,2) + Math.pow(curY - startY,2)));
		if ( swipeLength >= minLength ) {
			wppaCalculateAngle();
			wppaDetermineSwipeDirection();
			wppaProcessingRoutine();
			wppaTouchCancel(event); // reset the variables
		} else {
			wppaTouchCancel(event);
		}	
	} else {
		wppaTouchCancel(event);
	}
}

function wppaTouchCancel(event) {
	fingerCount = 0;
	startX = 0;
	startY = 0;
	curX = 0;
	curY = 0;
	deltaX = 0;
	deltaY = 0;
	horzDiff = 0;
	vertDiff = 0;
	swipeLength = 0;
	swipeAngle = null;
	swipeDirection = null;
	triggerElementID = null;
	wppaMocc = 0;
}

function wppaCalculateAngle() {
	var X = startX-curX;
	var Y = curY-startY;
	var Z = Math.round(Math.sqrt(Math.pow(X,2)+Math.pow(Y,2))); //the distance - rounded - in pixels
	var r = Math.atan2(Y,X); //angle in radians (Cartesian system)
	swipeAngle = Math.round(r*180/Math.PI); //angle in degrees
	if ( swipeAngle < 0 ) { swipeAngle =  360 - Math.abs(swipeAngle); }
}

function wppaDetermineSwipeDirection() {
	if ( (swipeAngle <= 45) && (swipeAngle >= 0) ) {
		swipeDirection = 'left';
	} else if ( (swipeAngle <= 360) && (swipeAngle >= 315) ) {
		swipeDirection = 'left';
	} else if ( (swipeAngle >= 135) && (swipeAngle <= 225) ) {
		swipeDirection = 'right';
	} else if ( (swipeAngle > 45) && (swipeAngle < 135) ) {
		swipeDirection = 'down';
	} else {
		swipeDirection = 'up';
	}
}

function wppaProcessingRoutine() {
	var swipedElement = document.getElementById(triggerElementID);
	if ( swipeDirection == 'left' ) {
		wppaNext(wppaMocc);
		wppaMocc = 0;
	} 
	else if ( swipeDirection == 'right' ) {
		wppaPrev(wppaMocc);
		wppaMocc = 0;
	} 
	else if ( swipeDirection == 'up' ) {
	} 
	else if ( swipeDirection == 'down' ) {
	}
}

// Part 2: Theme variables and functions
//

var wppaBackgroundColorImage = '';
var _wppaTimer = new Array();
var wppa_saved_id = new Array();
var wppaPopupLinkType = '';
var wppaPopupOnclick = new Array();
var wppaThumbTargetBlank = false;

// Popup of thumbnail images 
function wppaPopUp(mocc, elm, id, rating) {
	var topDivBig, topDivSmall, leftDivBig, leftDivSmall;
	var heightImgBig, heightImgSmall, widthImgBig, widthImgSmall, widthImgBigSpace;
	var puImg;
	
	// stop if running 
	clearTimeout(_wppaTimer[mocc]);
	
	// Give this' occurrances popup its content
	if (document.getElementById('x-'+id+'-'+mocc)) {
		var namediv = elm.alt ? '<div id="wppa-name-'+mocc+'" style="display:none; padding:1px;" class="wppa_pu_info">'+elm.alt+'</div>' : '';
		var descdiv = elm.title ? '<div id="wppa-desc-'+mocc+'" style="clear:both; display:none; padding:1px;" class="wppa_pu_info">'+elm.title+'</div>' : '';
		var ratediv = rating ? '<div id="wppa-rat-'+mocc+'" style="clear:both; display:none; padding:1px;" class="wppa_pu_info">'+rating+'</div>' : '';
		var popuptext = namediv+descdiv+ratediv;
		var target = '';
		if (wppaThumbTargetBlank) target = 'target="_blank"';
		switch (wppaPopupLinkType) {
			case 'none':
				jQuery('#wppa-popup-'+mocc).html('<div class="wppa-popup" style="background-color:'+wppaBackgroundColorImage+'; text-align:center;"><img id="wppa-img-'+mocc+'" src="'+elm.src+'" title="" style="border-width: 0px;" />'+popuptext+'</div>');
				break;
			case 'fullpopup':
				jQuery('#wppa-popup-'+mocc).html('<div class="wppa-popup" style="background-color:'+wppaBackgroundColorImage+'; text-align:center;"><img id="wppa-img-'+mocc+'" src="'+elm.src+'" title="" style="border-width: 0px;" onclick="'+wppaPopupOnclick[id]+'" />'+popuptext+'</div>');
				break;
			default:
				jQuery('#wppa-popup-'+mocc).html('<div class="wppa-popup" style="background-color:'+wppaBackgroundColorImage+'; text-align:center;"><a id="wppa-a" href="'+document.getElementById('x-'+id+'-'+mocc).href+'" '+target+' style="line-height:1px;" ><img id="wppa-img-'+mocc+'" src="'+elm.src+'" title="" style="border-width: 0px;" /></a>'+popuptext+'</div>');
		}
	}
	
	// Find handle to the popup image 
	puImg = document.getElementById('wppa-img-'+mocc);
	
	// Set width of text fields to width of a landscape image	
	if (puImg) jQuery(".wppa_pu_info").css('width', ((puImg.clientWidth > puImg.clientHeight ? puImg.clientWidth : puImg.clientHeight) - 8)+'px');	
	// Compute starting coords
	leftDivSmall = parseInt(elm.offsetLeft) - 7 - 5 - 1; // thumbnail_area:padding, wppa-img:padding, wppa-border; jQuery().css("padding") does not work for padding in css file, only when litaral in the tag
	topDivSmall = parseInt(elm.offsetTop) - 7 - 5 - 1;		
	// Compute starting sizes
	widthImgSmall = parseInt(elm.clientWidth);
	heightImgSmall = parseInt(elm.clientHeight);
	// Compute ending sizes
	widthImgBig = puImg.clientWidth; 
	heightImgBig = parseInt(puImg.clientHeight);
	widthImgBigSpace = puImg.clientWidth > puImg.clientHeight ? puImg.clientWidth : puImg.clientHeight;
	// Compute ending coords
	leftDivBig = leftDivSmall - parseInt((widthImgBigSpace - widthImgSmall) / 2);
	topDivBig = topDivSmall - parseInt((heightImgBig - heightImgSmall) / 2);
	
	// Setup starting properties
	jQuery('#wppa-popup-'+mocc).css({"marginLeft":leftDivSmall+"px","marginTop":topDivSmall+"px"});
	jQuery('#wppa-img-'+mocc).css({"width":widthImgSmall+"px","height":heightImgSmall+"px"});
	// Do the animation
	jQuery('#wppa-popup-'+mocc).stop().animate({"marginLeft":leftDivBig+"px","marginTop":topDivBig+"px"}, 400);
	jQuery('#wppa-img-'+mocc).stop().animate({"width":widthImgBig+"px","height":heightImgBig+"px"}, 400);
	// adding ", 'linear', wppaPopReady(occ) " fails, therefor our own timer to the "show info" module
	_wppaTimer[mocc] = setTimeout('wppaPopReady('+mocc+')', 400);
}
function wppaPopReady(mocc) {
	jQuery("#wppa-name-"+mocc).show();
	jQuery("#wppa-desc-"+mocc).show();
	jQuery("#wppa-rat-"+mocc).show();
}

// Dismiss popup
function wppaPopDown(mocc) {		// return; //debug
	jQuery('#wppa-popup-'+mocc).html("");
	return;
}

// Popup of fullsize image
function wppaFullPopUp(mocc, id, url, xwidth, xheight) {
	var height = xheight+50;
	var width  = xwidth+14;
	var name = '';
	var desc = '';
	
	var elm = document.getElementById('i-'+id+'-'+mocc);
	if (elm) {
		name = elm.alt;
		desc = elm.title;
	}	
	
	var wnd = window.open('', 'Print', 'width='+width+', height='+height+', location=no, resizable=no, menubar=yes ');
	wnd.document.write('<html>');
		wnd.document.write('<head>');	
			wnd.document.write('<style type="text/css">body{margin:0; padding:6px; background-color:'+wppaBackgroundColorImage+'; text-align:center;}</style>');
			wnd.document.write('<title>'+name+'</title>');
			wnd.document.write('<script type="text/javascript">function wppa_print(){document.getElementById("wppa_printer").style.visibility="hidden"; window.print(); }</script>');
		wnd.document.write('</head>');
		wnd.document.write('<body>');
			wnd.document.write('<div style="width:'+xwidth+'px;">');
				wnd.document.write('<img src="'+url+'" style="padding-bottom:6px;" /><br/>');
				wnd.document.write('<div style="text-align:center">'+desc+'</div>');
				var left = xwidth-30;
				wnd.document.write('<img src="'+wppaImageDirectory+'printer.png" id="wppa_printer" title="Print" style="position:absolute; top:6px; left:'+left+'px; background-color:'+wppaBackgroundColorImage+'; padding: 2px; cursor:pointer;" onclick="wppa_print();" />');
			wnd.document.write('</div>');
		wnd.document.write('</body>');
	wnd.document.write('</html>');
}

// Part 3: Ajax
// Additionally: functions to change the url for addthis during ajax and browse operations

// AJAX RENDERING INCLUDING HISTORY MANAGEMENT
// IF AJAX NOT ALLOWED, ALSO NO HISTORY MENAGEMENT!!

var wppaHis = 0;
var wppaStartHtml = new Array();
var wppaCanAjaxRender = false;	// Assume failure
var wppaCanPushState = false;
var wppaAllowAjax = true;		// Assume we are allowed to use ajax
var wppaMaxOccur = 0;
var wppaFirstOccur = 0;
var wppaUsePhotoNamesInUrls = false;

// Initialize
jQuery(document).ready(function(e) {
	// Are we allowed anyway?
	if ( ! wppaAllowAjax ) return;	// Not allowed today
	
	if ( wppaGetXmlHttp() ) {
		wppaCanAjaxRender = true;
	}
	if ( typeof(history.pushState) != 'undefined' ) {		
		// Save entire initial page content ( I do not know which container is going to be modified first )
		var i=1;
		while (i<=wppaMaxOccur) {
			wppaStartHtml[i] = jQuery('#wppa-container-'+i).html();
			i++;
		}
		wppaCanPushState = true;
	}
});

// Get the http request object
function wppaGetXmlHttp() {
	if (window.XMLHttpRequest) {		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {								// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	return xmlhttp;
}

// Setup an event handler for popstate events
window.onpopstate = function(event) { 
	var occ = 0;
	if ( wppaCanPushState ) {
		if ( event.state ) {
			occ = event.state.occur;
			switch ( event.state.type ) {
				case 'html':
					// Restore wppa container content
					jQuery('#wppa-container-'+occ).html(event.state.html);
					break;
				case 'slide':
					// Go to specified slide without the didgoto switch to avoid a stackpush here
					_wppaGoto(occ, event.state.slide);
					break;				
			}
		}
		else {
			occ = wppaFirstOccur;
			// Restore first modified occurrences content
			jQuery('#wppa-container-'+occ).html(wppaStartHtml[occ]);
			// Now we are back to the initial page
			wppaFirstOccur = 0;
			// If a photo number given goto that photo
			if (occ == 0) {	// Find current occur if not yet known
				var url = document.location.href;
				var urls = url.split("&wppa-occur=");
				occ = parseInt(urls[1]);			
			}
			var url = document.location.href;
			var urls = url.split("&wppa-photo=");
			var photo = parseInt(urls[1]);
			if (photo > 0) {
				var idx = 0;
				while ( idx < _wppaId[occ].length ) {
					if (_wppaId[occ][idx] == photo) break;
					idx++;
				}
				if ( idx < _wppaId[occ].length ) _wppaGoto(occ, idx);
			}
		}
		// If it is a slideshow, stop it
		if ( document.getElementById('theslide0-'+occ) ) {
			_wppaStop(occ);
		}
	}
};  

// The AJAX rendering routine
function wppaDoAjaxRender(mocc, ajaxurl, newurl) {

	// Create the http request object
	var xmlhttp = wppaGetXmlHttp();
		
	if ( wppaCanAjaxRender ) {	// Ajax possible
		// Setup process the result
		xmlhttp.onreadystatechange = function() {
			if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
			
				// Update the wppa container
				jQuery('#wppa-container-'+mocc).html(xmlhttp.responseText);
				if ( wppaCanPushState ) {
					// Push state on stack
					wppaHis++;
					cont = xmlhttp.responseText;
					try {
						history.pushState({page: wppaHis, occur: mocc, type: 'html', html: cont}, "---", newurl);
					}
					catch(err) {}
					if ( wppaFirstOccur == 0 ) wppaFirstOccur = mocc;
				}
				
				// If lightbox 3 is on board, refresh the imagelist. It has just changed, you know!
				if (typeof(myLightbox)!="undefined") myLightbox.updateImageList();
			}
		}
		// If it is a slideshow: Stop slideshow before pushing it on the stack
		if ( _wppaCurIdx[mocc] ) _wppaStop(mocc);
		// Do the Ajax action
		xmlhttp.open('GET',ajaxurl,true);
		xmlhttp.send();	
	}
	else {	// Ajax NOT possible
		document.location.href = newurl;
	}

	/* addthis */
	wppaUpdateAddThisUrl(newurl, '');
}

function wppaPushStateSlide(mocc, slide) {

	if ( wppaCanPushState ) {
		var url = wppaGetCurrentFullUrl(mocc, _wppaCurIdx[mocc]);
		history.pushState({page: wppaHis, occur: mocc, type: 'slide', slide: slide}, "---", url);
	}
}

// WPPA modules for addthis.
var wppaAddThis = false;

jQuery(document).ready(function(){
	if (typeof(addthis) != 'undefined') {
		addthis.init();	// In case loaded asynchronously
		wppaAddThis = true;
	}
});

function wppaUpdateAddThisUrl(url, title) {
	if ( ! wppaAddThis ) return;	// No addthis activated
	try {
		addthis.update('share', 'url', url);
	}
	catch(err){
	}
	if (title != '') {
		try {
			addthis.update('share', 'title', title);
		}
		catch(err) {
		}
	}
}

// WPPA EMBEDDED NATIVE LIGHTBOX FUNCTIONALITY
//
var wppaOvlUrls;
var wppaOvlUrl;
var wppaOvlTitles;
var wppaOvlTitle;
var wppaOvlIdx = -1;
var wppaOvlFirst = true;
var wppaOvlKbHandler = '';
var wppaOvlSizeHandler = '';
var wppaOvlPadTop = 5;
// The next var values become overwritten in wppa-non-admin.php -> wppa_load_footer()
var wppaOvlCloseTxt = 'CLOSE';
var wppaOvlTxtHeight = 36;	// 12 * ( n lines of text including the n/m line )
var wppaOvlOpacity = 0.8;
var wppaOvlOnclickType = 'none';
var wppaOvlTheme = 'black';
var wppaOvlAnimSpeed = 300;

jQuery(document).ready(function(){
	wppaInitOverlay();
});

function wppaOvlShow(arg) {

	if ( wppaLightBox != 'wppa' ) return;	// No, not this time.
	
	// Display spinner
	jQuery('#wppa-overlay-sp').css({left: (window.innerWidth/2)-16, top: (window.innerHeight/2)-16, visibility: 'visible'});
	
	var href;
	if (parseInt(arg) == arg) {	// Arg is Numeric
		if ( arg != -1 ) {
			wppaOvlUrl = wppaOvlUrls[arg];
			wppaOvlTitle = wppaOvlTitles[arg];
			wppaOvlIdx = arg;
		} // else redo the same single
	}
	else {						// Arg is 'this' arg
		wppaOvlIdx = -1;	// Assume single
		wppaOvlUrl = arg.href;
		wppaOvlTitle = arg.title;
		var rel = arg.rel;
		var temp = rel.split('[');
		if (temp[1]) {	// We are in a set
			wppaOvlUrls = new Array();
			wppaOvlTitles = new Array();
			var setname = temp[1];
			var anchors = jQuery('a');
			var anchor;
			var i, j = 0;
			wppaOvlIdx = -1;
			// Save the set
			for (i=0;i<anchors.length;i++) {
				anchor = anchors[i];
				if (anchor.rel) {
					temp = anchor.rel.split("[");
					if (temp[0] == 'wppa' && temp[1] == setname) {	// Same set
						wppaOvlUrls[j] = anchor.href;
						wppaOvlTitles[j] = anchor.title;
						if (anchor.href == wppaOvlUrl) wppaOvlIdx = j;	// Current index
						j++;
					}
				}
			}
		}
		else { 	// Single
			wppaOvlUrls = false;
			wppaOvlTitles = false;
			wppaOvlIdx = -1;
		}
	}

	var mw = 250;

	jQuery('#wppa-overlay-bg').fadeTo(300, wppaOvlOpacity);
	var lft = (window.innerWidth/2-125)+'px';
	var ptp = (window.innerHeight/2-125)+'px';
	jQuery('#wppa-overlay-ic').css({left: lft, paddingTop: ptp});
	var txtcol = wppaOvlTheme == 'black' ? '#a7a7a7' : '#272727';	// Normal font
	var qtxtcol = wppaOvlTheme == 'black' ? '#a7a7a7' : '#575757';	// Bold font
	var html = 	'<div id="wppa-overlay-qt-txt"  style="position:absolute; right:16px; top:'+(wppaOvlPadTop-1)+'px; visibility:hidden; box-shadow:none; font-family:helvetica; font-weight:bold; font-size:14px; color:'+qtxtcol+'; cursor:pointer; " onclick="wppaOvlHide()" >'+wppaOvlCloseTxt+'&nbsp;&nbsp;</div>'+
				'<img id="wppa-overlay-qt-img"  src="'+wppaImageDirectory+'smallcross-'+wppaOvlTheme+'.gif'+'" style="position:absolute; right:0; top:'+wppaOvlPadTop+'px; visibility:hidden; box-shadow:none; cursor:pointer" onclick="wppaOvlHide()" >'+
				'<img id="wppa-overlay-img" src="'+wppaOvlUrl+'" style="border-width:16px; border-style:solid; border-color:'+wppaOvlTheme+'; margin-bottom:-15px; max-width:'+mw+'px; visibility:hidden; box-shadow:none;" />'+
				'<div id="wppa-overlay-txt-container" style="padding:10px; background-color:'+wppaOvlTheme+'; color:'+txtcol+'; text-align:center; font-size: 10px; font-weight:bold; line-height:12px; visibility:hidden; box-shadow:none;" ></div>';
	jQuery('#wppa-overlay-ic').html(html);
	setTimeout('wppaOvlShow2()', 10);
	return false;
}
function wppaOvlShow2() {
	
	var img = document.getElementById('wppa-overlay-img');
	
	if (!img.complete) {
		setTimeout('wppaOvlShow2()', 10);	// Wait for load complete
		return;
	}
if (wppaOvlAnimSpeed!=0)
	img.style.visibility = 'visible';		// Display image
	
	setTimeout('wppaOvlShow3()', 10);
	return false;
}
function wppaOvlShow3() {
	// Remove spinner
	jQuery('#wppa-overlay-sp').css({visibility: 'hidden'});
	// Size to final dimensions
	jQuery('#wppa-overlay-txt-container').html('<div id="wppa-overlay-txt"></div>');	// reqd for sizeing
	var speed = wppaOvlAnimSpeed;
	wppaOvlSize(speed);
	// Go on
	setTimeout('wppaOvlShow4()', speed+50);
	return false;
}
function wppaOvlShow4() {

	var cw = document.getElementById('wppa-overlay-img').clientWidth;
	if (wppaOvlIdx != -1) {	// One out of a set
		var vl = 'visibility:hidden;';
		var vr = 'visibility:hidden;';
		var ht = 'height:'+wppaOvlTxtHeight+'px;';
		
		if (wppaOvlIdx != 0) vl = 'visible';
		if (wppaOvlIdx != (wppaOvlUrls.length-1)) vr = 'visible';
		if (wppaOvlTxtHeight == 'auto') ht = '';
		
		var html = 	'<img src="'+wppaImageDirectory+'prev-'+wppaOvlTheme+'.gif" style="margin-top:-8px; float:left; '+vl+'; box-shadow:none;" onclick="wppaOvlShowPrev()" / >'+
					'<img src="'+wppaImageDirectory+'next-'+wppaOvlTheme+'.gif" style="margin-top:-8px; float:right;'+vr+'; box-shadow:none;" onclick="wppaOvlShowNext()" / >'+
					'<div id="wppa-overlay-txt" style="text-align:center; min-height:36px; '+ht+' overflow:hidden; box-shadow:none; width:'+(cw-80)+'px;" >'+(wppaOvlIdx+1)+'/'+wppaOvlUrls.length+'<br />'+wppaOvlTitle+'</div>';
		jQuery('#wppa-overlay-txt-container').html(html);
	}
	else {
		jQuery('#wppa-overlay-txt-container').html('<br />'+wppaOvlTitle);
	}
	jQuery('#wppa-overlay-txt-container').css('visibility', 'visible');
	jQuery('#wppa-overlay-qt-txt').css({visibility: 'visible'});
	jQuery('#wppa-overlay-qt-img').css({visibility: 'visible'});

	// Almost done, Install eventhandlers
	if (wppaOvlFirst) {
		// Enable kb input
		if ( document.onkeydown ) {
			wppaOvlKbHandler = document.onkeydown; 
		}
		document.onkeydown = wppaKbAction; 

		// Window resize handler
		if ( window.onresize ) {
			wppaOvlSizeHandler = window.onresize;
		}
		window.onresize = function () {return wppaOvlResize(10);}
		
		wppaOvlFirst = false;
	}
	if (wppaOvlTxtHeight == 'auto') wppaOvlResize(10);	// Resize to accomodate for var text height
	return false;
}

function wppaOvlShowPrev() {
	if (wppaOvlIdx < 1) {
		wppaOvlHide();	// There is no prev, quit
		return false;
	}
	wppaOvlShow(wppaOvlIdx-1);
	return false;
}
function wppaOvlShowNext() {
	if (wppaOvlIdx >= (wppaOvlUrls.length-1)) {
		wppaOvlHide();	// There is no next, quit
		return false;
	}
	wppaOvlShow(wppaOvlIdx+1);
	return false;
}

function wppaOvlSize(speed) {	

	// Wait for text complete
	if (! document.getElementById('wppa-overlay-txt')) { setTimeout('wppaOvlSize('+speed+')', 10); return;}

	var iw = window.innerWidth;
	var img = document.getElementById('wppa-overlay-img');
	var cw = img.clientWidth;
	var nw = img.naturalWidth;
	var nh = img.naturalHeight;
	var mh;
	if (wppaOvlTxtHeight == 'auto') {
		var tch = document.getElementById('wppa-overlay-txt').clientHeight;
		if (tch == 0) tch = 36;
		mh = window.innerHeight - tch - 52;
	}
	else {
		mh = window.innerHeight - wppaOvlTxtHeight - 52;
	}
	var mw = parseInt(mh * nw / nh);
	var pt = wppaOvlPadTop;
	var lft = parseInt((iw-mw)/2);
	
	// Image too small?
	if (nh < mh) {
		pt = wppaOvlPadTop + (mh - nh)/2;
		lft = parseInt((iw-nw)/2);
	}

	if ( speed == 0 ) {
		jQuery('#wppa-overlay-img').css({maxWidth: mw+'px', visibility: 'visible'});
		jQuery('#wppa-overlay-ic').css({left: lft+'px', paddingTop: pt+'px'});
		jQuery('#wppa-overlay-qt-txt').css({top: (pt-3)+'px'});
		jQuery('#wppa-overlay-qt-img').css({top: pt+'px'});
	}
	else {
		jQuery('#wppa-overlay-img').stop().animate({maxWidth: mw+'px'}, speed);
		jQuery('#wppa-overlay-ic').stop().animate({left: lft+'px', paddingTop: pt+'px'}, speed);
		jQuery('#wppa-overlay-qt-txt').stop().animate({top: (pt-1)+'px'}, speed);
		jQuery('#wppa-overlay-qt-img').stop().animate({top: pt+'px'}, speed);
	}
	
	// If resizing, also resize txt elements when sizing is complete
	if ( document.getElementById('wppa-overlay-txt') ) {
		// Hide during resize if sizing takes longer than 10 ms.
		if (speed > 10) jQuery('#wppa-overlay-txt').css({visibility: 'hidden'});
}		
		setTimeout('wppaOvlSize2()', speed+20);
//	}
	return true;
}
function wppaOvlSize2() {
	
	var cw = document.getElementById('wppa-overlay-img').clientWidth;
	
	jQuery('#wppa-overlay-txt').css({width:(cw-80)+'px', visibility: 'visible'});

	return true;
}

function wppaOvlHide() {
	// Clear image container
	jQuery('#wppa-overlay-ic').html('');
	// Remove background
	jQuery('#wppa-overlay-bg').fadeOut(300);
	// Re-instal posssible original kb handler
	document.onkeydown = wppaOvlKbHandler;
	// Re-instal possible original resize handler
	window.onresize = wppaOvlSizeHandler;
	// Reset switch
	wppaOvlFirst = true;
}

function wppaOvlOnclick(event) {
	switch (wppaOvlOnclickType) {
		case 'none':
			break;
		case 'close':
			wppaOvlHide();
			break;
		case 'browse':
			var x = event.screenX - window.screenX;
			if (x < window.innerWidth/2) wppaOvlShowPrev();
			else wppaOvlShowNext();
			break;
		default:
			alert('Unimplemented action: '+wppaOvlOnclickType);
			break;
	}
	return true;
}

function wppaInitOverlay() {

	if ( wppaLightBox != 'wppa' ) return;	// No, not this time
	
	var anchors=jQuery('a');
	var anchor;
	var i;
		
	for(i=0;i<anchors.length;i++) {
		anchor = anchors[i];
		if (anchor.rel) {
			temp = anchor.rel.split("[");
			if (temp[0] == 'wppa') {
				wppaWppaOverlayActivated = true;	// found one
				anchor.onclick = function () {return wppaOvlShow(this);} 
			}
		}
	}
}

var wppaKbAction = function(e) {

	if (e == null) { // ie
		keycode = event.keyCode;
		escapeKey = 27;
	} else { // mozilla
		keycode = e.keyCode;
		escapeKey = e.DOM_VK_ESCAPE;
	}

	key = String.fromCharCode(keycode).toLowerCase();

	if ((key == 'x') || (key == 'o') || (key == 'c') || (key == 'q') || (keycode == escapeKey)) {
		wppaOvlHide();
	} 
	else if((key == 'p') || (keycode == 37)) {	
		wppaOvlShowPrev();
	} else if((key == 'n') || (keycode == 39)) {	
		wppaOvlShowNext();
	}
}

// This module is intented to be used in any onclick definition that opens or closes a psrt of the photo descrioption.
// this will automaticly adjust the picturesize sonthat the full description will be visible.
// Example: <a href="javascript://" onclick="myproc()" >Show Details</a>
// Change to: <a href="javascript://" onclick="myproc(); wppaOvlResize()" >Show Details</a>
// Isn't it simple?
function wppaOvlResize() {
	// See if generic lightbox is on
	if ( wppaLightBox != 'wppa' ) return;	// No, not this time.
	// Wait for completeion of text and do a size operation
	setTimeout('wppaOvlSize(10)', 50);		// After resizing, the number of lines may have changed
	setTimeout('wppaOvlSize(10)', 200);
	setTimeout('wppaOvlSize(10)', 500);
}