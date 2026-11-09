/**
 * Settings page: tabs, color & media fields, dependent rows and the ajax save, reset & load defaults.
 */
(function ($) {
	var data = window.CustomLoginDashboard;
	var form = document.querySelector(".cldashboard-settings-form");
	var footer = document.querySelector(".cldashboard-form-footer");
	var fields = document.querySelectorAll(
		".cldashboard-settings-form .general-setting-field"
	);
	var submitButton = document.querySelector(".cldashboard-submit-button");
	var resetButton = document.querySelector(".cldashboard-reset-button");
	var loadDefaultsButton = document.querySelector(
		".cldashboard-load-defaults-button"
	);
	var submitNotice = document.querySelector(".cldashboard-submit-notice");
	var resetNotice = document.querySelector(".cldashboard-reset-notice");
	var isProcessing = false;

	function init() {
		$(".color-picker-field").wpColorPicker({ palettes: true, hide: true });

		setupTabs();
		document.querySelectorAll(".cldashboard-media-field").forEach(setupMediaField);
		setupDependentRows();

		if (form) form.addEventListener("submit", onSubmit);
		if (resetButton) resetButton.addEventListener("click", onReset);
		if (loadDefaultsButton) {
			loadDefaultsButton.addEventListener("click", onLoadDefaults);
		}
	}

	/**
	 * Tabs: each tab & its panel share a data-panel name, the URL hash picks one.
	 */
	function setupTabs() {
		var tabs = document.querySelectorAll(".heatbox-tab-nav-item[data-panel]");
		var panels = document.querySelectorAll(".heatbox-admin-panel[data-panel]");

		if (!tabs.length) return;

		function showPanel(name) {
			var found = false;

			panels.forEach(function (panel) {
				var match = panel.dataset.panel === name;
				panel.style.display = match ? "block" : "none";
				if (match) found = true;
			});

			if (!found) return showPanel(tabs[0].dataset.panel);

			tabs.forEach(function (tab) {
				var active = tab.dataset.panel === name;
				var link = tab.querySelector("a");

				tab.classList.toggle("active", active);

				if (active) {
					link.setAttribute("aria-current", "page");
				} else {
					link.removeAttribute("aria-current");
				}
			});

			// The tools tab has its own forms.
			if (footer) footer.hidden = "tools" === name;
		}

		window.addEventListener("hashchange", function () {
			showPanel(window.location.hash.substring(1));
		});

		showPanel(window.location.hash.substring(1));
	}

	/**
	 * Media field: choose an image from the media library, or clear it.
	 *
	 * @param {HTMLInputElement} field The URL field.
	 */
	function setupMediaField(field) {
		var frame = wp
			.media({
				title: data.i18n.chooseImage,
				button: { text: data.i18n.useImage },
				multiple: false,
			})
			.on("select", function () {
				field.value = frame.state().get("selection").first().toJSON().url;
				field.dispatchEvent(new Event("change"));
			});

		var uploadButton = field.parentNode.querySelector(
			".cldashboard-upload-button"
		);
		var clearButton = field.parentNode.querySelector(
			".cldashboard-clear-button"
		);

		if (uploadButton) {
			uploadButton.addEventListener("click", function () {
				frame.open();
			});
		}

		if (clearButton) {
			clearButton.addEventListener("click", function () {
				field.value = "";
				field.dispatchEvent(new Event("change"));
			});
		}
	}

	/**
	 * Rows that depend on another field:
	 * data-show-if-field-checked="id" (shown while the checkbox is checked),
	 * data-hide-if-field="id" + data-hide-if-value="value" (hidden while the field has that value).
	 */
	function setupDependentRows() {
		document
			.querySelectorAll("[data-show-if-field-checked], [data-hide-if-field]")
			.forEach(function (row) {
				var parentId =
					row.dataset.showIfFieldChecked || row.dataset.hideIfField;
				var parent = document.getElementById(parentId);

				if (!parent) return;

				function update() {
					if (row.hasAttribute("data-show-if-field-checked")) {
						row.hidden = !parent.checked;
					} else {
						row.hidden = parent.value === row.dataset.hideIfValue;
					}
				}

				update();
				parent.addEventListener("change", update);
			});
	}

	/**
	 * Show a message next to the save or reset buttons for a few seconds.
	 *
	 * @param {HTMLElement} notice The notice element.
	 * @param {string} message The message.
	 * @param {boolean} success Whether it's a success message.
	 */
	function showNotice(notice, message, success) {
		notice.textContent = message;
		notice.classList.toggle("is-success", success);
		notice.classList.toggle("is-error", !success);
		notice.classList.add("is-shown");

		setTimeout(function () {
			notice.classList.remove("is-shown");
		}, 3000);
	}

	/**
	 * Send an ajax request & show its result.
	 *
	 * @param {Object} request The request data.
	 * @param {HTMLElement} button The button that started it.
	 * @param {HTMLElement} notice Where to show the result.
	 * @param {Function} onSuccess Called with the response data.
	 */
	function send(request, button, notice, onSuccess) {
		if (isProcessing) return;
		isProcessing = true;
		button.classList.add("is-loading");

		$.ajax({ url: window.ajaxurl, type: "POST", data: request })
			.done(function (r) {
				if (!r || !r.success) {
					showNotice(notice, data.i18n.genericError, false);
					return;
				}

				showNotice(notice, onSuccess(r.data), true);
			})
			.fail(function (jqXHR) {
				var message =
					jqXHR.responseJSON && jqXHR.responseJSON.data
						? String(jqXHR.responseJSON.data)
						: data.i18n.genericError;

				showNotice(notice, message, false);
			})
			.always(function () {
				isProcessing = false;
				button.classList.remove("is-loading");
			});
	}

	function onSubmit(e) {
		var request = {
			action: "cldashboard_save_settings",
			nonce: data.nonces.saveSettings,
		};

		e.preventDefault();

		fields.forEach(function (field) {
			if (field.type === "checkbox") {
				if (field.checked) request[field.name] = field.value;
			} else {
				request[field.name] = field.value;
			}
		});

		send(request, submitButton, submitNotice, function (message) {
			return message;
		});
	}

	function onReset(e) {
		e.preventDefault();

		if (!window.confirm(data.dialogs.resetSettingsConfirmation)) return;

		send(
			{ action: "cldashboard_reset_settings", nonce: data.nonces.resetSettings },
			resetButton,
			resetNotice,
			function (message) {
				clearForm();
				return message;
			}
		);
	}

	function onLoadDefaults(e) {
		e.preventDefault();

		if (!window.confirm(data.dialogs.loadDefaultSettingsConfirmation)) return;

		send(
			{
				action: "cldashboard_load_default_settings",
				nonce: data.nonces.loadDefaultSettings,
			},
			loadDefaultsButton,
			resetNotice,
			function (response) {
				populateForm(response.settings);
				return response.message;
			}
		);
	}

	/**
	 * Empty every field (after a reset).
	 */
	function clearForm() {
		fields.forEach(function (field) {
			if (field.tagName.toLowerCase() === "select") {
				field.selectedIndex = 0;
			} else if (field.type === "checkbox") {
				field.checked = false;
			} else {
				field.value = "";
			}

			notifyDependentRows(field);
		});

		// The color picker keeps showing the old color otherwise.
		// @link https://github.com/Automattic/Iris/issues/53
		$(".color-alpha").css("background-color", "");
	}

	/**
	 * Fill the fields with the given settings (after loading the defaults).
	 *
	 * @param {Object} settings The settings.
	 */
	function populateForm(settings) {
		Object.keys(settings).forEach(function (name) {
			var field = document.getElementById(name);
			var value = settings[name];

			if (!field) return;

			if (field.type === "checkbox") {
				field.checked = !!value;
			} else {
				field.value = value;
			}

			if (field.classList.contains("color-picker-field")) {
				$(field).wpColorPicker("color", value);
				$(field)
					.closest(".wp-picker-container")
					.find(".color-alpha")
					.css("background-color", value);
			}

			notifyDependentRows(field);
		});
	}

	/**
	 * Let dependent rows know a checkbox or select changed.
	 *
	 * @param {HTMLElement} field The field.
	 */
	function notifyDependentRows(field) {
		if (field.type === "checkbox" || field.tagName.toLowerCase() === "select") {
			field.dispatchEvent(new Event("change"));
		}
	}

	init();
})(jQuery);
