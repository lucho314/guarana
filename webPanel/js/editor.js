//var bg = chrome.extension.getBackgroundPage();

var storage;
var storageManager = new StorageManager();

var screenShotTab;

var DEBUG_PICT_DOWN = false;
var DEBUG_IMM_DOWN = false;

var canvastemp;
var context;
var contexttemp;
var clickedTool;
var previousClickedTool;
var canvasLeft;
var aborted = false;
var fontSize;
var fontBackground;

if (!window.BlobBuilder && window.WebKitBlobBuilder) {
	window.BlobBuilder = window.WebKitBlobBuilder;
}

function writeText(str, context, x, y, backgroundColor, backgroundWidth) {
	if (backgroundColor) {
		context.fillStyle = backgroundColor;
	    var width = backgroundWidth ? backgroundWidth : context.measureText(str).width;
	    context.fillRect(x-1, y+1 /* depends on font */, width+4, parseInt(fontSize, 10)+4);
	    context.fillStyle = drawingColor;
	}
	
	if (context.fillText) {
		context.fillText(str, x+1, y);
	} else if (context.strokeText) {
		context.strokeText(str, x, y);
	}
}

function watermarkImage() {
	if (typeof canvas != "undefined" && canvas.width > 200 && screenShotTab) {
		
		setShadow(true);
		
		context.font = 'normal 11px sans-serif';
		c.strokeStyle = c.fillStyle = "white";
		
		writeText("Explain and Send Screenshots", context, 10, 15);
		writeText(screenShotTab.url, context, 10, canvas.height-15);
		
		// reset shadow
		setShadow(false);
		
		c.strokeStyle = c.fillStyle = drawingColor;
	}
}

function setShadow(flag, offset) {
	if (flag) {
		if (!offset) {
			offset = 1;
		}
		context.shadowOffsetX = contexttemp.shadowOffsetX = offset;
		context.shadowOffsetY = contexttemp.shadowOffsetY = offset;
		context.shadowBlur = contexttemp.shadowBlur = 0;
		context.shadowColor = contexttemp.shadowColor = "gray";
	} else {
		//context.shadowOffsetX = contexttemp.shadowOffsetX = 0;
		//context.shadowOffsetY = contexttemp.shadowOffsetY = 0;
		context.shadowBlur = contexttemp.shadowBlur = 0;
		context.shadowColor = contexttemp.shadowColor = "none";
	}
}

function uploadImage() {
	return new Promise((resolve, reject) => {
		// note: imgurl does not accept jpg
		var base64Data = canvas.toDataURL().replace(/^data:image\/(png|jpg);base64,/, "");
		
		$.ajax({
			//url: "https://api.imgur.com/3/image",
			url: "https://imgur-apiv3.p.mashape.com/3/image",
			type: "POST",
			timeout: 45000,
			data: {type: "base64", image: base64Data},
			beforeSend : function(xhr) {
				xhr.setRequestHeader("X-Mashape-Key", "GIwSVEegkCmshTWehOzGWpxZJKcfp1zpdM1jsnCDkfLlgZu6n7");
				xhr.setRequestHeader("Authorization", "Client-ID " + "c26e99413670dbc");
			}
		}).done(function(data, textStatus, jqXHR) {
			var response = jqXHR.responseJSON;
			if (response.success) {
				var imageUrl = response.data.link;
				var deleteUrl = "https://imgur.com/delete/" + response.data.deletehash;
				
				// unshift adds to beginning of array
				storage.uploadedImgurFiles.unshift({url:imageUrl, deleteUrl:deleteUrl});
				// max items to have is 20
				if (storage.uploadedImgurFiles.length > 20) {
					storage.uploadedImgurFiles.pop();
				}
				storageManager.set("uploadedImgurFiles", storage.uploadedImgurFiles);
				
				resolve({imageUrl:imageUrl, deleteUrl:deleteUrl});
			} else {
				reject(response.data.error);
			}
		}).fail(function(jqXHR, textStatus, errorThrown) {
			try {
				if (jqXHR.status == 401) {
					reject(errorThrown);
				} else {
					if (jqXHR && jqXHR.responseJSON) {
						reject(jqXHR.responseJSON.data.error);
					} else {
						reject(textStatus + " " + errorThrown);
					}
				}
			} catch(e) {
				reject(e);
			}
		});		
	}).then(response => {
		return Promise.resolve(response);
	}).catch(error => {
		logError(error);
		openGenericDialog({content:"There was a problem. Download the image or try again later.<br><br><div style='color:gray;text-align:right;font-size:90%'>" + error + "</div>"});
		return Promise.reject(error);
	});
}

function showLoading() {
	$("#progress").css("opacity", 1);
	$("paper-header-panel").css("opacity", 0.4)
}

function hideLoading() {
	$("#progress").css("opacity", 0);
	$("paper-header-panel").css("opacity", 1)
}

function getFontBackground() {
	var backgroundColor;
	if (storage.fontBackground == "semi-transparent") {
		backgroundColor = "rgba(255, 255, 255, 0.6)";
	} else if (storage.fontBackground == "none") {
		backgroundColor = "transparent";
	} else {
		backgroundColor = storage.fontBackground;
	}
	return backgroundColor;
}

function initCanvasPosition() {
	canvasLeft = $("canvas").offset().left;
	$("#canvastemp").css("left", canvasLeft );
}

$(document).ready(function() {
	
	// Must load storage items first (because they are async)
	getStorage(storageManager).then(storage => {
		console.log("settings", storage);
		window.storage = storage;
		
		if (storage.alwaysUpload) {
			$("#gmailIssue").hide();
			$(".makeDefault").hide();
		} else {
			$("#removeDefault").hide();
		}
		
		function initFonts() {
			if (storage.fontSize.match(/small/i)) {
				fontSize = 12;
				lineHeight = 16;
			} else if (storage.fontSize.match(/normal/i)) {
				fontSize = 18;
				lineHeight = 22;
			} else {
				fontSize = 30;
				lineHeight = 34;
			}
			
			fontSize *= devicePixelRatio;
			lineHeight *= devicePixelRatio;
			
			context.font = contexttemp.font = 'normal ' + fontSize + "px " + storage.fontFamily;
			
			$("#text")
				.css("font", context.font)
				.css("line-height", lineHeight + "px")
				.css("background-color", getFontBackground())
			;
			
			if (DetectClient.isChrome()) {
				$("#text").addClass("chromeOnlyMultilinePatch");
			}
		}
		
		function initLineOptions() {
			c.lineWidth = storage.lineWeight;
		}

		chrome.runtime.getBackgroundPage(function(bg) {
			
			// save this now because bg might disappear after going idle because it's an event page
			screenShotTab = bg.screenShotTab;
			//console.log("in edit: " + bg.screenShotData.substring(0, 50));
			
			var image = new Image();
			image.src = bg.screenShotData;
			image.onload = function() {
				
				polymerPromise2.then(function() {

					console.log("width of image: " + image.width);
					
					canvas = document.getElementById('canvas');
					canvastemp = document.getElementById('canvastemp');
					context = canvas.getContext('2d');
					contexttemp = canvastemp.getContext('2d');
					
					var rightMargin = 0;
					if (localStorage.grabMethod == "entirePage") {
						rightMargin = SCROLLBAR_WIDTH;
					}
					canvas.width = canvastemp.width = image.width - rightMargin;
					canvas.height = canvastemp.height = image.height;
					
					// set canvas.STYLE.width for hidpi blurry issues, refer to http://www.html5rocks.com/en/tutorials/canvas/hidpi/
					canvas.style.width = canvastemp.style.width = canvas.width / devicePixelRatio + 'px';
					canvas.style.height = canvastemp.style.height = canvas.height / devicePixelRatio + 'px';
					
					context.drawImage(image, 0, 0);

					context.scale(devicePixelRatio, devicePixelRatio);
					contexttemp.scale(devicePixelRatio, devicePixelRatio);

					initCanvasPosition();
					
					// if image is small then lower the top of the image editing box to show a space between header and image
					if (canvas.width < 600) {
						$("#workspace").css("margin-top", "40px");
					}
					
					$(canvas).addClass("loaded");
	
					initPaint();
					
					initFonts();
					initLineOptions();
					
					undoSave(true);
					
					$("body").removeClass("postPolymerLoading");
				});
			}
		});
		
		$("#toolsButtons > *").click(function() {
			previousClickedTool = clickedTool;
			clickedTool = $(this).attr("id");
			if (clickedTool != "undo" && clickedTool != "color") {
				setShadow(false);
				c.globalAlpha = 1;
				$("#toolsButtons > *").removeClass("selected");
				$(this).addClass("selected");
				
				$(".subOptions").slideUp();
			}
			sendGA("actionButtons", clickedTool);
		})
		
		$("#refresh").click(function() {
			window.location.reload();
		});
		
		$("#undo").click(function() {
			undo();
		});
		
		$("#redo").click(function() {
			redo();
		});

		$("#rectangle").click(function() {
			initLineOptions();
			$("#lineOptions").slideDown();
			c.tool = new tool.rectangle();
			document.getElementById("canvas").className = "line";
		})

		$("#circle").click(function() {
			initLineOptions();
			$("#lineOptions").slideDown();
			c.tool = new tool.ellipse();
			document.getElementById("canvas").className = "line";
		})

		$("#crop").click(function() {
			c.tool = new tool.select();
			document.getElementById("canvas").className = "line";
		})
		
		$("#select").click(function() {
			$("#selectOptions").slideDown();
			c.tool = new tool.select();
			document.getElementById("canvas").className = "line";
		});
		
		$("#cut").click(function() {
			cut();
		});

		$("#copy").click(function() {
			copy();
		});

		$("#paste").click(function() {
			paste();
		});

		$("#delete").click(function() {
			c.tool.del();
		});
		
		$("#arrow").click(function() {
			initLineOptions();
			$("#lineOptions").slideDown();
			c.tool = new tool.arrow();
			document.getElementById("canvas").className = "line";
		});

		$("#line").click(function() {
			initLineOptions();
			$("#lineOptions").slideDown();
			c.tool = new tool.line();
			document.getElementById("canvas").className = "line";
		});

		$("#freeHand").click(function() {
			initLineOptions();
			$("#lineOptions").slideDown();
			c.tool = new tool.pencil();
			document.getElementById("canvas").className = "line";
		});

		$("#writeText").click(function() {
			initFonts();
			$("#textOptions").slideDown();
			c.tool = new tool.text();
			document.getElementById("canvas").className = "text";
		});

		$("#highlight").click(function() {
			console.log("highlight");
			c.tool = new tool.highlight();
			document.getElementById("canvas").className = "highlight";
		});

		$("#blur").click(function() {
			console.log("blur");
			c.tool = new tool.eraser();
			document.getElementById("canvas").className = "blur";
		});

		$("#color").click(function() {
			var $colorPicker = $("<color-picker id='colorPicker' width='200' height='200'></color-picker>");
			$("#colorPickerWrapper").empty().append( $colorPicker );

			$colorPicker
				.on("colorselected", function(e) {
					colorDetails = e.originalEvent.detail;
					console.log(colorDetails);
					
					//$("#tools iron-icon").css("color", colorDetails.hex);
					$("#color iron-icon").css("color", colorDetails.hex);
					
					setDrawingColor(colorDetails.hex);
					$("#text").css("color", colorDetails.hex);
					
					// need timeout before removing colorpicker because the click event would go through the layer to the canvas??
					setTimeout(function() {
						$("#colorPicker").remove();
					}, 100);
				})
			;

		});
		
		function changeZoom(factor, resetScale) {
			var oldCanvas = canvas.toDataURL();
			
			$("body").addClass("zoomed");
			
			$("#canvas").width( $("#canvas").width() * factor ); 
			$("#canvas").height( $("#canvas").height() * factor );
			
			canvas.width *= factor;
			canvas.height *= factor;
			
			$("#canvastemp").width( $("#canvas").width() );
			$("#canvastemp").height( $("#canvas").height() );
			
			canvastemp.width *= factor;
			canvastemp.height *= factor;
			
			initCanvasPosition();
			
			var img = new Image();
			img.src = oldCanvas;
			img.onload = function (){
				context.scale(devicePixelRatio * factor, devicePixelRatio * factor);
				context.drawImage(img, 0, 0);
				context.scale(devicePixelRatio * resetScale, devicePixelRatio * resetScale);
				
				if (previousClickedTool != "zoomIn" && previousClickedTool != "zoomOut") {
					if (!previousClickedTool) {
						previousClickedTool = "arrow";
					}
					$("#" + previousClickedTool).click();
				}
			}
			
			$("#refresh").removeClass("hidden");
			//$("#refresh").css("visibility", "visible");
		}
		
		$("#zoomIn").click(function() {
			changeZoom(1.5, 0.66666666);
		});
		
		$("#zoomOut").click(function() {
			changeZoom(0.66666666, 1.5);
		});		

		$("#textOptions paper-item").click(function() {
			setTimeout(function() {
				initFonts();
			}, 100);
		})
		
		$("#lineOptions paper-item").click(function() {
			setTimeout(function() {
				initLineOptions();
			}, 100);
		});

		$("#text").blur(function() {
			context.textBaseline = 'top';
			c.fillStyle = c.strokeStyle;

			var x = (($(this).position().left - canvasLeft)) + 1;
			var y = ($(this).position().top) - 1;
			
			var lines = $(this).val().split("\n");
			
			var largestWidth = 0;
			for (var a=0; a<lines.length; a++) {
				var width = context.measureText(lines[a]).width;
				if (width > largestWidth) {
					largestWidth = width;
				}
			}
			
			for (var a=0; a<lines.length; a++) {
				writeText(lines[a], context, x, y, getFontBackground(), largestWidth);
				y += parseInt($(this).css("line-height").replace("px", ""));
			}

			$(this).hide();
			document.getElementById("workspace").className = "text";
			
			undoSave();
		});
		
		$("#canvas").mousedown((event) => {
			// patch: when using Mac right click to save image it would paste as blank in Gmail - so execute the done action to save the canvas to image and voila it works 
			if (event.button == 2) {
				$("#done").click();
			} else {
				$("#refresh").removeClass("hidden");
			}
		});
		
		$("#done").click(function() {
			if (isEligibleForReducedDonation()) {
				$("#reducedDonation").fadeIn();
			}
			
			$("#toolsButtons").hide();
			$("#imageOptions").unhide();
			
			$("#workspace").addClass("done");
			
			$("#canvas, #finalImage")
				.attr("title", getMessage("rightClickImage"))
			;

			if (!storage.removeHeaderFooter) {
				watermarkImage();
				$("#removeWatermark").fadeIn();
			}

			// because the right click on a canvas only saves as .png, we must transfer to finalimage (img) node if user selected jpeg image format
			if (storage.imageFormat == "image/jpeg") {
				$("#finalImage")
					.css({width:canvas.style.width, height:canvas.style.height})
					.attr("src", canvas.toDataURL(storage.imageFormat))
					.show()
				;
				$("#canvas").hide();
			}
		
		});
		
		$("#back").click(function() {
			$("#reducedDonation").hide();
			$("#toolsButtons").show();
			$("#imageOptions").attr("hidden", "");
			$("#workspace").removeClass("done");
			$("#removeWatermark").hide();
			$("#finalImage").hide();
			$("#canvas").show();
		});
		
		$("#imageOptions > paper-button").click(function() {
			sendGA("afterEditingAction", $(this).attr("id"));
		});
		
		$("#download").click(function() {
			if (DetectClient.isFirefox()) {
				openGenericDialog({content: "Right click image and choose <b>Save Image As...</b>"});
			} else {
				downloadFile( screenShotTab.title + getImageFormatExtension(storage.imageFormat) );
			}
		});
		
		$("#open").click(function() {
			saveToLocalFile(screenShotTab.title + getImageFormatExtension(storage.imageFormat)).then(function(fileUrl) {
				location.href = fileUrl;
			});
		});

		$("#pdf").click(() => {
			openGenericDialog({
				content: "<ol><li>Click the <iron-icon icon='open-in-new'></iron-icon> <b>OPEN</b> button</li><li>Click the <b>Chrome menu</b> <iron-icon icon='menu'></iron-icon></li><li>Click <b>Print...</b></li><li>Select <b>Change</b> and select <b>Save as PDF</b></li><li>Click <b>Save</b></li></ol>" 
			});
		});

		$("#searchImage").click(() => {
			openGenericDialog({
				content: getMessage("copyToClipboardDesc") + " <b>Search Google for image</b>" 
			});
		});

		$("#upload").click(function() {
			var key = "displayUploadToImgurWarning";
			var value = true;
			
			return new Promise((resolve, reject) => {
				if (storage[key]) {
					resolve();
				} else {
					openDialog("shareImageDialogTemplate").then(function(response) {
						if (response == "ok") {
							storage[key] = value
							storageManager.set(key, value);
							resolve();
						} else {
							reject();
						}
					});
				}
			}).then(() => {
				showLoading();
				return uploadImage().then(response => {
					if (storage.afterGrabAction == "upload") {
						$("#hiddenLink").attr("value", response.imageUrl);
						$("#hiddenLink")[0].select();
						document.execCommand('Copy');
						showMessage(getMessage("linkCopied"));
					} else {
						var $template = initTemplate("shareLinkDialogTemplate");
						$template.find("#shareLink").attr("value", response.imageUrl);
						$template.find("#shareLink")[0].inputElement.select();
						
						$template.find(".otherDialog").click(function() {
							showMessage("Go into the Options > View Uploaded Files");
						});
						$template.find(".okDialog").click(function() {
							$template.find("#shareLink")[0].inputElement.select();
							document.execCommand('Copy');
							showMessage(getMessage("linkCopied"));
							$template[0].close();
						});
						openDialog($template);
					}
					hideLoading();
				});
			}, error => { // this is the 2nd parameter/reject parameter
				// do nothing user did not agree to upload
			}).catch(error => {
				hideLoading();
			});
		});
		
		$("#saveToDrive").click(function() {
			var targetExtensionId;
			if (chrome.runtime.id == "fpgjambkpmbhdocbdjocmmoggfpmgkdc") {
				targetExtensionId = "cmklgggigkoeoniaajabapekafkgnfmn"; // local
			} else {
				targetExtensionId = "pppfmbnpgflleackdcojndfgpiboghga"; // prod
			}
			
			showLoading();
			var data = canvas.toDataURL(storage.imageFormat).split(",")[1];
			chrome.runtime.sendMessage(targetExtensionId, {name:"Screenshot of " + screenShotTab.title, type:storage.imageFormat, data:data}, function(driveResponse) {
				console.log("response", driveResponse);
				hideLoading();
				
				if (chrome.runtime.lastError) { // might not be installed
					openGenericDialog({
						title: "Extension required",
						content: "This function requires my other extension Checker Plus for Google Drive",
						okLabel: "Get extension",
						showCancel: true
					}).then((response) => {
						if (response == "ok") {
							window.open("https://jasonsavard.com/Checker-Plus-for-Google-Drive?ref=screenshotExtension");
						}
					});
				} else {
					if (driveResponse.error) {
						console.log("error", driveResponse.error);
						var content;
						if (driveResponse.error.errorCode == "NO_TOKEN_OR_CODE") {
							content = "You must open the Drive extension and grant access!";
						} else {
							content = "There was a problem uploading the file to Google Drive: " + driveResponse.error;
						}
						openGenericDialog({
							content: content
						});
					} else {
						
						var driveUrl = "https://drive.google.com/drive/blank?action=locate&id=" + driveResponse.data.id;
						
						if (storage.afterGrabAction == "saveToGoogleDrive") {
							location.href = driveUrl;
						} else {
							var $template = initTemplate("driveDialogTemplate");
							$template.find("#driveTitle").attr("value", driveResponse.data.title);
							$template.find("#driveLink").attr("value", driveResponse.data.alternateLink);
							$template.find("#driveLink")[0].inputElement.select();
							$template.find("#driveLink, .copyLink").off().on("click", function() {
								$template.find("#driveLink")[0].inputElement.select();
								document.execCommand('Copy');
								showMessage(getMessage("linkCopied"));
							});
							
							openDialog($template).then(function(response) {
								if (response == "ok") {
									//location.href = driveResponse.data.alternateLink;
									
									// https://drive.google.com/folderview?id=0B42c3pO5_y67X0Fhcmg5XzhoR0U&usp=sharing
									// https://drive.google.com/drive/u/0/blank?action=locate&id=0B3UllN_9zvOYWDg3Tjc4aGNaMHc&parent=0AHUllN_9zvOYUk9PVA
									location.href = driveUrl;
								}
							});
						}
					}
				}
			});
		});

		$("#editInPixlr").click(function() {
			if (!storage.pixlrWarningDisplayed) {
				if (confirm("This will upload your image to Imgur.com to enable editing in Pixlr. Do you wish to continue?")) {
					storageManager.set("pixlrWarningDisplayed", true);
				} else {
					return false;
				}
			}
			
			showLoading();
			uploadImage().then(response => {
				location.href = "http://apps.pixlr.com/editor/?image=" + response.imageUrl + "&title=Captured by 'Explain and Send Screenshots'";
				hideLoading();
			}).catch(error => {
				hideLoading();
			});
		});
		
		$("#contribute").click(function() {
			chrome.tabs.create({url: 'donate.html?fromEditor'});
		});
		
		function sendMultipart(url, fileParams, dataParams, callback) {
			var BOUNDARY = "---------------------------1966284435497298061834782736";
			var rn = "\r\n";
			var data = new Blob()
			var append = function(dataParam) {
				data.append(dataParam)
			}
			/*
			var data = "", append = function(dataParam){
				data += dataParam;
			}
			*/
			append("--" + BOUNDARY);
			for (var i in dataParams) {
				append(rn + "Content-Disposition: form-data; name=\"" + i + "\"");
				append(rn + rn + dataParams[i] + rn + "--" + BOUNDARY);
			}
			append(rn + "Content-Disposition: form-data; name=\"" + fileParams.name + "\"");
			append("; filename=\"" + fileParams.filename + "\"" + rn + "Content-type: " + fileParams.contentType);
			append(rn + rn);
			var bin = atob(canvas.toDataURL().replace(/^data:image\/(png|jpg);base64,/, "")); //file.data
			var arr = new Uint8Array(bin.length);
			for(var i = 0, l = bin.length; i < l; i++) {
				arr[i] = bin.charCodeAt(i);
			}
			append(arr.buffer)
			//append(bin)

			append(rn + "--" + BOUNDARY);
			append("--");
				
			$.ajax({
				url: url,
				type: "POST",
				contentType: "multipart/form-data",
				timeout: 45000,
				processData: false, // Useful for not getting error when using blob in data
				data: data.getBlob(), // refer to processData flag just above
				beforeSend: function(request) {
					request.setRequestHeader("Content-type", "multipart/form-data; boundary=" + BOUNDARY);
				},
				complete: function(request, textStatus) {
					callback({request:request, textStatus:textStatus});
				}
			});
		};
		
		if (storage.donationClicked) {
			$("[mustDonate]").each(function(i, element) {
				$(this).removeAttr("mustDonate");
			});
		}
		
		$("#donate").click(function() {
			location.href = "donate.html?fromOptions=true";
		});
		
		$("#saveImageAs").click(function() {
			openDialog("saveImageAsDialogTemplate");
		});

		$("#copyToClipboard").click(function() {
			var $template = initTemplate("copyToClipboardDialogTemplate");
			if (DetectClient.isMac()) {
				$template.find("#pasteMac").removeAttr("hidden");
			} else {
				$template.find("#pastePC").removeAttr("hidden");
			}
			openDialog($template).then(function(response) {
				if (response == "cancel") {
					openGenericDialog({
						content: getMessage("useUploadButton", getMessage("upload"))
					});					
				}
			});
		});

		$("#share").click(function() {
			$("#copyToClipboard").click();
		});

		$("#options").click(function() {
			location.href = "options.html";
		});

		$("#help").click(function() {
			chrome.tabs.create({url: "https://jasonsavard.com/wiki/Explain_and_Send_Screenshots"});
		});
		
		function initButton(name) {
			if (!storage[name + "Button"]) {
				$("#" + name).hide();
			}
		}
		
		initButton("download");
		initButton("upload");
		initButton("saveToDrive");
		initButton("copyToClipboard");
		initButton("editInPixlr");
		initButton("share");
		initButton("open");
		initButton("pdf");
		initButton("searchImage");
		
		$("body")
			.click(function(e) {
				if ($(e.target).closest("#color").length || $(e.target).closest("#colorPicker").length) {
					// do nothing
				} else {
					setTimeout(function() {
						$("#colorPicker").remove();
					}, 100);
				}
			})
			.keydown(function(e) {
				if (isCtrlPressed(e) && e.keyCode == 67) {
					if (clickedTool != "select") {
						openGenericDialog({
							content: getMessage("rightClickImage") 
						});
					}
				}
			})
		;
		
		polymerPromise2.then(function() {
			initOptions(storage);
			
			setTimeout(function() {
				if (storage.afterGrabAction == "upload") {
					$("#upload").click();
				} else if (storage.afterGrabAction == "saveToGoogleDrive") {
					$("#saveToDrive").click();
				}
			}, 1);
			
		});
		
	});

});

function canvasToBlob(canvas) {
    // dataURI can be too big, let's blob instead http://code.google.com/p/chromium/issues/detail?id=69227#c27
    var dataURI = canvas.toDataURL(storage.imageFormat);

    // convert base64 to raw binary data held in a string doesn't handle URLEncoded DataURIs
    var byteString = atob(dataURI.split(',')[1]);
    
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

    // write the bytes of the string to an ArrayBuffer
    var ab = new ArrayBuffer(byteString.length);
    var ia = new Uint8Array(ab);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    // create a blob for writing to a file
    var blob = new Blob([ab], {type: mimeString});
    return blob;
}

function downloadFile(filename) {
    //var blob = new Blob([data], {type: 'text/json'}),
	var blob = canvasToBlob(canvas);
    
    e = document.createEvent('MouseEvents'),
    a = document.createElement('a')

    if (!filename) {
    	filename = "file" + getImageFormatExtension(storage.imageFormat);
    }
    a.download = filename;
    a.href = window.URL.createObjectURL(blob);
    a.dataset.downloadurl = [blob.mimeString, a.download, a.href].join(':');
    e.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
    a.dispatchEvent(e);
}
	    
function saveToLocalFile(filename) {
	return new Promise(function(resolve, reject) {
		var blob = canvasToBlob(canvas);
		
	    // file-system size with a little buffer
	    var size = blob.size + (1024/2);
	
	    //var name = ("test" + getImageFormatExtension(storage.imageFormat)).split('?')[0].split('#')[0];
	    //if (name) {
	        filename = filename
	            .replace(/^https?:\/\//, '')
	            .replace(/[^A-z0-9.]+/g, '-')
	            .replace(/-+/g, '-')
	            .replace(/^[_\-]+/, '')
	            .replace(/[_\-]+$/, '');
	        //name = '-' + name;
	    //} else {
	        //name = '';
	    //}
	    //name = 'screencapture' + name + '-' + Date.now() + getImageFormatExtension(storage.imageFormat);
	
	    // filesystem:chrome-extension://fpgjambkpmbhdocbdjocmmoggfpmgkdc/temporary/screencapture-test-png-1442851914126.png
	    function onwriteend() {
	    	resolve('filesystem:chrome-extension://' + chrome.i18n.getMessage('@@extension_id') + '/temporary/' + filename);
	    }
	
	    function errorHandler() {
	        reject();
	    }
	
	    window.webkitRequestFileSystem(window.TEMPORARY, size, function(fs){
	        fs.root.getFile(filename, {create: true}, function(fileEntry) {
	            fileEntry.createWriter(function(fileWriter) {
	                fileWriter.onwriteend = onwriteend;
	                fileWriter.write(blob);
	            }, errorHandler);
	        }, errorHandler);
	    }, errorHandler);
	});
}