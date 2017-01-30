var screenShotTab;
var screenShotData;

function overlayOnSelect(s, c) {
	$("#clickAndDrag").hide();
	var image = document.getElementById("image");
	var canvas = document.getElementById('canvas');
	var context = canvas.getContext('2d');
	
	// set canvas.STYLE.width for hidpi blurry issues, refer to http://www.html5rocks.com/en/tutorials/canvas/hidpi/
	// note this technique must also be used in the editor.html
    canvas.width = c.w * devicePixelRatio;
    canvas.height = c.h * devicePixelRatio;

    canvas.style.width = c.w + 'px';
    canvas.style.height = c.h + 'px';
	
	context.scale(devicePixelRatio, devicePixelRatio);
	
	// Crop and resize the image: sx, sy, sw, sh, dx, dy, dw, dh.
	//context.drawImage(image, c.x, c.y, c.w, c.h, 0, 0, c.w, c.h); // this worked for grab and crop
	context.drawImage(image, c.x * devicePixelRatio, c.y * devicePixelRatio, c.w * devicePixelRatio, c.h * devicePixelRatio, 0, 0, c.w, c.h);

	screenShotData = canvas.toDataURL();
	
	chrome.runtime.getBackgroundPage(function(bg) {
		bg.screenShotTab = screenShotTab;
		bg.screenShotData = screenShotData;
		location.href = "editor.html";
	});
}

function init() {
	$("#imageWrapper").mousemove(function(e) {
		$("#clickAndDrag").css({
			top: (e.pageY + 15) + "px",
			left: (e.pageX + 15) + "px"
		});
	});
	$('#image').attr("src", screenShotData);
	
	$('#image').on("load", function() {
		// patch: then turn off the above listener because .Jcrop would reload the image???
		$('#image').off();
		
		setTimeout(function() {
			$("#imageWrapper").show();

			$('#image').Jcrop({
				//aspectRatio: devicePixelRatio,
				canResize: false,
				fadeDuration: 0,
				bgOpacity: 0.7,
				minSize: [0,0],
				setSelect: [ -1, -1, -1, -1 ]
			});
			
			if (localStorage.grabMethod == "openFromClipboardAndCrop") {
				$("#imageWrapper").addClass("patchForOpenFromClipboardAndCrop");
				var $jcropCanvas = $("#imageWrapper canvas");
				$jcropCanvas.css("width", $jcropCanvas.width() / devicePixelRatio);
				$jcropCanvas.css("height", $jcropCanvas.height() / devicePixelRatio);
			}
			
			var container = $('#image').Jcrop("api").container;
			container
				.on('cropstart', function() {
					$(".jcrop-box, .jcrop-shades div, .jcrop-selection.jcrop-nodrag .jcrop-box, .jcrop-nodrag .jcrop-shades div").css("cursor", "crosshair");
					$("#clickAndDrag").hide();
				})
				.on('cropend', function(e, s, c) {
					console.log("crop end");
					overlayOnSelect(s, c);
				})
			;

			$("#clickAndDrag").show();
			
		}, 1);
	});
}

$(document).ready(function() {			
	chrome.runtime.getBackgroundPage(function(bg) {
		screenShotTab = bg.screenShotTab;
		screenShotData = bg.screenShotData;
		init();
	});
});