var startY;
var i = 0;
var docHeight = 0;
var winHeight = 0;
var winWidth = 0;

function canScrollAgain(i, stickyHeaderPadding) {
	if (i * (winHeight - stickyHeaderPadding) < docHeight) {
		return true;
	}
}

function listener(message, sender, response) {
	switch(message.msg) {
		case "scroll_init":
			console.log("init")
			docHeight = document.body.scrollHeight; // dependent on Chrome zoom
			winHeight = window.innerHeight; // dependent on Chrome zoom
			winWidth = window.innerWidth; // dependent on Chrome zoom
			startY = window.scrollY;
			window.scrollTo(0,0);
			i = 0;
			setTimeout(function() {
				response({height:docHeight, width:winWidth});
			}, 300);
			break;
		case "scroll_next":
			i++;
			if (canScrollAgain(i, message.stickyHeaderPadding)) {
				window.scrollTo(0, i * (winHeight - message.stickyHeaderPadding));
				setTimeout(function() {
					response({msg:'scroll_next_done', canScrollAgain:canScrollAgain(i+1, message.stickyHeaderPadding)});
				}, 300);
			} else {
				console.log("scroll_finished");
				window.scrollTo(0, startY);
				
				// remove this listener in case we re-run the scrip on this same page 
				chrome.runtime.onMessage.removeListener(listener);
				
				response({msg:'scroll_finished'});
			}
			break;
	}
	return true;
}

chrome.runtime.onMessage.addListener(listener);