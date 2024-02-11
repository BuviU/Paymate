/**
 * ebox Block ld-course-content
 *
 * @since 2.5.9
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
  SelectControl,
  TextControl,
  ToggleControl,
  PanelRow,
} from "@wordpress/components";
import ServerSideRender from "@wordpress/server-side-render";
import { useMemo } from "@wordpress/element";

const block_key = "ebox/ld-course-content";
const block_title = sprintf(
  // translators: placeholder: Course.
  _x("ebox %s Content", "placeholder: Course", "ebox"),
  ldlms_get_custom_label("course")
);

registerBlockType(block_key, {
  title: block_title,
  description: sprintf(
    // translators: placeholder: Course.
    _x(
      "This block displays the %s Content table.",
      "placeholder: Course",
      "ebox"
    ),
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
    display_type: {
      type: "string",
      default: "",
    },
    course_id: {
      type: "string",
      default: "",
    },
    post_id: {
      type: "string",
      default: "",
    },
    team_id: {
      type: "string",
      default: "",
    },
    per_page: {
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
  edit: (props) => {
    const {
      attributes: {
        display_type,
        course_id,
        post_id,
        team_id,
        per_page,
        preview_show,
        preview_user_id,
        example_show,
      },
      className,
      setAttributes,
    } = props;

    var display_type_control;
    var post_id_controls;
    var preview_post_id_controls;

    display_type_control = (
      <SelectControl
        key="display_type"
        label={__("Display Type", "ebox")}
        value={display_type}
        options={[
          {
            label: __("Select a Display Type", "ebox"),
            value: "",
          },
          {
            label: ldlms_get_custom_label("course"),
            value: "ebox-courses",
          },
          {
            label: ldlms_get_custom_label("team"),
            value: "teams",
          },
        ]}
        help={sprintf(
          // translators: placeholders: Course, Team.
          _x(
            "Leave blank to show the default %1$s or %2$s content table.",
            "placeholders: Course, Team",
            "ebox"
          ),
          ldlms_get_custom_label("course"),
          ldlms_get_custom_label("team")
        )}
        onChange={(display_type) => setAttributes({ display_type })}
      />
    );

    if ("ebox-courses" === display_type) {
      setAttributes({ team_id: "" });
      post_id_controls = (
        <React.Fragment>
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
              // translators: placeholders: Course, Course.
              _x(
                "Enter single Step ID. Leave blank if used within a %1$s or 0 to always show %2$s content table.",
                "placeholders: Course, Course",
                "ebox"
              ),
              ldlms_get_custom_label("course"),
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
        </React.Fragment>
      );
    } else if ("teams" === display_type) {
      setAttributes({ course_id: "" });
      setAttributes({ post_id: "" });
      post_id_controls = (
        <TextControl
          label={sprintf(
            // translators: placeholder: Team.
            _x("%s ID", "placeholder: Team", "ebox"),
            ldlms_get_custom_label("team")
          )}
          help={sprintf(
            // translators: placeholders: Team, Team.
            _x(
              "Enter single %1$s ID. Leave blank if used within a %2$s.",
              "placeholders: Team, Team",
              "ebox"
            ),
            ldlms_get_custom_label("team"),
            ldlms_get_custom_label("team")
          )}
          value={team_id || ""}
          type={"number"}
          onChange={function (new_team_id) {
            if (new_team_id != "" && new_team_id < 0) {
              setAttributes({ team_id: "0" });
            } else {
              setAttributes({ team_id: new_team_id });
            }
          }}
        />
      );
    }

    const inspectorControls = (
      <InspectorControls key="controls">
        {ldlms_get_block_legacy_support_panel()}
        <PanelBody title={__("Settings", "ebox")}>
          {display_type_control}
          {post_id_controls}
          <TextControl
            label={__("Items per page", "ebox")}
            help={__("Leave empty for default or 0 to show all items.", "ebox")}
            value={per_page || ""}
            type={"number"}
            onChange={function (new_per_page) {
              if (new_per_page != "" && new_per_page < 0) {
                setAttributes({ per_page: "0" });
              } else {
                setAttributes({ per_page: new_per_page });
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
            label={__("Preview User ID", "ebox")}
            help={__("Enter a User ID for preview.", "ebox")}
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
