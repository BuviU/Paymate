/**
 * ebox Block ld-visitor
 *
 * @since 2.5.9
 * @package ebox
 */

/**
 * ebox block functions
 */
import {
  ldlms_get_post_edit_meta,
  ldlms_get_custom_label,
  ldlms_get_integer_value,
} from "../ldlms.js";

/**
 * Internal block libraries
 */
import { __, _x, sprintf } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { InnerBlocks, InspectorControls } from "@wordpress/block-editor";
import {
  PanelBody,
  SelectControl,
  TextControl,
  ToggleControl,
} from "@wordpress/components";

const block_key = "ebox/ld-visitor";
const block_title = __("ebox Visitor", "ebox");

registerBlockType(block_key, {
  title: block_title,
  description: sprintf(
    // translators: placeholder: Course.
    _x(
      "This block shows the content if the user is not enrolled into the %s.",
      "placeholder: Course",
      "ebox"
    ),
    ldlms_get_custom_label("course")
  ),
  icon: "visibility",
  supports: {
    customClassName: false,
  },
  category: "ebox-blocks",
  attributes: {
    display_type: {
      type: "string",
      default: "",
    },
    course_id: {
      type: "string",
      default: "",
    },
    team_id: {
      type: "string",
      default: "",
    },
    user_id: {
      type: "string",
      default: "",
    },
    autop: {
      type: "boolean",
      default: true,
    },
  },
  edit: (props) => {
    const {
      attributes: { display_type, course_id, team_id, user_id, autop },
      className,
      setAttributes,
    } = props;

    var display_type_control;
    var post_id_controls;

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
      );
    } else if ("teams" === display_type) {
      setAttributes({ course_id: "" });
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
        <PanelBody title={__("Settings", "ebox")}>
          {display_type_control}
          {post_id_controls}
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
          <ToggleControl
            label={__("Auto Paragraph", "ebox")}
            checked={!!autop}
            onChange={(autop) => setAttributes({ autop })}
          />
        </PanelBody>
      </InspectorControls>
    );

    let preview_display_type = display_type;
    if (preview_display_type === "") {
      let editing_post_meta = ldlms_get_post_edit_meta();
      if ("undefined" !== typeof editing_post_meta.post_type) {
        if (editing_post_meta.post_type === "ebox-courses") {
          preview_display_type = "ebox-courses";
        } else if (editing_post_meta.post_type === "teams") {
          preview_display_type = "teams";
        }
      }
    }

    let ld_block_error_message = "";
    if ("ebox-courses" === preview_display_type) {
      let preview_course_id = ldlms_get_integer_value(course_id);

      if (preview_course_id === 0) {
        preview_course_id = ldlms_get_post_edit_meta("course_id");
        preview_course_id = ldlms_get_integer_value(preview_course_id);

        if (preview_course_id == 0) {
          ld_block_error_message = sprintf(
            // translators: placeholders: Course, Course.
            _x(
              "%1$s ID is required when not used within a %2$s.",
              "placeholders: Course, Course",
              "ebox"
            ),
            ldlms_get_custom_label("course"),
            ldlms_get_custom_label("course")
          );
        }
      }
    } else if ("teams" === preview_display_type) {
      let preview_team_id = ldlms_get_integer_value(team_id);

      if (preview_team_id === 0) {
        preview_team_id = ldlms_get_post_edit_meta("post_id");
        preview_team_id = ldlms_get_integer_value(preview_team_id);

        if (preview_team_id == 0) {
          ld_block_error_message = sprintf(
            // translators: placeholders: Team, Team.
            _x(
              "%1$s ID is required when not used within a %2$s.",
              "placeholders: Team, Team",
              "ebox"
            ),
            ldlms_get_custom_label("team"),
            ldlms_get_custom_label("team")
          );
        }
      }
    }

    if (ld_block_error_message.length) {
      ld_block_error_message = (
        <span className="ebox-block-error-message">
          {ld_block_error_message}
        </span>
      );
    }

    const outputBlock = (
      <div className={className} key="ebox/ld-visitor">
        <span className="ebox-inner-header">{block_title}</span>
        <div className="ebox-block-inner">
          {ld_block_error_message}
          <InnerBlocks />
        </div>
      </div>
    );

    return [inspectorControls, outputBlock];
  },
  save: (props) => {
    return <InnerBlocks.Content />;
  },
});
