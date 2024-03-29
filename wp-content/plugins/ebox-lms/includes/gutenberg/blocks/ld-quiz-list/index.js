/**
 * ebox Block ld-quiz-list
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
} from "@wordpress/components";
import ServerSideRender from "@wordpress/server-side-render";
import { useMemo } from "@wordpress/element";

const block_key = "ebox/ld-quiz-list";
const block_title = sprintf(
  // translators: placeholder: Quiz.
  _x("ebox %s List", "placeholder: Quiz", "ebox"),
  ldlms_get_custom_label("quiz")
);

registerBlockType(block_key, {
  title: block_title,
  description: sprintf(
    // translators: placeholder: Quizzes.
    _x("This block shows a list of %s.", "placeholder: Quizzes", "ebox"),
    ldlms_get_custom_label("quizzes")
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
    lesson_id: {
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
    quiz_category_name: {
      type: "string",
      default: "",
    },
    quiz_cat: {
      type: "string",
      default: "",
    },
    quiz_categoryselector: {
      type: "boolean",
      default: false,
    },
    quiz_tag: {
      type: "string",
      default: "",
    },
    quiz_tag_id: {
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
        lesson_id,
        show_content,
        show_thumbnail,
        quiz_category_name,
        quiz_cat,
        quiz_categoryselector,
        quiz_tag,
        quiz_tag_id,
        category_name,
        cat,
        categoryselector,
        tag,
        tag_id,
        course_grid,
        col,
        preview_show,
        example_show,
      },
      setAttributes,
    } = props;

    let field_show_content = "";
    let field_show_thumbnail = "";
    let panel_quiz_grid_section = "";

    let course_grid_default = true;
    if (ldlms_settings["plugins"]["ebox-course-grid"]["enabled"] === true) {
      if (
        typeof course_grid !== "undefined" &&
        (course_grid == true || course_grid == false)
      ) {
        course_grid_default = course_grid;
      }

      let quiz_grid_section_open = false;
      if (course_grid_default == true) {
        quiz_grid_section_open = true;
      }
      panel_quiz_grid_section = (
        <PanelBody
          title={__("Grid Settings", "ebox")}
          initialOpen={quiz_grid_section_open}
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
        <TextControl
          label={sprintf(
            // translators: placeholder: Lesson.
            _x("%s ID", "placeholder: Lesson", "ebox"),
            ldlms_get_custom_label("lesson")
          )}
          help={sprintf(
            // translators: placeholders: Lesson, Course.
            _x(
              "Enter single %1$s ID to limit listing. Leave blank if used within a %2$s. Zero for global.",
              "placeholders: Lesson, Course",
              "ebox"
            ),
            ldlms_get_custom_label("lesson"),
            ldlms_get_custom_label("course")
          )}
          value={lesson_id || ""}
          type={"number"}
          onChange={function (new_lesson_id) {
            if (new_lesson_id != "" && new_lesson_id < 0) {
              setAttributes({ lesson_id: "0" });
            } else {
              setAttributes({ lesson_id: new_lesson_id });
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
            // translators: placeholder: Quizzes.
            _x("%s per page", "placeholder: Quizzes", "ebox"),
            ldlms_get_custom_label("quizzes")
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

    let panel_quiz_category_section = "";
    if (
      ldlms_settings["settings"]["quizzes_taxonomies"]["ld_quiz_category"] ===
      "yes"
    ) {
      let panel_quiz_category_section_open = false;
      if (quiz_category_name != "" || quiz_cat != "") {
        panel_quiz_category_section_open = true;
      }
      panel_quiz_category_section = (
        <PanelBody
          title={sprintf(
            // translators: placeholder: Quiz.
            _x("%s Category Settings", "placeholder: Quiz", "ebox"),
            ldlms_get_custom_label("quiz")
          )}
          initialOpen={panel_quiz_category_section_open}
        >
          <TextControl
            label={sprintf(
              // translators: placeholder: Quiz.
              _x("%s Category Slug", "placeholder: Quiz", "ebox"),
              ldlms_get_custom_label("quiz")
            )}
            help={sprintf(
              // translators: placeholder: Quizzes.
              _x(
                "shows %s with mentioned category slug.",
                "placeholder: Quizzes",
                "ebox"
              ),
              ldlms_get_custom_label("quizzes")
            )}
            value={quiz_category_name || ""}
            onChange={(quiz_category_name) =>
              setAttributes({ quiz_category_name })
            }
          />

          <TextControl
            label={sprintf(
              // translators: placeholder: Quiz.
              _x("%s Category ID", "placeholder: Quiz", "ebox"),
              ldlms_get_custom_label("quiz")
            )}
            help={sprintf(
              // translators: placeholder: Quizzes.
              _x(
                "shows %s with mentioned category ID.",
                "placeholder: Quizzes",
                "ebox"
              ),
              ldlms_get_custom_label("quizzes")
            )}
            value={quiz_cat || ""}
            type={"number"}
            onChange={function (new_quiz_cat) {
              if (new_quiz_cat != "" && new_quiz_cat < 0) {
                setAttributes({ quiz_cat: "0" });
              } else {
                setAttributes({ quiz_cat: new_quiz_cat });
              }
            }}
          />
          <ToggleControl
            label={sprintf(
              // translators: placeholder: Quiz.
              _x("%s Category Selector", "placeholder: Quiz", "ebox"),
              ldlms_get_custom_label("quiz")
            )}
            help={sprintf(
              // translators: placeholder: Quizzes.
              _x(
                "shows a %s category dropdown.",
                "placeholder: Quizzes",
                "ebox"
              ),
              ldlms_get_custom_label("quizzes")
            )}
            checked={!!quiz_categoryselector}
            onChange={(quiz_categoryselector) =>
              setAttributes({ quiz_categoryselector })
            }
          />
        </PanelBody>
      );
    }

    let panel_quiz_tag_section = "";
    if (
      ldlms_settings["settings"]["quizzes_taxonomies"]["ld_quiz_tag"] === "yes"
    ) {
      let panel_quiz_tag_section_open = false;
      if (quiz_tag != "" || quiz_tag_id != "") {
        panel_quiz_tag_section_open = true;
      }
      panel_quiz_tag_section = (
        <PanelBody
          title={sprintf(
            // translators: placeholder: Quiz.
            _x("%s Tag Settings", "placeholder: Quiz", "ebox"),
            ldlms_get_custom_label("quiz")
          )}
          initialOpen={panel_quiz_tag_section_open}
        >
          <TextControl
            label={sprintf(
              // translators: placeholder: Quiz.
              _x("%s Tag Slug", "placeholder: Quiz", "ebox"),
              ldlms_get_custom_label("quiz")
            )}
            help={sprintf(
              // translators: placeholder: Quizzes.
              _x(
                "shows %s with mentioned tag slug.",
                "placeholder: Quizzes",
                "ebox"
              ),
              ldlms_get_custom_label("quizzes")
            )}
            value={quiz_tag || ""}
            onChange={(quiz_tag) => setAttributes({ quiz_tag })}
          />

          <TextControl
            label={sprintf(
              // translators: placeholder: Quiz.
              _x("%s Tag ID", "placeholder: Quiz", "ebox"),
              ldlms_get_custom_label("quiz")
            )}
            help={sprintf(
              // translators: placeholder: Quizzes.
              _x(
                "shows %s with mentioned tag ID.",
                "placeholder: Quizzes",
                "ebox"
              ),
              ldlms_get_custom_label("quizzes")
            )}
            value={quiz_tag_id || ""}
            type={"number"}
            onChange={function (new_quiz_tag_id) {
              if (new_quiz_tag_id != "" && new_quiz_tag_id < 0) {
                setAttributes({ quiz_tag_id: "0" });
              } else {
                setAttributes({ quiz_tag_id: new_quiz_tag_id });
              }
            }}
          />
        </PanelBody>
      );
    }

    let panel_wp_category_section = "";
    if (
      ldlms_settings["settings"]["quizzes_taxonomies"]["wp_post_category"] ===
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
              // translators: placeholder: Quizzes.
              _x(
                "shows %s with mentioned WP Category slug.",
                "placeholder: Quizzes",
                "ebox"
              ),
              ldlms_get_custom_label("quizzes")
            )}
            value={category_name || ""}
            onChange={(category_name) => setAttributes({ category_name })}
          />

          <TextControl
            label={sprintf(
              // translators: placeholder: Quiz.
              _x("%s Category ID", "placeholder: Quiz", "ebox"),
              ldlms_get_custom_label("quiz")
            )}
            help={sprintf(
              // translators: placeholder: Quizzes.
              _x(
                "shows %s with mentioned category ID.",
                "placeholder: Quizzes",
                "ebox"
              ),
              ldlms_get_custom_label("quizzes")
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
      ldlms_settings["settings"]["quizzes_taxonomies"]["wp_post_tag"] === "yes"
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
              // translators: placeholder: Quizzes.
              _x(
                "shows %s with mentioned WP tag slug.",
                "placeholder: Quizzes",
                "ebox"
              ),
              ldlms_get_custom_label("quizzes")
            )}
            value={tag || ""}
            onChange={(tag) => setAttributes({ tag })}
          />

          <TextControl
            label={__("WP Tag ID", "ebox")}
            help={sprintf(
              // translators: placeholder: Quizzes.
              _x(
                "shows %s with mentioned WP tag ID.",
                "placeholder: Quizzes",
                "ebox"
              ),
              ldlms_get_custom_label("quizzes")
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
      </PanelBody>
    );

    const inspectorControls = (
      <InspectorControls key="controls">
        {panelbody_header}
        {panel_quiz_grid_section}
        {panel_quiz_category_section}
        {panel_quiz_tag_section}
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

  save: (props) => {},
});
