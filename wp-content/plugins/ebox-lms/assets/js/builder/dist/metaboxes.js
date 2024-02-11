/******/ (() => {
  // webpackBootstrap
  var __webpack_exports__ = {};
  /*!**********************!*\
  !*** ./metaboxes.js ***!
  \**********************/
  if ("block" === window.ebox_builder_metaboxes.editor) {
    // If Gutenberg is used, make sure some metaboxes can't be toggled off
    wp.data.subscribe(() => {
      // "Always On" panels.
      const alwaysOn = [
        "meta-box-ebox-course-access-settings",
        "meta-box-ebox-course-display-content-settings",
        "meta-box-ebox-course-navigation-settings",
        "meta-box-ebox_course_builder",
        "meta-box-ebox_course_teams",
        "meta-box-ebox_quiz_builder",
        "meta-box-ebox-course-modules",
        "meta-box-ebox-course-quizzes",
        "meta-box-ebox-course-topics",
        "meta-box-ebox-quiz",
      ]; // WordPress Data Store information.

      const store = wp.data.select("core/edit-post");
      const panels = store.getPreference("panels"); // Loop over the panels object, but only those panels listed as "Always ON".

      for (const id in panels) {
        if (panels.hasOwnProperty(id) && alwaysOn.includes(id)) {
          const panel = panels[id]; // Only perform the actions with panels with enabled property.

          if (panel.hasOwnProperty("enabled")) {
            if (!panel.enabled) {
              wp.data.dispatch("core/edit-post").toggleEditorPanelEnabled(id);
            }
          }
        }
      }
    });
  } else {
    // Metaboxes IDs
    const alwaysOn = [
      "ebox-course-access-settings",
      "ebox-course-display-content-settings",
      "ebox-course-navigation-settings",
      "ebox_course_builder",
      "ebox_course_teams",
      "ebox_quiz_builder",
      "ebox-course-modules",
      "ebox-course-quizzes",
      "ebox-course-topics",
      "ebox-quiz",
    ]; // We need to follow the core postbox.js to bind the events

    jQuery(".hide-postbox-tog").on("click.postboxes", function (e) {
      const $el = jQuery(this),
        boxId = $el.val(),
        $postbox = jQuery("#" + boxId); // Check if the metabox is in "always on"

      if (-1 < alwaysOn.indexOf(boxId)) {
        if (!$el.prop("checked")) {
          // Prevent unchecking and force visibility
          e.preventDefault();
          $postbox.show();
          $el.prop("checked", true);
        }
      }
    });
  }
  /******/
})();
//# sourceMappingURL=metaboxes.js.map
