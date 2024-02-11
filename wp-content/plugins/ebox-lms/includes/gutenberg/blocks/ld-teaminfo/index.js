/**
 * ebox Block ld-teaminfo
 *
 * @since 3.2.0
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
  SelectControl,
  TextControl,
  ToggleControl,
  PanelRow,
} from "@wordpress/components";
import ServerSideRender from "@wordpress/server-side-render";
import { useMemo } from "@wordpress/element";

const block_key = "ebox/ld-teaminfo";
const block_title = sprintf(
  // translators: placeholder: Team.
  _x("ebox %s Info [teaminfo]", "placeholder: Team", "ebox"),
  ldlms_get_custom_label("team")
);

registerBlockType(block_key, {
  title: block_title,
  description: sprintf(
    // translators: placeholder: Team.
    _x(
      "This block displays %s related information",
      "placeholder: Team",
      "ebox"
    ),
    ldlms_get_custom_label("team")
  ),
  icon: "analytics",
  category: "ebox-blocks",
  supports: {
    customClassName: false,
  },
  attributes: {
    show: {
      type: "string",
    },
    team_id: {
      type: "string",
      default: "",
    },
    user_id: {
      type: "string",
      default: "",
    },
    format: {
      type: "string",
    },
    decimals: {
      type: "string",
    },
    preview_show: {
      type: "boolean",
      default: 1,
    },
    preview_user_id: {
      type: "string",
      default: "",
    },
    editing_post_meta: {
      type: "object",
    },
  },
  edit: (props) => {
    const {
      attributes: {
        team_id,
        show,
        user_id,
        format,
        decimals,
        preview_show,
        preview_user_id,
      },
      setAttributes,
    } = props;

    const field_show = (
      <SelectControl
        key="show"
        value={show}
        label={__("Show", "ebox")}
        options={[
          {
            label: sprintf(
              // translators: placeholder: Team.
              _x("%s Title", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            ),
            value: "team_title",
          },
          {
            label: sprintf(
              // translators: placeholder: Team.
              _x("%s URL", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            ),
            value: "team_url",
          },
          {
            label: sprintf(
              // translators: placeholder: Team.
              _x("%s Price", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            ),
            value: "team_price",
          },
          {
            label: sprintf(
              // translators: placeholder: Team.
              _x("%s Price Type", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            ),
            value: "team_price_type",
          },
          {
            label: sprintf(
              // translators: placeholder: Team.
              _x("%s Enrolled Users Count", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            ),
            value: "team_users_count",
          },
          {
            label: sprintf(
              // translators: placeholders: Team, Courses.
              _x("%1$s %2$s Count", "placeholders: Team, Courses", "ebox"),
              ldlms_get_custom_label("team"),
              ldlms_get_custom_label("courses")
            ),
            value: "team_courses_count",
          },
          {
            label: sprintf(
              // translators: placeholder: Team.
              _x("User %s Status", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            ),
            value: "user_team_status",
          },

          {
            label: sprintf(
              // translators: placeholder: Team.
              _x("%s Completed On (date)", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            ),
            value: "completed_on",
          },
          {
            label: sprintf(
              // translators: placeholder: Team.
              _x("%s Enrolled On (date)", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            ),
            value: "enrolled_on",
          },
          {
            label: sprintf(
              // translators: placeholder: Team.
              _x("%s Completed Percentage", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            ),
            value: "percent_completed",
          },
        ]}
        onChange={(show) => setAttributes({ show })}
      />
    );

    const field_team_id = (
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
    let field_user_id = "";

    if (
      [
        "user_team_status",
        "completed_on",
        "enrolled_on",
        "percent_completed",
      ].includes(show)
    ) {
      field_user_id = (
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
      );
    }

    let field_format = "";
    if (["completed_on", "enrolled_on"].includes(show)) {
      field_format = (
        <TextControl
          label={__("Format", "ebox")}
          help={__(
            'This can be used to change the date format. Default: "F j, Y, g:i a.',
            "ebox"
          )}
          value={format || ""}
          onChange={(format) => setAttributes({ format })}
        />
      );
    }

    let field_decimals = "";
    if (["percent_completed"].includes(show)) {
      field_decimals = (
        <TextControl
          label={__("Decimals", "ebox")}
          help={__("Number of decimal places to show. Default is 2.", "ebox")}
          value={decimals || ""}
          type={"number"}
          onChange={function (new_decimals) {
            if (new_decimals != "" && new_decimals < 0) {
              setAttributes({ decimals: "0" });
            } else {
              setAttributes({ decimals: new_decimals });
            }
          }}
        />
      );
    }

    const panel_preview = (
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
    );

    const inspectorControls = (
      <InspectorControls key="controls">
        <PanelBody title={__("Settings", "ebox")}>
          {field_team_id}
          {field_user_id}
          {field_show}
          {field_format}
          {field_decimals}
        </PanelBody>
        {panel_preview}
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
  save: function (props) {
    delete props.attributes.example_show;
    delete props.attributes.editing_post_meta;
  },
});
