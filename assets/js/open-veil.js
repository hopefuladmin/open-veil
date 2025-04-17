/**
 * Open Veil Scripts
 *
 * @package OpenVeil
 */

; (($) => {
  // Make sure jQuery is available
  if (typeof jQuery === "undefined") {
    console.error("jQuery is required for this script.")
    return
  }

  // Make sure openVeil is available
  if (typeof openVeil === "undefined") {
    console.error("openVeil is required for this script.")
    return
  }

  // Initialize when document is ready
  $(document).ready(() => {
    // Protocol and Trial filters
    initFilters()

    // Protocol comparison
    initComparison()

    // Trial submission form
    initTrialForm()
    
    // View toggle
    initViewToggle()
  })

  /**
   * Initialize filters
   */
  function initFilters() {
    // Auto-submit filter form when select changes
    $(".protocol-filters select, .trial-filters select").on("change", function () {
      if ($(this).val() !== "") {
        $(this).closest("form").submit()
      }
    })
  }

  /**
   * Initialize protocol comparison
   */
  function initComparison() {
    // Highlight differences in comparison table
    $(".comparison-table td:nth-child(4)").each(function () {
      var value = $(this).text().trim()
      if (value.indexOf("+") === 0) {
        $(this).addClass("variance-positive")
      } else if (value.indexOf("-") === 0) {
        $(this).addClass("variance-negative")
      } else if (value === "Different") {
        $(this).addClass("variance-different")
      }
    })
  }

  /**
   * Initialize trial submission form
   */
  function initTrialForm() {
    // If we're on the trial submission form
    if ($("#trial-submission-form").length) {
      // When protocol is selected, pre-fill fields
      $("#protocol_id").on("change", function () {
        var protocolId = $(this).val()

        if (protocolId) {
          // Fetch protocol data via AJAX
          $.ajax({
            url: openVeil.restUrl + "protocol/" + protocolId,
            method: "GET",
            beforeSend: (xhr) => {
              xhr.setRequestHeader("X-WP-Nonce", openVeil.nonce)
            },
            success: (response) => {
              // Pre-fill form fields with protocol values
              if (response.meta) {
                $("#laser_wavelength").val(response.meta.laser_wavelength)
                $("#laser_power").val(response.meta.laser_power)
                $("#substance_dose").val(response.meta.substance_dose)
                $("#projection_distance").val(response.meta.projection_distance)
              }

              // Pre-select taxonomies
              if (response.taxonomies) {
                // Handle each taxonomy
                for (var taxonomy in response.taxonomies) {
                  if (response.taxonomies.hasOwnProperty(taxonomy)) {
                    var terms = response.taxonomies[taxonomy]
                    var termIds = terms.map((term) => term.id)

                    // Select the terms in the form
                    $("#" + taxonomy + " option").each(function () {
                      if (termIds.indexOf(Number.parseInt($(this).val())) !== -1) {
                        $(this).prop("selected", true)
                      }
                    })
                  }
                }
              }
            },
          })
        }
      })
    }
  }

  /**
   * Initialize view toggle functionality
   */
  function initViewToggle() {
    $('.view-toggle-button').on('click', function() {
      const viewType = $(this).data('view');
      const container = $(this).closest('.container').find('.view-container');
      
      // Update active button state
      $(this).siblings('.view-toggle-button').removeClass('active');
      $(this).addClass('active');
      
      // Update view container classes
      if (viewType === 'grid') {
        container.removeClass('list-view-active').addClass('grid-view-active');
      } else {
        container.removeClass('grid-view-active').addClass('list-view-active');
      }
      
      // Store preference in localStorage if available
      if (typeof(Storage) !== "undefined") {
        localStorage.setItem('openVeilViewPreference', viewType);
      }
    });
    
    // Apply saved preference if available
    if (typeof(Storage) !== "undefined" && localStorage.getItem('openVeilViewPreference')) {
      const savedView = localStorage.getItem('openVeilViewPreference');
      $(`.view-toggle-button[data-view="${savedView}"]`).trigger('click');
    }
  }
})(jQuery)
