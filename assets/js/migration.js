/**
 * Migrate from Erident Custom Login & Dashboard to Ultimate Dashboard.
 *
 * Nothing is removed before Ultimate Dashboard is installed:
 * 1. Check & install Ultimate Dashboard.
 * 2. Move the settings over & remove Erident (server).
 * 3. Activate Ultimate Dashboard.
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
		elms.install = wrapper.querySelector(".ultimate-dashboard-installed");
		elms.migrate = wrapper.querySelector(".cldashboard-uninstalled");
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
		step.classList.remove("is-done");
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
	 * @param {boolean} canRetry Whether the button can be used again (nothing was removed yet).
	 */
	function fail(step, message, canRetry) {
		elms.errorMessage.textContent = message || i18n.genericError;
		step.classList.remove("is-waiting");
		elms.failed.classList.add("is-done");
		button.classList.remove("is-loading");
		button.disabled = !canRetry;
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

		if (response && response.data && typeof response.data === "string") {
			return response.data;
		}

		if (response && response.errorMessage) {
			return String(response.errorMessage);
		}

		return "";
	}

	/**
	 * Send a migration step to the server.
	 *
	 * @param {string} step The step: start, finish or abort.
	 */
	function request(step) {
		return $.ajax({
			url: window.ajaxurl,
			type: "post",
			dataType: "json",
			data: {
				action: "cldashboard_migration",
				nonce: data.nonces.migration,
				step: step,
			},
		});
	}

	/**
	 * Step 1: check if the migration can run, then install Ultimate Dashboard.
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

		getElements($(button).closest(".cldashboard-migration")[0]);
		elms.failed.classList.remove("is-done");
		stepWaiting(elms.install, i18n.checking);

		request("start")
			.done(function (r) {
				if (!r.success) {
					fail(elms.install, getErrorMessage(r), true);
					return;
				}

				installUltimateDashboard();
			})
			.fail(function (jqXHR) {
				fail(elms.install, getErrorMessage(jqXHR), true);
			});
	}

	/**
	 * Install Ultimate Dashboard. If that fails, nothing has been removed.
	 */
	function installUltimateDashboard() {
		stepWaiting(elms.install, i18n.installing);

		wp.updates.installPlugin({
			slug: data.newPlugin.slug,
			success: function () {
				stepDone(elms.install, i18n.installed);
				finishMigration();
			},
			error: function (response) {
				// Release the lock, so the migration can be started again.
				request("abort");
				fail(elms.install, getErrorMessage(response), true);
			},
		});
	}

	/**
	 * Step 2: move the settings to Ultimate Dashboard & remove Erident.
	 */
	function finishMigration() {
		stepWaiting(elms.migrate, i18n.uninstalling);

		request("finish")
			.done(function (r) {
				if (!r.success) {
					fail(elms.migrate, getErrorMessage(r), false);
					return;
				}

				stepDone(elms.migrate, r.data.message);
				activateUltimateDashboard();
			})
			.fail(function (jqXHR) {
				fail(elms.migrate, getErrorMessage(jqXHR), false);
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
			.fail(function () {
				fail(elms.activate, i18n.activateFailed, false);
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
