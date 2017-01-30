var inPopup;
if (location.href.indexOf("popup.html") != -1) {
	inPopup = true;
}

var pageLoadingAnimationTimer;

$("document").ready(function() {

	pageLoadingAnimationTimer = setTimeout(function() {
		$("body").addClass("page-loading-animation");
	}, location.href.indexOf("popup.htm") != -1 ? 150 : 10);
	
})

function loadPolymer(href) {
	return new Promise(function(resolve, reject) {
		$("document").ready(function() {
			console.time(href);
			var link = document.createElement('link');
			link.rel = 'import';
			link.href = href;
			//link.async = 'true'
			link.onload = function(e) {
				console.timeEnd(href);
				resolve();
			};
			link.onerror = function(e) {
				console.log("jerror loading polymer: ", e);
				reject(e);
			};
			document.head.appendChild(link);
		});
	});
}

var delay;
if (inPopup) {
	if (DetectClient.isChrome()) {
		delay = 200;
	} else {
		delay = 200;
	}
} else {
	delay = 1;
}

var polymerPromise = new Promise(function(resolve, reject) {
	setTimeout(function() {
		loadPolymer("vulcanized-polymer.html").then(function() {
			clearTimeout(pageLoadingAnimationTimer);
			$("body").removeClass("page-loading-animation");
			// polymer removes this autmoatically in Chrome, but not in Firefox when using polyfills
			$("body").removeAttr("unresolved");
			resolve();
		});
	}, delay);
});
	
var polymerPromise2 = new Promise(function(resolve, reject) {
	polymerPromise.then(function() {
		setTimeout(function() {
			loadPolymer("vulcanized-polymer2.html").then(function() {
				$("body").removeAttr("unresolved2");
				$("body").attr("resolved2", "");
				$("body").removeClass("page-loading-animation");
				resolve();
			});
		}, delay);
	});
});

function initTemplate(idOrObject) {
	var $template;
	var isId;
	var $innerNode;
	
	if (typeof idOrObject === "string") {
		$template = $("#" + idOrObject);
		isId = true;
		
	} else {
		$template = idOrObject;
	}
	
	if ($template.length) {
		console.log("import template")
		var template = $template[0];
		
		var newNode = document.importNode(template.content, true);
		$template.replaceWith(newNode);
		
		if (isId) {
			$innerNode = $("#" + idOrObject.replace("Template", ""));
		}

		if ($innerNode && $innerNode.length) {
			initMessages("#" + idOrObject.replace("Template", "") + " *");
		} else {
			initMessages($template);
		}
	}

	if (isId) {
	// cannot use the $template handle above, must refetch new node because it seems the polymer methods are not attached when reusing $template object above
		$template = $("#" + idOrObject.replace("Template", ""));
	}
	
	if (!$template.length) {
		//alert("Could not find template: " + id.replace("Template", ""));
	}
	
	return $template;
}

function openDialog(idOrObject, params) {
	return new Promise(function(resolve, reject) {
		setTimeout(function() {
			params = initUndefinedObject(params);
		
			var $dialog;
			if (typeof idOrObject == "string") {
				if (idOrObject.indexOf("Template") != -1) {
					$dialog = initTemplate(idOrObject);
				} else {
					$dialog = $("#" + idOrObject);
				}
			} else {
				$dialog = idOrObject;
			}
			
			// required timeout to finish rendering the dialog node from initTemplate
			setTimeout(function() {
				polymerPromise2.then(function() {

					$dialog.find("[dialog-other]").click(function() {
						resolve("other");
					});

					$dialog.find("[dialog-dismiss], .cancelDialog").click(function() {
						resolve("cancel");
					});
		
					$dialog.find("[dialog-confirm], .okDialog").click(function() {
						resolve("ok");
					});
					
					$dialog[0].open();
				});
				
			}, 1);
			
		}, 100);
	});
}

function showLoading() {
	showToast({toastId:"processing", text:getMessage("loading")});
}

function showSaving() {
	showToast({toastId:"processing", text:getMessage("saving")});
}

function showMessage(msg) {
	showToast({toastId:"message", text:msg, duration:3});
}

function showError(msg, actionParams) {
	showToast({toastId:"error", text:msg, duration:10, actionParams:actionParams});
}

function showToast(params) {
	var $toast = $("#" + params.toastId);
	
	polymerPromise2.then(function() {
		$toast[0].hide();
		$toast[0].show({text:params.text, duration:seconds(params.duration ? params.duration : 60)});
		// must do this after .show to override text
		$toast.find("#label").html(params.text); // for html
		
		$toast.find(".closeToast").click(function() {
			$toast[0].hide();
		});
	
		if (!params.keepToastLinks) {
			var $toastLink = $toast.find(".toastLink");
			if (params.actionParams) {
				$toastLink
					.removeAttr("hidden")
					.off()
					.on("click", function() {
						params.actionParams.onClick();
					})
				;
				if (params.actionParams.text) {
					Polymer.dom($toastLink[0]).textContent = params.actionParams.text;
				}
			} else {
				$toastLink.attr("hidden", "");
			}
		}
	});
}

function hideLoading() {
	polymerPromise2.then(function() {
		$("#processing")[0].hide();
	});
}

function hideSaving() {
	$("#processing")[0].hide();
}

function dismissToast($dismiss) {
	$dismiss.closest("paper-toast")[0].hide();
}

function openGenericDialog(params) {
	return new Promise(function(resolve, reject) {
		var TITLE_SELECTOR = "h2";
		var CONTENT_SELECTOR = ".dialogDescription .scrollable";
		
		polymerPromise2.then(function() {
	
			function setButtonLabel(buttonSelector, text) {
				var button = $dialog.find(buttonSelector)[0];
				if (button) {
					Polymer.dom(button).textContent = text;
				}
			}
			
			var $dialog = initTemplate("genericDialogTemplate");
			
			if (!params.title) {
				params.title = "";
			}
			$dialog.find(TITLE_SELECTOR).html(params.title);
		
			if (!params.content) {
				params.content = "";
			}
			
			if (typeof params.content == 'jquery') {
				$dialog.find(CONTENT_SELECTOR).append(params.content);
			} else {
				$dialog.find(CONTENT_SELECTOR).html(params.content);
			}
			
		
			if (!params.okLabel) {
				params.okLabel = getMessage("ok");
			}
			setButtonLabel(".okDialog", params.okLabel)
			
			var cancelLabel;
			if (params.cancelLabel) {
				cancelLabel = params.cancelLabel;
			} else {
				cancelLabel = getMessage("cancel");
			}
			setButtonLabel(".cancelDialog", cancelLabel)
			
			if (params.showCancel || params.cancelLabel) {
				$dialog.find(".cancelDialog").removeAttr("hidden");
			} else {
				$dialog.find(".cancelDialog").attr("hidden", true);
			}
			
			if (params.otherLabel) {
				$dialog.find(".otherDialog").removeAttr("hidden");
				setButtonLabel(".otherDialog", params.otherLabel)
			} else {
				$dialog.find(".otherDialog").attr("hidden", "");
			}
		
			if (params.otherLabel2) {
				$dialog.find(".otherDialog2").removeAttr("hidden");
				setButtonLabel(".otherDialog2", params.otherLabel2)
			} else {
				$dialog.find(".otherDialog2").attr("hidden", "");
			}

			if (params.noAutoFocus) {
				$dialog.find("[autofocus]").removeAttr("autofocus");
			}

			openDialog($dialog).then(function(response) {
				resolve(response);
			});
		});
	});
}

function isFocusOnInputElement() {
	return document.activeElement.nodeName == "SELECT" || document.activeElement.nodeName == "TEXTAREA" || document.activeElement.nodeName == "INPUT" || document.activeElement.nodeName == "OVERLAY-HOST" || document.activeElement.nodeName == "PAPER-BUTTON" || document.activeElement.nodeName == "PAPER-INPUT" || document.activeElement.nodeName == "PAPER-ITEM";
}