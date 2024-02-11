/**
 * ebox Block ld-lesson-list
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

const block_key = "ebox/ld-lesson-list";
const block_title = sprintf(
  // translators: placeholder: Lesson.
  _x("ebox %s List", "placeholder: Lesson", "ebox"),
  ldlms_get_custom_label("lesson")
);
registerBlockType(block_key, {
  title: block_title,
  description: sprintf(
    // translators: placeholder: modules.
    _x("This block shows a list of %s.", "placeholder: modules", "ebox"),
    ldlms_get_custom_label("modules")
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
    course_id: {
      type: "string",
      default: "",
    },
    show_content: {
      type: "boolean",
      default: true,
    },
    show_thumbnail: {
      type: "boolean",
      default: true,
    },
    lesson_category_name: {
      type: "string",
      default: "",
    },
    lesson_cat: {
      type: "string",
      default: "",
    },
    lesson_categoryselector: {
      type: "boolean",
      default: false,
    },
    lesson_tag: {
      type: "string",
      default: "",
    },
    lesson_tag_id: {
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
    col: {
      type: "integer",
      default:
        ldlms_settings["plugins"]["ebox-course-grid"]["enabled"][
          "col_default"
        ] || 3,
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
        course_id,
        show_content,
        show_thumbnail,
        lesson_category_name,
        lesson_cat,
        lesson_categoryselector,
        lesson_tag,
        lesson_tag_id,
        category_name,
        cat,
        categoryselector,
        tag,
        tag_id,
        course_grid,
        col,
        preview_show,
        preview_user_id,
        example_show,
      },
      setAttributes,
    } = props;

    let field_show_content = "";
    let field_show_thumbnail = "";
    let panel_lesson_grid_section = "";

    let course_grid_default = true;
    if (ldlms_settings["plugins"]["ebox-course-grid"]["enabled"] === true) {
      if (
        typeof course_grid !== "undefined" &&
        (course_grid == true || course_grid == false)
      ) {
        course_grid_default = course_grid;
      }

      let lesson_grid_section_open = false;
      if (course_grid_default == true) {
        lesson_grid_section_open = true;
      }
      panel_lesson_grid_section = (
        <PanelBody
          title={__("Grid Settings", "ebox")}
          initialOpen={lesson_grid_section_open}
        >
          <ToggleControl
            label={__("Show Grid", "ebox")}
            checked={!!course_grid_default}
            onChange={(course_grid) => setAttributes({ course_grid })}
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

    const panelbody_header = (
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
              "Enter single %1$s ID to limit listing. Leave blank if used within a %2$s.",
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
            // translators: placeholder: modules.
            _x("%s per page", "placeholder: modules", "ebox"),
            ldlms_get_custom_label("modules")
          )}
          help={sprintf(
            // translators: placeholder: per_page.
            _x(
              "Leave empty for default (%d) or 0 to show all items.",
              "placeholder: per_page",
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

        {field_show_content}
        {field_show_thumbnail}
      </PanelBody>
    );

    let panel_lesson_category_section = "";
    if (
      ldlms_settings["settings"]["modules_taxonomies"]["ld_lesson_category"] ===
      "yes"
    ) {
      let panel_lesson_category_section_open = false;
      if (lesson_category_name != "" || lesson_cat != "") {
        panel_lesson_category_section_open = true;
      }
      panel_lesson_category_section = (
        <PanelBody
          title={sprintf(
            // translators: placeholder: Lesson.
            _x("%s Category Settings", "placeholder: Lesson", "ebox"),
            ldlms_get_custom_label("lesson")
          )}
          initialOpen={panel_lesson_category_section_open}
        >
          <TextControl
            label={sprintf(
              // translators: placeholder: Lesson.
              _x("%s Category Slug", "placeholder: Lesson", "ebox"),
              ldlms_get_custom_label("lesson")
            )}
            help={sprintf(
              // translators: placeholder: modules.
              _x(
                "shows %s with mentioned category slug.",
                "placeholder: modules",
                "ebox"
              ),
              ldlms_get_custom_label("modules")
            )}
            value={lesson_category_name || ""}
            onChange={(lesson_category_name) =>
              setAttributes({ lesson_category_name })
            }
          />

          <TextControl
            label={sprintf(
              // translators: placeholder: Lesson.
              _x("%s Category ID", "placeholder: Lesson", "ebox"),
              ldlms_get_custom_label("lesson")
            )}
            help={sprintf(
              // translators: placeholder: modules.
              _x(
                "shows %s with mentioned category ID.",
                "placeholder: modules",
                "ebox"
              ),
              ldlms_get_custom_label("modules")
            )}
            value={lesson_cat || ""}
            type={"number"}
            onChange={function (new_lesson_cat) {
              if (new_lesson_cat != "" && new_lesson_cat < 0) {
                setAttributes({ lesson_cat: "0" });
              } else {
                setAttributes({ lesson_cat: new_lesson_cat });
              }
            }}
          />
          <ToggleControl
            label={sprintf(
              // translators: placeholder: Lesson.
              _x("%s Category Selector", "placeholder: Lesson", "ebox"),
              ldlms_get_custom_label("lesson")
            )}
            help={sprintf(
              // translators: placeholder: modules.
              _x(
                "shows a %s category dropdown.",
                "placeholder: modules",
                "ebox"
              ),
              ldlms_get_custom_label("modules")
            )}
            checked={!!lesson_categoryselector}
            onChange={(lesson_categoryselector) =>
              setAttributes({ lesson_categoryselector })
            }
          />
        </PanelBody>
      );
    }

    let panel_lesson_tag_section = "";
    if (
      ldlms_settings["settings"]["modules_taxonomies"]["ld_lesson_tag"] ===
      "yes"
    ) {
      let panel_lesson_tag_section_open = false;
      if (lesson_tag != "" || lesson_tag_id != "") {
        panel_lesson_tag_section_open = true;
      }
      panel_lesson_tag_section = (
        <PanelBody
          title={sprintf(
            // translators: placeholder: Lesson.
            _x("%s Tag Settings", "placeholder: Lesson", "ebox"),
            ldlms_get_custom_label("lesson")
          )}
          initialOpen={panel_lesson_tag_section_open}
        >
          <TextControl
            label={sprintf(
              // translators: placeholder: Lesson.
              _x("%s Tag Slug", "placeholder: Lesson", "ebox"),
              ldlms_get_custom_label("lesson")
            )}
            help={sprintf(
              // translators: placeholder: modules.
              _x(
                "shows %s with mentioned tag slug.",
                "placeholder: modules",
                "ebox"
              ),
              ldlms_get_custom_label("modules")
            )}
            value={lesson_tag || ""}
            onChange={(lesson_tag) => setAttributes({ lesson_tag })}
          />

          <TextControl
            label={sprintf(
              // translators: placeholder: Lesson.
              _x("%s Tag ID", "placeholder: Lesson", "ebox"),
              ldlms_get_custom_label("lesson")
            )}
            help={sprintf(
              // translators: placeholder: modules.
              _x(
                "shows %s with mentioned tag ID.",
                "placeholder: modules",
                "ebox"
              ),
              ldlms_get_custom_label("modules")
            )}
            value={lesson_tag_id || ""}
            type={"number"}
            onChange={function (new_lesson_tag_id) {
              if (new_lesson_tag_id != "" && new_lesson_tag_id < 0) {
                setAttributes({ lesson_tag_id: "0" });
              } else {
                setAttributes({ lesson_tag_id: new_lesson_tag_id });
              }
            }}
          />
        </PanelBody>
      );
    }

    let panel_wp_category_section = "";
    if (
      ldlms_settings["settings"]["modules_taxonomies"]["wp_post_category"] ===
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
              // translators: placeholder: modules.
              _x(
                "shows %s with mentioned WP Category slug.",
                "placeholder: modules",
                "ebox"
              ),
              ldlms_get_custom_label("modules")
            )}
            value={category_name || ""}
            onChange={(category_name) => setAttributes({ category_name })}
          />

          <TextControl
            label={sprintf(
              // translators: placeholder: Lesson.
              _x("%s Category ID", "placeholder: Lesson", "ebox"),
              ldlms_get_custom_label("lesson")
            )}
            help={sprintf(
              // translators: placeholder: modules.
              _x(
                "shows %s with mentioned category ID.",
                "placeholder: modules",
                "ebox"
              ),
              ldlms_get_custom_label("modules")
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
      ldlms_settings["settings"]["modules_taxonomies"]["wp_post_tag"] === "yes"
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
              // translators: placeholder: modules.
              _x(
                "shows %s with mentioned WP tag slug.",
                "placeholder: modules",
                "ebox"
              ),
              ldlms_get_custom_label("modules")
            )}
            value={tag || ""}
            onChange={(tag) => setAttributes({ tag })}
          />

          <TextControl
            label={__("WP Tag ID", "ebox")}
            help={sprintf(
              // translators: placeholder: modules.
              _x(
                "shows %s with mentioned WP tag ID.",
                "placeholder: modules",
                "ebox"
              ),
              ldlms_get_custom_label("modules")
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
        {panelbody_header}
        {panel_lesson_grid_section}
        {panel_lesson_category_section}
        {panel_lesson_tag_section}
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
