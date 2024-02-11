/**
 * ebox Block ld-team-list
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
  ldlms_get_per_page,
} from "../ldlms.js";

/**
 * Internal block libraries
 */
import { __, _x, sprintf } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls } from "@wordpress/block-editor";
import {
  PanelBody,
  RangeControl,
  SelectControl,
  TextControl,
  ToggleControl,
  PanelRow,
} from "@wordpress/components";
import ServerSideRender from "@wordpress/server-side-render";
import { useMemo } from "@wordpress/element";

const block_key = "ebox/ld-team-list";
const block_title = sprintf(
  // translators: placeholder: Team.
  _x("ebox %s List", "placeholder: Team", "ebox"),
  ldlms_get_custom_label("team")
);

registerBlockType(block_key, {
  title: block_title,
  description: sprintf(
    // translators: placeholder: Teams.
    _x("This block shows a list of %s.", "placeholder: Teams", "ebox"),
    ldlms_get_custom_label("teams")
  ),
  icon: "list-view",
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
    orderby: {
      type: "string",
      default: "ID",
    },
    order: {
      type: "string",
      default: "DESC",
    },
    per_page: {
      type: "string",
      default: "",
    },
    myteams: {
      type: "string",
      default: "",
    },
    status: {
      type: "array",
      default: ["not_started", "in_progress", "completed"],
    },
    show_content: {
      type: "boolean",
      default: true,
    },
    show_thumbnail: {
      type: "boolean",
      default: true,
    },
    team_category_name: {
      type: "string",
      default: "",
    },
    team_cat: {
      type: "string",
      default: "",
    },
    team_categoryselector: {
      type: "boolean",
      default: false,
    },
    team_tag: {
      type: "string",
      default: "",
    },
    team_tag_id: {
      type: "string",
      default: "",
    },
    category_name: {
      type: "string",
      default: "",
    },
    cat: {
      type: "string",
      default: "",
    },
    categoryselector: {
      type: "boolean",
      default: false,
    },
    tag: {
      type: "string",
      default: "",
    },
    tag_id: {
      type: "string",
      default: "",
    },
    course_grid: {
      type: "boolean",
    },
    progress_bar: {
      type: "boolean",
      default: false,
    },
    col: {
      type: "integer",
      default:
        ldlms_settings["plugins"]["ebox-course-grid"]["enabled"][
          "col_default"
        ] || 3,
    },
    price_type: {
      type: "array",
      default: ["free", "paynow", "subscribe", "closed"],
    },
    preview_show: {
      type: "boolean",
      default: true,
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
    const {
      attributes: {
        orderby,
        order,
        per_page,
        myteams,
        status,
        show_content,
        show_thumbnail,
        team_category_name,
        team_cat,
        team_categoryselector,
        team_tag,
        team_tag_id,
        category_name,
        cat,
        categoryselector,
        tag,
        tag_id,
        course_grid,
        progress_bar,
        col,
        preview_user_id,
        preview_show,
        example_show,
        price_type,
      },
      setAttributes,
    } = props;

    let field_show_content = "";
    let field_show_thumbnail = "";
    let panel_course_grid_section = "";

    let course_grid_default = true;
    if (ldlms_settings["plugins"]["ebox-course-grid"]["enabled"] === true) {
      if (
        typeof course_grid !== "undefined" &&
        (course_grid == true || course_grid == false)
      ) {
        course_grid_default = course_grid;
      }

      let course_grid_section_open = false;
      if (course_grid_default == true) {
        course_grid_section_open = true;
      }
      panel_course_grid_section = (
        <PanelBody
          title={__("Grid Settings", "ebox")}
          initialOpen={course_grid_section_open}
        >
          <ToggleControl
            label={__("Show Grid", "ebox")}
            checked={!!course_grid_default}
            onChange={(course_grid) => setAttributes({ course_grid })}
          />
          <ToggleControl
            label={__("Show Progress Bar", "ebox")}
            checked={!!progress_bar}
            onChange={(progress_bar) => setAttributes({ progress_bar })}
          />
          <RangeControl
            label={__("Columns", "ebox")}
            value={
              col ||
              ldlms_settings["plugins"]["ebox-course-grid"]["enabled"][
                "col_default"
              ]
            }
            min={1}
            max={
              ldlms_settings["plugins"]["ebox-course-grid"]["enabled"][
                "col_max"
              ]
            }
            step={1}
            onChange={(col) => setAttributes({ col })}
          />
        </PanelBody>
      );
    }

    //if (course_grid !== true) {
    field_show_content = (
      <ToggleControl
        label={__("Show Content", "ebox")}
        checked={!!show_content}
        onChange={(show_content) => setAttributes({ show_content })}
      />
    );

    field_show_thumbnail = (
      <ToggleControl
        label={__("Show Thumbnail", "ebox")}
        checked={!!show_thumbnail}
        onChange={(show_thumbnail) => setAttributes({ show_thumbnail })}
      />
    );
    //}

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

    const panelbody_header = (
      <PanelBody
        className="ebox-block-controls-panel ebox-block-controls-panel-ld-team-list"
        title={__("Settings", "ebox")}
      >
        <SelectControl
          key="orderby"
          label={__("Order by", "ebox")}
          value={orderby}
          options={[
            {
              label: __("ID - Order by post id. (default)", "ebox"),
              value: "ID",
            },
            {
              label: __("Title - Order by post title", "ebox"),
              value: "title",
            },
            {
              label: __("Date - Order by post date", "ebox"),
              value: "date",
            },
            {
              label: __("Menu - Order by Page Order Value", "ebox"),
              value: "menu_order",
            },
          ]}
          onChange={(orderby) => setAttributes({ orderby })}
        />
        <SelectControl
          key="order"
          label={__("Order", "ebox")}
          value={order}
          options={[
            {
              label: __("DESC - highest to lowest values (default)", "ebox"),
              value: "DESC",
            },
            {
              label: __("ASC - lowest to highest values", "ebox"),
              value: "ASC",
            },
          ]}
          onChange={(order) => setAttributes({ order })}
        />
        <TextControl
          label={sprintf(
            // translators: placeholder: Teams.
            _x("%s per page", "placeholder: Teams", "ebox"),
            ldlms_get_custom_label("teams")
          )}
          help={sprintf(
            // translators: placeholder: default per page.
            _x(
              "Leave empty for default (%d) or 0 to show all items.",
              "placeholder: default per page",
              "ebox"
            ),
            ldlms_get_per_page("per_page")
          )}
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

        <SelectControl
          multiple
          key="price_type"
          label={sprintf(
            // translators: placeholder: Team Access Mode(s).
            _x(
              "%s Access Mode(s)",
              "placeholder: Team Access Mode(s)",
              "ebox"
            ),
            ldlms_get_custom_label("team")
          )}
          help={__("Ctrl+click to deselect selected items.", "ebox")}
          value={price_type}
          options={[
            {
              label: __("Free", "ebox"),
              value: "free",
            },
            {
              label: __("Buy Now", "ebox"),
              value: "paynow",
            },
            {
              label: __("Recurring", "ebox"),
              value: "subscribe",
            },
            {
              label: __("Closed", "ebox"),
              value: "closed",
            },
          ]}
          onChange={(price_type) => setAttributes({ price_type })}
        />

        <SelectControl
          key="myteams"
          label={sprintf(
            // translators: placeholder: Teams.
            _x("My %s", "placeholder: Teams", "ebox"),
            ldlms_get_custom_label("teams")
          )}
          value={myteams}
          options={[
            {
              label: sprintf(
                // translators: placeholder: Teams.
                _x("Show All %s (default)", "placeholder: Teams", "ebox"),
                ldlms_get_custom_label("teams")
              ),
              value: "",
            },
            {
              label: sprintf(
                // translators: placeholder: Teams.
                _x("Show Enrolled %s only", "placeholder: Teams", "ebox"),
                ldlms_get_custom_label("teams")
              ),
              value: "enrolled",
            },
            {
              label: sprintf(
                // translators: placeholder: Teams.
                _x("Show not-Enrolled %s only", "placeholder: Teams", "ebox"),
                ldlms_get_custom_label("Teams")
              ),
              value: "not-enrolled",
            },
          ]}
          onChange={(myteams) => setAttributes({ myteams })}
        />
        {"enrolled" === myteams && (
          <SelectControl
            multiple
            key="status"
            label={sprintf(
              // translators: placeholder: Teams.
              _x("Enrolled %s Status", "placeholder: Teams", "ebox"),
              ldlms_get_custom_label("teams")
            )}
            value={status}
            options={[
              {
                label: __("Not Started", "ebox"),
                value: "not_started",
              },
              {
                label: __("In Progress", "ebox"),
                value: "in_progress",
              },
              {
                label: __("Completed", "ebox"),
                value: "completed",
              },
            ]}
            onChange={(status) => setAttributes({ status })}
          />
        )}
        {field_show_content}
        {field_show_thumbnail}
      </PanelBody>
    );

    let panel_team_category_section = "";
    if (
      ldlms_settings["settings"]["teams_taxonomies"]["ld_team_category"] ===
      "yes"
    ) {
      let panel_team_category_section_open = false;
      if (team_category_name != "" || team_cat != "") {
        panel_team_category_section_open = true;
      }
      panel_team_category_section = (
        <PanelBody
          title={sprintf(
            // translators: placeholder: Team.
            _x("%s Category Settings", "placeholder: Team", "ebox"),
            ldlms_get_custom_label("team")
          )}
          initialOpen={panel_team_category_section_open}
        >
          <TextControl
            label={sprintf(
              // translators: placeholder: Team.
              _x("%s Category Slug", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            )}
            help={sprintf(
              // translators: placeholder: Teams.
              _x(
                "shows %s with mentioned category slug.",
                "placeholder: Teams",
                "ebox"
              ),
              ldlms_get_custom_label("teams")
            )}
            value={team_category_name || ""}
            onChange={(team_category_name) =>
              setAttributes({ team_category_name })
            }
          />

          <TextControl
            label={sprintf(
              // translators: placeholder: Team.
              _x("%s Category ID", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            )}
            help={sprintf(
              // translators: placeholder: Teams.
              _x(
                "shows %s with mentioned category ID.",
                "placeholder: Teams",
                "ebox"
              ),
              ldlms_get_custom_label("teams")
            )}
            value={team_cat || ""}
            type={"number"}
            onChange={function (new_team_cat) {
              if (new_team_cat != "" && new_team_cat < 0) {
                setAttributes({ team_cat: "0" });
              } else {
                setAttributes({ team_cat: new_team_cat });
              }
            }}
          />
          <ToggleControl
            label={sprintf(
              // translators: placeholder: Team.
              _x("%s Category Selector", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            )}
            help={sprintf(
              // translators: placeholder: Teams.
              _x(
                "shows a %s category dropdown.",
                "placeholder: Teams",
                "ebox"
              ),
              ldlms_get_custom_label("teams")
            )}
            checked={!!team_categoryselector}
            onChange={(team_categoryselector) =>
              setAttributes({ team_categoryselector })
            }
          />
        </PanelBody>
      );
    }

    let panel_team_tag_section = "";
    if (
      ldlms_settings["settings"]["teams_taxonomies"]["ld_team_tag"] === "yes"
    ) {
      let panel_team_tag_section_open = false;
      if (team_tag != "" || team_tag_id != "") {
        panel_team_tag_section_open = true;
      }
      panel_team_tag_section = (
        <PanelBody
          title={sprintf(
            // translators: placeholder: Team.
            _x("%s Tag Settings", "placeholder: Team", "ebox"),
            ldlms_get_custom_label("team")
          )}
          initialOpen={panel_team_tag_section_open}
        >
          <TextControl
            label={sprintf(
              // translators: placeholder: Team.
              _x("%s Tag Slug", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            )}
            help={sprintf(
              // translators: placeholder: Teams.
              _x(
                "shows %s with mentioned tag slug.",
                "placeholder: Teams",
                "ebox"
              ),
              ldlms_get_custom_label("teams")
            )}
            value={team_tag || ""}
            onChange={(team_tag) => setAttributes({ team_tag })}
          />

          <TextControl
            label={sprintf(
              // translators: placeholder: Team.
              _x("%s Tag ID", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            )}
            help={sprintf(
              // translators: placeholder: Teams.
              _x(
                "shows %s with mentioned tag ID.",
                "placeholder: Teams",
                "ebox"
              ),
              ldlms_get_custom_label("teams")
            )}
            value={team_tag_id || ""}
            type={"number"}
            onChange={function (new_team_tag_id) {
              if (new_team_tag_id != "" && new_team_tag_id < 0) {
                setAttributes({ team_tag_id: "0" });
              } else {
                setAttributes({ team_tag_id: new_team_tag_id });
              }
            }}
          />
        </PanelBody>
      );
    }

    let panel_wp_category_section = "";
    if (
      ldlms_settings["settings"]["teams_taxonomies"]["wp_post_category"] ===
      "yes"
    ) {
      let panel_wp_category_section_open = false;
      if (category_name != "" || cat != "") {
        panel_wp_category_section_open = true;
      }
      panel_wp_category_section = (
        <PanelBody
          title={__("WP Category Settings", "ebox")}
          initialOpen={panel_wp_category_section_open}
        >
          <TextControl
            label={__("WP Category Slug", "ebox")}
            help={sprintf(
              // translators: placeholder: Teams.
              _x(
                "shows %s with mentioned WP Category slug.",
                "placeholder: Teams",
                "ebox"
              ),
              ldlms_get_custom_label("teams")
            )}
            value={category_name || ""}
            onChange={(category_name) => setAttributes({ category_name })}
          />

          <TextControl
            label={sprintf(
              // translators: placeholder: Team.
              _x("%s Category ID", "placeholder: Team", "ebox"),
              ldlms_get_custom_label("team")
            )}
            help={sprintf(
              // translators: placeholder: Teams.
              _x(
                "shows %s with mentioned category ID.",
                "placeholder: Teams",
                "ebox"
              ),
              ldlms_get_custom_label("teams")
            )}
            value={cat || ""}
            type={"number"}
            onChange={function (new_cat) {
              if (new_cat != "" && new_cat < 0) {
                setAttributes({ cat: "0" });
              } else {
                setAttributes({ cat: new_cat });
              }
            }}
          />
          <ToggleControl
            label={__("WP Category Selector", "ebox")}
            help={__("shows a WP category dropdown.", "ebox")}
            checked={!!categoryselector}
            onChange={(categoryselector) => setAttributes({ categoryselector })}
          />
        </PanelBody>
      );
    }

    let panel_wp_tag_section = "";
    if (
      ldlms_settings["settings"]["teams_taxonomies"]["wp_post_tag"] === "yes"
    ) {
      let panel_wp_tag_section_open = false;
      if (tag != "" || tag_id != "") {
        panel_wp_tag_section_open = true;
      }
      panel_wp_tag_section = (
        <PanelBody
          title={__("WP Tag Settings", "ebox")}
          initialOpen={panel_wp_tag_section_open}
        >
          <TextControl
            label={__("WP Tag Slug", "ebox")}
            help={sprintf(
              // translators: placeholder: Teams.
              _x(
                "shows %s with mentioned WP tag slug.",
                "placeholder: Teams",
                "ebox"
              ),
              ldlms_get_custom_label("teams")
            )}
            value={tag || ""}
            onChange={(tag) => setAttributes({ tag })}
          />

          <TextControl
            label={__("WP Tag ID", "ebox")}
            help={sprintf(
              // translators: placeholder: Teams.
              _x(
                "shows %s with mentioned WP tag ID.",
                "placeholder: Teams",
                "ebox"
              ),
              ldlms_get_custom_label("teams")
            )}
            value={tag_id || ""}
            type={"number"}
            onChange={function (new_tag_id) {
              if (new_tag_id != "" && new_tag_id < 0) {
                setAttributes({ tag_id: "0" });
              } else {
                setAttributes({ tag_id: new_tag_id });
              }
            }}
          />
        </PanelBody>
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
        {panel_teams_not_public}
        {panelbody_header}
        {panel_course_grid_section}
        {panel_team_category_section}
        {panel_team_tag_section}
        {panel_wp_category_section}
        {panel_wp_tag_section}
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

  save: (props) => {
    delete props.attributes.example_show;
    delete props.attributes.editing_post_meta;
  },
});
