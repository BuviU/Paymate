/**
 * ebox Block ld-course-expire-status
 *
 * @since 2.5.9
 * @package ebox
 */

/**
 * ebox block functions
 */
import { ldlms_get_post_edit_meta, ldlms_get_custom_label } from "../ldlms.js";

/**
 * Internal block libraries
 */
import { __, _x, sprintf } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls } from "@wordpress/block-editor";
import {
  PanelBody,
  TextControl,
  ToggleControl,
  PanelRow,
} from "@wordpress/components";
import ServerSideRender from "@wordpress/server-side-render";
import { useMemo } from "@wordpress/element";

const block_key = "ebox/ld-course-expire-status";
const block_title = sprintf(
  // translators: placeholder: Course.
  _x("ebox %s Expire Status", "placeholder: Course", "ebox"),
  ldlms_get_custom_label("course")
);

registerBlockType(block_key, {
  title: block_title,
  description: sprintf(
    // translators: placeholder: Course.
    _x(
      "This block displays the user %s access expire date.",
      "placeholders: Course",
      "ebox"
    ),
    ldlms_get_custom_label("course")
  ),
  icon: "clock",
  category: "ebox-blocks",
  example: {
    attributes: {
      example_show: 1,
    },
  },
  supports: {
    customClassName: false,
  },
  attributes: {
    course_id: {
      type: "string",
      default: "",
    },
    user_id: {
      type: "string",
      default: "",
    },
    label_before: {
      type: "string",
      default: "",
    },
    label_after: {
      type: "string",
      default: "",
    },
    preview_show: {
      type: "boolean",
      default: 1,
    },
    preview_user_id: {
      type: "string",
      default: "",
    },
    example_show: {
      type: "boolean",
      default: 0,
    },
    editing_post_meta: {
      type: "object",
    },
  },
  edit: function (props) {
    let {
      attributes: { course_id },
      className,
    } = props;
    const {
      attributes: {
        user_id,
        label_before,
        label_after,
        preview_show,
        preview_user_id,
        example_show,
      },
      setAttributes,
    } = props;

    const inspectorControls = (
      <InspectorControls key="controls">
        <PanelBody title={__("Settings", "ebox")}>
          <TextControl
            label={sprintf(
              // translators: placeholder: Course.
              _x("%s ID", "placeholder: Course", "ebox"),
              ldlms_get_custom_label("course")
            )}
            help={sprintf(
              // translators: placeholders: Course, Course.
              _x(
                "Enter single %1$s ID. Leave blank if used within a %2$s.",
                "placeholders: Course, Course",
                "ebox"
              ),
              ldlms_get_custom_label("course"),
              ldlms_get_custom_label("course")
            )}
            value={course_id || ""}
            type={"number"}
            onChange={function (new_course_id) {
              if (new_course_id != "" && new_course_id < 0) {
                setAttributes({ course_id: "0" });
              } else {
                setAttributes({ course_id: new_course_id });
              }
            }}
          />
          <TextControl
            label={__("User ID", "ebox")}
            help={__(
              "Enter specific User ID. Leave blank for current User.",
              "ebox"
            )}
            value={user_id || ""}
            type={"number"}
            onChange={function (new_user_id) {
              if (new_user_id != "" && new_user_id < 0) {
                setAttributes({ user_id: "0" });
              } else {
                setAttributes({ user_id: new_user_id });
              }
            }}
          />

          <TextControl
            label={__("Label Before Expire", "ebox")}
            help={__(
              "The label prefix shown before the access expires",
              "ebox"
            )}
            value={label_before || ""}
            onChange={(label_before) => setAttributes({ label_before })}
          />
          <TextControl
            label={__("Label After Expire", "ebox")}
            help={__("The label prefix shown after access has expired", "ebox")}
            value={label_after || ""}
            onChange={(label_after) => setAttributes({ label_after })}
          />
        </PanelBody>
        <PanelBody title={__("Preview", "ebox")} initialOpen={false}>
          <ToggleControl
            label={__("Show Preview", "ebox")}
            checked={!!preview_show}
            onChange={(preview_show) => setAttributes({ preview_show })}
          />

          <PanelRow className="ebox-block-error-message">
            {__("Preview settings are not saved.", "ebox")}
          </PanelRow>

          <TextControl
            label={__("Preview User ID", "ebox")}
            help={__("Enter a User ID to test preview", "ebox")}
            value={preview_user_id || ""}
            type={"number"}
            onChange={function (preview_new_user_id) {
              if (preview_new_user_id != "" && preview_new_user_id < 0) {
                setAttributes({ preview_user_id: "0" });
              } else {
                setAttributes({ preview_user_id: preview_new_user_id });
              }
            }}
          />
        </PanelBody>
      </InspectorControls>
    );

    function get_default_message() {
      return sprintf(
        // translators: placeholder: block_title.
        _x("%s block output shown here", "placeholder: block_title", "ebox"),
        block_title
      );
    }

    function empty_response_placeholder_function(props) {
      return get_default_message();
    }

    function do_serverside_render(attributes) {
      if (attributes.preview_show == true) {
        // We add the meta so the server knowns what is being edited.
        attributes.editing_post_meta = ldlms_get_post_edit_meta();

        return (
          <ServerSideRender
            block={block_key}
            attributes={attributes}
            key={block_key}
            EmptyResponsePlaceholder={empty_response_placeholder_function}
          />
        );
      } else {
        return get_default_message();
      }
    }

    return [
      inspectorControls,
      useMemo(() => do_serverside_render(props.attributes), [props.attributes]),
    ];
  },

  save: (props) => {
    delete props.attributes.example_show;
    delete props.attributes.editing_post_meta;
  },
});
