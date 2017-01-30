var ITEM_ID = "screenshots";
var canvas;
var context;
var cy = 0;
var SCROLLBAR_WIDTH = 22;
var STICKY_HEADER_PADDING = 200;

var TEST_REDUCED_DONATION = false;

function captureRecursively(params, callback) {
	console.log("captrecur")
	captureToCanvas(params, function() {
		console.log("sendnext")
		window.setTimeout(function() {
			chrome.tabs.sendMessage(params.tab.id, {msg:"scroll_next", stickyHeaderPadding:STICKY_HEADER_PADDING}, function(response) {
				if (response.msg == "scroll_next_done") {
					console.log("sendnextdone")
					if (response.canScrollAgain) {
						params.stickyHeaderPadding = STICKY_HEADER_PADDING;
					} else {
						params.stickyHeaderPadding = 0;
					}
					captureRecursively(params, callback);
				} else {
					console.log("finish")
					callback();
				}
			});
		}, 150);
	});
}

function captureToCanvas(params, callback) {
	chrome.tabs.captureVisibleTab(null, {format:getCaptureVisibleTabFormat(storage.imageFormat)}, function(data) {
		console.log("capture");
		var image = new Image();
		image.onload = function() {
			
			if (!canvas) {
				canvas = document.createElement('canvas');
				context = canvas.getContext("2d");

				//alert(params.scrollInitResponse.width + "  "+ image.width) // 1280 1920
				canvas.width = params.scrollInitResponse.width * params.zoomFactor;
				canvas.height = params.scrollInitResponse.height * params.zoomFactor;
				
				cy = 0;
			}
			
			var height = (cy+image.height > canvas.height) ? canvas.height-cy : image.height;
			if (height > 0) {
				var sx = 0;
				var sy = image.height - height;
				if (params.stickyHeaderPadding) {
					sy += params.stickyHeaderPadding * params.zoomFactor;
				}
				//sy *= params.zoomFactor;
				
				var sWidth = image.width-SCROLLBAR_WIDTH;
				//sWidth *= params.zoomFactor;
				
				var sHeight = height;
				if (params.stickyHeaderPadding) {
					sHeight -= params.stickyHeaderPadding;
				}
				//sHeight *= params.zoomFactor;
				var width = canvas.width-SCROLLBAR_WIDTH;
				if (params.stickyHeaderPadding) {
					height -= params.stickyHeaderPadding;
				}
				//width *= params.zoomFactor;
				//height *= params.zoomFactor;
				
				//context.drawImage(image, sx, sy, sWidth * devicePixelRatio, sHeight * devicePixelRatio, 0, cy, width, height);
				context.drawImage(image, sx, sy, sWidth, sHeight, 0, cy, width, height);
			}
			
			if (cy+image.height < canvas.height) {
				cy += image.height;// / params.zoomFactor;
				if (params.stickyHeaderPadding) {
					cy -= params.stickyHeaderPadding * params.zoomFactor;
				}
			}
			
			callback();
		};
		image.src = data;
	});
}

function captureVisibleTab(urlToGoAfter, delay) {
	return new Promise(function(resolve, reject) {
		chrome.tabs.captureVisibleTab(null, {format:getCaptureVisibleTabFormat(storage.imageFormat)}, function(data) {
			if (chrome.runtime.lastError) {
				if (delay) {
					showMessageNotification("Problem with screenshot", "You must stay within the same tab");
				}
				reject(chrome.runtime.lastError.message);
			} else {
				chrome.runtime.getBackgroundPage(function(bg) {
					getActiveTab(function(tab) {
						bg.screenShotTab = tab;
						bg.screenShotData = data;
						chrome.tabs.create({url: urlToGoAfter});
						resolve();
					});
				});
			}
		});
	});
}

function possibleDelay(delay) {
	return new Promise((resolve, reject) => {
		if (delay) {
			setTimeout(function() {
				resolve();
			}, delay);
		} else {
			resolve();
		}
	});
}

function grabSelectedArea(delay) {
	return possibleDelay(delay).then(() => {
		localStorage.grabMethod = "selectedArea";
		return captureVisibleTab("snapshot.html", delay);
	});
}

function grabVisiblePart(delay) {
	return possibleDelay(delay).then(() => {
		localStorage.grabMethod = "visibleArea";
		return captureVisibleTab("editor.html", delay);
	});
}

function grabEntirePage() {
	return new Promise(function(resolve, reject) {
		localStorage.grabMethod = "entirePage";
		
		getActiveTab(function(tab) {
			var sendMessageResponded = false;
			
			chrome.tabs.executeScript(tab.id, {file:"js/contentScript.js"}, function() {
	
				if (chrome.extension.lastError) {
					console.error("error", chrome.extension.lastError.message);
					reject(chrome.extension.lastError.message);
				} else {
					
					chrome.tabs.getZoom(function(zoomFactor) {
						chrome.tabs.sendMessage(tab.id, {msg:"scroll_init"}, function(response) {
							sendMessageResponded = true;
							captureRecursively({tab:tab, zoomFactor:zoomFactor, scrollInitResponse:response}, function() {
								chrome.runtime.getBackgroundPage(function(bg) {
									bg.screenShotTab = tab;
									bg.screenShotData = canvas.toDataURL();
									chrome.tabs.create({url: 'editor.html'});
									resolve();
								});
							});
		
						});
					});
				}
				
			});
	
			setTimeout(function() {
				if (!sendMessageResponded) {
					reject("no sendMessageResponded");
				}
			}, 500);
	
		});
	});
}

function openEditor(dataUrl, sameWindow) {
	chrome.runtime.getBackgroundPage(function(bg) {
		bg.screenShotData = dataUrl;
		if (sameWindow) {
			location.href = "editor.html";
		} else {
			chrome.tabs.create({url: "editor.html"});
		}
	});
}

function openFromClipboard(crop) {
	return new Promise(function(resolve, reject) {
		if (crop) {
			localStorage.grabMethod = "openFromClipboardAndCrop";
		} else {
			localStorage.grabMethod = "openFromClipboard";
		}
		
		chrome.permissions.request({permissions: ["clipboardRead"]}, function(granted) {
			if (chrome.runtime.lastError) {
				alert(chrome.runtime.lastError.message);
			} else {
				if (granted) {
					document.execCommand("paste");
					resolve();
				} else {
					// do nothing
					reject({permissionNotGranted:true});
			  	}
			}
		});
	});
}

function initPopup(storage) {
	if (storage.presetButtonAction == "popupWindow") {
		chrome.browserAction.setPopup({popup:"popup.html"});
	} else {
		chrome.browserAction.setPopup({popup:""});
	}
}

function setButtonIcon(storage) {
	chrome.browserAction.setIcon({ path: {
			"19": "images/icons/" + storage.buttonIcon + "19.png",
			"38": "images/icons/" + storage.buttonIcon + "38.png"
		}
	});
}

function initContextMenu(storage) {
	chrome.contextMenus.removeAll();
	
	var contexts = ["browser_action"];
	if (!storage.removeMenuItems) {
		contexts.push("page");
	}

	chrome.contextMenus.create({id: "grabSelectedArea", title: getMessage("grabSelectedArea"), contexts: contexts});
	chrome.contextMenus.create({id: "grabVisiblePart", title: getMessage("grabVisiblePart"), contexts: contexts});
	chrome.contextMenus.create({id: "grabEntirePage", title: getMessage("grabEntirePage"), contexts: contexts});
}

function getStorage(storageManager) {
	return new Promise(function(resolve, reject) {
		// must declare ALL defaults or they are NOT retrieved when calling storage.get
		var storageDefaults = {};
		storageDefaults.uploadedImgurFiles = [];
		storageDefaults.presetButtonAction = "popupWindow";
		storageDefaults.imageFormat = "image/jpeg";
		storageDefaults.buttonIcon = "default";
		storageDefaults.afterGrabAction = "openEditor";
		storageDefaults.fontFamily = "cambria";
		storageDefaults.fontSize = "normal";
		storageDefaults.lineWeight = 5;
		storageDefaults.fontBackground = "semi-transparent";
		
		storageDefaults.downloadButton = true;
		storageDefaults.uploadButton = true;
		storageDefaults.saveToDriveButton = true;
		storageDefaults.copyToClipboardButton = true;
		storageDefaults.editInPixlrButton = true;
		storageDefaults.shareButton = true;
		storageDefaults.openButton = true;
		storageDefaults.pdfButton = true;
		storageDefaults.searchImageButton = true;
		storageDefaults.grabEntireScreenButton = true;
		storageDefaults.openFileButton = true;

		// Must load storage items first (because they are async)
		storageManager.get(storageDefaults).then(function(response) {
			resolve(response.items);
		});
	});
}

function daysElapsedSinceFirstInstalled() {
	if (TEST_REDUCED_DONATION) {
		return true;
	}
	
	return Math.abs(new Date(storage.installDate).diffInDays());
}

function isEligibleForReducedDonation() {
	if (TEST_REDUCED_DONATION) {
		return true;
	}
	
	return (daysElapsedSinceFirstInstalled() >= (20) && !pref("donationClicked"));
}

function getImageFormatExtension(imageFormat) {
	var extension;
	if (imageFormat == "image/jpeg") {
		extension = ".jpg";
	} else {
		extension = ".png";
	}
	return extension;
}

function getCaptureVisibleTabFormat(imageFormat) {
	var captureVisibleTabFormat;
	if (imageFormat == "image/jpeg") {
		captureVisibleTabFormat = "jpeg";
	} else {
		captureVisibleTabFormat = "png";
	}
	return captureVisibleTabFormat;
}

function showMessageNotification(title, message, error) {
   var options = {
		   type: "basic",
		   title: title,
		   message: message,
		   iconUrl: "images/icons/default128_white.png",
		   priority: 1
   }
   
   var notificationId;
   if (error) {
	   notificationId = "error";
	   options.contextMessage = "Error: " + error;
	   if (DetectClient.isChrome()) {
		   options.buttons = [{title:"If this is frequent then click here to report it", iconUrl:"images/open.svg"}];
	   }
   } else {
	   notificationId = "message";
   }
   
   chrome.notifications.create(notificationId, options, function(notificationId) {
	   if (chrome.runtime.lastError) {
		   console.error(chrome.runtime.lastError.message);
	   } else {
		   setTimeout(function () {
			   chrome.notifications.clear(notificationId);
		   }, error ? seconds(15) : seconds(5));
	   }
   });
}