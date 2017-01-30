var storageManager = new StorageManager();
var storage;
var screenShotTab;
var screenShotData;

function getManagedStorage() {
	return new Promise((resolve, reject) => {
		if (chrome.storage.managed) {
			chrome.storage.managed.get(function(items) {
				if (chrome.runtime.lastError) {
					console.error("managed error: " + chrome.runtime.lastError.message);
				} else {
					console.log("items", items);
				}
				resolve(items);
			});
		} else {
			resolve();
		}
	});
}

if (chrome.runtime.onInstalled && chrome.storage.sync) {
	chrome.runtime.onInstalled.addListener(function(details) {
		if (details.reason == "install") {
			storageManager.set("installDate", new Date().toJSON());
			
			getManagedStorage().then(items => {
				var doNotOpenWebsite;
				if (items && items.DoNotOpenWebsiteOnInstall) {
					doNotOpenWebsite = true;
				}
				if (!doNotOpenWebsite) {
					chrome.storage.sync.get(null, function(items) {
						console.info("sync items", items);
						if (chrome.runtime.lastError) {
							console.error("sync error: " + chrome.runtime.lastError.message);
						} else {
							if (items && items.installDate) {
								if (items.donationClicked) {
									// transfer from sync to local
									storageManager.set("donationClicked", items.donationClicked);
									storageManager.set("removeHeaderFooter", items.removeHeaderFooter);
								}
							} else {
								chrome.tabs.create({url:"https://jasonsavard.com/?ref=SSInstall"});
								chrome.storage.sync.set({"installDate": new Date().toJSON()}, function() {
									// nothing
								});
							}
						}
					});
				}
			});
			
			
		} else if (details.reason == "update") {
			// seems that Reloading extension from extension page will trigger an onIntalled with reason "update"
			// so let's make sure this is a real version update by comparing versions
			var realUpdate = details.previousVersion != chrome.runtime.getManifest().version;
			if (realUpdate) {
				console.log("real version changed");
			}
			
			var previousVersionObj = parseVersionString(details.previousVersion)
			var currentVersionObj = parseVersionString(chrome.runtime.getManifest().version);
			if (previousVersionObj.major != currentVersionObj.major || previousVersionObj.minor != currentVersionObj.minor) {
				var options = {
						type: "basic",
						title: getMessage("extensionUpdated"),
						message: "Explain and Send Screenshots " + chrome.runtime.getManifest().version,
						iconUrl: "images/icons/default128_white.png",
						buttons: [{title: getMessage("seeUpdates"), iconUrl: "images/open.svg"}, {title: getMessage("dismiss"), iconUrl: "images/DND.svg"}]
				}
				
				chrome.notifications.create("extensionUpdate", options, function(notificationId) {
					if (chrome.runtime.lastError) {
						console.error(chrome.runtime.lastError.message);
					}
				});
			}
		}	
	});
} else {
	window.failedOnInstall = true;
}

if (DetectClient.isChrome()) {
	chrome.commands.onCommand.addListener(function(command) {
		if (command == "grab_selected_area") {
			grabSelectedArea().catch(function() {
				alert(errorResponse);
			});
		} else if (command == "grab_visible_page") {
			grabVisiblePart().catch(function(errorResponse) {
				alert(errorResponse);
			});
		} else if (command == "grab_entire_page") {
			grabEntirePage().catch(function(errorResponse) {
				alert(errorResponse);
			});
		}
	});
}

getStorage(storageManager).then(function(storage) {
	window.storage = storage;
	
	if (window.failedOnInstall && !storage.installDate) {
		storageManager.set("installDate", new Date().toString());
		chrome.tabs.create({url:"https://jasonsavard.com/?ref=SSInstall"});
	}
	
	initPopup(storage);
	setButtonIcon(storage);
	initContextMenu(storage);
});

//Add listener once only here and it will only activate when browser action for popup = ""
chrome.browserAction.onClicked.addListener(function(tab) {
	
	getStorage(storageManager).then(function(storage) {
		window.storage = storage;
		console.log("buttonAction: ", storage);
		if (storage.presetButtonAction == "grabSelectedArea") {
			grabSelectedArea().catch(function() {
				alert(errorResponse);
			});
		} else if (storage.presetButtonAction == "grabVisiblePart") {
			grabVisiblePart().catch(function(errorResponse) {
				alert(errorResponse);
			});
		} else if (storage.presetButtonAction == "grabEntirePage") {
			grabEntirePage().catch(function(errorResponse) {
				alert(errorResponse);
			});
		}
	});
});

function openChangelogAndCloseNotification(notificationId) {
	chrome.windows.update(chrome.windows.WINDOW_ID_CURRENT, {focused:true});
	chrome.tabs.create({url:"https://jasonsavard.com/wiki/Explain_and_Send_Screenshots_changelog", active:true});
	chrome.notifications.clear(notificationId, function() {});
}

chrome.contextMenus.onClicked.addListener(function(info, tab) {
	if (info.menuItemId == "grabSelectedArea") {
		grabSelectedArea().catch(function() {
			alert(errorResponse);
		});
	} else if (info.menuItemId == "grabVisiblePart") {
		grabVisiblePart().catch(function() {
			alert(errorResponse);
		});
	} else if (info.menuItemId == "grabEntirePage") {
		grabEntirePage().catch(function() {
			alert(errorResponse);
		});
	}
});

chrome.notifications.onClicked.addListener(function(notificationId) {
	console.log("notif onclick", notificationId);
	
	if (notificationId == "extensionUpdate") {
		openChangelogAndCloseNotification(notificationId);
	}
});

// buttons clicked
chrome.notifications.onButtonClicked.addListener(function(notificationId, buttonIndex) {
	if (notificationId == "extensionUpdate") {
		if (buttonIndex == 0) {
			openChangelogAndCloseNotification(notificationId);
		} else if (buttonIndex == 1) {
			chrome.notifications.clear(notificationId, function(wasCleared) {
				// nothing
			});
		}
	}
});

// closed notif
chrome.notifications.onClosed.addListener(function(notificationId, byUser) {
	console.log("notif onclose", notificationId, byUser);
	
	if (notificationId == "extensionUpdate") {
		if (byUser) {
			
		}
	}
});

if (chrome.permissions) {
	chrome.permissions.onAdded.addListener(function(permissions) {
		var options = {
			type: "basic",
			title: "Access granted",
			iconUrl: "images/icons/default128_white.png",
			message: "Repeat the same action to grab from clipboard."
		}
		
		chrome.notifications.create("permissionAdded", options, function(notificationId) {
			if (chrome.runtime.lastError) {
				console.error(chrome.runtime.lastError.message);
			}
		});
	});
}

if (chrome.runtime.setUninstallURL) {
	chrome.runtime.setUninstallURL("https://jasonsavard.com/uninstalled?app=screenshots");
}