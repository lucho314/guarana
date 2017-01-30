var storageResponse;
var storageManager;
var storage;

$(document).ready(function() {
	
	polymerPromise.then(function() {
		$("body").addClass("page-loading-animation");

		storageManager = new StorageManager();
		getStorage(storageManager).then(function(storage) {
	
			console.log("settings", storage);
			window.storage = storage;
	
			if (pref("donationClicked")) {
				$("[mustDonate]").each(function(i, element) {
					$(this).removeAttr("mustDonate");
				});
			}
	
			// must init options BEFORE polymer because paper-radio-group would keep it's ripple on if i tried selecting it after it was loaded
			initOptions(storage);
			
			$("#logo").dblclick(function() {
		    	if (storage.donationClicked) {
		    		storageManager.remove("donationClicked");
		    	} else {
		    		storageManager.set("donationClicked", true);
		    	}
		    	location.reload(true);
			});
			
			$("#presetButtonAction").on("iron-select", function(event) {
				setTimeout(function() {
					initPopup(storage);
				}, 500);
			});
			
			$("#buttonIcons paper-radio-button").change(function(event) {
				if (pref("donationClicked")) {
					setTimeout(function() {
						setButtonIcon(storage);
					}, 1);
				}
			});
			
			$("#removeMenuItems").change(function() {
				setTimeout(function() {
					var storageManager = new StorageManager();
					getStorage(storageManager).then(function(storage) {
						initContextMenu(storage);
					});
				}, 500);
			});
			
			$("#viewUploadedFiles").click(function() {
				if (storage.uploadedImgurFiles.length) {
					if ($("#uploadedFiles").is(":visible")) {
						$("#uploadedFiles").slideUp();
					} else {
						$("#uploadedFiles").empty();
						storage.uploadedImgurFiles.forEach(function(file, index) {
							var $div = $("<div class='file layout self-start'><img style='cursor:pointer;max-width:200px;vertical-align:middle' src='" + file.url + "'/> <paper-icon-button class='delete' style='vertical-align:top' icon='delete'></paper-icon-button></div>")
							$div.data("file", file);
							$div.find("img").click(function() {
								window.open(file.url);
							});
							$div.find(".delete").click(function() {
								window.open(file.deleteUrl);
								storage.uploadedImgurFiles.some(function(thisFile, thisIndex) {
									if (thisFile.url == file.url) {
										storage.uploadedImgurFiles.splice(thisIndex, 1);
										return true;
									}
								});
								storageManager.set("uploadedImgurFiles", storage.uploadedImgurFiles);
								$div.slideUp();
							});
							$("#uploadedFiles").append($div);
						});
						$("#uploadedFiles").slideDown();
					}
				} else {
					openGenericDialog({content: "No files uploaded to Imgur!"});
				}
			});
			
			Polymer.dom($("#version")[0]).textContent = "v." + chrome.runtime.getManifest().version;
			$("#version").click(function() {
				showLoading();
				chrome.runtime.requestUpdateCheck(function(status, details) {
					hideLoading();
					console.log("updatechec:", details)
					if (status == "no_update") {
						openGenericDialog({title:"No update!", otherLabel: "More info"}).then(function(response) {
							if (response == "other") {
								location.href = "https://jasonsavard.com/wiki/Extension_Updates";
							}
						})
					} else if (status == "throttled") {
						openGenericDialog({title:"Throttled, try again later!"});
					} else {
						openGenericDialog({title:"Response: " + status + " new version " + details.version});
					}
				});
			});
			
		});
	});
	
});