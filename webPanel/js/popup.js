var storageManager;
var storage;

function showCannotCaptureWarning(node, error) {
	hideSpinner(node);
	openDialog("cannotCaptureWarningTemplate").then(response => {
		if (response == "ok") {
			// nothing
		}
	});
	if (error) {
		showError("Error: " + error);
	}
}

function showSpinner(node) {
	$(node).find("iron-icon").first().after("<paper-spinner active></paper-spinner");
	$(node).find("iron-icon").first().hide();
}

function hideSpinner(node) {
	$(node).find("paper-spinner").remove();
	$(node).find("iron-icon").show();
}

$(document).ready(function() {
	
	storageManager = new StorageManager();
	getStorage(storageManager).then(function(storage) {
		console.log("settings", storage);
		window.storage = storage;
		
		var BODY_HEIGHT = DetectClient.isChrome() ? 409 : 265;
		var HEADER_HEIGHT = 56;
		var SEPERATOR_HEIGHT = 17;
		var MENU_ITEM_HEIGHT = 48;
		
		var height = BODY_HEIGHT;
		if (storage.removeToolbarInPopup) {
			$("[main] paper-toolbar").hide();
			height -= HEADER_HEIGHT;
		}
		if (!storage.grabEntireScreenButton) {
			$("#entireScreen").hide();
			height -= MENU_ITEM_HEIGHT;
		}
		if (!storage.openFileButton) {
			$(".separator").hide();
			$("#openFile").hide();
			height -= SEPERATOR_HEIGHT;
			height -= MENU_ITEM_HEIGHT;
		}
		
		$("body").css("height", height);
	});
	
	$("#titleClickArea").click(function() {
		chrome.tabs.create({url:"https://jasonsavard.com?ref=SSHeader"});
	});
	
	$("#reloadTab").click(function() {
		getActiveTab(function(tab) {
			chrome.tabs.reload(tab.id);
			self.close();
		});
	});
	
	$("#selectedArea").click(function() {
		var that = this;
		showSpinner(that);
		grabSelectedArea().then(() => {
			window.close();
		}).catch(error => {
			showCannotCaptureWarning(that, error);
		});
		sendGA('popup', 'selectedArea');
	});
	
	$("#selectedArea .delay").click(function() {
		var that = this;
		$(that).addClass("clicked");
		showSpinner(that);
		chrome.runtime.getBackgroundPage(bg => {
			bg.grabSelectedArea(seconds(3)).then(() => {
				window.close();
			}).catch(error => {
				showCannotCaptureWarning(that, error);
			});
		});
		sendGA('popup', 'selectedAreaDelay');
		return false;
	});
	
	$("#visibleArea, #justInstalledGrabVisibleArea").click(function() {
		var that = this;
		showSpinner(that);
		grabVisiblePart().then(() => {
			window.close();
		}).catch(error => {
			showCannotCaptureWarning(that, error);
		});
		sendGA('popup', 'visibleArea');
	});

	$("#visibleArea .delay").click(function() {
		var that = this;
		$(that).addClass("clicked");
		showSpinner(that);
		chrome.runtime.getBackgroundPage(bg => {
			bg.grabVisiblePart(seconds(3)).then(() => {
				window.close();
			}).catch(error => {
				showCannotCaptureWarning(that, error);
			});
		});
		sendGA('popup', 'visibleAreaDelay');
		return false;
	});

	$("#entirePage").click(function() {
		var that = this;
		showSpinner(that);

		grabEntirePage().then(() => {
			window.close();
		}).catch(error => {
			hideSpinner(that);
			openDialog("entirePageIssueDialogTemplate").then(response => {
				if (response == "ok") {
					// nothing
				} else {
					$("#visibleArea").click();
				}
			});
			showError("Error: " + error);
		});
		sendGA('popup', 'entirePage');
	});

	$("#entireScreen").click(function() {
		$(".instructions").slideUp();
		if (navigator.userAgent.toLowerCase().indexOf('mac') != -1) {
			openDialog("entireScreenMacInstructionsTemplate").then(function(response) {});
		} else if (navigator.userAgent.toLowerCase().indexOf('cros') != -1) {
			openDialog("entireScreenCrOSInstructionsTemplate").then(function(response) {});
		} else {
			var $dialog = initTemplate("entireScreenWindowsInstructionsTemplate");
			chrome.system.display.getInfo(function(displayInfoArray) {
				if (chrome.runtime.lastError) {
					console.error("error: " + chrome.runtime.lastError.message);
				} else {
					if (displayInfoArray && displayInfoArray.length >= 2) {
						$dialog.find("#multipleMonitors").removeAttr("hidden");
					}
				}
				openDialog($dialog).then(function(response) {});
			})
		}
		sendGA('popup', 'entireScreen');
	});

	$("#openFile").click(function() {
		sendGA('popup', 'openFile');
		chrome.tabs.create({url:"openFile.html"});
		window.close();
	});

	$("#openFromClipboardAndCrop").click(function() {
		sendGA('popup', 'openFromClipboardAndCrop');
		openFromClipboard(true).catch(response => {
			if (response.permissionNotGranted) {
				showError("You must grant this minimal permission if you want this extension to grab your image from the clipboard!");
			}
		});
	});

	$("#openFromClipboard").click(function() {
		sendGA('popup', 'openFromClipboard');
		openFromClipboard().catch(function(response) {
			if (response.permissionNotGranted) {
				showError("You must grant this minimal permission if you want this extension to grab your image from the clipboard!");
			}
		});
	});
	
	// Delay some
	setTimeout(function() {
		var $optionsMenu = initTemplate("optionsMenuItemsTemplate");
		initMessages("#options-menu *");
		
		$(".contribute").click(function() {
			chrome.tabs.create({url: 'donate.html?fromPopup'});
			window.close();
		});

		$(".discoverMyApps").click(function() {
			chrome.tabs.create({url:"https://jasonsavard.com?ref=SSOptionsMenu"});
			window.close();
		});

		$(".feedback").click(function() {
			chrome.tabs.create({url:"https://jasonsavard.com/forum/categories/explain-and-send-screenshots?ref=SSOptionsMenu"});
			window.close();
		});

		$(".changelog").click(function() {
			chrome.tabs.create({url:"https://jasonsavard.com/wiki/Explain_and_Send_Screenshots_changelog?ref=SSOptionsMenu"});
			window.close();
		});

		$(".options").click(function() {
			chrome.tabs.create({url:"options.html"});
			window.close();
		});

		$(".aboutMe").click(function() {
			chrome.tabs.create({url:"https://jasonsavard.com/about?ref=SSOptionsMenu"});
			window.close();
		});

		$(".help").click(function() {
			chrome.tabs.create({url:"https://jasonsavard.com/wiki/Explain_and_Send_Screenshots"});
			window.close();
		});
		
	}, 400);
	
	$(".close").click(function() {
		window.close();
	});

	function processClipboardItem(item) {
		return new Promise(function(resolve, reject) {
			if (item.kind == "file") {
				var fileName = item.name;
				var fileType = item.type;
		  		console.log("mimetype", JSON.stringify(item)); // will give you the mime types
		  		var blob = item.getAsFile();
		  		var reader = new FileReader();
		  		reader.onload = function(event) {
		  			resolve({fileName:fileName, fileType:fileType, dataUrl:event.target.result});
		  		};
		  		reader.readAsDataURL(blob);
			} else if (item.kind == "string") {
				// when i paste text, type=text/plain
				// when i copy/paste from paint it's a file with type=image/png
				// when i right click and "Copy image" type=text/html
				if (item.type == "text/html") {
					item.getAsString(function(s) {
						// returns this:   <html><body><xxStartFragmentxx><img src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAA...2EpZhOpUxE7OGFwX1dzxv3vX5ufsAUAFABQAUAFABQAUAFABQB//Z"/><xxEndFragmentxx></body></html> 
						console.log("getasstring:", item, s);
						var $div = $("<div></div>");
						$div.append(s);
						var dataUrl = $div.find("img").attr("src");

						if (dataUrl && dataUrl.indexOf("data:") == 0) {
							console.log("data: found");
							resolve({dataUrl:dataUrl});
						} else {
							resolve({couldNotProcessReason:"Can't process url: " + s});
						}
					});
				} else if (item.type == "text/plain") {
					item.getAsString(function(s) {
						if (s.indexOf("http") == 0) {
							resolve({urlWasCopied:true, url:s});
						} else {
							resolve({couldNotProcessReason:"Could not parse this text/plain: " + s});
						}
					});
				} else {
					resolve({couldNotProcessReason:"Could not determine item.type: " + item.type});
				}
			} else {
				resolve({couldNotProcessReason:"Could not determine item.kind: " + item.kind});
			}
		});
	}
	
	$("body").on("paste", function(e) {
		console.log("paste");
		var items = (event.clipboardData || event.originalEvent.clipboardData).items;
		
		var promises = [];
		for (var a=0; a<items.length; a++) {
			var promise = processClipboardItem(items[a]);
			promises.push(promise);
		}
		
		Promise.all(promises).then(function(promisesResponse) {
			var success = false;
			var urlWasCopied = false;
			var errors = [];
			promisesResponse.some(function(promiseResponse, index) {
				if (promiseResponse.couldNotProcessReason) {
					var error = "item " + index + " " + promiseResponse.couldNotProcessReason;
					errors.push(error);
					console.log(error);
				} else if (promiseResponse.urlWasCopied) {
					urlWasCopied = true;
					openDialog("urlWasCopiedTemplate").then(function(response) {
						if (response == "ok") {
							// nothing
						} else {
							chrome.tabs.create({url:promiseResponse.url});
						}
					});
				} else {
					console.log("item " + index, promiseResponse);
					
					if (localStorage.grabMethod == "openFromClipboardAndCrop") {
						chrome.runtime.getBackgroundPage(bg => {
							bg.screenShotData = promiseResponse.dataUrl;
							chrome.tabs.create({url: "snapshot.html"});
						});
					} else {
						openEditor(promiseResponse.dataUrl);
					}
					
					success = true;
					return true;
				}
			});
			if (!success && !urlWasCopied) {
				showError("No images in clipboard!");
			}
		}).catch(function(error) {
			showError(error);
		});

	});

});