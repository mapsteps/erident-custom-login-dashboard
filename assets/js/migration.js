/**
 * Migrate from Erident Custom Login & Dashboard to Ultimate Dashboard.
 */
(function ($) {
	var data = window.CldashboardMigration;
	var i18n = data.i18n;
	var isRequesting = false;
	var button;
	var elms = {};

	/**
	 * Call the main functions here.
	 */
	function init() {
		$(document).on("click", ".cldashboard-migration-button", startMigration);
		$(document).on(
			"click",
			".cldashboard-migration-notice .notice-dismiss",
			dismissNotice
		);
	}

	/**
	 * Get the status elements of the box or notice that was clicked.
	 *
	 * @param {HTMLElement} wrapper The migration box or notice.
	 */
	function getElements(wrapper) {
		elms.failed = wrapper.querySelector(".migration-failed");
		elms.errorMessage = elms.failed.querySelector(".error-message");
		elms.uninstall = wrapper.querySelector(".cldashboard-uninstalled");
		elms.install = wrapper.querySelector(".ultimate-dashboard-installed");
		elms.activate = wrapper.querySelector(".ultimate-dashboard-activated");
	}

	/**
	 * Show a step as running.
	 *
	 * @param {HTMLElement} step The status element.
	 * @param {string} message The message.
	 */
	function stepWaiting(step, message) {
		step.querySelector(".process-message").textContent = message;
		step.classList.add("is-waiting");
	}

	/**
	 * Show a step as done.
	 *
	 * @param {HTMLElement} step The status element.
	 * @param {string} message The message.
	 */
	function stepDone(step, message) {
		step.querySelector(".process-message").textContent = message;
		step.classList.remove("is-waiting");
		step.classList.add("is-done");
	}

	/**
	 * Show an error & stop.
	 *
	 * @param {HTMLElement} step The status element that failed.
	 * @param {string} message The error message.
	 */
	function fail(step, message) {
		elms.errorMessage.textContent = message || i18n.genericError;
		step.classList.remove("is-waiting");
		elms.failed.classList.add("is-done");
		button.classList.remove("is-loading");
		button.disabled = false;
		isRequesting = false;
	}

	/**
	 * Get the error message of a failed request.
	 *
	 * @param {Object} response The response.
	 */
	function getErrorMessage(response) {
		if (response && response.responseJSON && response.responseJSON.data) {
			return String(response.responseJSON.data);
		}

		if (response && response.errorMessage) {
			return String(response.errorMessage);
		}

		return "";
	}

	/**
	 * Step 1: migrate the settings & remove Erident.
	 *
	 * @param {Event} e The click event.
	 */
	function startMigration(e) {
		e.preventDefault();

		if (isRequesting || !window.confirm(i18n.confirm)) return;

		isRequesting = true;
		button = this;
		button.classList.add("is-loading");
		button.disabled = true;

		getElements(button.closest(".cldashboard-migration"));
		elms.failed.classList.remove("is-done");
		stepWaiting(elms.uninstall, i18n.uninstalling);

		$.ajax({
			url: window.ajaxurl,
			type: "post",
			dataType: "json",
			data: {
				action: "cldashboard_migration",
				nonce: data.nonces.migration,
			},
		})
			.done(function (r) {
				if (!r.success) {
					fail(elms.uninstall, r.data);
					return;
				}

				stepDone(elms.uninstall, i18n.uninstalled);
				installUltimateDashboard();
			})
			.fail(function (jqXHR) {
				fail(elms.uninstall, getErrorMessage(jqXHR));
			});
	}

	/**
	 * Step 2: install Ultimate Dashboard.
	 */
	function installUltimateDashboard() {
		stepWaiting(elms.install, i18n.installing);

		wp.updates.installPlugin({
			slug: data.newPlugin.slug,
			success: function () {
				stepDone(elms.install, i18n.installed);
				activateUltimateDashboard();
			},
			error: function (response) {
				fail(elms.install, getErrorMessage(response));
			},
		});
	}

	/**
	 * Step 3: activate Ultimate Dashboard & go to its welcome screen.
	 */
	function activateUltimateDashboard() {
		stepWaiting(elms.activate, i18n.activating);

		$.ajax({
			type: "GET",
			url: data.newPlugin.activationUrl,
		})
			.done(function () {
				stepDone(elms.activate, i18n.activated);
				window.location.replace(data.redirectUrl);
			})
			.fail(function (jqXHR) {
				fail(elms.activate, getErrorMessage(jqXHR));
			});
	}

	/**
	 * Hide the notice for the current user.
	 */
	function dismissNotice() {
		$.post(window.ajaxurl, {
			action: "cldashboard_dismiss_migration_notice",
			nonce: data.nonces.dismiss,
		});
	}

	init();
})(jQuery);
