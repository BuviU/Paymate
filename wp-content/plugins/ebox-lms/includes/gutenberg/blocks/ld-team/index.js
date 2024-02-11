/**
 * ebox Block ld-team
 *
 * @since 2.5.9
 * @package ebox
 */

/**
 * ebox block functions
 */
import { ldlms_get_custom_label, ldlms_get_integer_value } from "../ldlms.js";

/**
 * Internal block libraries
 */
import { __, _x, sprintf } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { InnerBlocks, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, TextControl, ToggleControl } from "@wordpress/components";

const block_key = "ebox/ld-team";
const block_title = sprintf(
  // translators: placeholder: Team.
  _x("ebox %s", "placeholder: Team", "ebox"),
  ldlms_get_custom_label("team")
);

registerBlockType(block_key, {
  title: block_title,
  description: sprintf(
    // translators: placeholder: Team.
    _x(
      "This block shows the content if the user is enrolled into the %s.",
      "placeholder: Team",
      "ebox"
    ),
    ldlms_get_custom_label("team")
  ),
  icon: "teams",
  category: "ebox-blocks",
  supports: {
    customClassName: false,
  },
  attributes: {
    team_id: {
      type: "string",
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
      attributes: { team_id, user_id, autop },
      className,
      setAttributes,
    } = props;

    const inspectorControls = (
      <InspectorControls key="controls">
        <PanelBody title={__("Settings", "ebox")}>
          <TextControl
            label={sprintf(
              // translators: placeholder: Team.
              _x("%s ID", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            )}
            help={sprintf(
              // translators: placeholder: Team.
              _x("%s ID (required)", "placeholder: Team", "ebox"),
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

    let ld_block_error_message = "";
    let preview_team_id = ldlms_get_integer_value(team_id);
    if (preview_team_id == 0) {
      ld_block_error_message = sprintf(
        // translators: placeholder: Team.
        _x("%s ID is required.", "placeholder: Team", "ebox"),
        ldlms_get_custom_label("team")
      );
    }

    if (ld_block_error_message.length) {
      ld_block_error_message = (
        <span className="ebox-block-error-message">
          {ld_block_error_message}
        </span>
      );
    }

    const outputBlock = (
      <div className={className} key="ebox/ld-team">
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
