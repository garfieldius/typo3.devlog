/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */


var here = top || window,
	TYPO3 = here.TYPO3 || {};

TYPO3.Devlog = {};

(function(exports, $) {
	"use strict";

	var latestData;

	function getIcon(name) {
		return $('.icon-' + name).find("span").clone().css("display", "inline-block");
	}

	function createToggler(element, label, startNotActive) {
		var toggler = $("<a />").attr("title", "Show / Hide subelements").html(" " + (label || "") + " "),
			expand = getIcon('expand').appendTo(toggler),
			collapse = getIcon('collapse').appendTo(toggler),
			active = false;

		toggler.click(function() {
			if (active) {
				element.hide();
				expand.show();
				collapse.hide();
			} else {
				element.show();
				expand.hide();
				collapse.show();
			}
			active = !active;
		});

		if (startNotActive) {
			expand.hide();
			active = true;
		} else {
			collapse.hide();
		}

		return toggler;
	}

	function buildDebugDataTable(data, container) {

		var entry = $('<table />').appendTo(container);

		$.each(data, function(name, value) {

			var row = $('<tr />').appendTo(entry),
				data = $('<td />'),
				subElementsContainer;

			row.append($('<td />').html(name));
			row.append(data);

			if (value.type == "array" || (value.type == "object" && value.collection)) {

				data.html(
					"<em>" +
					(value.collection ? 'collection: ' + value["class"] : value.type) +
					" (" + value.length + " elements)</em>"
				);

				if (value.length) {
					subElementsContainer = $('<div class="data-table" style="display:none;"></div>');
					createToggler(subElementsContainer).appendTo(data);
					subElementsContainer.appendTo(data);
					buildDebugDataTable(value.elements, subElementsContainer);
				}

			} else if (value.type == "object") {

				data.html(
					"<em> object " +
					value["class"] +
					(value.length ? " (" + value.length + " properties)" : '') +
					'</em>'
				);

				if (value.length) {
					subElementsContainer = $('<div class="data-table" style="display:none;"></div>');
					createToggler(subElementsContainer).appendTo(data);
					subElementsContainer.appendTo(data);
					buildDebugDataTable(value.properties, subElementsContainer);
				}

			} else if (value.type == "string") {
				data.html( "<em>" + value.type + " (" + value.value.length + " chars)</em> '" + value.value + "'");
			} else {
				data.html( "<em>" + value.type + "</em> " + value.value);
			}

		});
	}

	exports.Init = function(data) {
		latestData = data;
	};

	exports.LoadDetails = function(url, clickable) {

		$(clickable).hide().next().show();

		$.getJSON(url, function(data) {

			var container = $("<td colspan='10' />");
			$(clickable).parents("tr").after( $('<tr />').append(container) );
			container = $('<div class="debug-data-container" />').appendTo(container);


			buildDebugDataTable(data, container);
			$(clickable).parent().empty().append(createToggler(container, "Debug Data", true));

		}).error(function() {
			TYPO3.Flashmessage.display(
				TYPO3.Severity.error,
				'Connection Error',
				'The request did not succeed. Please try again.'
			);
			$(clickable).show().next().hide();
		});
	};

	exports.CallCleaner = function(url) {
		$(".call-cleaner").hide();
		$(".working-cleaner").show();
		$.get(url)
			.success(function(response) {
				if (response == 'yes') {
					TYPO3.Flashmessage.display(
						TYPO3.Severity.ok,
						'Success',
						'The table has been cleaned'
					);
				} else {
					TYPO3.Flashmessage.display(
						TYPO3.Severity.notice,
						'Already clean',
						'The table is not bloated yet'
					);
				}
			})
			.error(function() {
				TYPO3.Flashmessage.display(
					TYPO3.Severity.error,
					'Connection Error',
					'The request did not succeed. Please try again.'
				);
			})
			.complete(function() {
				$(".call-cleaner").show();
				$(".working-cleaner").hide();
			});
	}

})(TYPO3.Devlog, jQuery);
