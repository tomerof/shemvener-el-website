"use strict";

(function ($) {
    var pendingChanges = {};
    var debounceTimer = null;
    var DEBOUNCE_MS = 1500;
    var categoryOpenState = {};

    function init() {
        initAccordion();
        initToggles();
        initSearch();
        initBulkActions();
    }

    // === Accordion ===
    function initAccordion() {
        $(document).on("click", ".dce-feature-group-header", function (e) {
            if ($(e.target).closest(".dce-group-bulk").length) {
                return;
            }
            var $group = $(this).closest(".dce-feature-group");
            $group.toggleClass("is-open");
        });
    }

    // === Toggle Save ===
    function initToggles() {
        $(document).on("change", ".dce-feature-row .dce-toggle input", function () {
            var $row = $(this).closest(".dce-feature-row");
            var feature = $row.data("feature");
            var isChecked = $(this).is(":checked");
            var usage = parseInt($row.data("usage"), 10) || 0;

            if (!isChecked && usage > 0) {
                showConfirmation($row, feature, usage);
                return;
            }

            $row.toggleClass("is-inactive", !isChecked);
            queueChange(feature, isChecked ? "active" : "inactive");
        });
    }

    function showConfirmation($row, feature, usage) {
        var $checkbox = $row.find(".dce-toggle input");
        $checkbox.prop("checked", true);
        $row.find(".dce-toggle").addClass("is-pending");

        var $banner = $row.next(".dce-confirmation-banner").filter('[data-feature="' + feature + '"]');
        var msg = dceFeatures.i18n.confirmDeactivate.replace("%d", usage);
        $banner.find(".dce-confirm-message").text(msg);
        $banner.addClass("is-visible");

        $banner.find(".dce-confirm-yes").off("click").on("click", function () {
            $banner.removeClass("is-visible");
            $row.find(".dce-toggle").removeClass("is-pending");
            $checkbox.prop("checked", false);
            $row.addClass("is-inactive");
            queueChange(feature, "inactive");
        });

        $banner.find(".dce-confirm-cancel").off("click").on("click", function () {
            $banner.removeClass("is-visible");
            $row.find(".dce-toggle").removeClass("is-pending");
        });
    }

    function queueChange(feature, status) {
        pendingChanges[feature] = status;
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }
        debounceTimer = setTimeout(flushChanges, DEBOUNCE_MS);
    }

    function flushChanges() {
        var changes = $.extend({}, pendingChanges);
        pendingChanges = {};
        debounceTimer = null;

        if ($.isEmptyObject(changes)) {
            return;
        }

        var $rows = [];
        $.each(changes, function (feature) {
            var $row = $('.dce-feature-row[data-feature="' + feature + '"]');
            $row.addClass("is-saving");
            $rows.push($row);
        });

        $.post(ajaxurl, {
            action: "dce_save_feature_status",
            nonce: dceFeatures.nonce,
            features: changes,
        })
            .done(function (response) {
                $.each($rows, function (_, $row) {
                    $row.removeClass("is-saving");
                });
                updateCounters();
                showToast(dceFeatures.i18n.saved);
            })
            .fail(function () {
                $.each($rows, function (_, $row) {
                    $row.removeClass("is-saving");
                    var $checkbox = $row.find(".dce-toggle input");
                    var wasActive = changes[$row.data("feature")] === "active";
                    $checkbox.prop("checked", !wasActive);
                    $row.toggleClass("is-inactive", wasActive);
                });
                showToast(dceFeatures.i18n.saveError, "error");
            });
    }

    function showToast(message, type) {
        type = type || "success";
        var $existing = $(".dce-toast");
        if ($existing.length) {
            $existing.remove();
        }
        var $toast = $('<div class="dce-toast dce-toast-' + type + '">' + message + '</div>');
        $("body").append($toast);
        // Trigger reflow for animation
        $toast[0].offsetHeight;
        $toast.addClass("is-visible");
        setTimeout(function () {
            $toast.removeClass("is-visible");
            setTimeout(function () {
                $toast.remove();
            }, 300);
        }, 2000);
    }

    function updateCounters() {
        $(".dce-feature-group").each(function () {
            var $group = $(this);
            var total = $group.find(".dce-feature-row").length;
            var active = $group.find(".dce-feature-row .dce-toggle input:checked").length;
            var inactive = total - active;
            var summaryHtml = "";
            if (active > 0) {
                summaryHtml += '<span class="active-count">&#9679; ' + active + " active</span>";
            }
            if (inactive > 0) {
                summaryHtml += '<span class="inactive-count">&#9679; ' + inactive + " inactive</span>";
            }
            $group.find(".dce-group-summary").html(summaryHtml);
        });

        var totalAll = $(".dce-feature-row").length;
        var activeAll = $(".dce-feature-row .dce-toggle input:checked").length;
        $(".dce-features-subtitle").text(activeAll + " of " + totalAll + " active");
    }

    // === Search ===
    function initSearch() {
        var searchTimer = null;
        $(document).on("input", "#dce-feature-search", function () {
            var query = $(this).val().toLowerCase().trim();
            if (searchTimer) {
                clearTimeout(searchTimer);
            }
            searchTimer = setTimeout(function () {
                filterFeatures(query);
            }, 200);
        });
    }

    function filterFeatures(query) {
        if (!query) {
            $(".dce-feature-row").removeClass("is-search-hidden");
            $(".dce-feature-group").removeClass("is-search-hidden");
            // Restore previous open state
            $(".dce-feature-group").each(function () {
                var id = $(this).find(".dce-group-name").text();
                if (categoryOpenState[id]) {
                    $(this).addClass("is-open");
                } else {
                    $(this).removeClass("is-open");
                }
            });
            return;
        }

        // Save current open state
        $(".dce-feature-group").each(function () {
            var id = $(this).find(".dce-group-name").text();
            categoryOpenState[id] = $(this).hasClass("is-open");
        });

        $(".dce-feature-row").each(function () {
            var title = $(this).data("title") || "";
            var desc = $(this).data("description") || "";
            var matches = title.indexOf(query) !== -1 || desc.indexOf(query) !== -1;
            $(this).toggleClass("is-search-hidden", !matches);
            $(this).next(".dce-confirmation-banner").toggleClass("is-search-hidden", !matches);
        });

        $(".dce-feature-group").each(function () {
            var visibleRows = $(this).find(".dce-feature-row:not(.is-search-hidden)").length;
            $(this).toggleClass("is-search-hidden", visibleRows === 0);
            if (visibleRows > 0) {
                $(this).addClass("is-open");
            }
        });
    }

    // === Bulk Actions ===
    function initBulkActions() {
        // Global activate all
        $(document).on("click", "#dce-feature-activate-all", function (e) {
            e.preventDefault();
            $(".dce-feature-row .dce-toggle input:not(:disabled):not(:checked)").each(function () {
                $(this).prop("checked", true);
                var $row = $(this).closest(".dce-feature-row");
                $row.removeClass("is-inactive");
                queueChange($row.data("feature"), "active");
            });
        });

        // Global deactivate all
        $(document).on("click", "#dce-feature-deactivate-all", function (e) {
            e.preventDefault();
            var usedFeatures = [];
            $(".dce-feature-row .dce-toggle input:not(:disabled):checked").each(function () {
                var $row = $(this).closest(".dce-feature-row");
                var usage = parseInt($row.data("usage"), 10) || 0;
                if (usage > 0) {
                    usedFeatures.push({ row: $row, usage: usage });
                }
            });

            if (usedFeatures.length > 0) {
                var totalUsed = usedFeatures.reduce(function (sum, f) { return sum + f.usage; }, 0);
                var msg = dceFeatures.i18n.confirmBulkDeactivate
                    .replace("%d", usedFeatures.length)
                    .replace("%t", totalUsed);
                if (!confirm(msg)) {
                    return;
                }
            }

            $(".dce-feature-row .dce-toggle input:not(:disabled):checked").each(function () {
                $(this).prop("checked", false);
                var $row = $(this).closest(".dce-feature-row");
                $row.addClass("is-inactive");
                queueChange($row.data("feature"), "inactive");
            });
        });

        // Group activate all
        $(document).on("click", ".dce-group-activate-all", function (e) {
            e.preventDefault();
            $(this).closest(".dce-feature-group").find(".dce-feature-row .dce-toggle input:not(:disabled):not(:checked)").each(function () {
                $(this).prop("checked", true);
                var $row = $(this).closest(".dce-feature-row");
                $row.removeClass("is-inactive");
                queueChange($row.data("feature"), "active");
            });
        });

        // Group deactivate all
        $(document).on("click", ".dce-group-deactivate-all", function (e) {
            e.preventDefault();
            var $group = $(this).closest(".dce-feature-group");
            var usedFeatures = [];
            $group.find(".dce-feature-row .dce-toggle input:not(:disabled):checked").each(function () {
                var $row = $(this).closest(".dce-feature-row");
                var usage = parseInt($row.data("usage"), 10) || 0;
                if (usage > 0) {
                    usedFeatures.push({ row: $row, usage: usage });
                }
            });

            if (usedFeatures.length > 0) {
                var totalUsed = usedFeatures.reduce(function (sum, f) { return sum + f.usage; }, 0);
                var msg = dceFeatures.i18n.confirmBulkDeactivate
                    .replace("%d", usedFeatures.length)
                    .replace("%t", totalUsed);
                if (!confirm(msg)) {
                    return;
                }
            }

            $group.find(".dce-feature-row .dce-toggle input:not(:disabled):checked").each(function () {
                $(this).prop("checked", false);
                var $row = $(this).closest(".dce-feature-row");
                $row.addClass("is-inactive");
                queueChange($row.data("feature"), "inactive");
            });
        });
    }

    $(document).ready(init);
})(jQuery);
