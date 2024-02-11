jQuery(function () {
  /**
   * Moves the new LD admin panel into the correct position in the DOM.
   */

  if (jQuery("#ebox-header").length) {
    if (jQuery("#wpbody-content #screen-meta-links").length) {
      jQuery("#ebox-header").insertAfter("#wpbody-content #screen-meta-links");
    } else if (jQuery("#wpbody-content #screen-meta").length) {
      jQuery("#ebox-header").insertAfter("#wpbody-content #screen-meta");
    } else if (jQuery("#wpbody-content").length) {
      jQuery("#ebox-header").prepend("#wpbody-content");
    }
  }

  // Move the onboarding to be below the header
  if (
    jQuery("section.ld-onboarding-screen").length &&
    jQuery("#ebox-header").length
  ) {
    // In the onboarding section is within a metabox we leave it.
    var parent = jQuery("section.ld-onboarding-screen").closest(
      ".meta-box-sortables"
    );
    if (typeof parent === "undefined" || parent.length == 0) {
      jQuery("section.ld-onboarding-screen").insertAfter("#ebox-header");
      jQuery(".wrap").hide();
    }
  }
});
