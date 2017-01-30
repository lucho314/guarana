var licenseType = "singleUser";
var licenseSelected;
var minimumDonation = 1; // but being set overwritten when donate buttons are clicked
var currencySymbol = "$";
var currencyCode;

var donateButtonClicked = null;
var extensionName = getMessage("nameNoTM");
var multipleCurrencyFlag;
var storage;
var storageManager;

Controller();

function showLoading() {
	$("body").addClass("processing");
}

function hideLoading() {
	$("body").removeClass("processing");
}

if (!extensionName) {
	try {
		extensionName = chrome.runtime.getManifest().name;
	} catch (e) {
		console.error("Manifest has not been loaded yet: " + e);
	}
	
	var prefix = "__MSG_";
	// look for key name to message file
	if (extensionName && extensionName.match(prefix)) {
		var keyName = extensionName.replace(prefix, "").replace("__", "");
		extensionName = getMessage(keyName);
	}
}

function getMessageFromCurrencySelection(key) {
	var idx = document.getElementById("multipleCurrency").selectedIndex;
	var suffix = idx == 0 ? "" : idx+1;
	return getMessage(key + suffix);
}

function initCurrency() {
	$("#multipleCurrency").find("option").each(function (idx) {
		// TWD is not supported for alertPay so disable it (dont' remove it because the selector uses it's order in the list)
		if (donateButtonClicked == "alertPay" && /tw/i.test(window.navigator.language) && $(this).attr("value") == "TWD") {
			$(this).attr("disabled", "true");
			if (idx==0) {
				$("#multipleCurrency")[0].selectedIndex=1;
			}
		} else {
			$(this).removeAttr("disabled");
		}
	});
	
	function initCodesAndMinimums(donateButtonClicked) {
		var messageReducedPrefix;
		var messagePrefix;
		
		if (licenseType == "multipleUsers") {
			currencyCode = "USD"; // hard coded to USD for multipe user license
		} else {
			if (donateButtonClicked == "paypal") {
				messagePrefix = "minimumDonation";
				messageReducedPrefix = "minimumDonationPaypalReduced";
			} else if (donateButtonClicked == "stripe") {
				messagePrefix = "minimumDonationStripe";
				messageReducedPrefix = "minimumDonationStripeReduced";
			} else if (donateButtonClicked == "coinbase") {
				messagePrefix = "minimumDonationCoinbase";
				messageReducedPrefix = "minimumDonationCoinbaseReduced";
			}
			
			if ($("#multipleCurrency").val() == "BTC") {
				currencyCode = "BTC";
				currencySymbol = "BTC";
				
				$("#amountSelections").fadeOut();

				if (isEligibleForReducedDonation()) {
					minimumDonation = parseFloat(getMessage("minimumDonationBitcoinReduced"));
				} else {
					minimumDonation = parseFloat(getMessage("minimumDonationBitcoin"));
				}
			} else if (donateButtonClicked == "stripe") {
				currencyCode = "USD";
				currencySymbol = "$";

				if (isEligibleForReducedDonation()) {
					minimumDonation = parseFloat(getMessage("minimumDonationStripeReduced"));
				} else {
					minimumDonation = parseFloat(getMessage("minimumDonationStripe"));
				}
			} else {
				currencyCode = getMessageFromCurrencySelection("currencyCode");
				currencySymbol = getMessageFromCurrencySelection("currencySymbol");
				
				if (isEligibleForReducedDonation()) {
					minimumDonation = parseFloat(getMessageFromCurrencySelection(messageReducedPrefix));
				} else {			
					minimumDonation = parseFloat(getMessageFromCurrencySelection(messagePrefix));
				}
			}
		}

		// General
		$("#currencyCode").text(currencyCode);
		$("#currencySymbol").text(currencySymbol);				
		if (multipleCurrencyFlag) {
			$("#singleCurrencyWrapper").hide();
			$("#multipleCurrencyWrapper").removeAttr("hidden");
		}
		
		if (currencyCode == "USD" || currencyCode == "EUR" || currencyCode == "GBP") {
			$("#amountSelections").show();
		} else {
			$("#amountSelections").hide();
			$("#amount")
				.removeAttr("placeholder")
				.focus()
			;
		}
	}
	
	initCodesAndMinimums(donateButtonClicked);
}

function initPaymentDetails(buttonClicked) {
	donateButtonClicked = buttonClicked;

	$("#multipleUserLicenseIntro").slideUp();

	if (buttonClicked == "paypal") {
		$("#recurringPayment").show();
	} else {
		$("#recurringPayment").hide();
	}
	
	if (licenseType == "singleUser") {
		$('#donateAmountWrapper').attr("hidden", "");
			
			// If atleast 2 then we have multiple currencies
			multipleCurrencyFlag = getMessage("currencyCode2");
			
			$("#multipleCurrency").empty();
			var multipleCurrencyNode = $("#multipleCurrency")[0];
			for (var a=1; a<10; a++) {
				var suffix = a==1 ? "" : a + "";
				var currencyCode2 = getMessage("currencyCode" + suffix);
				if (currencyCode2) {
					var currencySymbol2 = getMessage("currencySymbol" + suffix);
					multipleCurrency.add(new Option(currencyCode2 + " " + currencySymbol2, currencyCode2), null);
				}
			}
			
			if (donateButtonClicked == "coinbase") {
				multipleCurrencyFlag = true;
				multipleCurrency.add(new Option("BTC", "BTC"), null);
			}
			
		$('#donateAmountWrapper').unhide();
			initCurrency();
	} else {
		initCurrency();
		var price = licenseSelected.price;
		initPaymentProcessor(price);
	}
}

function getAmountNumberOnly() {
	var amount = $("#amount").val();
	amount = amount.replace(",", ".");
	amount = amount.replace("$", "");

	if (amount.indexOf(".") == 0) {
		amount = "0" + amount;
	}
	
	amount = $.trim(amount);
	return amount;
}

function showSuccessfulPayment() {
	Controller.processFeatures();
	$("#extraFeatures").hide();
	$("#video").attr("src", "http://www.youtube.com/embed/Ue-li7gl3LM?rel=0&autoplay=1&showinfo=0&theme=light");
	$("#paymentArea").hide();
	$("#paymentComplete").removeAttr("hidden");
}

function showPaymentMethods(licenseType) {
	window.licenseType = licenseType;
	$("#paymentMethods").slideUp("fast", function() {
		$("#paymentMethods").slideDown();
	});
}

function initPaymentProcessor(price) {
	if (donateButtonClicked == "paypal") {
		sendGA("paypal", 'start');
		
		showLoading();

		var donationPageUrl = location.protocol + "//" + location.hostname + location.pathname;

		// seems description is not used - if item name is entered, but i put it anyways
		var data = {itemId:ITEM_ID, itemName:extensionName, currency:currencyCode, price:price, description:extensionName, successUrl:donationPageUrl + "?action=paypalSuccess", errorUrl:donationPageUrl + "?action=paypalError", cancelUrl:Controller.FULLPATH_TO_PAYMENT_FOLDERS + "paymentSystems/paypal/redirectToExtension.php?url=" + encodeURIComponent(donationPageUrl)};
		if (licenseType == "multipleUsers") {
			data.license = licenseSelected.number;
		}
		if ($("#recurringPayment")[0].checked) {
			data.action = "recurring";
		}
		
		ajax({
			type: "post",
			url: Controller.FULLPATH_TO_PAYMENT_FOLDERS + "paymentSystems/paypal/createPayment.php",
			data: data,
			timeout: seconds(10)
			//xhrFields: {
			      //withCredentials: true // patch: because session & cookies were not being saved on apps.jasonsavard.com
			//},
		}).then((data, textStatus, jqXHR) => {
			location.href = data;
		}).catch(error => {
					hideLoading();
			console.error("error", error);
					openGenericDialog({
				title: getMessage("theresAProblem") + " - " + error.statusText,
						content: getMessage("tryAgainLater") + " " + getMessage("or") + " " + "try Stripe instead."
					});
					sendGA("paypal", 'failure on createPayment');
		});
	} else if (donateButtonClicked == "stripe") {
		sendGA("stripe", 'start');
		
		var licenseParamValue = "";
		if (licenseType == "multipleUsers") {
			licenseParamValue = licenseSelected.number;
		}

		var stripeAmount = price * 100;
		var stripeCurrency = currencyCode;
		
		var stripeHandler = StripeCheckout.configure({
			key: "pk_live_GYOxYcszcmgEMwflDGxnRL6e",
		    image: "https://jasonsavard.com/images/jason.png",
		    locale: 'auto',
		    token: function(response) {
		        console.log("token result:", response);
				ajax({
					type: "POST",
					url: "https://apps.jasonsavard.com/paymentSystems/stripe/charge.php",
					data: {stripeToken:response.id, amount:stripeAmount, currency:stripeCurrency, itemId:ITEM_ID, description:extensionName, license:licenseParamValue}
				}).then((data, textStatus, jqXHR) => {
					showSuccessfulPayment();
					sendGA("stripe", "success", "daysElapsedSinceFirstInstalled", daysElapsedSinceFirstInstalled());
				}).catch(jqXHR => {
					openGenericDialog({
						title: getMessage("theresAProblem") + " - " + jqXHR.responseText,
						content: getMessage("tryAgainLater") + " " + getMessage("or") + " " + "try PayPal instead."
					});
					sendGA("stripe", 'error with token: ' + jqXHR.responseText);
				});
	      	}
		});
		
		showLoading();
		
		stripeHandler.open({
        	address:     false,
        	amount:      stripeAmount,
        	name:        extensionName,
        	currency:    stripeCurrency,
        	allowRememberMe: false,
        	bitcoin:	 true,
        	alipay:		 true,
        	opened: function() {
        		hideLoading();
        	}
      	});

	} else if (donateButtonClicked == "coinbase") {
		sendGA("coinbase", 'start');
		
		var licenseParamValue = "";
		if (licenseType == "multipleUsers") {
			licenseParamValue = licenseSelected.number;
		}

		var borderRadius = "border-radius:10px;";
		var $coinbaseWrapper = $("<div id='coinbaseWrapper' style='" + borderRadius + "transition:top 800ms ease-in-out;left: 38%;top: 182px;text-align:center;position:fixed;background:white;width: 460px; height: 350px;box-shadow: 0 1px 3px rgba(0,0,0,0.25);'><paper-spinner id='loadingCoinbase' style='margin-top:35%' active></paper-spinner><iron-icon id='closeCoinbase' icon='close' style='border-radius: 50%;background-color: white;cursor:pointer;top: -16px;right: -16px;position: absolute;'/></div>");
		$coinbaseWrapper.find("#closeCoinbase").click(function() {
			$coinbaseWrapper.remove();
		});
		$("body").append($coinbaseWrapper);
		
		Controller.ajax({data:"action=createCoinbaseButton&name=" + extensionName + "&price=" + price + "&currency=" + currencyCode}).then(response => {
			var code = response.button.code;
			var customParam = {itemID:ITEM_ID, license:licenseParamValue};
			var url = "https://coinbase.com/checkouts/" + code + "/inline?c=" + encodeURIComponent( JSON.stringify(customParam) );

			var $coinbaseIframe = $("<iframe src='" + url + "' style='" + borderRadius + "width: 460px; height: 348px; border: none;' allowtransparency='true' frameborder='0'></iframe>");
			$coinbaseWrapper.find("#loadingCoinbase").hide();
			$coinbaseWrapper.append($coinbaseIframe);
		}).catch(error => {
			console.error(error);
			if (error.responseJSON) {
				error = error.responseJSON.error;
			}
				openGenericDialog({
					title: getMessage("theresAProblem"),
					content: getMessage("tryAgainLater") + " " + getMessage("or") + " " + "try PayPal instead."
				});
				sendGA("coinbase", 'error with createCoinbaseButton');
			if (error.statusText != "timeout") {
				Controller.email({subject:"Payment error - coinbase", message:"error:<br>" + error.statusText});
			}			
		});
	} else {
		openGenericDialog({
			title: getMessage("theresAProblem"),
			content: 'invalid payment process'
		});
	}
}

function ensureEmail(closeWindow) {
	if (!email) {
		openGenericDialog({
			title: "Email not found",
			content: "You must first grant access via the popup window so that I can associate your account with the contribution."
		}).then(function() {
			if (closeWindow) {
				window.close();
			}
		});
	}
}
		
$(document).ready(function() {
	
	$("title, .titleLink").text(extensionName);
	
	$("#multipleUserLicenseWrapper").slideUp();

	storageManager = new StorageManager();
	getStorage(storageManager).then(function(storage) {
		window.storage = storage;

		var action = getUrlValue(location.href, "action");
		
		if (action == "paypalSuccess") {
			showSuccessfulPayment();
			sendGA("paypal", 'success', "daysElapsedSinceFirstInstalled", daysElapsedSinceFirstInstalled());
		} else if (action == "paypalError") {
			var error = getUrlValue(location.href, "error");
		if (!error) {
				error = "";
			}
			
			openGenericDialog({
				title: getMessage("theresAProblem") + " " + error,
				content: getMessage("failureWithPayPalPurchase", "Stripe")
			});
			
			sendGA("paypal", 'failure ' + error);
		} else if (action) {
			// nothing
		} else {
			// nothing
		}
	});
	
	// If multiple currencies load them
	$("#multipleCurrency").change(function() {
		$("#amount").val("");
		initCurrency();
	});
	
	$("#paypal").click(function() {
		initPaymentDetails("paypal");
	});
	
	$("#stripe").click(function() {
		initPaymentDetails("stripe");
	});

	$("#coinbase").click(function() {
		initPaymentDetails("coinbase");
	});

	$("#amountSelections paper-button").click(function() {
		var amount = $(this).attr("amount");
		sendGA("donationAmount", 'submitted', amount);
		initPaymentProcessor(amount);
	});

	$("#submitDonationAmount").click(function() {				
		sendGA("donationAmount", 'submitted', $("#amount").val());
		
		var amount = getAmountNumberOnly();
		
		if (amount == "") {
			showError(getMessage("enterAnAmount"));
			$("#amount").focus();
		} else if (parseFloat(amount) < minimumDonation) {
			var minAmountFormatted = minimumDonation; //minimumDonation.toFixed(2).replace("\.00", "");
			showError(getMessage("minimumAmount", currencySymbol + " " + minAmountFormatted));
			$("#amount").val(minAmountFormatted).focus();
		} else {
			initPaymentProcessor(amount);
		}
	});

	$('#amount')
		.click(function(event) {
			$(this).removeAttr("placeholder");
		})
		.keypress(function(event) {
			if (event.keyCode == '13') {
				$("#submitDonationAmount").click();
			} else {
				$("#submitDonationAmount").addClass("visible");
			}
		})
	;
	
	$("#alreadyDonated").click(function() {
		openDialog("alreadyDonatedDialogTemplate").then(function(response) {
			if (response == "ok") {
				if (!$("#alreadyDonatedEmail")[0].inputElement.value) {
					openGenericDialog({
						title: "Must enter email"
					});
				} else if ($("#confirmationNumber")[0].inputElement.value.length >= 8) {
					Controller.processFeatures();
					openGenericDialog({
						title: "Thank you",
						content: "The information has been sent to the developer! If it passes validation, the features will be automatically unlocked later today!"
					});
				} else {
					$("#alreadyDonated").attr("disabled", "");
					openGenericDialog({
						title: "Invalid information",
						content: "Please retry in 30 seconds"
					});
					setTimeout(function() {
						$("#alreadyDonated").removeAttr("disabled");
					}, 30000);
				}
				sendGA("verifyAlreadyDonated", $("#confirmationNumber").val(), $("#alreadyDonatedEmail").val());
			} else if (response == "other") {
				window.open("https://jasonsavard.com/wiki/Confirmation_number");
			}
		});
	});
	
	$("#help").click(function() {
		location.href = "https://jasonsavard.com/wiki/Extra_features_and_donations";
	});
	
	$("#multipleUserLicenseLink, #multipleUserLicenseButton").click(function(e) {
		$("#multipleUserLicenseIntro").slideUp();
		$('#donateAmountWrapper').attr("hidden", "");
		if (email) {
			$("#licenseDomain").text("@" + email.split("@")[1]);
			// invalid domains					
			if (/@yahoo\.|@gmail\.|@mail\.|@comcast\.|@googlemail\.|@hotmail\./.test(email)) {
				//$("#licenseDomain, #changeThisDomain").css("color", "red");
				//$("#invalidDomain").show();
				$("#licenseOnlyValidFor").hide();
				$("#signInAsUserOfOrg").show();
				$("#licenseOptions").hide();
				
				$("#exampleEmail").empty().append( $("<span>").text(email.split("@")[0]), $("<b>").text("@mycompany.com") );
			} else {
				//var licenses = [{number:"5", price:"0.02"}, {number:"10", price:"20"}, {number:"20", price:"40"}, {number:"unlimited", price:"0.03"}];
				var licenses = [{number:"5", price:"50"}, {number:"10", price:"90"}, {number:"25", price:"200"}, {number:"100", price:"500"}, {number:"unlimited", price:"1000"}];
				$("#licenseOptions").empty();
				$.each(licenses, function(index, license) {
					var li = $("<li><input id='licenseOption" + index + "' type='radio' name='licenseOption'/>&nbsp;<label for='licenseOption" + index + "'>" + license.number + " users for USD $" + license.price + "</label></li>");
					li.find("input").data("data", license);
					$("#licenseOptions").append(li);
				});
			}
			$("#multipleUserLicenseWrapper").slideDown();
		} else {
			ensureEmail();
		}
	});
	
	$("#multipleUserLicenseWrapper").on("click", "#licenseOptions input", function() {
		$("#multipleUserLicenseWrapper").slideUp();
		showPaymentMethods("multipleUsers");
	});
	
	$("#options").click(function() {
		location.href = "options.html";
	});
	
	$(".signOutAndSignIn").click(function() {
		location.href = "https://accounts.google.com/Logout?continue=https%3A%2F%2Faccounts.google.com%2FServiceLoginAuth"; //%3Fcontinue%3D" + encodeURIComponent(location.href);
		// &il=true&zx=1ecpu2nnl1qyn
	});
	
	// load these things at the end
	
	// prevent jumping due to anchor # and because we can't javascript:; or else content security errors appear
	$("a[href='#']").on("click", function(e) {
		e.preventDefault()
	});

	$(window).on('message', function(messageEvent) {
		console.log("message", messageEvent);
		var origin = messageEvent.originalEvent.origin;
		var data = messageEvent.originalEvent.data;
		
		if (origin && /https:\/\/(www\.)?coinbase.com/.test(origin)) {
			console.log(origin, data);
			try {
			    var eventType = data.split('|')[0];     // "coinbase_payment_complete"
			    var eventId   = data.split('|')[1];     // ID for this payment type

			    if (eventType == 'coinbase_payment_complete') {
			    	setTimeout(function() {
			    		$("#coinbaseWrapper").css("top", "580px");
			    		showSuccessfulPayment();			    		
			    	}, 500);
			    	sendGA("coinbase", "success", "daysElapsedSinceFirstInstalled", daysElapsedSinceFirstInstalled());
			    } else if (eventType == 'coinbase_payment_mispaid') {
					openGenericDialog({
						title: "Mispaid amount!",
						content: getMessage("tryAgain")
					});
			    } else if (eventType == 'coinbase_payment_expired') {
					openGenericDialog({
						title: "Time expired!",
						content: getMessage("tryAgain")
					});
			    } else {
			        // Do something else, or ignore
			    	console.log("coinbase message: " + eventType);
			    }
			} catch (e) {
				Controller.email({subject:"Coinbase error", message:"error:<br>" + JSON.stringify(e) + "<br><br>message event:<br>" + JSON.stringify(messageEvent)});
			}
	    }
	});

});