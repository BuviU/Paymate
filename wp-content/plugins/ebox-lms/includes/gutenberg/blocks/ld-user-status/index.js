/**
 * ebox Block ld-user-status
 *
 * @since 4.0
 * @package ebox
 */

/**
 * ebox block functions
 */
import {
  ldlms_get_post_edit_meta,
  ldlms_get_block_legacy_support_panel,
} from "../ldlms.js";

/**
 * Internal block libraries
 */
import { __, _x } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls } from "@wordpress/block-editor";
import {
  PanelBody,
  TextControl,
  ToggleControl,
  SelectControl,
  PanelRow,
} from "@wordpress/components";
import ServerSideRender from "@wordpress/server-side-render";
import { useMemo } from "@wordpress/element";

const block_key = "ebox/ld-user-status";
const block_title = __("ebox User Status");

registerBlockType(block_key, {
  title: block_title,
  description: __(
    "This block displays information of enrolled courses and their progress for a user. Defaults to current logged in user if no ID specified.",
    "ebox"
  ),
  icon: "analytics",
  category: "ebox-blocks",
  supports: {
    customClassName: false,
  },
  attributes: {
    user_id: {
      type: "string",
      default: "",
    },
    registered_num: {
      type: "string",
      default: "",
    },
    registered_order_by: {
      type: "string",
    },
    registered_order: {
      type: "string",
    },
    preview_show: {
      type: "boolean",
      default: true,
    },
    preview_user_id: {
      type: "string",
      default: "",
    },
    isblock: {
      type: "boolean",
      default: 1,
    },
    editing_post_meta: {
      type: "object",
    },
  },
  edit: (props) => {
    const {
      attributes: {
        user_id,
        registered_num,
        registered_order_by,
        registered_order,
        preview_show,
        preview_user_id,
        isblock,
      },
      setAttributes,
    } = props;

    const field_user_id = (
      <TextControl
        label={__("User ID", "ebox")}
        help={__("ID of the user to display information for.", "ebox")}
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

    const field_registered_num = (
      <TextControl
        label={__("Courses per page", "ebox")}
        help={__(
          "Number of courses to display per page. Set to 0 for no pagination.",
          "ebox"
        )}
        value={registered_num || ""}
        type={"number"}
        onChange={function (new_registered_num) {
          if (new_registered_num != "" && new_registered_num < 0) {
            setAttributes({ registered_num: "0" });
          } else {
            setAttributes({ registered_num: new_registered_num });
          }
        }}
      />
    );

    const field_order_by = (
      <SelectControl
        key="registered_order_by"
        value={registered_order_by}
        label={__("Order By", "ebox")}
        options={[
          {
            label: __("Title", "ebox"),
            value: "post_title",
          },
          {
            label: __("ID", "ebox"),
            value: "post_id",
          },
          {
            label: __("Date", "ebox"),
            value: "post_date",
          },
          {
            label: __("Menu", "ebox"),
            value: "menu_order",
          },
        ]}
        onChange={(registered_order_by) =>
          setAttributes({ registered_order_by })
        }
      />
    );

    const field_order = (
      <SelectControl
        key="registered_order"
        value={registered_order}
        label={__("Order", "ebox")}
        options={[
          {
            label: __("ASC (default)", "ebox"),
            value: "ASC",
          },
          {
            label: __("DESC", "ebox"),
            value: "DESC",
          },
        ]}
        onChange={(registered_order) => setAttributes({ registered_order })}
      />
    );

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
        {ldlms_get_block_legacy_support_panel()}
        <PanelBody title={__("Settings", "ebox")}>
          {field_user_id}
          {field_registered_num}
          {field_order_by}
          {field_order}
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
