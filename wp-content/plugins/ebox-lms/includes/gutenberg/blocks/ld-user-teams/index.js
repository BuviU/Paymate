/**
 * ebox Block ld-user-teams
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
  PanelRow,
  TextControl,
  ToggleControl,
} from "@wordpress/components";
import ServerSideRender from "@wordpress/server-side-render";
import { useMemo } from "@wordpress/element";

const block_key = "ebox/ld-user-teams";
const block_title = sprintf(
  // translators: placeholder: Teams.
  _x("ebox User %s", "placeholder: Teams", "ebox"),
  ldlms_get_custom_label("teams")
);
registerBlockType(block_key, {
  title: block_title,
  description: sprintf(
    // translators: placeholder: Teams.
    _x(
      "This block displays the list of %s users are assigned to as users or leaders.",
      "placeholder: Teams",
      "ebox"
    ),
    ldlms_get_custom_label("teams")
  ),
  icon: "teams",
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
    user_id: {
      type: "string",
      default: "",
    },
    preview_show: {
      type: "boolean",
      default: 1,
    },
    preview_user_id: {
      type: "string",
    },
    editing_post_meta: {
      type: "object",
    },
  },
  edit: function (props) {
    const {
      attributes: { user_id, preview_user_id, preview_show },
      setAttributes,
    } = props;

    let panel_teams_not_public = "";
    if (ldlms_settings["settings"]["teams_cpt"]["public"] === "") {
      panel_teams_not_public = (
        <PanelBody title={__("Warning", "ebox")} opened={true}>
          <TextControl
            help={sprintf(
              // translators: placeholders: Teams, Teams.
              _x(
                "%1$s are not public, please visit the %2$s Settings page and set them to Public to enable access on the front end.",
                "placeholders: Teams, Teams",
                "ebox"
              ),
              ldlms_get_custom_label("teams"),
              ldlms_get_custom_label("teams")
            )}
            value={""}
            type={"hidden"}
            className={"notice notice-error"}
          />
        </PanelBody>
      );
    }

    const inspectorControls = (
      <InspectorControls key="controls">
        {panel_teams_not_public}
        <PanelBody title={__("Settings", "ebox")}>
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
