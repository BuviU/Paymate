/**
 * ebox Block ld-navigation
 *
 * @since 4.0.0
 * @package ebox
 */

/**
 * ebox block functions
 */
import {
  ldlms_get_custom_label,
  ldlms_get_post_edit_meta,
  ldlms_get_block_legacy_support_panel,
} from "../ldlms.js";

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

const block_key = "ebox/ld-navigation";
const block_title = sprintf(
  // translators: placeholder: Course.
  _x("ebox %s Navigation", "placeholder: Course", "ebox"),
  ldlms_get_custom_label("course")
);

registerBlockType(block_key, {
  title: block_title,
  description: sprintf(
    // translators: placeholder: Course.
    _x("This block displays the %s Navigation.", "placeholder: Course", "ebox"),
    ldlms_get_custom_label("course")
  ),
  icon: "format-aside",
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
    post_id: {
      type: "string",
      default: "",
    },
    preview_show: {
      type: "boolean",
      default: 1,
    },
    preview_post_id: {
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
  edit: (props) => {
    const {
      attributes: {
        course_id,
        post_id,
        preview_show,
        preview_post_id,
        example_show,
      },
      className,
      setAttributes,
    } = props;

    const inspectorControls = (
      <InspectorControls key="controls">
        {ldlms_get_block_legacy_support_panel()}
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
            label={__("Step ID", "ebox")}
            help={sprintf(
              // translators: placeholder: Course.
              _x(
                "Enter single Step ID. Leave blank if used within a %s.",
                "placeholder: Course",
                "ebox"
              ),
              ldlms_get_custom_label("course")
            )}
            value={post_id || ""}
            type={"number"}
            onChange={function (new_post_id) {
              if (new_post_id != "" && new_post_id < 0) {
                setAttributes({ post_id: "0" });
              } else {
                setAttributes({ post_id: new_post_id });
              }
            }}
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
            label={__("Step ID", "ebox")}
            help={__("Enter a Step ID to test preview", "ebox")}
            value={preview_post_id || ""}
            type={"number"}
            onChange={function (preview_new_post_id) {
              if (preview_new_post_id != "" && preview_new_post_id < 0) {
                setAttributes({ preview_post_id: "0" });
              } else {
                setAttributes({ preview_post_id: preview_new_post_id });
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
