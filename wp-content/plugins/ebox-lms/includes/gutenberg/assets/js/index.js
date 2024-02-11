!(function () {
  "use strict";
  var e = {
      n: function (t) {
        var a =
          t && t.__esModule
            ? function () {
                return t.default;
              }
            : function () {
                return t;
              };
        return e.d(a, { a: a }), a;
      },
      d: function (t, a) {
        for (var l in a)
          e.o(a, l) &&
            !e.o(t, l) &&
            Object.defineProperty(t, l, { enumerable: !0, get: a[l] });
      },
      o: function (e, t) {
        return Object.prototype.hasOwnProperty.call(e, t);
      },
    },
    t = window.wp.element,
    a = window.wp.i18n,
    l = window.wp.blocks,
    r = window.wp.blockEditor,
    s = window.wp.components,
    n = window.wp.serverSideRender,
    o = e.n(n);
  const i = "ebox/ld-login",
    d = (0, a.__)("ebox Login", "ebox");
  function u() {
    let e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "";
    return "" !== e && void 0 !== ldlms_settings.meta.post[e]
      ? ldlms_settings.meta.post[e]
      : void 0 !== ldlms_settings.meta.post
      ? ldlms_settings.meta.post
      : void 0;
  }
  function c() {
    let e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "";
    return void 0 !== ldlms_settings.meta.post &&
      "" !== e &&
      void 0 !== ldlms_settings.settings.custom_labels[e]
      ? ldlms_settings.settings.custom_labels[e]
      : e;
  }
  function p() {
    let e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "";
    if ("" !== e) {
      if (void 0 !== ldlms_settings.settings.per_page[e])
        return ldlms_settings.settings.per_page[e];
    } else if (void 0 !== ldlms_settings.meta.posts_per_page)
      return ldlms_settings.meta.posts_per_page;
  }
  function _(e) {
    if (void 0 === e) return 0;
    const t = parseInt(e);
    return isNaN(t) ? 0 : t;
  }
  function h() {
    return void 0 !== ldlms_settings.templates.active
      ? ldlms_settings.templates.active
      : "";
  }
  function g() {
    let e = (function () {
      if ("legacy" == h()) {
        let e = (function () {
          if (void 0 !== ldlms_settings.templates.list) {
            let e = h();
            if (
              void 0 !== e &&
              "" !== e &&
              void 0 !== ldlms_settings.templates.list[e]
            )
              return ldlms_settings.templates.list[e];
          }
          return "";
        })();
        return sprintf(
          // translators: placeholder: current template name.
          (0, a._x)(
            'The current ebox template "%s" does not support this block. Please select a different template.',
            "placeholder: current template name",
            "ebox"
          ),
          e
        );
      }
      return "";
    })();
    return "" !== e
      ? (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Warning", "ebox"), opened: !0 },
          (0, t.createElement)(s.TextControl, {
            help: e,
            value: "",
            type: "hidden",
            className: "notice notice-error",
          })
        )
      : "";
  }
  (0, l.registerBlockType)(i, {
    title: d,
    description: (0, a.__)(
      "This block adds the login button on any page",
      "ebox"
    ),
    icon: "admin-network",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      login_url: { type: "string", default: "" },
      login_label: { type: "string", default: "" },
      login_placement: { type: "string", default: "" },
      login_button: { type: "string", default: "" },
      logout_url: { type: "string", default: "" },
      logout_label: { type: "string", default: "" },
      logout_placement: { type: "string", default: "right" },
      logout_button: { type: "string", default: "" },
      preview_show: { type: "boolean", default: !0 },
      preview_action: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
    },
    edit: function (e) {
      const {
          attributes: {
            login_url: l,
            login_label: n,
            login_placement: u,
            login_button: c,
            logout_url: p,
            logout_label: _,
            logout_placement: h,
            logout_button: g,
            preview_show: m,
            preview_action: b,
            example_show: y,
          },
          setAttributes: f,
        } = e,
        w = (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Login Settings", "ebox") },
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Login URL", "ebox"),
            help: (0, a.__)("Override default login URL", "ebox"),
            value: l || "",
            onChange: (e) => f({ login_url: e }),
          }),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Login Label", "ebox"),
            help: (0, a.__)('Override default label "Login"', "ebox"),
            value: n || "",
            onChange: (e) => f({ login_label: e }),
          }),
          (0, t.createElement)(s.SelectControl, {
            key: "login_placement",
            label: (0, a.__)("Login Icon Placement", "ebox"),
            value: u,
            options: [
              {
                label: (0, a.__)("Left - To left of label", "ebox"),
                value: "",
              },
              {
                label: (0, a.__)("Right - To right of label", "ebox"),
                value: "right",
              },
              {
                label: (0, a.__)("None - No icon", "ebox"),
                value: "none",
              },
            ],
            onChange: (e) => f({ login_placement: e }),
          }),
          (0, t.createElement)(s.SelectControl, {
            key: "login_button",
            label: (0, a.__)("Login Displayed as", "ebox"),
            help: (0, a.__)("Display as Button or link", "ebox"),
            value: c,
            options: [
              { label: (0, a.__)("Button", "ebox"), value: "" },
              { label: (0, a.__)("Link", "ebox"), value: "link" },
            ],
            onChange: (e) => f({ login_button: e }),
          })
        ),
        v = (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Logout Settings", "ebox") },
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Logout URL", "ebox"),
            help: (0, a.__)("Override default logout URL", "ebox"),
            value: p || "",
            onChange: (e) => f({ logout_url: e }),
          }),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Logout Label", "ebox"),
            help: (0, a.__)('Override default label "Logout"', "ebox"),
            value: _ || "",
            onChange: (e) => f({ logout_label: e }),
          }),
          (0, t.createElement)(s.SelectControl, {
            key: "logout_placement",
            label: (0, a.__)("Logout Icon Placement", "ebox"),
            value: h,
            options: [
              {
                label: (0, a.__)("Left - To left of label", "ebox"),
                value: "left",
              },
              {
                label: (0, a.__)("Right - To right of label", "ebox"),
                value: "right",
              },
              {
                label: (0, a.__)("None - No icon", "ebox"),
                value: "none",
              },
            ],
            onChange: (e) => f({ logout_placement: e }),
          }),
          (0, t.createElement)(s.SelectControl, {
            key: "logout_button",
            label: (0, a.__)("Logout Displayed as", "ebox"),
            help: (0, a.__)("Display as Button or link", "ebox"),
            value: g,
            options: [
              { label: (0, a.__)("Button", "ebox"), value: "" },
              { label: (0, a.__)("Link", "ebox"), value: "link" },
            ],
            onChange: (e) => f({ logout_button: e }),
          })
        ),
        C = (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!m,
            onChange: (e) => f({ preview_show: e }),
          }),
          (0, t.createElement)(
            s.PanelRow,
            { className: "ebox-block-error-message" },
            (0, a.__)("Preview settings are not saved.", "ebox")
          ),
          (0, t.createElement)(s.SelectControl, {
            key: "preview_action",
            label: (0, a.__)("Preview Action", "ebox"),
            value: b,
            options: [
              { label: (0, a.__)("Login", "ebox"), value: "login" },
              { label: (0, a.__)("Logout", "ebox"), value: "logout" },
            ],
            onChange: (e) => f({ preview_action: e }),
          })
        );
      function E() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          d
        );
      }
      function x(e) {
        return E();
      }
      return [
        (0, t.createElement)(r.InspectorControls, { key: "controls" }, w, v, C),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? (0, t.createElement)(o(), {
                block: i,
                attributes: a,
                key: i,
                EmptyResponsePlaceholder: x,
              })
            : E();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {},
  });
  const m = "ebox/ld-profile",
    b = (0, a.__)("ebox Profile", "ebox");
  (0, l.registerBlockType)(m, {
    title: b,
    description: (0, a.sprintf)(
      // translators: placeholders: Courses, Course, Quiz.
      (0, a._x)(
        "Displays user's enrolled %1$s, %2$s progress, %3$s scores, and achieved certificates.",
        "placeholders: Courses, Course, Quiz",
        "ebox"
      ),
      c("courses"),
      c("course"),
      c("quiz")
    ),
    icon: "id-alt",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      per_page: { type: "string", default: "" },
      orderby: { type: "string", default: "ID" },
      order: { type: "string", default: "DESC" },
      course_points_user: { type: "boolean", default: 1 },
      expand_all: { type: "boolean", default: 0 },
      profile_link: { type: "boolean", default: 1 },
      show_header: { type: "boolean", default: 1 },
      show_search: { type: "boolean", default: 1 },
      show_quizzes: { type: "boolean", default: 1 },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
      quiz_num: { type: "string", default: "" },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      const {
          attributes: {
            per_page: l,
            orderby: n,
            order: i,
            course_points_user: d,
            expand_all: _,
            profile_link: h,
            show_header: g,
            show_search: y,
            show_quizzes: f,
            preview_user_id: w,
            preview_show: v,
            quiz_num: C,
            example_show: E,
          },
          setAttributes: x,
        } = e,
        k = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)("%s per page", "placeholder: Courses", "ebox"),
                c("courses")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: per_page.
                (0, a._x)(
                  "Leave empty for default (%d) or 0 to show all items.",
                  "placeholder: per_page",
                  "ebox"
                ),
                p("per_page")
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                x("" != e && e < 0 ? { per_page: "0" } : { per_page: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholders: Quiz, Course.
                (0, a._x)(
                  "%1$s attempts per %2$s",
                  "placeholders: Quiz, Course",
                  "ebox"
                ),
                c("quiz"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: per_page.
                (0, a._x)(
                  "Leave empty for default (%d) or 0 to show all attempts.",
                  "placeholder: per_page",
                  "ebox"
                ),
                p("per_page")
              ),
              value: C || "",
              type: "number",
              onChange: function (e) {
                x("" != e && e < 0 ? { quiz_num: "0" } : { quiz_num: e });
              },
            }),
            (0, t.createElement)(s.SelectControl, {
              key: "orderby",
              label: (0, a.__)("Order by", "ebox"),
              value: n,
              options: [
                {
                  label: (0, a.__)("ID - Order by post id. (default)", "ebox"),
                  value: "ID",
                },
                {
                  label: (0, a.__)("Title - Order by post title", "ebox"),
                  value: "title",
                },
                {
                  label: (0, a.__)("Date - Order by post date", "ebox"),
                  value: "date",
                },
                {
                  label: (0, a.__)("Menu - Order by Page Order Value", "ebox"),
                  value: "menu_order",
                },
              ],
              onChange: (e) => x({ orderby: e }),
            }),
            (0, t.createElement)(s.SelectControl, {
              key: "order",
              label: (0, a.__)("Order", "ebox"),
              value: i,
              options: [
                {
                  label: (0, a.__)(
                    "DESC - highest to lowest values (default)",
                    "ebox"
                  ),
                  value: "DESC",
                },
                {
                  label: (0, a.__)("ASC - lowest to highest values", "ebox"),
                  value: "ASC",
                },
              ],
              onChange: (e) => x({ order: e }),
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Search", "ebox"),
              checked: !!y,
              onChange: (e) => x({ show_search: e }),
              help: (0, a.__)("LD30 template only", "ebox"),
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Profile Header", "ebox"),
              checked: !!g,
              onChange: (e) => x({ show_header: e }),
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)(
                  "Show Earned %s Points",
                  "placeholder: Course",
                  "ebox"
                ),
                c("course")
              ),
              checked: !!d,
              onChange: (e) => x({ course_points_user: e }),
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Profile Link", "ebox"),
              checked: !!h,
              onChange: (e) => x({ profile_link: e }),
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("Show User %s Attempts", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              checked: !!f,
              onChange: (e) => x({ show_quizzes: e }),
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)(
                  "Expand All %s Sections",
                  "placeholder: Course",
                  "ebox"
                ),
                c("course")
              ),
              checked: !!_,
              onChange: (e) => x({ expand_all: e }),
            })
          ),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Preview", "ebox"),
              checked: !!v,
              onChange: (e) => x({ preview_show: e }),
            }),
            (0, t.createElement)(
              s.PanelRow,
              { className: "ebox-block-error-message" },
              (0, a.__)("Preview settings are not saved.", "ebox")
            ),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Preview User ID", "ebox"),
              help: (0, a.__)("Enter a User ID to test preview", "ebox"),
              value: w || "",
              type: "number",
              onChange: function (e) {
                x(
                  "" != e && e < 0
                    ? { preview_user_id: "0" }
                    : { preview_user_id: e }
                );
              },
            })
          )
        );
      function T() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          b
        );
      }
      function P(e) {
        return T();
      }
      return [
        k,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: m,
                attributes: a,
                key: m,
                EmptyResponsePlaceholder: P,
              }))
            : T();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const y = "ebox/ld-course-list",
    f = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox %s List", "placeholder: Course", "ebox"),
      c("course")
    );
  (0, l.registerBlockType)(y, {
    title: f,
    description: (0, a.sprintf)(
      // translators: placeholder: Courses.
      (0, a._x)(
        "This block shows a list of %s.",
        "placeholder: Courses",
        "ebox"
      ),
      c("courses")
    ),
    icon: "list-view",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      orderby: { type: "string", default: "ID" },
      order: { type: "string", default: "DESC" },
      per_page: { type: "string", default: "" },
      mycourses: { type: "string", default: "" },
      status: {
        type: "array",
        default: ["not_started", "in_progress", "completed"],
      },
      show_content: { type: "boolean", default: !0 },
      show_thumbnail: { type: "boolean", default: !0 },
      course_category_name: { type: "string", default: "" },
      course_cat: { type: "string", default: "" },
      course_categoryselector: { type: "boolean", default: !1 },
      course_tag: { type: "string", default: "" },
      course_tag_id: { type: "string", default: "" },
      category_name: { type: "string", default: "" },
      cat: { type: "string", default: "" },
      categoryselector: { type: "boolean", default: !1 },
      tag: { type: "string", default: "" },
      tag_id: { type: "string", default: "" },
      course_grid: { type: "boolean" },
      progress_bar: { type: "boolean", default: !1 },
      col: {
        type: "integer",
        default: ldlms_settings.plugins["ebox-course-grid"].col_default || 3,
      },
      price_type: {
        type: "array",
        default: ["open", "free", "paynow", "subscribe", "closed"],
      },
      preview_show: { type: "boolean", default: !0 },
      preview_user_id: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      const {
        attributes: {
          orderby: l,
          order: n,
          per_page: i,
          mycourses: d,
          status: _,
          show_content: h,
          show_thumbnail: g,
          course_category_name: m,
          course_cat: b,
          course_categoryselector: w,
          course_tag: v,
          course_tag_id: C,
          category_name: E,
          cat: x,
          categoryselector: k,
          tag: T,
          tag_id: P,
          course_grid: D,
          progress_bar: I,
          col: S,
          preview_user_id: z,
          preview_show: q,
          example_show: B,
          price_type: L,
        },
        setAttributes: O,
      } = e;
      let N = "",
        U = "",
        A = "",
        $ = !0;
      if (!0 === ldlms_settings.plugins["ebox-course-grid"].enabled) {
        void 0 === D || (1 != D && 0 != D) || ($ = D);
        let e = !1;
        1 == $ && (e = !0),
          (A = (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Grid Settings", "ebox"), initialOpen: e },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Grid", "ebox"),
              checked: !!$,
              onChange: (e) => O({ course_grid: e }),
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Progress Bar", "ebox"),
              checked: !!I,
              onChange: (e) => O({ progress_bar: e }),
            }),
            (0, t.createElement)(s.RangeControl, {
              label: (0, a.__)("Columns", "ebox"),
              value:
                S || ldlms_settings.plugins["ebox-course-grid"].col_default,
              min: 1,
              max: ldlms_settings.plugins["ebox-course-grid"].col_max,
              step: 1,
              onChange: (e) => O({ col: e }),
            })
          ));
      }
      (N = (0, t.createElement)(s.ToggleControl, {
        label: (0, a.__)("Show Content", "ebox"),
        checked: !!h,
        onChange: (e) => O({ show_content: e }),
      })),
        (U = (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Thumbnail", "ebox"),
          checked: !!g,
          onChange: (e) => O({ show_thumbnail: e }),
        }));
      const G = (0, t.createElement)(
        s.PanelBody,
        {
          className:
            "ebox-block-controls-panel ebox-block-controls-panel-ld-course-list",
          title: (0, a.__)("Settings", "ebox"),
        },
        (0, t.createElement)(s.SelectControl, {
          key: "orderby",
          label: (0, a.__)("Order by", "ebox"),
          value: l,
          options: [
            {
              label: (0, a.__)("ID - Order by post id. (default)", "ebox"),
              value: "ID",
            },
            {
              label: (0, a.__)("Title - Order by post title", "ebox"),
              value: "title",
            },
            {
              label: (0, a.__)("Date - Order by post date", "ebox"),
              value: "date",
            },
            {
              label: (0, a.__)("Menu - Order by Page Order Value", "ebox"),
              value: "menu_order",
            },
          ],
          onChange: (e) => O({ orderby: e }),
        }),
        (0, t.createElement)(s.SelectControl, {
          key: "order",
          label: (0, a.__)("Order", "ebox"),
          value: n,
          options: [
            {
              label: (0, a.__)(
                "DESC - highest to lowest values (default)",
                "ebox"
              ),
              value: "DESC",
            },
            {
              label: (0, a.__)("ASC - lowest to highest values", "ebox"),
              value: "ASC",
            },
          ],
          onChange: (e) => O({ order: e }),
        }),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Courses.
            (0, a._x)("%s per page", "placeholder: Courses", "ebox"),
            c("courses")
          ),
          help: (0, a.sprintf)(
            // translators: placeholder: default per page.
            (0, a._x)(
              "Leave empty for default (%d) or 0 to show all items.",
              "placeholder: default per page",
              "ebox"
            ),
            p("per_page")
          ),
          value: i || "",
          type: "number",
          onChange: function (e) {
            O("" != e && e < 0 ? { per_page: "0" } : { per_page: e });
          },
        }),
        (0, t.createElement)(s.SelectControl, {
          multiple: !0,
          key: "price_type",
          label: (0, a.sprintf)(
            // translators: placeholder: Course Access Mode(s).
            (0, a._x)(
              "%s Access Mode(s)",
              "placeholder: Course Access Mode(s)",
              "ebox"
            ),
            c("course")
          ),
          help: (0, a.__)("Ctrl+click to deselect selected items.", "ebox"),
          value: L,
          options: [
            { label: (0, a.__)("Open", "ebox"), value: "open" },
            { label: (0, a.__)("Free", "ebox"), value: "free" },
            { label: (0, a.__)("Buy Now", "ebox"), value: "paynow" },
            { label: (0, a.__)("Recurring", "ebox"), value: "subscribe" },
            { label: (0, a.__)("Closed", "ebox"), value: "closed" },
          ],
          onChange: (e) => O({ price_type: e }),
        }),
        (0, t.createElement)(s.SelectControl, {
          key: "mycourses",
          label: (0, a.sprintf)(
            // translators: placeholder: Courses.
            (0, a._x)("My %s", "placeholder: Courses", "ebox"),
            c("courses")
          ),
          value: d,
          options: [
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "Show All %s (default)",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: "",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "Show Enrolled %s only",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: "enrolled",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "Show not-Enrolled %s only",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: "not-enrolled",
            },
          ],
          onChange: (e) => O({ mycourses: e }),
        }),
        "enrolled" === d &&
          (0, t.createElement)(s.SelectControl, {
            multiple: !0,
            key: "status",
            label: (0, a.sprintf)(
              // translators: placeholder: Courses.
              (0, a._x)("Enrolled %s Status", "placeholder: Courses", "ebox"),
              c("courses")
            ),
            help: (0, a.__)("Ctrl+click to deselect selected items.", "ebox"),
            value: _,
            options: [
              {
                label: (0, a.__)("Not Started", "ebox"),
                value: "not_started",
              },
              {
                label: (0, a.__)("In Progress", "ebox"),
                value: "in_progress",
              },
              {
                label: (0, a.__)("Completed", "ebox"),
                value: "completed",
              },
            ],
            onChange: (e) => O({ status: e }),
          }),
        N,
        U
      );
      let R = "";
      if (
        "yes" === ldlms_settings.settings.courses_taxonomies.ld_course_category
      ) {
        let e = !1;
        ("" == m && "" == b) || (e = !0),
          (R = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)(
                  "%s Category Settings",
                  "placeholder: Course",
                  "ebox"
                ),
                c("course")
              ),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Category Slug", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "shows %s with mentioned category slug.",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: m || "",
              onChange: (e) => O({ course_category_name: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Category ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "shows %s with mentioned category ID.",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: b || "",
              type: "number",
              onChange: function (e) {
                O("" != e && e < 0 ? { course_cat: "0" } : { course_cat: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)(
                  "%s Category Selector",
                  "placeholder: Course",
                  "ebox"
                ),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "shows a %s category dropdown.",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              checked: !!w,
              onChange: (e) => O({ course_categoryselector: e }),
            })
          ));
      }
      let Q = "";
      if ("yes" === ldlms_settings.settings.courses_taxonomies.ld_course_tag) {
        let e = !1;
        ("" == v && "" == C) || (e = !0),
          (Q = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Tag Settings", "placeholder: Course", "ebox"),
                c("course")
              ),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Tag Slug", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "shows %s with mentioned tag slug.",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: v || "",
              onChange: (e) => O({ course_tag: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Tag ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "shows %s with mentioned tag ID.",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: C || "",
              type: "number",
              onChange: function (e) {
                O(
                  "" != e && e < 0
                    ? { course_tag_id: "0" }
                    : { course_tag_id: e }
                );
              },
            })
          ));
      }
      let M = "";
      if (
        "yes" === ldlms_settings.settings.courses_taxonomies.wp_post_category
      ) {
        let e = !1;
        ("" == E && "" == x) || (e = !0),
          (M = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.__)("WP Category Settings", "ebox"),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Category Slug", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "shows %s with mentioned WP Category slug.",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: E || "",
              onChange: (e) => O({ category_name: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Category ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "shows %s with mentioned category ID.",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: x || "",
              type: "number",
              onChange: function (e) {
                O("" != e && e < 0 ? { cat: "0" } : { cat: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("WP Category Selector", "ebox"),
              help: (0, a.__)("shows a WP category dropdown.", "ebox"),
              checked: !!k,
              onChange: (e) => O({ categoryselector: e }),
            })
          ));
      }
      let W = "";
      if ("yes" === ldlms_settings.settings.courses_taxonomies.wp_post_tag) {
        let e = !1;
        ("" == T && "" == P) || (e = !0),
          (W = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.__)("WP Tag Settings", "ebox"),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Tag Slug", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "shows %s with mentioned WP tag slug.",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: T || "",
              onChange: (e) => O({ tag: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Tag ID", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Courses.
                (0, a._x)(
                  "shows %s with mentioned WP tag ID.",
                  "placeholder: Courses",
                  "ebox"
                ),
                c("courses")
              ),
              value: P || "",
              type: "number",
              onChange: function (e) {
                O("" != e && e < 0 ? { tag_id: "0" } : { tag_id: e });
              },
            })
          ));
      }
      const j = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
        (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Preview", "ebox"),
          checked: !!q,
          onChange: (e) => O({ preview_show: e }),
        }),
        (0, t.createElement)(
          s.PanelRow,
          { className: "ebox-block-error-message" },
          (0, a.__)("Preview settings are not saved.", "ebox")
        ),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Preview User ID", "ebox"),
          help: (0, a.__)("Enter a User ID to test preview", "ebox"),
          value: z || "",
          type: "number",
          onChange: function (e) {
            O(
              "" != e && e < 0
                ? { preview_user_id: "0" }
                : { preview_user_id: e }
            );
          },
        })
      );
      function F() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          f
        );
      }
      function V(e) {
        return F();
      }
      return [
        (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          G,
          A,
          R,
          Q,
          M,
          W,
          j
        ),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: y,
                attributes: a,
                key: y,
                EmptyResponsePlaceholder: V,
              }))
            : F();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const w = "ebox/ld-lesson-list",
    v = (0, a.sprintf)(
      // translators: placeholder: Lesson.
      (0, a._x)("ebox %s List", "placeholder: Lesson", "ebox"),
      c("lesson")
    );
  (0, l.registerBlockType)(w, {
    title: v,
    description: (0, a.sprintf)(
      // translators: placeholder: modules.
      (0, a._x)(
        "This block shows a list of %s.",
        "placeholder: modules",
        "ebox"
      ),
      c("modules")
    ),
    icon: "list-view",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      orderby: { type: "string", default: "ID" },
      order: { type: "string", default: "DESC" },
      per_page: { type: "string", default: "" },
      course_id: { type: "string", default: "" },
      show_content: { type: "boolean", default: !0 },
      show_thumbnail: { type: "boolean", default: !0 },
      lesson_category_name: { type: "string", default: "" },
      lesson_cat: { type: "string", default: "" },
      lesson_categoryselector: { type: "boolean", default: !1 },
      lesson_tag: { type: "string", default: "" },
      lesson_tag_id: { type: "string", default: "" },
      category_name: { type: "string", default: "" },
      cat: { type: "string", default: "" },
      categoryselector: { type: "boolean", default: !1 },
      tag: { type: "string", default: "" },
      tag_id: { type: "string", default: "" },
      course_grid: { type: "boolean" },
      col: {
        type: "integer",
        default:
          ldlms_settings.plugins["ebox-course-grid"].enabled.col_default || 3,
      },
      preview_show: { type: "boolean", default: !0 },
      preview_user_id: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      const {
        attributes: {
          orderby: l,
          order: n,
          per_page: i,
          course_id: d,
          show_content: _,
          show_thumbnail: h,
          lesson_category_name: g,
          lesson_cat: m,
          lesson_categoryselector: b,
          lesson_tag: y,
          lesson_tag_id: f,
          category_name: C,
          cat: E,
          categoryselector: x,
          tag: k,
          tag_id: T,
          course_grid: P,
          col: D,
          preview_show: I,
          preview_user_id: S,
          example_show: z,
        },
        setAttributes: q,
      } = e;
      let B = "",
        L = "",
        O = "",
        N = !0;
      if (!0 === ldlms_settings.plugins["ebox-course-grid"].enabled) {
        void 0 === P || (1 != P && 0 != P) || (N = P);
        let e = !1;
        1 == N && (e = !0),
          (O = (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Grid Settings", "ebox"), initialOpen: e },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Grid", "ebox"),
              checked: !!N,
              onChange: (e) => q({ course_grid: e }),
            }),
            (0, t.createElement)(s.RangeControl, {
              label: (0, a.__)("Columns", "ebox"),
              value:
                D ||
                ldlms_settings.plugins["ebox-course-grid"].enabled.col_default,
              min: 1,
              max: ldlms_settings.plugins["ebox-course-grid"].enabled.col_max,
              step: 1,
              onChange: (e) => q({ col: e }),
            })
          ));
      }
      (B = (0, t.createElement)(s.ToggleControl, {
        label: (0, a.__)("Show Content", "ebox"),
        checked: !!_,
        onChange: (e) => q({ show_content: e }),
      })),
        (L = (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Thumbnail", "ebox"),
          checked: !!h,
          onChange: (e) => q({ show_thumbnail: e }),
        }));
      const U = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Settings", "ebox") },
        (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Course.
            (0, a._x)("%s ID", "placeholder: Course", "ebox"),
            c("course")
          ),
          help: (0, a.sprintf)(
            // translators: placeholders: Course, Course.
            (0, a._x)(
              "Enter single %1$s ID to limit listing. Leave blank if used within a %2$s.",
              "placeholders: Course, Course",
              "ebox"
            ),
            c("course"),
            c("course")
          ),
          value: d || "",
          type: "number",
          onChange: function (e) {
            q("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
          },
        }),
        (0, t.createElement)(s.SelectControl, {
          key: "orderby",
          label: (0, a.__)("Order by", "ebox"),
          value: l,
          options: [
            {
              label: (0, a.__)("ID - Order by post id. (default)", "ebox"),
              value: "ID",
            },
            {
              label: (0, a.__)("Title - Order by post title", "ebox"),
              value: "title",
            },
            {
              label: (0, a.__)("Date - Order by post date", "ebox"),
              value: "date",
            },
            {
              label: (0, a.__)("Menu - Order by Page Order Value", "ebox"),
              value: "menu_order",
            },
          ],
          onChange: (e) => q({ orderby: e }),
        }),
        (0, t.createElement)(s.SelectControl, {
          key: "order",
          label: (0, a.__)("Order", "ebox"),
          value: n,
          options: [
            {
              label: (0, a.__)(
                "DESC - highest to lowest values (default)",
                "ebox"
              ),
              value: "DESC",
            },
            {
              label: (0, a.__)("ASC - lowest to highest values", "ebox"),
              value: "ASC",
            },
          ],
          onChange: (e) => q({ order: e }),
        }),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: modules.
            (0, a._x)("%s per page", "placeholder: modules", "ebox"),
            c("modules")
          ),
          help: (0, a.sprintf)(
            // translators: placeholder: per_page.
            (0, a._x)(
              "Leave empty for default (%d) or 0 to show all items.",
              "placeholder: per_page",
              "ebox"
            ),
            p("per_page")
          ),
          value: i || "",
          type: "number",
          onChange: function (e) {
            q("" != e && e < 0 ? { per_page: "0" } : { per_page: e });
          },
        }),
        B,
        L
      );
      let A = "";
      if (
        "yes" === ldlms_settings.settings.modules_taxonomies.ld_lesson_category
      ) {
        let e = !1;
        ("" == g && "" == m) || (e = !0),
          (A = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.sprintf)(
                // translators: placeholder: Lesson.
                (0, a._x)(
                  "%s Category Settings",
                  "placeholder: Lesson",
                  "ebox"
                ),
                c("lesson")
              ),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Lesson.
                (0, a._x)("%s Category Slug", "placeholder: Lesson", "ebox"),
                c("lesson")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: modules.
                (0, a._x)(
                  "shows %s with mentioned category slug.",
                  "placeholder: modules",
                  "ebox"
                ),
                c("modules")
              ),
              value: g || "",
              onChange: (e) => q({ lesson_category_name: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Lesson.
                (0, a._x)("%s Category ID", "placeholder: Lesson", "ebox"),
                c("lesson")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: modules.
                (0, a._x)(
                  "shows %s with mentioned category ID.",
                  "placeholder: modules",
                  "ebox"
                ),
                c("modules")
              ),
              value: m || "",
              type: "number",
              onChange: function (e) {
                q("" != e && e < 0 ? { lesson_cat: "0" } : { lesson_cat: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Lesson.
                (0, a._x)(
                  "%s Category Selector",
                  "placeholder: Lesson",
                  "ebox"
                ),
                c("lesson")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: modules.
                (0, a._x)(
                  "shows a %s category dropdown.",
                  "placeholder: modules",
                  "ebox"
                ),
                c("modules")
              ),
              checked: !!b,
              onChange: (e) => q({ lesson_categoryselector: e }),
            })
          ));
      }
      let $ = "";
      if ("yes" === ldlms_settings.settings.modules_taxonomies.ld_lesson_tag) {
        let e = !1;
        ("" == y && "" == f) || (e = !0),
          ($ = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.sprintf)(
                // translators: placeholder: Lesson.
                (0, a._x)("%s Tag Settings", "placeholder: Lesson", "ebox"),
                c("lesson")
              ),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Lesson.
                (0, a._x)("%s Tag Slug", "placeholder: Lesson", "ebox"),
                c("lesson")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: modules.
                (0, a._x)(
                  "shows %s with mentioned tag slug.",
                  "placeholder: modules",
                  "ebox"
                ),
                c("modules")
              ),
              value: y || "",
              onChange: (e) => q({ lesson_tag: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Lesson.
                (0, a._x)("%s Tag ID", "placeholder: Lesson", "ebox"),
                c("lesson")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: modules.
                (0, a._x)(
                  "shows %s with mentioned tag ID.",
                  "placeholder: modules",
                  "ebox"
                ),
                c("modules")
              ),
              value: f || "",
              type: "number",
              onChange: function (e) {
                q(
                  "" != e && e < 0
                    ? { lesson_tag_id: "0" }
                    : { lesson_tag_id: e }
                );
              },
            })
          ));
      }
      let G = "";
      if (
        "yes" === ldlms_settings.settings.modules_taxonomies.wp_post_category
      ) {
        let e = !1;
        ("" == C && "" == E) || (e = !0),
          (G = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.__)("WP Category Settings", "ebox"),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Category Slug", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: modules.
                (0, a._x)(
                  "shows %s with mentioned WP Category slug.",
                  "placeholder: modules",
                  "ebox"
                ),
                c("modules")
              ),
              value: C || "",
              onChange: (e) => q({ category_name: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Lesson.
                (0, a._x)("%s Category ID", "placeholder: Lesson", "ebox"),
                c("lesson")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: modules.
                (0, a._x)(
                  "shows %s with mentioned category ID.",
                  "placeholder: modules",
                  "ebox"
                ),
                c("modules")
              ),
              value: E || "",
              type: "number",
              onChange: function (e) {
                q("" != e && e < 0 ? { cat: "0" } : { cat: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("WP Category Selector", "ebox"),
              help: (0, a.__)("shows a WP category dropdown.", "ebox"),
              checked: !!x,
              onChange: (e) => q({ categoryselector: e }),
            })
          ));
      }
      let R = "";
      if ("yes" === ldlms_settings.settings.modules_taxonomies.wp_post_tag) {
        let e = !1;
        ("" == k && "" == T) || (e = !0),
          (R = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.__)("WP Tag Settings", "ebox"),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Tag Slug", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: modules.
                (0, a._x)(
                  "shows %s with mentioned WP tag slug.",
                  "placeholder: modules",
                  "ebox"
                ),
                c("modules")
              ),
              value: k || "",
              onChange: (e) => q({ tag: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Tag ID", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: modules.
                (0, a._x)(
                  "shows %s with mentioned WP tag ID.",
                  "placeholder: modules",
                  "ebox"
                ),
                c("modules")
              ),
              value: T || "",
              type: "number",
              onChange: function (e) {
                q("" != e && e < 0 ? { tag_id: "0" } : { tag_id: e });
              },
            })
          ));
      }
      const Q = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
        (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Preview", "ebox"),
          checked: !!I,
          onChange: (e) => q({ preview_show: e }),
        }),
        (0, t.createElement)(
          s.PanelRow,
          { className: "ebox-block-error-message" },
          (0, a.__)("Preview settings are not saved.", "ebox")
        ),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Preview User ID", "ebox"),
          help: (0, a.__)("Enter a User ID to test preview", "ebox"),
          value: S || "",
          type: "number",
          onChange: function (e) {
            q(
              "" != e && e < 0
                ? { preview_user_id: "0" }
                : { preview_user_id: e }
            );
          },
        })
      );
      function M() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          v
        );
      }
      function W(e) {
        return M();
      }
      return [
        (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          U,
          O,
          A,
          $,
          G,
          R,
          Q
        ),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: w,
                attributes: a,
                key: w,
                EmptyResponsePlaceholder: W,
              }))
            : M();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const C = "ebox/ld-topic-list",
    E = (0, a.sprintf)(
      // translators: placeholder: Topic.
      (0, a._x)("ebox %s List", "placeholder: Topic", "ebox"),
      c("topic")
    );
  (0, l.registerBlockType)(C, {
    title: E,
    description: (0, a.sprintf)(
      // translators: placeholder: Topics.
      (0, a._x)(
        "This block shows a list of %s.",
        "placeholder: Topics",
        "ebox"
      ),
      c("topics")
    ),
    icon: "list-view",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      orderby: { type: "string", default: "ID" },
      order: { type: "string", default: "DESC" },
      per_page: { type: "string", default: "" },
      course_id: { type: "string", default: "" },
      lesson_id: { type: "string", default: "" },
      show_content: { type: "boolean", default: !0 },
      show_thumbnail: { type: "boolean", default: !0 },
      topic_category_name: { type: "string", default: "" },
      topic_cat: { type: "string", default: "" },
      topic_categoryselector: { type: "boolean", default: !1 },
      topic_tag: { type: "string", default: "" },
      topic_tag_id: { type: "string", default: "" },
      category_name: { type: "string", default: "" },
      cat: { type: "string", default: "" },
      categoryselector: { type: "boolean", default: !1 },
      tag: { type: "string", default: "" },
      tag_id: { type: "string", default: "" },
      course_grid: { type: "boolean" },
      col: {
        type: "integer",
        default:
          ldlms_settings.plugins["ebox-course-grid"].enabled.col_default || 3,
      },
      example_show: { type: "boolean", default: 0 },
      preview_show: { type: "boolean", default: !0 },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      const {
        attributes: {
          orderby: l,
          order: n,
          per_page: i,
          course_id: d,
          lesson_id: _,
          show_content: h,
          show_thumbnail: g,
          topic_category_name: m,
          topic_cat: b,
          topic_categoryselector: y,
          topic_tag: f,
          topic_tag_id: w,
          category_name: v,
          cat: x,
          categoryselector: k,
          tag: T,
          tag_id: P,
          course_grid: D,
          col: I,
          preview_show: S,
          example_show: z,
        },
        setAttributes: q,
      } = e;
      let B = "",
        L = "",
        O = "",
        N = !0;
      if (!0 === ldlms_settings.plugins["ebox-course-grid"].enabled) {
        void 0 === D || (1 != D && 0 != D) || (N = D);
        let e = !1;
        1 == N && (e = !0),
          (O = (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Grid Settings", "ebox"), initialOpen: e },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Grid", "ebox"),
              checked: !!N,
              onChange: (e) => q({ course_grid: e }),
            }),
            (0, t.createElement)(s.RangeControl, {
              label: (0, a.__)("Columns", "ebox"),
              value:
                I ||
                ldlms_settings.plugins["ebox-course-grid"].enabled.col_default,
              min: 1,
              max: ldlms_settings.plugins["ebox-course-grid"].enabled.col_max,
              step: 1,
              onChange: (e) => q({ col: e }),
            })
          ));
      }
      (B = (0, t.createElement)(s.ToggleControl, {
        label: (0, a.__)("Show Content", "ebox"),
        checked: !!h,
        onChange: (e) => q({ show_content: e }),
      })),
        (L = (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Thumbnail", "ebox"),
          checked: !!g,
          onChange: (e) => q({ show_thumbnail: e }),
        }));
      const U = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Settings", "ebox") },
        (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Course.
            (0, a._x)("%s ID", "placeholder: Course", "ebox"),
            c("course")
          ),
          help: (0, a.sprintf)(
            // translators: placeholders: Course, Course.
            (0, a._x)(
              "Enter single %1$s ID to limit listing. Leave blank if used within a %2$s.",
              "placeholders: Course, Course",
              "ebox"
            ),
            c("course"),
            c("course")
          ),
          value: d || "",
          type: "number",
          onChange: function (e) {
            q("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
          },
        }),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Lesson.
            (0, a._x)("%s ID", "placeholder: Lesson", "ebox"),
            c("lesson")
          ),
          help: (0, a.sprintf)(
            // translators: placeholders: Lesson, Course.
            (0, a._x)(
              "Enter single %1$s ID to limit listing. Leave blank if used within a %2$s.",
              "placeholders: Lesson, Course",
              "ebox"
            ),
            c("lesson"),
            c("course")
          ),
          value: _ || "",
          type: "number",
          onChange: function (e) {
            q("" != e && e < 0 ? { lesson_id: "0" } : { lesson_id: e });
          },
        }),
        (0, t.createElement)(s.SelectControl, {
          key: "orderby",
          label: (0, a.__)("Order by", "ebox"),
          value: l,
          options: [
            {
              label: (0, a.__)("ID - Order by post id. (default)", "ebox"),
              value: "ID",
            },
            {
              label: (0, a.__)("Title - Order by post title", "ebox"),
              value: "title",
            },
            {
              label: (0, a.__)("Date - Order by post date", "ebox"),
              value: "date",
            },
            {
              label: (0, a.__)("Menu - Order by Page Order Value", "ebox"),
              value: "menu_order",
            },
          ],
          onChange: (e) => q({ orderby: e }),
        }),
        (0, t.createElement)(s.SelectControl, {
          key: "order",
          label: (0, a.__)("Order", "ebox"),
          value: n,
          options: [
            {
              label: (0, a.__)(
                "DESC - highest to lowest values (default)",
                "ebox"
              ),
              value: "DESC",
            },
            {
              label: (0, a.__)("ASC - lowest to highest values", "ebox"),
              value: "ASC",
            },
          ],
          onChange: (e) => q({ order: e }),
        }),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Topics.
            (0, a._x)("%s per page", "placeholder: Topics", "ebox"),
            c("topics")
          ),
          help: (0, a.sprintf)(
            // translators: placeholder: per_page.
            (0, a._x)(
              "Leave empty for default (%d) or 0 to show all items.",
              "placeholder: per_page",
              "ebox"
            ),
            p("per_page")
          ),
          value: i || "",
          type: "number",
          onChange: function (e) {
            q("" != e && e < 0 ? { per_page: "0" } : { per_page: e });
          },
        }),
        B,
        L
      );
      let A = "";
      if (
        "yes" === ldlms_settings.settings.topics_taxonomies.ld_topic_category
      ) {
        let e = !1;
        ("" == m && "" == b) || (e = !0),
          (A = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.sprintf)(
                // translators: placeholder: Topic.
                (0, a._x)("%s Category Settings", "placeholder: Topic", "ebox"),
                c("topic")
              ),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Topic.
                (0, a._x)("%s Category Slug", "placeholder: Topic", "ebox"),
                c("topic")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Topics.
                (0, a._x)(
                  "shows %s with mentioned category slug.",
                  "placeholder: Topics",
                  "ebox"
                ),
                c("topics")
              ),
              value: m || "",
              onChange: (e) => q({ topic_category_name: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Topic.
                (0, a._x)("%s Category ID", "placeholder: Topic", "ebox"),
                c("topic")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Topics.
                (0, a._x)(
                  "shows %s with mentioned category ID.",
                  "placeholder: Topics",
                  "ebox"
                ),
                c("topics")
              ),
              value: b || "",
              type: "number",
              onChange: function (e) {
                q("" != e && e < 0 ? { topic_cat: "0" } : { topic_cat: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: topic.
                (0, a._x)("%s Category Selector", "placeholder: topic", "ebox"),
                c("topic")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Topics.
                (0, a._x)(
                  "shows a %s category dropdown.",
                  "placeholder: Topics",
                  "ebox"
                ),
                c("topics")
              ),
              checked: !!y,
              onChange: (e) => q({ topic_categoryselector: e }),
            })
          ));
      }
      let $ = "";
      if ("yes" === ldlms_settings.settings.topics_taxonomies.ld_topic_tag) {
        let e = !1;
        ("" == f && "" == w) || (e = !0),
          ($ = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.sprintf)(
                // translators: placeholder: Topic.
                (0, a._x)("%s Tag Settings", "placeholder: Topic", "ebox"),
                c("topic")
              ),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Topic.
                (0, a._x)("%s Tag Slug", "placeholder: Topic", "ebox"),
                c("topic")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Topics.
                (0, a._x)(
                  "shows %s with mentioned tag slug.",
                  "placeholder: Topics",
                  "ebox"
                ),
                c("topics")
              ),
              value: f || "",
              onChange: (e) => q({ topic_tag: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Topic.
                (0, a._x)("%s Tag ID", "placeholder: Topic", "ebox"),
                c("topic")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Topics.
                (0, a._x)(
                  "shows %s with mentioned tag ID.",
                  "placeholder: Topics",
                  "ebox"
                ),
                c("topics")
              ),
              value: w || "",
              type: "number",
              onChange: function (e) {
                q(
                  "" != e && e < 0 ? { topic_tag_id: "0" } : { topic_tag_id: e }
                );
              },
            })
          ));
      }
      let G = "";
      if (
        "yes" === ldlms_settings.settings.topics_taxonomies.wp_post_category
      ) {
        let e = !1;
        ("" == v && "" == x) || (e = !0),
          (G = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.__)("WP Category Settings", "ebox"),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Category Slug", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Topics.
                (0, a._x)(
                  "shows %s with mentioned WP Category slug.",
                  "placeholder: Topics",
                  "ebox"
                ),
                c("topics")
              ),
              value: v || "",
              onChange: (e) => q({ category_name: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Topic.
                (0, a._x)("%s Category ID", "placeholder: Topic", "ebox"),
                c("topic")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Topics.
                (0, a._x)(
                  "shows %s with mentioned category ID.",
                  "placeholder: Topics",
                  "ebox"
                ),
                c("topics")
              ),
              value: x || "",
              type: "number",
              onChange: function (e) {
                q("" != e && e < 0 ? { cat: "0" } : { cat: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("WP Category Selector", "ebox"),
              help: (0, a.__)("shows a WP category dropdown.", "ebox"),
              checked: !!k,
              onChange: (e) => q({ categoryselector: e }),
            })
          ));
      }
      let R = "";
      if ("yes" === ldlms_settings.settings.topics_taxonomies.wp_post_tag) {
        let e = !1;
        ("" == T && "" == P) || (e = !0),
          (R = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.__)("WP Tag Settings", "ebox"),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Tag Slug", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Topics.
                (0, a._x)(
                  "shows %s with mentioned WP tag slug.",
                  "placeholder: Topics",
                  "ebox"
                ),
                c("topics")
              ),
              value: T || "",
              onChange: (e) => q({ tag: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Tag ID", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Topics.
                (0, a._x)(
                  "shows %s with mentioned WP tag ID.",
                  "placeholder: Topics",
                  "ebox"
                ),
                c("topics")
              ),
              value: P || "",
              type: "number",
              onChange: function (e) {
                q("" != e && e < 0 ? { tag_id: "0" } : { tag_id: e });
              },
            })
          ));
      }
      const Q = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
        (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Preview", "ebox"),
          checked: !!S,
          onChange: (e) => q({ preview_show: e }),
        })
      );
      function M() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          E
        );
      }
      function W(e) {
        return M();
      }
      return [
        (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          U,
          O,
          A,
          $,
          G,
          R,
          Q
        ),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: C,
                attributes: a,
                key: C,
                EmptyResponsePlaceholder: W,
              }))
            : M();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const x = "ebox/ld-quiz-list",
    k = (0, a.sprintf)(
      // translators: placeholder: Quiz.
      (0, a._x)("ebox %s List", "placeholder: Quiz", "ebox"),
      c("quiz")
    );
  (0, l.registerBlockType)(x, {
    title: k,
    description: (0, a.sprintf)(
      // translators: placeholder: Quizzes.
      (0, a._x)(
        "This block shows a list of %s.",
        "placeholder: Quizzes",
        "ebox"
      ),
      c("quizzes")
    ),
    icon: "list-view",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      orderby: { type: "string", default: "ID" },
      order: { type: "string", default: "DESC" },
      per_page: { type: "string", default: "" },
      course_id: { type: "string", default: "" },
      lesson_id: { type: "string", default: "" },
      show_content: { type: "boolean", default: !0 },
      show_thumbnail: { type: "boolean", default: !0 },
      quiz_category_name: { type: "string", default: "" },
      quiz_cat: { type: "string", default: "" },
      quiz_categoryselector: { type: "boolean", default: !1 },
      quiz_tag: { type: "string", default: "" },
      quiz_tag_id: { type: "string", default: "" },
      category_name: { type: "string", default: "" },
      cat: { type: "string", default: "" },
      categoryselector: { type: "boolean", default: !1 },
      tag: { type: "string", default: "" },
      tag_id: { type: "string", default: "" },
      course_grid: { type: "boolean" },
      col: {
        type: "integer",
        default:
          ldlms_settings.plugins["ebox-course-grid"].enabled.col_default || 3,
      },
      preview_show: { type: "boolean", default: !0 },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      const {
        attributes: {
          orderby: l,
          order: n,
          per_page: i,
          course_id: d,
          lesson_id: _,
          show_content: h,
          show_thumbnail: g,
          quiz_category_name: m,
          quiz_cat: b,
          quiz_categoryselector: y,
          quiz_tag: f,
          quiz_tag_id: w,
          category_name: v,
          cat: C,
          categoryselector: E,
          tag: T,
          tag_id: P,
          course_grid: D,
          col: I,
          preview_show: S,
          example_show: z,
        },
        setAttributes: q,
      } = e;
      let B = "",
        L = "",
        O = "",
        N = !0;
      if (!0 === ldlms_settings.plugins["ebox-course-grid"].enabled) {
        void 0 === D || (1 != D && 0 != D) || (N = D);
        let e = !1;
        1 == N && (e = !0),
          (O = (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Grid Settings", "ebox"), initialOpen: e },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Grid", "ebox"),
              checked: !!N,
              onChange: (e) => q({ course_grid: e }),
            }),
            (0, t.createElement)(s.RangeControl, {
              label: (0, a.__)("Columns", "ebox"),
              value:
                I ||
                ldlms_settings.plugins["ebox-course-grid"].enabled.col_default,
              min: 1,
              max: ldlms_settings.plugins["ebox-course-grid"].enabled.col_max,
              step: 1,
              onChange: (e) => q({ col: e }),
            })
          ));
      }
      (B = (0, t.createElement)(s.ToggleControl, {
        label: (0, a.__)("Show Content", "ebox"),
        checked: !!h,
        onChange: (e) => q({ show_content: e }),
      })),
        (L = (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Thumbnail", "ebox"),
          checked: !!g,
          onChange: (e) => q({ show_thumbnail: e }),
        }));
      const U = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Settings", "ebox") },
        (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Course.
            (0, a._x)("%s ID", "placeholder: Course", "ebox"),
            c("course")
          ),
          help: (0, a.sprintf)(
            // translators: placeholders: Course, Course.
            (0, a._x)(
              "Enter single %1$s ID to limit listing. Leave blank if used within a %2$s.",
              "placeholders: Course, Course",
              "ebox"
            ),
            c("course"),
            c("course")
          ),
          value: d || "",
          type: "number",
          onChange: function (e) {
            q("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
          },
        }),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Lesson.
            (0, a._x)("%s ID", "placeholder: Lesson", "ebox"),
            c("lesson")
          ),
          help: (0, a.sprintf)(
            // translators: placeholders: Lesson, Course.
            (0, a._x)(
              "Enter single %1$s ID to limit listing. Leave blank if used within a %2$s. Zero for global.",
              "placeholders: Lesson, Course",
              "ebox"
            ),
            c("lesson"),
            c("course")
          ),
          value: _ || "",
          type: "number",
          onChange: function (e) {
            q("" != e && e < 0 ? { lesson_id: "0" } : { lesson_id: e });
          },
        }),
        (0, t.createElement)(s.SelectControl, {
          key: "orderby",
          label: (0, a.__)("Order by", "ebox"),
          value: l,
          options: [
            {
              label: (0, a.__)("ID - Order by post id. (default)", "ebox"),
              value: "ID",
            },
            {
              label: (0, a.__)("Title - Order by post title", "ebox"),
              value: "title",
            },
            {
              label: (0, a.__)("Date - Order by post date", "ebox"),
              value: "date",
            },
            {
              label: (0, a.__)("Menu - Order by Page Order Value", "ebox"),
              value: "menu_order",
            },
          ],
          onChange: (e) => q({ orderby: e }),
        }),
        (0, t.createElement)(s.SelectControl, {
          key: "order",
          label: (0, a.__)("Order", "ebox"),
          value: n,
          options: [
            {
              label: (0, a.__)(
                "DESC - highest to lowest values (default)",
                "ebox"
              ),
              value: "DESC",
            },
            {
              label: (0, a.__)("ASC - lowest to highest values", "ebox"),
              value: "ASC",
            },
          ],
          onChange: (e) => q({ order: e }),
        }),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Quizzes.
            (0, a._x)("%s per page", "placeholder: Quizzes", "ebox"),
            c("quizzes")
          ),
          help: (0, a.sprintf)(
            // translators: placeholder: per_page.
            (0, a._x)(
              "Leave empty for default (%d) or 0 to show all items.",
              "placeholder: per_page",
              "ebox"
            ),
            p("per_page")
          ),
          value: i || "",
          type: "number",
          onChange: function (e) {
            q("" != e && e < 0 ? { per_page: "0" } : { per_page: e });
          },
        }),
        B,
        L
      );
      let A = "";
      if (
        "yes" === ldlms_settings.settings.quizzes_taxonomies.ld_quiz_category
      ) {
        let e = !1;
        ("" == m && "" == b) || (e = !0),
          (A = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Category Settings", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Category Slug", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "shows %s with mentioned category slug.",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: m || "",
              onChange: (e) => q({ quiz_category_name: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Category ID", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "shows %s with mentioned category ID.",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: b || "",
              type: "number",
              onChange: function (e) {
                q("" != e && e < 0 ? { quiz_cat: "0" } : { quiz_cat: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Category Selector", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "shows a %s category dropdown.",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              checked: !!y,
              onChange: (e) => q({ quiz_categoryselector: e }),
            })
          ));
      }
      let $ = "";
      if ("yes" === ldlms_settings.settings.quizzes_taxonomies.ld_quiz_tag) {
        let e = !1;
        ("" == f && "" == w) || (e = !0),
          ($ = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Tag Settings", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Tag Slug", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "shows %s with mentioned tag slug.",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: f || "",
              onChange: (e) => q({ quiz_tag: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Tag ID", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "shows %s with mentioned tag ID.",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: w || "",
              type: "number",
              onChange: function (e) {
                q("" != e && e < 0 ? { quiz_tag_id: "0" } : { quiz_tag_id: e });
              },
            })
          ));
      }
      let G = "";
      if (
        "yes" === ldlms_settings.settings.quizzes_taxonomies.wp_post_category
      ) {
        let e = !1;
        ("" == v && "" == C) || (e = !0),
          (G = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.__)("WP Category Settings", "ebox"),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Category Slug", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "shows %s with mentioned WP Category slug.",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: v || "",
              onChange: (e) => q({ category_name: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Category ID", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "shows %s with mentioned category ID.",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: C || "",
              type: "number",
              onChange: function (e) {
                q("" != e && e < 0 ? { cat: "0" } : { cat: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("WP Category Selector", "ebox"),
              help: (0, a.__)("shows a WP category dropdown.", "ebox"),
              checked: !!E,
              onChange: (e) => q({ categoryselector: e }),
            })
          ));
      }
      let R = "";
      if ("yes" === ldlms_settings.settings.quizzes_taxonomies.wp_post_tag) {
        let e = !1;
        ("" == T && "" == P) || (e = !0),
          (R = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.__)("WP Tag Settings", "ebox"),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Tag Slug", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "shows %s with mentioned WP tag slug.",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: T || "",
              onChange: (e) => q({ tag: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Tag ID", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "shows %s with mentioned WP tag ID.",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: P || "",
              type: "number",
              onChange: function (e) {
                q("" != e && e < 0 ? { tag_id: "0" } : { tag_id: e });
              },
            })
          ));
      }
      const Q = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
        (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Preview", "ebox"),
          checked: !!S,
          onChange: (e) => q({ preview_show: e }),
        })
      );
      function M() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          k
        );
      }
      function W(e) {
        return M();
      }
      return [
        (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          U,
          O,
          A,
          $,
          G,
          R,
          Q
        ),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: x,
                attributes: a,
                key: x,
                EmptyResponsePlaceholder: W,
              }))
            : M();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {},
  });
  const T = wp.element.createElement,
    P = T(
      "svg",
      { width: 300, height: 300, viewBox: "0 0 50 10" },
      T("path", {
        d: "M47.1,0h-44c-1.7,0-3,1.3-3,3v4c0,1.7,1.3,3,3,3h44c1.7,0,3-1.3,3-3V3C50.1,1.3,48.7,0,47.1,0z M48.1,7c0,0.6-0.4,1-1,1h-12 V2h12c0.6,0,1,0.4,1,1V7z",
      })
    ),
    D = "ebox/ld-course-progress",
    I = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox %s Progress", "placeholders: Course", "ebox"),
      c("course")
    );
  (0, l.registerBlockType)(D, {
    title: I,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block displays users progress bar for the %s.",
        "placeholders: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: P,
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      course_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string" },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      let {
        attributes: { course_id: l },
        className: n,
      } = e;
      const {
          attributes: {
            user_id: i,
            preview_show: d,
            preview_user_id: p,
            example_show: _,
          },
          setAttributes: h,
        } = e,
        g = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                h("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("User ID", "ebox"),
              help: (0, a.__)(
                "Enter specific User ID. Leave blank for current User.",
                "ebox"
              ),
              value: i || "",
              type: "number",
              onChange: function (e) {
                h("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
              },
            })
          ),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Preview", "ebox"),
              checked: !!d,
              onChange: (e) => h({ preview_show: e }),
            }),
            (0, t.createElement)(
              s.PanelRow,
              { className: "ebox-block-error-message" },
              (0, a.__)("Preview settings are not saved.", "ebox")
            ),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Preview User ID", "ebox"),
              help: (0, a.__)("Enter a User ID to test preview", "ebox"),
              value: p || "",
              type: "number",
              onChange: function (e) {
                h(
                  "" != e && e < 0
                    ? { preview_user_id: "0" }
                    : { preview_user_id: e }
                );
              },
            })
          )
        );
      function m() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          I
        );
      }
      function b(e) {
        return m();
      }
      return [
        g,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: D,
                attributes: a,
                key: D,
                EmptyResponsePlaceholder: b,
              }))
            : m();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const S = (0, a.__)("ebox Visitor", "ebox");
  (0, l.registerBlockType)("ebox/ld-visitor", {
    title: S,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block shows the content if the user is not enrolled into the %s.",
        "placeholder: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "visibility",
    supports: { customClassName: !1 },
    category: "ebox-blocks",
    attributes: {
      display_type: { type: "string", default: "" },
      course_id: { type: "string", default: "" },
      team_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      autop: { type: "boolean", default: !0 },
    },
    edit: (e) => {
      const {
        attributes: {
          display_type: l,
          course_id: n,
          team_id: o,
          user_id: i,
          autop: d,
        },
        className: p,
        setAttributes: h,
      } = e;
      var g, m;
      (g = (0, t.createElement)(s.SelectControl, {
        key: "display_type",
        label: (0, a.__)("Display Type", "ebox"),
        value: l,
        options: [
          { label: (0, a.__)("Select a Display Type", "ebox"), value: "" },
          { label: c("course"), value: "ebox-courses" },
          { label: c("team"), value: "teams" },
        ],
        help: (0, a.sprintf)(
          // translators: placeholders: Course, Team.
          (0, a._x)(
            "Leave blank to show the default %1$s or %2$s content table.",
            "placeholders: Course, Team",
            "ebox"
          ),
          c("course"),
          c("team")
        ),
        onChange: (e) => h({ display_type: e }),
      })),
        "ebox-courses" === l
          ? (h({ team_id: "" }),
            (m = (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                h("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            })))
          : "teams" === l &&
            (h({ course_id: "" }),
            (m = (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s ID", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Team, Team.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Team, Team",
                  "ebox"
                ),
                c("team"),
                c("team")
              ),
              value: o || "",
              type: "number",
              onChange: function (e) {
                h("" != e && e < 0 ? { team_id: "0" } : { team_id: e });
              },
            })));
      const b = (0, t.createElement)(
        r.InspectorControls,
        { key: "controls" },
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Settings", "ebox") },
          g,
          m,
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("User ID", "ebox"),
            help: (0, a.__)(
              "Enter specific User ID. Leave blank for current User.",
              "ebox"
            ),
            value: i || "",
            type: "number",
            onChange: function (e) {
              h("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
            },
          }),
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Auto Paragraph", "ebox"),
            checked: !!d,
            onChange: (e) => h({ autop: e }),
          })
        )
      );
      let y = l;
      if ("" === y) {
        let e = u();
        void 0 !== e.post_type &&
          ("ebox-courses" === e.post_type
            ? (y = "ebox-courses")
            : "teams" === e.post_type && (y = "teams"));
      }
      let f = "";
      if ("ebox-courses" === y) {
        let e = _(n);
        0 === e &&
          ((e = u("course_id")),
          (e = _(e)),
          0 == e &&
            (f = (0, a.sprintf)(
              // translators: placeholders: Course, Course.
              (0, a._x)(
                "%1$s ID is required when not used within a %2$s.",
                "placeholders: Course, Course",
                "ebox"
              ),
              c("course"),
              c("course")
            )));
      } else if ("teams" === y) {
        let e = _(o);
        0 === e &&
          ((e = u("post_id")),
          (e = _(e)),
          0 == e &&
            (f = (0, a.sprintf)(
              // translators: placeholders: Team, Team.
              (0, a._x)(
                "%1$s ID is required when not used within a %2$s.",
                "placeholders: Team, Team",
                "ebox"
              ),
              c("team"),
              c("team")
            )));
      }
      return (
        f.length &&
          (f = (0, t.createElement)(
            "span",
            { className: "ebox-block-error-message" },
            f
          )),
        [
          b,
          (0, t.createElement)(
            "div",
            { className: p, key: "ebox/ld-visitor" },
            (0, t.createElement)("span", { className: "ebox-inner-header" }, S),
            (0, t.createElement)(
              "div",
              { className: "ebox-block-inner" },
              f,
              (0, t.createElement)(r.InnerBlocks, null)
            )
          ),
        ]
      );
    },
    save: (e) => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
  const z = (0, a.__)("ebox Student", "ebox");
  (0, l.registerBlockType)("ebox/ld-student", {
    title: z,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block shows the content if the user is enrolled in the %s.",
        "placeholders: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "welcome-learn-more",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      display_type: { type: "string", default: "" },
      course_id: { type: "string", default: "" },
      team_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      autop: { type: "boolean", default: !0 },
    },
    edit: (e) => {
      const {
        attributes: {
          display_type: l,
          course_id: n,
          team_id: o,
          user_id: i,
          autop: d,
        },
        className: p,
        setAttributes: h,
      } = e;
      var g, m;
      (g = (0, t.createElement)(s.SelectControl, {
        key: "display_type",
        label: (0, a.__)("Display Type", "ebox"),
        value: l,
        options: [
          { label: (0, a.__)("Select a Display Type", "ebox"), value: "" },
          { label: c("course"), value: "ebox-courses" },
          { label: c("team"), value: "teams" },
        ],
        help: (0, a.sprintf)(
          // translators: placeholders: Course, Team.
          (0, a._x)(
            "Leave blank to show the default %1$s or %2$s content table.",
            "placeholders: Course, Team",
            "ebox"
          ),
          c("course"),
          c("team")
        ),
        onChange: (e) => h({ display_type: e }),
      })),
        "ebox-courses" === l
          ? (h({ team_id: "" }),
            (m = (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                h("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            })))
          : "teams" === l &&
            (h({ course_id: "" }),
            (m = (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s ID", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Team, Team.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Team, Team",
                  "ebox"
                ),
                c("team"),
                c("team")
              ),
              value: o || "",
              type: "number",
              onChange: function (e) {
                h("" != e && e < 0 ? { team_id: "0" } : { team_id: e });
              },
            })));
      const b = (0, t.createElement)(
        r.InspectorControls,
        { key: "controls" },
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Settings", "ebox") },
          g,
          m,
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("User ID", "ebox"),
            help: (0, a.__)(
              "Enter specific User ID. Leave blank for current User.",
              "ebox"
            ),
            value: i || "",
            type: "number",
            onChange: function (e) {
              h("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
            },
          }),
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Auto Paragraph", "ebox"),
            checked: !!d,
            onChange: (e) => h({ autop: e }),
          })
        )
      );
      let y = l;
      if ("" === y) {
        let e = u();
        void 0 !== e.post_type &&
          ("ebox-courses" === e.post_type
            ? (y = "ebox-courses")
            : "teams" === e.post_type && (y = "teams"));
      }
      let f = "";
      if ("ebox-courses" === y) {
        let e = _(n);
        0 === e &&
          ((e = u("course_id")),
          (e = _(e)),
          0 == e &&
            (f = (0, a.sprintf)(
              // translators: placeholders: Course, Course.
              (0, a._x)(
                "%1$s ID is required when not used within a %2$s.",
                "placeholders: Course, Course",
                "ebox"
              ),
              c("course"),
              c("course")
            )));
      } else if ("teams" === y) {
        let e = _(o);
        0 === e &&
          ((e = u("post_id")),
          (e = _(e)),
          0 == e &&
            (f = (0, a.sprintf)(
              // translators: placeholders: Team, Team.
              (0, a._x)(
                "%1$s ID is required when not used within a %2$s.",
                "placeholders: Team, Team",
                "ebox"
              ),
              c("team"),
              c("team")
            )));
      }
      return (
        f.length &&
          (f = (0, t.createElement)(
            "span",
            { className: "ebox-block-error-message" },
            f
          )),
        [
          b,
          (0, t.createElement)(
            "div",
            { className: p, key: "ebox/ld-student" },
            (0, t.createElement)("span", { className: "ebox-inner-header" }, z),
            (0, t.createElement)(
              "div",
              { className: "ebox-block-inner" },
              f,
              (0, t.createElement)(r.InnerBlocks, null)
            )
          ),
        ]
      );
    },
    save: (e) => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
  const q = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox %s Complete", "placeholder: Course", "ebox"),
      c("course")
    ),
    B = "ebox/ld-course-complete";
  (0, l.registerBlockType)(B, {
    title: q,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block shows the content if the user is enrolled into the %s and it is completed.",
        "placeholders: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "star-filled",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      course_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      autop: { type: "boolean", default: !0 },
    },
    edit: (e) => {
      const {
          attributes: { course_id: l, user_id: n, autop: o },
          className: i,
          setAttributes: d,
        } = e,
        p = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                d("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("User ID", "ebox"),
              help: (0, a.__)(
                "Enter specific User ID. Leave blank for current User.",
                "ebox"
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                d("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Auto Paragraph", "ebox"),
              checked: !!o,
              onChange: (e) => d({ autop: e }),
            })
          )
        );
      let h = "",
        g = _(l);
      return (
        0 === g && (g = _(u("course_id"))),
        0 == g &&
          (h = (0, a.sprintf)(
            // translators: placeholders: Course, Course.
            (0, a._x)(
              "%1$s ID is required when not used within a %2$s.",
              "placeholders: Course, Course",
              "ebox"
            ),
            c("course"),
            c("course")
          )),
        h.length &&
          (h = (0, t.createElement)(
            "span",
            { className: "ebox-block-error-message" },
            h
          )),
        [
          p,
          (0, t.createElement)(
            "div",
            { className: i, key: B },
            (0, t.createElement)("span", { className: "ebox-inner-header" }, q),
            (0, t.createElement)(
              "div",
              { className: "ebox-block-inner" },
              h,
              (0, t.createElement)(r.InnerBlocks, null)
            )
          ),
        ]
      );
    },
    save: (e) => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
  const L = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox %s In Progress", "placeholder: Course", "ebox"),
      c("course")
    ),
    O = "ebox/ld-course-inprogress";
  (0, l.registerBlockType)(O, {
    title: L,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block shows the content if the user is enrolled into the %s but not yet completed.",
        "placeholder: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "star-half",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      course_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      autop: { type: "boolean", default: !0 },
    },
    edit: (e) => {
      const {
          attributes: { course_id: l, user_id: n, autop: o },
          className: i,
          setAttributes: d,
        } = e,
        p = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                d("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("User ID", "ebox"),
              help: (0, a.__)(
                "Enter specific User ID. Leave blank for current User.",
                "ebox"
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                d("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Auto Paragraph", "ebox"),
              checked: !!o,
              onChange: (e) => d({ autop: e }),
            })
          )
        );
      let h = "",
        g = _(l);
      return (
        0 === g &&
          ((g = u("course_id")),
          (g = _(g)),
          0 == g &&
            (h = (0, a.sprintf)(
              // translators: placeholders: Course, Course.
              (0, a._x)(
                "%1$s ID is required when not used within a %2$s.",
                "placeholders: Course, Course",
                "ebox"
              ),
              c("course"),
              c("course")
            ))),
        h.length &&
          (h = (0, t.createElement)(
            "span",
            { className: "ebox-block-error-message" },
            h
          )),
        [
          p,
          (0, t.createElement)(
            "div",
            { className: i, key: O },
            (0, t.createElement)("span", { className: "ebox-inner-header" }, L),
            (0, t.createElement)(
              "div",
              { className: "ebox-block-inner" },
              h,
              (0, t.createElement)(r.InnerBlocks, null)
            )
          ),
        ]
      );
    },
    save: (e) => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
  const N = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox %s Not Started", "placeholder: Course", "ebox"),
      c("course")
    ),
    U = "ebox/ld-course-notstarted";
  (0, l.registerBlockType)(U, {
    title: N,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block shows the content if the user is enrolled into the %s but not yet started.",
        "placeholder: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "star-empty",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      course_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      autop: { type: "boolean", default: !0 },
    },
    edit: (e) => {
      const {
          attributes: { course_id: l, user_id: n, autop: o },
          className: i,
          setAttributes: d,
        } = e,
        p = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                d("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("User ID", "ebox"),
              help: (0, a.__)(
                "Enter specific User ID. Leave blank for current User.",
                "ebox"
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                d("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Auto Paragraph", "ebox"),
              checked: !!o,
              onChange: (e) => d({ autop: e }),
            })
          )
        );
      let h = "",
        g = _(l);
      return (
        0 === g &&
          ((g = u("course_id")),
          (g = _(g)),
          0 == g &&
            (h = (0, a.sprintf)(
              // translators: placeholders: Course, Course.
              (0, a._x)(
                "%1$s ID is required when not used within a %2$s.",
                "placeholders: Course, Course",
                "ebox"
              ),
              c("course"),
              c("course")
            ))),
        h.length &&
          (h = (0, t.createElement)(
            "span",
            { className: "ebox-block-error-message" },
            h
          )),
        [
          p,
          (0, t.createElement)(
            "div",
            { className: i, key: U },
            (0, t.createElement)("span", { className: "ebox-inner-header" }, N),
            (0, t.createElement)(
              "div",
              { className: "ebox-block-inner" },
              h,
              (0, t.createElement)(r.InnerBlocks, null)
            )
          ),
        ]
      );
    },
    save: (e) => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
  const A = "ebox/ld-course-resume",
    $ = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("%s Resume", "placeholder: Course", "ebox"),
      c("course")
    );
  (0, l.registerBlockType)(A, {
    title: $,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("Return to %s link/button.", "placeholder: Course", "ebox"),
      c("course")
    ),
    icon: "welcome-learn-more",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    example: { attributes: { example_show: 1 } },
    attributes: {
      course_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      label: { type: "string", default: "" },
      html_class: { type: "string", default: "" },
      button: { type: "string", default: "" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
          attributes: {
            course_id: l,
            user_id: n,
            label: i,
            html_class: d,
            button: p,
            preview_show: _,
            preview_user_id: h,
            example_show: g,
          },
          className: m,
          setAttributes: b,
        } = e,
        y = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                b("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("User ID", "ebox"),
              help: (0, a.__)(
                "Enter specific User ID. Leave blank for current User.",
                "ebox"
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                b("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
              },
            }),
            (0, t.createElement)(s.SelectControl, {
              key: "button",
              label: (0, a.__)("Show as button", "ebox"),
              value: p,
              options: [
                { label: (0, a.__)("Yes", "ebox"), value: "true" },
                { label: (0, a.__)("No", "ebox"), value: "false" },
              ],
              onChange: (e) => b({ button: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Label", "ebox"),
              help: (0, a.__)("Label for link shown to user", "ebox"),
              value: i || "",
              onChange: (e) => b({ label: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              key: "html_class",
              label: (0, a.__)("Class", "ebox"),
              help: (0, a.__)("HTML class for link element", "ebox"),
              value: d || "",
              onChange: (e) => b({ html_class: e }),
            })
          ),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Preview", "ebox"),
              checked: !!_,
              onChange: (e) => b({ preview_show: e }),
            }),
            (0, t.createElement)(
              s.PanelRow,
              { className: "ebox-block-error-message" },
              (0, a.__)("Preview settings are not saved.", "ebox")
            ),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Preview User ID", "ebox"),
              help: (0, a.__)("Enter a User ID to test preview", "ebox"),
              value: h || "",
              type: "number",
              onChange: function (e) {
                b(
                  "" != e && e < 0
                    ? { preview_user_id: "0" }
                    : { preview_user_id: e }
                );
              },
            })
          )
        );
      function f() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          $
        );
      }
      function w(e) {
        return f();
      }
      return [
        y,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: A,
                attributes: a,
                key: A,
                EmptyResponsePlaceholder: w,
              }))
            : f();
          var a;
        }, [e.attributes]),
      ];
    },
    save: function (e) {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const G = "ebox/ld-course-info",
    R = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox %s Info [ld_course_info]", "placeholder: Course", "ebox"),
      c("course")
    );
  (0, l.registerBlockType)(G, {
    title: R,
    description: (0, a.sprintf)(
      // translators: placeholder: Courses.
      (0, a._x)(
        "This block shows the %s and progress for the user.",
        "placeholder: Courses",
        "ebox"
      ),
      c("course")
    ),
    icon: "analytics",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      user_id: { type: "string", default: 0 },
      registered_show: { type: "boolean" },
      registered_show_thumbnail: { type: "boolean", default: !0 },
      registered_num: { type: "string", default: "" },
      registered_orderby: { type: "string", default: "title" },
      registered_order: { type: "string", default: "ASC" },
      progress_show: { type: "boolean" },
      progress_num: { type: "string", default: "" },
      progress_orderby: { type: "string", default: "title" },
      progress_order: { type: "string", default: "ASC" },
      quiz_show: { type: "boolean" },
      quiz_num: { type: "string", default: "" },
      quiz_orderby: { type: "string", default: "taken" },
      quiz_order: { type: "string", default: "DESC" },
      preview_show: { type: "boolean", default: !0 },
      preview_user_id: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      const {
        attributes: {
          user_id: l,
          registered_show: n,
          registered_show_thumbnail: i,
          registered_num: d,
          registered_orderby: _,
          registered_order: h,
          progress_show: g,
          progress_num: m,
          progress_orderby: b,
          progress_order: y,
          quiz_show: f,
          quiz_num: w,
          quiz_orderby: v,
          quiz_order: C,
          preview_user_id: E,
          preview_show: x,
        },
        setAttributes: k,
      } = e;
      void 0 === n && k({ registered_show: !0 }),
        void 0 === g && k({ progress_show: !0 }),
        void 0 === f && k({ quiz_show: !0 }),
        !1 === n &&
          !1 === g &&
          !1 === f &&
          (k({ registered_show: !0 }),
          k({ progress_show: !0 }),
          k({ quiz_show: !0 }));
      const T = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Settings", "ebox") },
        (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("User ID", "ebox"),
          help: (0, a.__)(
            "Enter specific User ID. Leave blank for current User.",
            "ebox"
          ),
          value: l || "",
          type: "number",
          onChange: function (e) {
            k("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
          },
        }),
        (0, t.createElement)(s.ToggleControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Courses.
            (0, a._x)("Show Registered %s", "placeholder: Courses", "ebox"),
            c("courses")
          ),
          checked: !!n,
          onChange: (e) => k({ registered_show: e }),
        }),
        (0, t.createElement)(s.ToggleControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Course.
            (0, a._x)("Show %s Progress", "placeholder: Course", "ebox"),
            c("course")
          ),
          checked: !!g,
          onChange: (e) => k({ progress_show: e }),
        }),
        (0, t.createElement)(s.ToggleControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Quiz.
            (0, a._x)("Show %s Attempts", "placeholder: Quiz", "ebox"),
            c("quiz")
          ),
          checked: !!f,
          onChange: (e) => k({ quiz_show: e }),
        })
      );
      var P = "";
      !0 === n &&
        (P = (0, t.createElement)(
          s.PanelBody,
          {
            title: (0, a.sprintf)(
              // translators: placeholder: Courses.
              (0, a._x)("Registered %s", "placeholder: Courses", "ebox"),
              c("courses")
            ),
            initialOpen: !1,
          },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Thumbnail", "ebox"),
            checked: !!i,
            onChange: (e) => k({ registered_show_thumbnail: e }),
          }),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("per page", "ebox"),
            help: (0, a.sprintf)(
              // translators: placeholder: per_page.
              (0, a._x)(
                "Leave empty for default (%d) or 0 to show all items.",
                "placeholder: per_page",
                "ebox"
              ),
              p("per_page")
            ),
            value: d || "",
            min: 0,
            max: 100,
            type: "number",
            onChange: function (e) {
              k(
                "" != e && e < 0
                  ? { registered_num: "0" }
                  : { registered_num: e }
              );
            },
          }),
          (0, t.createElement)(s.SelectControl, {
            key: "registered_orderby",
            label: (0, a.__)("Order by", "ebox"),
            value: _,
            options: [
              {
                label: (0, a.__)(
                  "Title - Order by post title (default)",
                  "ebox"
                ),
                value: "title",
              },
              {
                label: (0, a.__)("ID - Order by post id", "ebox"),
                value: "ID",
              },
              {
                label: (0, a.__)("Date - Order by post date", "ebox"),
                value: "date",
              },
              {
                label: (0, a.__)("Menu - Order by Page Order Value", "ebox"),
                value: "menu_order",
              },
            ],
            onChange: (e) => k({ registered_orderby: e }),
          }),
          (0, t.createElement)(s.SelectControl, {
            key: "registered_order",
            label: (0, a.__)("Order", "ebox"),
            value: h,
            options: [
              {
                label: (0, a.__)(
                  "ASC - lowest to highest values (default)",
                  "ebox"
                ),
                value: "ASC",
              },
              {
                label: (0, a.__)("DESC - highest to lowest values", "ebox"),
                value: "DESC",
              },
            ],
            onChange: (e) => k({ registered_order: e }),
          })
        ));
      var D = "";
      !0 === g &&
        (D = (0, t.createElement)(
          s.PanelBody,
          {
            title: (0, a.sprintf)(
              // translators: placeholder: Course.
              (0, a._x)("%s Progress", "placeholder: Course", "ebox"),
              c("course")
            ),
            initialOpen: !1,
          },
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("per page", "ebox"),
            help: (0, a.sprintf)(
              // translators: placeholder: progress_num.
              (0, a._x)(
                "Leave empty for default (%d) or 0 to show all items.",
                "placeholder: progress_num",
                "ebox"
              ),
              p("progress_num")
            ),
            value: m || "",
            min: 0,
            max: 100,
            type: "number",
            onChange: function (e) {
              k("" != e && e < 0 ? { progress_num: "0" } : { progress_num: e });
            },
          }),
          (0, t.createElement)(s.SelectControl, {
            key: "progress_orderby",
            label: (0, a.__)("Order by", "ebox"),
            value: b,
            options: [
              {
                label: (0, a.__)(
                  "Title - Order by post title (default)",
                  "ebox"
                ),
                value: "title",
              },
              {
                label: (0, a.__)("ID - Order by post id", "ebox"),
                value: "ID",
              },
              {
                label: (0, a.__)("Date - Order by post date", "ebox"),
                value: "date",
              },
              {
                label: (0, a.__)("Menu - Order by Page Order Value", "ebox"),
                value: "menu_order",
              },
            ],
            onChange: (e) => k({ progress_orderby: e }),
          }),
          (0, t.createElement)(s.SelectControl, {
            key: "progress_order",
            label: (0, a.__)("Order", "ebox"),
            value: y,
            options: [
              {
                label: (0, a.__)(
                  "ASC - lowest to highest values (default)",
                  "ebox"
                ),
                value: "ASC",
              },
              {
                label: (0, a.__)("DESC - highest to lowest values", "ebox"),
                value: "DESC",
              },
            ],
            onChange: (e) => k({ progress_order: e }),
          })
        ));
      var I = "";
      !0 === f &&
        (I = (0, t.createElement)(
          s.PanelBody,
          {
            title: (0, a.sprintf)(
              // translators: placeholder: Quiz.
              (0, a._x)("%s Attempts", "placeholder: Quiz", "ebox"),
              c("quiz")
            ),
            initialOpen: !1,
          },
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("per page", "ebox"),
            help: (0, a.sprintf)(
              // translators: placeholder: quiz_num.
              (0, a._x)(
                "Leave empty for default (%d) or 0 to show all items.",
                "placeholder: quiz_num",
                "ebox"
              ),
              p("quiz_num")
            ),
            value: w || "",
            min: 0,
            max: 100,
            type: "number",
            onChange: function (e) {
              k("" != e && e < 0 ? { quiz_num: "0" } : { quiz_num: e });
            },
          }),
          (0, t.createElement)(s.SelectControl, {
            key: "quiz_orderby",
            label: (0, a.__)("Order by", "ebox"),
            value: v,
            options: [
              {
                label: (0, a.__)(
                  "Date Taken (default) - Order by date taken",
                  "ebox"
                ),
                value: "taken",
              },
              {
                label: (0, a.__)("Title - Order by post title", "ebox"),
                value: "title",
              },
              {
                label: (0, a.__)("ID - Order by post id. (default)", "ebox"),
                value: "ID",
              },
              {
                label: (0, a.__)("Date - Order by post date", "ebox"),
                value: "date",
              },
              {
                label: (0, a.__)("Menu - Order by Page Order Value", "ebox"),
                value: "menu_order",
              },
            ],
            onChange: (e) => k({ quiz_orderby: e }),
          }),
          (0, t.createElement)(s.SelectControl, {
            key: "quiz_order",
            label: (0, a.__)("Order", "ebox"),
            value: C,
            options: [
              {
                label: (0, a.__)(
                  "DESC - highest to lowest values (default)",
                  "ebox"
                ),
                value: "DESC",
              },
              {
                label: (0, a.__)("ASC - lowest to highest values", "ebox"),
                value: "ASC",
              },
            ],
            onChange: (e) => k({ quiz_order: e }),
          })
        ));
      const S = (0, t.createElement)(
        r.InspectorControls,
        { key: "controls" },
        T,
        P,
        D,
        I,
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!x,
            onChange: (e) => k({ preview_show: e }),
          }),
          (0, t.createElement)(
            s.PanelRow,
            { className: "ebox-block-error-message" },
            (0, a.__)("Preview settings are not saved.", "ebox")
          ),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Preview User ID", "ebox"),
            help: (0, a.__)("Enter a User ID to test preview", "ebox"),
            value: E || "",
            type: "number",
            onChange: function (e) {
              k(
                "" != e && e < 0
                  ? { preview_user_id: "0" }
                  : { preview_user_id: e }
              );
            },
          })
        )
      );
      function z() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          R
        );
      }
      function q(e) {
        return z();
      }
      return [
        S,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: G,
                attributes: a,
                key: G,
                EmptyResponsePlaceholder: q,
              }))
            : z();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const Q = "ebox/ld-user-course-points",
    M = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox User %s Points", "placeholder: Course", "ebox"),
      c("course")
    );
  (0, l.registerBlockType)(Q, {
    title: M,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block shows the earned %s points for the user.",
        "placeholder: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "chart-area",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      user_id: { type: "string", default: "" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string" },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
          attributes: { user_id: l, preview_show: n, preview_user_id: i },
          setAttributes: d,
        } = e,
        c = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("User ID", "ebox"),
              help: (0, a.__)(
                "Enter specific User ID. Leave blank for current User.",
                "ebox"
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                d("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
              },
            })
          ),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Preview", "ebox"),
              checked: !!n,
              onChange: (e) => d({ preview_show: e }),
            }),
            (0, t.createElement)(
              s.PanelRow,
              { className: "ebox-block-error-message" },
              (0, a.__)("Preview settings are not saved.", "ebox")
            ),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Preview User ID", "ebox"),
              help: (0, a.__)("Enter a User ID to test preview", "ebox"),
              value: i || "",
              type: "number",
              onChange: function (e) {
                d(
                  "" != e && e < 0
                    ? { preview_user_id: "0" }
                    : { preview_user_id: e }
                );
              },
            })
          )
        );
      function p() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          M
        );
      }
      function _(e) {
        return p();
      }
      return [
        c,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: Q,
                attributes: a,
                key: Q,
                EmptyResponsePlaceholder: _,
              }))
            : p();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.preview_user_id;
    },
  });
  const W = "ebox/ld-team-list",
    j = (0, a.sprintf)(
      // translators: placeholder: Team.
      (0, a._x)("ebox %s List", "placeholder: Team", "ebox"),
      c("team")
    );
  (0, l.registerBlockType)(W, {
    title: j,
    description: (0, a.sprintf)(
      // translators: placeholder: Teams.
      (0, a._x)(
        "This block shows a list of %s.",
        "placeholder: Teams",
        "ebox"
      ),
      c("teams")
    ),
    icon: "list-view",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      orderby: { type: "string", default: "ID" },
      order: { type: "string", default: "DESC" },
      per_page: { type: "string", default: "" },
      myteams: { type: "string", default: "" },
      status: {
        type: "array",
        default: ["not_started", "in_progress", "completed"],
      },
      show_content: { type: "boolean", default: !0 },
      show_thumbnail: { type: "boolean", default: !0 },
      team_category_name: { type: "string", default: "" },
      team_cat: { type: "string", default: "" },
      team_categoryselector: { type: "boolean", default: !1 },
      team_tag: { type: "string", default: "" },
      team_tag_id: { type: "string", default: "" },
      category_name: { type: "string", default: "" },
      cat: { type: "string", default: "" },
      categoryselector: { type: "boolean", default: !1 },
      tag: { type: "string", default: "" },
      tag_id: { type: "string", default: "" },
      course_grid: { type: "boolean" },
      progress_bar: { type: "boolean", default: !1 },
      col: {
        type: "integer",
        default:
          ldlms_settings.plugins["ebox-course-grid"].enabled.col_default || 3,
      },
      price_type: {
        type: "array",
        default: ["free", "paynow", "subscribe", "closed"],
      },
      preview_show: { type: "boolean", default: !0 },
      preview_user_id: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      const {
        attributes: {
          orderby: l,
          order: n,
          per_page: i,
          myteams: d,
          status: _,
          show_content: h,
          show_thumbnail: g,
          team_category_name: m,
          team_cat: b,
          team_categoryselector: y,
          team_tag: f,
          team_tag_id: w,
          category_name: v,
          cat: C,
          categoryselector: E,
          tag: x,
          tag_id: k,
          course_grid: T,
          progress_bar: P,
          col: D,
          preview_user_id: I,
          preview_show: S,
          example_show: z,
          price_type: q,
        },
        setAttributes: B,
      } = e;
      let L = "",
        O = "",
        N = "",
        U = !0;
      if (!0 === ldlms_settings.plugins["ebox-course-grid"].enabled) {
        void 0 === T || (1 != T && 0 != T) || (U = T);
        let e = !1;
        1 == U && (e = !0),
          (N = (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Grid Settings", "ebox"), initialOpen: e },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Grid", "ebox"),
              checked: !!U,
              onChange: (e) => B({ course_grid: e }),
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Progress Bar", "ebox"),
              checked: !!P,
              onChange: (e) => B({ progress_bar: e }),
            }),
            (0, t.createElement)(s.RangeControl, {
              label: (0, a.__)("Columns", "ebox"),
              value:
                D ||
                ldlms_settings.plugins["ebox-course-grid"].enabled.col_default,
              min: 1,
              max: ldlms_settings.plugins["ebox-course-grid"].enabled.col_max,
              step: 1,
              onChange: (e) => B({ col: e }),
            })
          ));
      }
      (L = (0, t.createElement)(s.ToggleControl, {
        label: (0, a.__)("Show Content", "ebox"),
        checked: !!h,
        onChange: (e) => B({ show_content: e }),
      })),
        (O = (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Thumbnail", "ebox"),
          checked: !!g,
          onChange: (e) => B({ show_thumbnail: e }),
        }));
      let A = "";
      "" === ldlms_settings.settings.teams_cpt.public &&
        (A = (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Warning", "ebox"), opened: !0 },
          (0, t.createElement)(s.TextControl, {
            help: (0, a.sprintf)(
              // translators: placeholders: Teams, Teams.
              (0, a._x)(
                "%1$s are not public, please visit the %2$s Settings page and set them to Public to enable access on the front end.",
                "placeholders: Teams, Teams",
                "ebox"
              ),
              c("teams"),
              c("teams")
            ),
            value: "",
            type: "hidden",
            className: "notice notice-error",
          })
        ));
      const $ = (0, t.createElement)(
        s.PanelBody,
        {
          className:
            "ebox-block-controls-panel ebox-block-controls-panel-ld-team-list",
          title: (0, a.__)("Settings", "ebox"),
        },
        (0, t.createElement)(s.SelectControl, {
          key: "orderby",
          label: (0, a.__)("Order by", "ebox"),
          value: l,
          options: [
            {
              label: (0, a.__)("ID - Order by post id. (default)", "ebox"),
              value: "ID",
            },
            {
              label: (0, a.__)("Title - Order by post title", "ebox"),
              value: "title",
            },
            {
              label: (0, a.__)("Date - Order by post date", "ebox"),
              value: "date",
            },
            {
              label: (0, a.__)("Menu - Order by Page Order Value", "ebox"),
              value: "menu_order",
            },
          ],
          onChange: (e) => B({ orderby: e }),
        }),
        (0, t.createElement)(s.SelectControl, {
          key: "order",
          label: (0, a.__)("Order", "ebox"),
          value: n,
          options: [
            {
              label: (0, a.__)(
                "DESC - highest to lowest values (default)",
                "ebox"
              ),
              value: "DESC",
            },
            {
              label: (0, a.__)("ASC - lowest to highest values", "ebox"),
              value: "ASC",
            },
          ],
          onChange: (e) => B({ order: e }),
        }),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Teams.
            (0, a._x)("%s per page", "placeholder: Teams", "ebox"),
            c("teams")
          ),
          help: (0, a.sprintf)(
            // translators: placeholder: default per page.
            (0, a._x)(
              "Leave empty for default (%d) or 0 to show all items.",
              "placeholder: default per page",
              "ebox"
            ),
            p("per_page")
          ),
          value: i || "",
          type: "number",
          onChange: function (e) {
            B("" != e && e < 0 ? { per_page: "0" } : { per_page: e });
          },
        }),
        (0, t.createElement)(s.SelectControl, {
          multiple: !0,
          key: "price_type",
          label: (0, a.sprintf)(
            // translators: placeholder: Team Access Mode(s).
            (0, a._x)(
              "%s Access Mode(s)",
              "placeholder: Team Access Mode(s)",
              "ebox"
            ),
            c("team")
          ),
          help: (0, a.__)("Ctrl+click to deselect selected items.", "ebox"),
          value: q,
          options: [
            { label: (0, a.__)("Free", "ebox"), value: "free" },
            { label: (0, a.__)("Buy Now", "ebox"), value: "paynow" },
            { label: (0, a.__)("Recurring", "ebox"), value: "subscribe" },
            { label: (0, a.__)("Closed", "ebox"), value: "closed" },
          ],
          onChange: (e) => B({ price_type: e }),
        }),
        (0, t.createElement)(s.SelectControl, {
          key: "myteams",
          label: (0, a.sprintf)(
            // translators: placeholder: Teams.
            (0, a._x)("My %s", "placeholder: Teams", "ebox"),
            c("teams")
          ),
          value: d,
          options: [
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "Show All %s (default)",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              value: "",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "Show Enrolled %s only",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              value: "enrolled",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "Show not-Enrolled %s only",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("Teams")
              ),
              value: "not-enrolled",
            },
          ],
          onChange: (e) => B({ myteams: e }),
        }),
        "enrolled" === d &&
          (0, t.createElement)(s.SelectControl, {
            multiple: !0,
            key: "status",
            label: (0, a.sprintf)(
              // translators: placeholder: Teams.
              (0, a._x)("Enrolled %s Status", "placeholder: Teams", "ebox"),
              c("teams")
            ),
            value: _,
            options: [
              {
                label: (0, a.__)("Not Started", "ebox"),
                value: "not_started",
              },
              {
                label: (0, a.__)("In Progress", "ebox"),
                value: "in_progress",
              },
              {
                label: (0, a.__)("Completed", "ebox"),
                value: "completed",
              },
            ],
            onChange: (e) => B({ status: e }),
          }),
        L,
        O
      );
      let G = "";
      if (
        "yes" === ldlms_settings.settings.teams_taxonomies.ld_team_category
      ) {
        let e = !1;
        ("" == m && "" == b) || (e = !0),
          (G = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Category Settings", "placeholder: Team", "ebox"),
                c("team")
              ),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Category Slug", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "shows %s with mentioned category slug.",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              value: m || "",
              onChange: (e) => B({ team_category_name: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Category ID", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "shows %s with mentioned category ID.",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              value: b || "",
              type: "number",
              onChange: function (e) {
                B("" != e && e < 0 ? { team_cat: "0" } : { team_cat: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Category Selector", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "shows a %s category dropdown.",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              checked: !!y,
              onChange: (e) => B({ team_categoryselector: e }),
            })
          ));
      }
      let R = "";
      if ("yes" === ldlms_settings.settings.teams_taxonomies.ld_team_tag) {
        let e = !1;
        ("" == f && "" == w) || (e = !0),
          (R = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Tag Settings", "placeholder: Team", "ebox"),
                c("team")
              ),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Tag Slug", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "shows %s with mentioned tag slug.",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              value: f || "",
              onChange: (e) => B({ team_tag: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Tag ID", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "shows %s with mentioned tag ID.",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              value: w || "",
              type: "number",
              onChange: function (e) {
                B(
                  "" != e && e < 0 ? { team_tag_id: "0" } : { team_tag_id: e }
                );
              },
            })
          ));
      }
      let Q = "";
      if (
        "yes" === ldlms_settings.settings.teams_taxonomies.wp_post_category
      ) {
        let e = !1;
        ("" == v && "" == C) || (e = !0),
          (Q = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.__)("WP Category Settings", "ebox"),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Category Slug", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "shows %s with mentioned WP Category slug.",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              value: v || "",
              onChange: (e) => B({ category_name: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Category ID", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "shows %s with mentioned category ID.",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              value: C || "",
              type: "number",
              onChange: function (e) {
                B("" != e && e < 0 ? { cat: "0" } : { cat: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("WP Category Selector", "ebox"),
              help: (0, a.__)("shows a WP category dropdown.", "ebox"),
              checked: !!E,
              onChange: (e) => B({ categoryselector: e }),
            })
          ));
      }
      let M = "";
      if ("yes" === ldlms_settings.settings.teams_taxonomies.wp_post_tag) {
        let e = !1;
        ("" == x && "" == k) || (e = !0),
          (M = (0, t.createElement)(
            s.PanelBody,
            {
              title: (0, a.__)("WP Tag Settings", "ebox"),
              initialOpen: e,
            },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Tag Slug", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "shows %s with mentioned WP tag slug.",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              value: x || "",
              onChange: (e) => B({ tag: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("WP Tag ID", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Teams.
                (0, a._x)(
                  "shows %s with mentioned WP tag ID.",
                  "placeholder: Teams",
                  "ebox"
                ),
                c("teams")
              ),
              value: k || "",
              type: "number",
              onChange: function (e) {
                B("" != e && e < 0 ? { tag_id: "0" } : { tag_id: e });
              },
            })
          ));
      }
      const F = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
        (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Preview", "ebox"),
          checked: !!S,
          onChange: (e) => B({ preview_show: e }),
        }),
        (0, t.createElement)(
          s.PanelRow,
          { className: "ebox-block-error-message" },
          (0, a.__)("Preview settings are not saved.", "ebox")
        ),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Preview User ID", "ebox"),
          help: (0, a.__)("Enter a User ID to test preview", "ebox"),
          value: I || "",
          type: "number",
          onChange: function (e) {
            B(
              "" != e && e < 0
                ? { preview_user_id: "0" }
                : { preview_user_id: e }
            );
          },
        })
      );
      function V() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          j
        );
      }
      function H(e) {
        return V();
      }
      return [
        (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          A,
          $,
          N,
          G,
          R,
          Q,
          M,
          F
        ),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: W,
                attributes: a,
                key: W,
                EmptyResponsePlaceholder: H,
              }))
            : V();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const F = "ebox/ld-user-teams",
    V = (0, a.sprintf)(
      // translators: placeholder: Teams.
      (0, a._x)("ebox User %s", "placeholder: Teams", "ebox"),
      c("teams")
    );
  (0, l.registerBlockType)(F, {
    title: V,
    description: (0, a.sprintf)(
      // translators: placeholder: Teams.
      (0, a._x)(
        "This block displays the list of %s users are assigned to as users or leaders.",
        "placeholder: Teams",
        "ebox"
      ),
      c("teams")
    ),
    icon: "teams",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      user_id: { type: "string", default: "" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string" },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      const {
        attributes: { user_id: l, preview_user_id: n, preview_show: i },
        setAttributes: d,
      } = e;
      let p = "";
      "" === ldlms_settings.settings.teams_cpt.public &&
        (p = (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Warning", "ebox"), opened: !0 },
          (0, t.createElement)(s.TextControl, {
            help: (0, a.sprintf)(
              // translators: placeholders: Teams, Teams.
              (0, a._x)(
                "%1$s are not public, please visit the %2$s Settings page and set them to Public to enable access on the front end.",
                "placeholders: Teams, Teams",
                "ebox"
              ),
              c("teams"),
              c("teams")
            ),
            value: "",
            type: "hidden",
            className: "notice notice-error",
          })
        ));
      const _ = (0, t.createElement)(
        r.InspectorControls,
        { key: "controls" },
        p,
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Settings", "ebox") },
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("User ID", "ebox"),
            help: (0, a.__)(
              "Enter specific User ID. Leave blank for current User.",
              "ebox"
            ),
            value: l || "",
            type: "number",
            onChange: function (e) {
              d("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
            },
          })
        ),
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!i,
            onChange: (e) => d({ preview_show: e }),
          }),
          (0, t.createElement)(
            s.PanelRow,
            { className: "ebox-block-error-message" },
            (0, a.__)("Preview settings are not saved.", "ebox")
          ),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Preview User ID", "ebox"),
            help: (0, a.__)("Enter a User ID to test preview", "ebox"),
            value: n || "",
            type: "number",
            onChange: function (e) {
              d(
                "" != e && e < 0
                  ? { preview_user_id: "0" }
                  : { preview_user_id: e }
              );
            },
          })
        )
      );
      function h() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          V
        );
      }
      function g(e) {
        return h();
      }
      return [
        _,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: F,
                attributes: a,
                key: F,
                EmptyResponsePlaceholder: g,
              }))
            : h();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const H = (0, a.sprintf)(
    // translators: placeholder: Team.
    (0, a._x)("ebox %s", "placeholder: Team", "ebox"),
    c("team")
  );
  (0, l.registerBlockType)("ebox/ld-team", {
    title: H,
    description: (0, a.sprintf)(
      // translators: placeholder: Team.
      (0, a._x)(
        "This block shows the content if the user is enrolled into the %s.",
        "placeholder: Team",
        "ebox"
      ),
      c("team")
    ),
    icon: "teams",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      team_id: { type: "string" },
      user_id: { type: "string", default: "" },
      autop: { type: "boolean", default: !0 },
    },
    edit: (e) => {
      const {
          attributes: { team_id: l, user_id: n, autop: o },
          className: i,
          setAttributes: d,
        } = e,
        u = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s ID", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s ID (required)", "placeholder: Team", "ebox"),
                c("team")
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                d("" != e && e < 0 ? { team_id: "0" } : { team_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("User ID", "ebox"),
              help: (0, a.__)(
                "Enter specific User ID. Leave blank for current User.",
                "ebox"
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                d("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Auto Paragraph", "ebox"),
              checked: !!o,
              onChange: (e) => d({ autop: e }),
            })
          )
        );
      let p = "";
      return (
        0 == _(l) &&
          (p = (0, a.sprintf)(
            // translators: placeholder: Team.
            (0, a._x)("%s ID is required.", "placeholder: Team", "ebox"),
            c("team")
          )),
        p.length &&
          (p = (0, t.createElement)(
            "span",
            { className: "ebox-block-error-message" },
            p
          )),
        [
          u,
          (0, t.createElement)(
            "div",
            { className: i, key: "ebox/ld-team" },
            (0, t.createElement)("span", { className: "ebox-inner-header" }, H),
            (0, t.createElement)(
              "div",
              { className: "ebox-block-inner" },
              p,
              (0, t.createElement)(r.InnerBlocks, null)
            )
          ),
        ]
      );
    },
    save: (e) => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
  const Y = "ebox/ld-payment-buttons",
    Z = (0, a.__)("ebox Payment Buttons", "ebox");
  (0, l.registerBlockType)(Y, {
    title: Z,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block displays the %s payment buttons",
        "placeholder: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "cart",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      display_type: { type: "string", default: "" },
      course_id: { type: "string" },
      team_id: { type: "string" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
        attributes: {
          display_type: l,
          course_id: n,
          team_id: i,
          preview_show: d,
          preview_user_id: p,
        },
        className: _,
        setAttributes: h,
      } = e;
      var g, m;
      (g = (0, t.createElement)(s.SelectControl, {
        key: "display_type",
        label: (0, a.__)("Display Type", "ebox"),
        value: l,
        options: [
          { label: (0, a.__)("Select a Display Type", "ebox"), value: "" },
          { label: c("course"), value: "ebox-courses" },
          { label: c("team"), value: "teams" },
        ],
        help: (0, a.sprintf)(
          // translators: placeholders: Course, Team.
          (0, a._x)(
            "Leave blank to show the default %1$s or %2$s content table.",
            "placeholders: Course, Team",
            "ebox"
          ),
          c("course"),
          c("team")
        ),
        onChange: (e) => h({ display_type: e }),
      })),
        "ebox-courses" === l
          ? (h({ team_id: "" }),
            (m = (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                h("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            })))
          : "teams" === l &&
            (h({ course_id: "" }),
            (m = (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s ID", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Team, Team.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Team, Team",
                  "ebox"
                ),
                c("team"),
                c("team")
              ),
              value: i || "",
              type: "number",
              onChange: function (e) {
                h("" != e && e < 0 ? { team_id: "0" } : { team_id: e });
              },
            })));
      const b = (0, t.createElement)(
        r.InspectorControls,
        { key: "controls" },
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Settings", "ebox") },
          g,
          m
        ),
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!d,
            onChange: (e) => h({ preview_show: e }),
          }),
          (0, t.createElement)(
            s.PanelRow,
            { className: "ebox-block-error-message" },
            (0, a.__)("Preview settings are not saved.", "ebox")
          ),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Preview User ID", "ebox"),
            help: (0, a.__)("Enter a User ID for preview.", "ebox"),
            value: p || "",
            type: "number",
            onChange: function (e) {
              h(
                "" != e && e < 0
                  ? { preview_user_id: "0" }
                  : { preview_user_id: e }
              );
            },
          })
        )
      );
      function y() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          Z
        );
      }
      function f(e) {
        return y();
      }
      return [
        b,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: Y,
                attributes: a,
                key: Y,
                EmptyResponsePlaceholder: f,
              }))
            : y();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const J = "ebox/ld-course-content",
    K = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox %s Content", "placeholder: Course", "ebox"),
      c("course")
    );
  (0, l.registerBlockType)(J, {
    title: K,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block displays the %s Content table.",
        "placeholder: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "format-aside",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      display_type: { type: "string", default: "" },
      course_id: { type: "string", default: "" },
      post_id: { type: "string", default: "" },
      team_id: { type: "string", default: "" },
      per_page: { type: "string", default: "" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
        attributes: {
          display_type: l,
          course_id: n,
          post_id: i,
          team_id: d,
          per_page: p,
          preview_show: _,
          preview_user_id: h,
          example_show: m,
        },
        className: b,
        setAttributes: y,
      } = e;
      var f, w;
      (f = (0, t.createElement)(s.SelectControl, {
        key: "display_type",
        label: (0, a.__)("Display Type", "ebox"),
        value: l,
        options: [
          { label: (0, a.__)("Select a Display Type", "ebox"), value: "" },
          { label: c("course"), value: "ebox-courses" },
          { label: c("team"), value: "teams" },
        ],
        help: (0, a.sprintf)(
          // translators: placeholders: Course, Team.
          (0, a._x)(
            "Leave blank to show the default %1$s or %2$s content table.",
            "placeholders: Course, Team",
            "ebox"
          ),
          c("course"),
          c("team")
        ),
        onChange: (e) => y({ display_type: e }),
      })),
        "ebox-courses" === l
          ? (y({ team_id: "" }),
            (w = (0, t.createElement)(
              React.Fragment,
              null,
              (0, t.createElement)(s.TextControl, {
                label: (0, a.sprintf)(
                  // translators: placeholder: Course.
                  (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                  c("course")
                ),
                help: (0, a.sprintf)(
                  // translators: placeholders: Course, Course.
                  (0, a._x)(
                    "Enter single %1$s ID. Leave blank if used within a %2$s.",
                    "placeholders: Course, Course",
                    "ebox"
                  ),
                  c("course"),
                  c("course")
                ),
                value: n || "",
                type: "number",
                onChange: function (e) {
                  y("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
                },
              }),
              (0, t.createElement)(s.TextControl, {
                label: (0, a.__)("Step ID", "ebox"),
                help: (0, a.sprintf)(
                  // translators: placeholders: Course, Course.
                  (0, a._x)(
                    "Enter single Step ID. Leave blank if used within a %1$s or 0 to always show %2$s content table.",
                    "placeholders: Course, Course",
                    "ebox"
                  ),
                  c("course"),
                  c("course")
                ),
                value: i || "",
                type: "number",
                onChange: function (e) {
                  y("" != e && e < 0 ? { post_id: "0" } : { post_id: e });
                },
              })
            )))
          : "teams" === l &&
            (y({ course_id: "" }),
            y({ post_id: "" }),
            (w = (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s ID", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Team, Team.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Team, Team",
                  "ebox"
                ),
                c("team"),
                c("team")
              ),
              value: d || "",
              type: "number",
              onChange: function (e) {
                y("" != e && e < 0 ? { team_id: "0" } : { team_id: e });
              },
            })));
      const v = (0, t.createElement)(
        r.InspectorControls,
        { key: "controls" },
        g(),
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Settings", "ebox") },
          f,
          w,
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Items per page", "ebox"),
            help: (0, a.__)(
              "Leave empty for default or 0 to show all items.",
              "ebox"
            ),
            value: p || "",
            type: "number",
            onChange: function (e) {
              y("" != e && e < 0 ? { per_page: "0" } : { per_page: e });
            },
          })
        ),
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!_,
            onChange: (e) => y({ preview_show: e }),
          }),
          (0, t.createElement)(
            s.PanelRow,
            { className: "ebox-block-error-message" },
            (0, a.__)("Preview settings are not saved.", "ebox")
          ),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Preview User ID", "ebox"),
            help: (0, a.__)("Enter a User ID for preview.", "ebox"),
            value: h || "",
            type: "number",
            onChange: function (e) {
              y(
                "" != e && e < 0
                  ? { preview_user_id: "0" }
                  : { preview_user_id: e }
              );
            },
          })
        )
      );
      function C() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          K
        );
      }
      function E(e) {
        return C();
      }
      return [
        v,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: J,
                attributes: a,
                key: J,
                EmptyResponsePlaceholder: E,
              }))
            : C();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const X = "ebox/ld-course-expire-status",
    ee = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox %s Expire Status", "placeholder: Course", "ebox"),
      c("course")
    );
  (0, l.registerBlockType)(X, {
    title: ee,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block displays the user %s access expire date.",
        "placeholders: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "clock",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      course_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      label_before: { type: "string", default: "" },
      label_after: { type: "string", default: "" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      let {
        attributes: { course_id: l },
        className: n,
      } = e;
      const {
          attributes: {
            user_id: i,
            label_before: d,
            label_after: p,
            preview_show: _,
            preview_user_id: h,
            example_show: g,
          },
          setAttributes: m,
        } = e,
        b = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                m("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("User ID", "ebox"),
              help: (0, a.__)(
                "Enter specific User ID. Leave blank for current User.",
                "ebox"
              ),
              value: i || "",
              type: "number",
              onChange: function (e) {
                m("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Label Before Expire", "ebox"),
              help: (0, a.__)(
                "The label prefix shown before the access expires",
                "ebox"
              ),
              value: d || "",
              onChange: (e) => m({ label_before: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Label After Expire", "ebox"),
              help: (0, a.__)(
                "The label prefix shown after access has expired",
                "ebox"
              ),
              value: p || "",
              onChange: (e) => m({ label_after: e }),
            })
          ),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Preview", "ebox"),
              checked: !!_,
              onChange: (e) => m({ preview_show: e }),
            }),
            (0, t.createElement)(
              s.PanelRow,
              { className: "ebox-block-error-message" },
              (0, a.__)("Preview settings are not saved.", "ebox")
            ),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Preview User ID", "ebox"),
              help: (0, a.__)("Enter a User ID to test preview", "ebox"),
              value: h || "",
              type: "number",
              onChange: function (e) {
                m(
                  "" != e && e < 0
                    ? { preview_user_id: "0" }
                    : { preview_user_id: e }
                );
              },
            })
          )
        );
      function y() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          ee
        );
      }
      function f(e) {
        return y();
      }
      return [
        b,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: X,
                attributes: a,
                key: X,
                EmptyResponsePlaceholder: f,
              }))
            : y();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const te = "ebox/ld-certificate",
    ae = (0, a.__)("ebox Certificate", "ebox");
  (0, l.registerBlockType)(te, {
    title: ae,
    description: (0, a.__)(
      "This shortcode shows a Certificate download link.",
      "ebox"
    ),
    icon: "welcome-learn-more",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    example: { attributes: { example_show: 1 } },
    attributes: {
      display_type: { type: "string", default: "" },
      course_id: { type: "string", default: "" },
      team_id: { type: "string", default: "" },
      quiz_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      display_as: { type: "string", default: "" },
      label: { type: "string", default: "" },
      class_html: { type: "string", default: "" },
      context: { type: "string", default: "" },
      callback: { type: "string", default: "" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
        attributes: {
          display_type: l,
          course_id: n,
          team_id: i,
          quiz_id: d,
          user_id: p,
          display_as: _,
          label: h,
          class_html: m,
          context: b,
          callback: y,
          preview_show: f,
          preview_user_id: w,
          example_show: v,
        },
        title: C,
        className: E,
        setAttributes: x,
      } = e;
      var k, T, P;
      "" == _ &&
        (("ebox-courses" != u("post_type") && "teams" != u("post_type")) ||
          x({ display_as: "banner" })),
        (k = (0, t.createElement)(s.SelectControl, {
          key: "display_type",
          label: (0, a.__)("Display Type", "ebox"),
          value: l,
          help: (0, a.sprintf)(
            // translators: placeholders: Course, Team, Quiz.
            (0, a._x)(
              "Require if not used within a %1$s, %2$s, or %3$s. Or to override default display.",
              "placeholders: Course, Team, Quiz",
              "ebox"
            ),
            c("course"),
            c("team"),
            c("quiz")
          ),
          options: [
            {
              label: (0, a.__)("Select a Display Type", "ebox"),
              value: "",
            },
            { label: c("course"), value: "ebox-courses" },
            { label: c("team"), value: "teams" },
            { label: c("quiz"), value: "ebox-quiz" },
          ],
          onChange: (e) => x({ display_type: e }),
        })),
        "ebox-courses" === l
          ? (x({ team_id: "" }),
            x({ quiz_id: "" }),
            (T = (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("Enter single %s ID.", "placeholder: Course", "ebox"),
                c("course")
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                x("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            })))
          : "teams" === l
          ? (x({ course_id: "" }),
            x({ quiz_id: "" }),
            (T = (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s ID", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("Enter single %s ID.", "placeholder: Team", "ebox"),
                c("team")
              ),
              value: i || "",
              type: "number",
              onChange: function (e) {
                x("" != e && e < 0 ? { team_id: "0" } : { team_id: e });
              },
            })))
          : "ebox-quiz" === l &&
            (x({ team_id: "" }),
            (T = (0, t.createElement)(
              React.Fragment,
              null,
              (0, t.createElement)(s.TextControl, {
                label: (0, a.sprintf)(
                  // translators: placeholder: Quiz.
                  (0, a._x)("%s ID", "placeholder: Quiz", "ebox"),
                  c("quiz")
                ),
                help: (0, a.sprintf)(
                  // translators: placeholder: Quiz.
                  (0, a._x)("Enter single %s ID.", "placeholder: Quiz", "ebox"),
                  c("quiz")
                ),
                value: d || "",
                type: "number",
                onChange: function (e) {
                  x("" != e && e < 0 ? { quiz_id: "0" } : { quiz_id: e });
                },
              }),
              (0, t.createElement)(s.TextControl, {
                label: (0, a.sprintf)(
                  // translators: placeholder: Course.
                  (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                  c("course")
                ),
                help: (0, a.sprintf)(
                  // translators: placeholders: Course, Quiz, Course.
                  (0, a._x)(
                    "Enter single %1$s ID. Required if %2$s is within a %3$s",
                    "placeholders: Course, Quiz, Course",
                    "ebox"
                  ),
                  c("course"),
                  c("quiz"),
                  c("course")
                ),
                value: n || "",
                type: "number",
                onChange: function (e) {
                  x("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
                },
              })
            ))),
        "button" == _ &&
          (P = (0, t.createElement)(
            React.Fragment,
            null,
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Label", "ebox"),
              help: (0, a.__)("Label for link shown to user", "ebox"),
              value: h || "",
              onChange: (e) => x({ label: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Class", "ebox"),
              help: (0, a.__)("HTML class for link element", "ebox"),
              value: m || "",
              onChange: (e) => x({ class_html: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Context", "ebox"),
              help: (0, a.__)(
                "User defined value to be passed into shortcode handler",
                "ebox"
              ),
              value: b || "",
              onChange: (e) => x({ context: e }),
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Callback", "ebox"),
              help: (0, a.__)(
                "Custom callback function to be used instead of default output",
                "ebox"
              ),
              value: y || "",
              onChange: (e) => x({ callback: e }),
            })
          ));
      const D = (0, t.createElement)(
        r.InspectorControls,
        { key: "controls" },
        g(),
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Settings", "ebox") },
          k,
          T,
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("User ID", "ebox"),
            help: (0, a.__)(
              "Enter specific User ID. Leave blank for current User.",
              "ebox"
            ),
            value: p || "",
            type: "number",
            onChange: function (e) {
              x("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
            },
          })
        ),
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Advanced", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.SelectControl, {
            key: "display_as",
            label: (0, a.__)("Displayed as", "ebox"),
            help: (0, a.__)("Display as Button or Banner", "ebox"),
            value: _ || "button",
            options: [
              { label: (0, a.__)("Button", "ebox"), value: "button" },
              {
                label: (0, a.sprintf)(
                  // translators: placeholders: Course, Team.
                  (0, a._x)(
                    "Banner (%1$s or %2$s only)",
                    "placeholders: Course, Team",
                    "ebox"
                  ),
                  c("course"),
                  c("team")
                ),
                value: "banner",
              },
            ],
            onChange: (e) => x({ display_as: e }),
          }),
          P
        ),
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!f,
            onChange: (e) => x({ preview_show: e }),
          }),
          (0, t.createElement)(
            s.PanelRow,
            { className: "ebox-block-error-message" },
            (0, a.__)("Preview settings are not saved.", "ebox")
          ),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Preview User ID", "ebox"),
            help: (0, a.__)("Enter a User ID for preview.", "ebox"),
            value: w || "",
            type: "number",
            onChange: function (e) {
              x(
                "" != e && e < 0
                  ? { preview_user_id: "0" }
                  : { preview_user_id: e }
              );
            },
          })
        )
      );
      function I() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          ae
        );
      }
      function S(e) {
        return I();
      }
      return [
        D,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: te,
                attributes: a,
                key: te,
                EmptyResponsePlaceholder: S,
              }))
            : I();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const le = (0, a.sprintf)(
    // translators: placeholder: Quiz.
    (0, a._x)("ebox %s Complete", "placeholder: Quiz", "ebox"),
    c("quiz")
  );
  (0, l.registerBlockType)("ebox/ld-quiz-complete", {
    title: le,
    description: (0, a.sprintf)(
      // translators: placeholder: Quiz.
      (0, a._x)(
        "This block shows the content if the user has completed the %s.",
        "placeholder: Quiz",
        "ebox"
      ),
      c("quiz")
    ),
    icon: "star-filled",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      course_id: { type: "string", default: "" },
      quiz_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      autop: { type: "boolean", default: !0 },
    },
    edit: (e) => {
      const {
          attributes: { course_id: l, quiz_id: n, user_id: o, autop: i },
          className: d,
          setAttributes: p,
        } = e,
        h = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s ID", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Quiz, Quiz.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Quiz, Quiz",
                  "ebox"
                ),
                c("quiz"),
                c("quiz")
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                p("" != e && e < 0 ? { quiz_id: "0" } : { quiz_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                p("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("User ID", "ebox"),
              help: (0, a.__)(
                "Enter specific User ID. Leave blank for current User.",
                "ebox"
              ),
              value: o || "",
              type: "number",
              onChange: function (e) {
                p("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
              },
            }),
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Auto Paragraph", "ebox"),
              checked: !!i,
              onChange: (e) => p({ autop: e }),
            })
          )
        );
      let g = "",
        m = _(n);
      return (
        0 === m &&
          ("ebox-quiz" === u("post_type") && ((m = u("post_id")), (m = _(m))),
          0 == m &&
            (g = (0, a.sprintf)(
              // translators: placeholders: Quiz, Quiz.
              (0, a._x)(
                "%1$s ID is required when not used within a %2$s.",
                "placeholders: Quiz, Quiz",
                "ebox"
              ),
              c("quiz"),
              c("quiz")
            ))),
        g.length &&
          (g = (0, t.createElement)(
            "span",
            { className: "ebox-block-error-message" },
            g
          )),
        [
          h,
          (0, t.createElement)(
            "div",
            { className: d, key: "ld-quiz-complete" },
            (0, t.createElement)(
              "span",
              { className: "ebox-inner-header" },
              le
            ),
            (0, t.createElement)(
              "div",
              { className: "ebox-block-inner" },
              g,
              (0, t.createElement)(r.InnerBlocks, null)
            )
          ),
        ]
      );
    },
    save: (e) => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
  const re = "ebox/ld-courseinfo",
    se = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox %s Info [courseinfo]", "placeholder: Course", "ebox"),
      c("course")
    );
  (0, l.registerBlockType)(re, {
    title: se,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block displays %s related information",
        "placeholder: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "analytics",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      show: { type: "string" },
      course_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      format: { type: "string" },
      seconds_format: { type: "string" },
      decimals: { type: "string" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
          attributes: {
            course_id: l,
            show: n,
            user_id: i,
            format: d,
            seconds_format: p,
            decimals: _,
            preview_show: h,
            preview_user_id: g,
          },
          className: m,
          setAttributes: b,
        } = e,
        y = (0, t.createElement)(s.SelectControl, {
          key: "show",
          value: n || "course_title",
          label: (0, a.__)("Show", "ebox"),
          options: [
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Title", "placeholder: Course", "ebox"),
                c("course")
              ),
              value: "course_title",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s URL", "placeholder: Course", "ebox"),
                c("course")
              ),
              value: "course_url",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Points", "placeholder: Course", "ebox"),
                c("course")
              ),
              value: "course_points",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Price", "placeholder: Course", "ebox"),
                c("course")
              ),
              value: "course_price",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Price Type", "placeholder: Course", "ebox"),
                c("course")
              ),
              value: "course_price_type",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)(
                  "%s Enrolled Users Count",
                  "placeholder: Course",
                  "ebox"
                ),
                c("course")
              ),
              value: "course_users_count",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)(
                  "Total User %s Points",
                  "placeholder: Course",
                  "ebox"
                ),
                c("course")
              ),
              value: "user_course_points",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("Total User %s Time", "placeholder: Course", "ebox"),
                c("course")
              ),
              value: "user_course_time",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)(
                  "%s Completed On (date)",
                  "placeholder: Course",
                  "ebox"
                ),
                c("course")
              ),
              value: "completed_on",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)(
                  "%s Enrolled On (date)",
                  "placeholder: Course",
                  "ebox"
                ),
                c("course")
              ),
              value: "enrolled_on",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "Cumulative %s Score",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: "cumulative_score",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "Cumulative %s Points",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: "cumulative_points",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "Possible Cumulative %s Total Points",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: "cumulative_total_points",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "Cumulative %s Percentage",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: "cumulative_percentage",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "Cumulative %s Time Spent",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: "cumulative_timespent",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "Aggregate %s Percentage",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: "aggregate_percentage",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)("Aggregate %s Score", "placeholder: Quizzes", "ebox"),
                c("quizzes")
              ),
              value: "aggregate_score",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "Aggregate %s Points",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: "aggregate_points",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "Possible Aggregate %s Total Points",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: "aggregate_total_points",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quizzes.
                (0, a._x)(
                  "Aggregate %s Time Spent",
                  "placeholder: Quizzes",
                  "ebox"
                ),
                c("quizzes")
              ),
              value: "aggregate_timespent",
            },
          ],
          onChange: (e) => b({ show: e }),
        }),
        f = (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Course.
            (0, a._x)("%s ID", "placeholder: Course", "ebox"),
            c("course")
          ),
          help: (0, a.sprintf)(
            // translators: placeholders: Course, Course.
            (0, a._x)(
              "Enter single %1$s ID. Leave blank if used within a %2$s or certificate.",
              "placeholders: Course, Course",
              "ebox"
            ),
            c("course"),
            c("course")
          ),
          value: l || "",
          type: "number",
          onChange: function (e) {
            b("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
          },
        });
      let w = "";
      [
        "user_course_points",
        "user_course_time",
        "completed_on",
        "enrolled_on",
        "cumulative_score",
        "cumulative_points",
        "cumulative_total_points",
        "cumulative_percentage",
        "cumulative_timespent",
        "aggregate_percentage",
        "aggregate_score",
        "aggregate_points",
        "aggregate_total_points",
        "aggregate_timespent",
      ].includes(n) &&
        (w = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("User ID", "ebox"),
          help: (0, a.__)(
            "Enter specific User ID. Leave blank for current User.",
            "ebox"
          ),
          value: i || "",
          type: "number",
          onChange: function (e) {
            b("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
          },
        }));
      let v = "";
      ("completed_on" != n && "enrolled_on" != n) ||
        (v = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Format", "ebox"),
          help: (0, a.__)(
            'This can be used to change the date format. Default: "F j, Y, g:i a.',
            "ebox"
          ),
          value: d || "",
          onChange: (e) => b({ format: e }),
        }));
      let C = "";
      "user_course_time" == n &&
        (C = (0, t.createElement)(s.SelectControl, {
          key: "seconds_format",
          value: p,
          label: (0, a.__)("Seconds Format", "ebox"),
          options: [
            {
              label: (0, a.__)("Time - 20min 49sec", "ebox"),
              value: "time",
            },
            {
              label: (0, a.__)("Seconds - 1436", "ebox"),
              value: "seconds",
            },
          ],
          onChange: (e) => b({ seconds_format: e }),
        }));
      let E = "";
      ("course_points" != n && "user_course_points" != n) ||
        (E = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Decimals", "ebox"),
          help: (0, a.__)(
            "Number of decimal places to show. Default is 2.",
            "ebox"
          ),
          value: _ || "",
          type: "number",
          onChange: function (e) {
            b("" != e && e < 0 ? { decimals: "0" } : { decimals: e });
          },
        }));
      const x = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
        (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Preview", "ebox"),
          checked: !!h,
          onChange: (e) => b({ preview_show: e }),
        }),
        (0, t.createElement)(
          s.PanelRow,
          { className: "ebox-block-error-message" },
          (0, a.__)("Preview settings are not saved.", "ebox")
        ),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Preview User ID", "ebox"),
          help: (0, a.__)("Enter a User ID to test preview", "ebox"),
          value: g || "",
          type: "number",
          onChange: function (e) {
            b(
              "" != e && e < 0
                ? { preview_user_id: "0" }
                : { preview_user_id: e }
            );
          },
        })
      );
      function k() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          se
        );
      }
      function T(e) {
        return k();
      }
      return [
        (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            f,
            w,
            y,
            v,
            C,
            E
          ),
          x
        ),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: re,
                attributes: a,
                key: re,
                EmptyResponsePlaceholder: T,
              }))
            : k();
          var a;
        }, [e.attributes]),
      ];
    },
    save: function (e) {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const ne = "ebox/ld-quizinfo",
    oe = (0, a.sprintf)(
      // translators: placeholder: Quiz.
      (0, a._x)("ebox %s Info [quizinfo]", "placeholder: Quiz", "ebox"),
      c("quiz")
    );
  (0, l.registerBlockType)(ne, {
    title: oe,
    description: (0, a.sprintf)(
      // translators: placeholder: Quiz.
      (0, a._x)(
        "This block displays %s related information",
        "placeholder: Quiz",
        "ebox"
      ),
      c("quiz")
    ),
    icon: "analytics",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      show: { type: "string", default: "quiz_title" },
      quiz_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      format: { type: "string" },
      field_id: { type: "string" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
          attributes: {
            quiz_id: l,
            user_id: n,
            timestamp: i,
            show: d,
            format: p,
            field_id: _,
            preview_show: h,
            preview_user_id: g,
          },
          className: m,
          setAttributes: b,
        } = e,
        y = (0, t.createElement)(s.SelectControl, {
          key: "show",
          value: d || "quiz_title",
          label: (0, a.__)("Show", "ebox"),
          options: [
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Title", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              value: "quiz_title",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Score", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              value: "score",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Count", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              value: "count",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Pass", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              value: "pass",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Timestamp", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              value: "timestamp",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Points", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              value: "points",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Total Points", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              value: "total_points",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Percentage", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              value: "percentage",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s Title", "placeholder: Course", "ebox"),
                c("course")
              ),
              value: "course_title",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Time Spent", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              value: "timespent",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Quiz.
                (0, a._x)("%s Form Field", "placeholder: Quiz", "ebox"),
                c("quiz")
              ),
              value: "field",
            },
          ],
          onChange: (e) => b({ show: e }),
        });
      let f = "";
      "field" == d &&
        (f = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Custom Field ID", "ebox"),
          help: (0, a.sprintf)(
            // translators: placeholder: Quiz.
            (0, a._x)(
              "The Field ID is shown on the %s Custom Fields table.",
              "placeholder: Quiz",
              "ebox"
            ),
            c("quiz")
          ),
          value: _ || "",
          onChange: (e) => b({ field_id: e }),
        }));
      let w = "";
      ("timestamp" != d && "field" != d) ||
        (w = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Format", "ebox"),
          help: (0, a.__)(
            "This can be used to change the date format. Default: F j, Y, g:i a.",
            "ebox"
          ),
          value: p || "",
          onChange: (e) => b({ format: e }),
        }));
      const v = (0, t.createElement)(
        r.InspectorControls,
        { key: "controls" },
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Settings", "ebox") },
          y,
          f,
          w,
          (0, t.createElement)(s.TextControl, {
            label: (0, a.sprintf)(
              // translators: placeholder: Quiz.
              (0, a._x)("%s ID", "placeholder: Quiz", "ebox"),
              c("quiz")
            ),
            help: (0, a.sprintf)(
              // translators: placeholders: Quiz, Quiz.
              (0, a._x)(
                "Enter a single %1$s ID. Leave blank if used within a %2$s or Certificate.",
                "placeholders: Quiz, Quiz",
                "ebox"
              ),
              c("quiz"),
              c("quiz")
            ),
            value: l || "",
            type: "number",
            onChange: function (e) {
              b("" != e && e < 0 ? { quiz_id: "0" } : { quiz_id: e });
            },
          }),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("User ID", "ebox"),
            help: (0, a.sprintf)(
              // translators: placeholder: Quiz.
              (0, a._x)(
                "Enter a single User ID. Leave blank if used within a %s or Certificate.",
                "placeholder: Quiz",
                "ebox"
              ),
              c("quiz")
            ),
            value: n || "",
            type: "number",
            onChange: function (e) {
              b("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
            },
          }),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Attempt timestamp", "ebox"),
            help: (0, a.sprintf)(
              // translators: placeholder: Quiz.
              (0, a._x)(
                'Single %s attempt timestamp. See WP user profile "#" link on attempt row. Leave blank to use latest attempt or within a Certificate.',
                "placeholder: Quiz",
                "ebox"
              ),
              c("quiz")
            ),
            value: i || "",
            onChange: function (e) {
              if (e.length && e.startsWith("data:quizinfo:", 0)) {
                var t = e.split(":");
                if (t.length > 2) {
                  var a = "";
                  for (let e = 2; e < t.length; e++)
                    "" != a
                      ? ("quiz_id" == a
                          ? b({ quiz_id: t[e] })
                          : "user_id" == a
                          ? b({ user_id: t[e] })
                          : "time" == a && b({ timestamp: t[e] }),
                        (a = ""))
                      : "quiz" == t[e]
                      ? (a = "quiz_id")
                      : "user" == t[e]
                      ? (a = "user_id")
                      : "time" == t[e] && (a = "time");
                }
              }
            },
          })
        ),
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!h,
            onChange: (e) => b({ preview_show: e }),
          }),
          (0, t.createElement)(
            s.PanelRow,
            { className: "ebox-block-error-message" },
            (0, a.__)("Preview settings are not saved.", "ebox")
          ),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Preview User ID", "ebox"),
            help: (0, a.__)("Enter a User ID to test preview", "ebox"),
            value: g || "",
            type: "number",
            onChange: function (e) {
              b(
                "" != e && e < 0
                  ? { preview_user_id: "0" }
                  : { preview_user_id: e }
              );
            },
          })
        )
      );
      function C() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          oe
        );
      }
      function E(e) {
        return C();
      }
      return [
        v,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: ne,
                attributes: a,
                key: ne,
                EmptyResponsePlaceholder: E,
              }))
            : C();
          var a;
        }, [e.attributes]),
      ];
    },
    save: function (e) {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const ie = "ebox/ld-teaminfo",
    de = (0, a.sprintf)(
      // translators: placeholder: Team.
      (0, a._x)("ebox %s Info [teaminfo]", "placeholder: Team", "ebox"),
      c("team")
    );
  (0, l.registerBlockType)(ie, {
    title: de,
    description: (0, a.sprintf)(
      // translators: placeholder: Team.
      (0, a._x)(
        "This block displays %s related information",
        "placeholder: Team",
        "ebox"
      ),
      c("team")
    ),
    icon: "analytics",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      show: { type: "string" },
      team_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      format: { type: "string" },
      decimals: { type: "string" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
          attributes: {
            team_id: l,
            show: n,
            user_id: i,
            format: d,
            decimals: p,
            preview_show: _,
            preview_user_id: h,
          },
          setAttributes: g,
        } = e,
        m = (0, t.createElement)(s.SelectControl, {
          key: "show",
          value: n,
          label: (0, a.__)("Show", "ebox"),
          options: [
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Title", "placeholder: Team", "ebox"),
                c("team")
              ),
              value: "team_title",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s URL", "placeholder: Team", "ebox"),
                c("team")
              ),
              value: "team_url",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Price", "placeholder: Team", "ebox"),
                c("team")
              ),
              value: "team_price",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("%s Price Type", "placeholder: Team", "ebox"),
                c("team")
              ),
              value: "team_price_type",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)(
                  "%s Enrolled Users Count",
                  "placeholder: Team",
                  "ebox"
                ),
                c("team")
              ),
              value: "team_users_count",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholders: Team, Courses.
                (0, a._x)(
                  "%1$s %2$s Count",
                  "placeholders: Team, Courses",
                  "ebox"
                ),
                c("team"),
                c("courses")
              ),
              value: "team_courses_count",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)("User %s Status", "placeholder: Team", "ebox"),
                c("team")
              ),
              value: "user_team_status",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)(
                  "%s Completed On (date)",
                  "placeholder: Team",
                  "ebox"
                ),
                c("team")
              ),
              value: "completed_on",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)(
                  "%s Enrolled On (date)",
                  "placeholder: Team",
                  "ebox"
                ),
                c("team")
              ),
              value: "enrolled_on",
            },
            {
              label: (0, a.sprintf)(
                // translators: placeholder: Team.
                (0, a._x)(
                  "%s Completed Percentage",
                  "placeholder: Team",
                  "ebox"
                ),
                c("team")
              ),
              value: "percent_completed",
            },
          ],
          onChange: (e) => g({ show: e }),
        }),
        b = (0, t.createElement)(s.TextControl, {
          label: (0, a.sprintf)(
            // translators: placeholder: Team.
            (0, a._x)("%s ID", "placeholder: Team", "ebox"),
            c("team")
          ),
          help: (0, a.sprintf)(
            // translators: placeholders: Team, Team.
            (0, a._x)(
              "Enter single %1$s ID. Leave blank if used within a %2$s.",
              "placeholders: Team, Team",
              "ebox"
            ),
            c("team"),
            c("team")
          ),
          value: l || "",
          type: "number",
          onChange: function (e) {
            g("" != e && e < 0 ? { team_id: "0" } : { team_id: e });
          },
        });
      let y = "";
      [
        "user_team_status",
        "completed_on",
        "enrolled_on",
        "percent_completed",
      ].includes(n) &&
        (y = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("User ID", "ebox"),
          help: (0, a.__)(
            "Enter specific User ID. Leave blank for current User.",
            "ebox"
          ),
          value: i || "",
          type: "number",
          onChange: function (e) {
            g("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
          },
        }));
      let f = "";
      ["completed_on", "enrolled_on"].includes(n) &&
        (f = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Format", "ebox"),
          help: (0, a.__)(
            'This can be used to change the date format. Default: "F j, Y, g:i a.',
            "ebox"
          ),
          value: d || "",
          onChange: (e) => g({ format: e }),
        }));
      let w = "";
      ["percent_completed"].includes(n) &&
        (w = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Decimals", "ebox"),
          help: (0, a.__)(
            "Number of decimal places to show. Default is 2.",
            "ebox"
          ),
          value: p || "",
          type: "number",
          onChange: function (e) {
            g("" != e && e < 0 ? { decimals: "0" } : { decimals: e });
          },
        }));
      const v = (0, t.createElement)(
        s.PanelBody,
        { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
        (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Show Preview", "ebox"),
          checked: !!_,
          onChange: (e) => g({ preview_show: e }),
        }),
        (0, t.createElement)(
          s.PanelRow,
          { className: "ebox-block-error-message" },
          (0, a.__)("Preview settings are not saved.", "ebox")
        ),
        (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Preview User ID", "ebox"),
          help: (0, a.__)("Enter a User ID to test preview", "ebox"),
          value: h || "",
          type: "number",
          onChange: function (e) {
            g(
              "" != e && e < 0
                ? { preview_user_id: "0" }
                : { preview_user_id: e }
            );
          },
        })
      );
      function C() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          de
        );
      }
      function E(e) {
        return C();
      }
      return [
        (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            b,
            y,
            m,
            f,
            w
          ),
          v
        ),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: ie,
                attributes: a,
                key: ie,
                EmptyResponsePlaceholder: E,
              }))
            : C();
          var a;
        }, [e.attributes]),
      ];
    },
    save: function (e) {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const ue = "ebox/ld-usermeta",
    ce = (0, a.__)("ebox User meta", "ebox");
  (0, l.registerBlockType)(ue, {
    title: ce,
    description: (0, a.__)("This block displays User meta field", "ebox"),
    icon: "id",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      field: { type: "string", default: "user_login" },
      user_id: { type: "string", default: "" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
          attributes: {
            field: l,
            user_id: n,
            preview_show: i,
            preview_user_id: d,
          },
          setAttributes: c,
        } = e,
        p = (0, t.createElement)(s.SelectControl, {
          key: "field",
          value: l,
          label: (0, a.__)("Field", "ebox"),
          options: [
            {
              label: (0, a.__)("User Login", "ebox"),
              value: "user_login",
            },
            {
              label: (0, a.__)("User First Name", "ebox"),
              value: "first_name",
            },
            {
              label: (0, a.__)("User Last Name", "ebox"),
              value: "last_name",
            },
            {
              label: (0, a.__)("User First and Last Name", "ebox"),
              value: "first_last_name",
            },
            {
              label: (0, a.__)("User Display Name", "ebox"),
              value: "display_name",
            },
            {
              label: (0, a.__)("User Nicename", "ebox"),
              value: "user_nicename",
            },
            {
              label: (0, a.__)("User Nickname", "ebox"),
              value: "nickname",
            },
            {
              label: (0, a.__)("User Email", "ebox"),
              value: "user_email",
            },
            { label: (0, a.__)("User URL", "ebox"), value: "user_url" },
            {
              label: (0, a.__)("User Description", "ebox"),
              value: "description",
            },
          ],
          onChange: (e) => c({ field: e }),
        }),
        _ = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("User ID", "ebox"),
          help: (0, a.__)(
            "Enter specific User ID. Leave blank for current User.",
            "ebox"
          ),
          value: n || "",
          type: "number",
          onChange: function (e) {
            c("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
          },
        }),
        h = (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!i,
            onChange: (e) => c({ preview_show: e }),
          }),
          (0, t.createElement)(
            s.PanelRow,
            { className: "ebox-block-error-message" },
            (0, a.__)("Preview settings are not saved.", "ebox")
          ),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Preview User ID", "ebox"),
            help: (0, a.__)("Enter a User ID to test preview", "ebox"),
            value: d || "",
            type: "number",
            onChange: function (e) {
              c(
                "" != e && e < 0
                  ? { preview_user_id: "0" }
                  : { preview_user_id: e }
              );
            },
          })
        );
      function g() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          ce
        );
      }
      function m(e) {
        return g();
      }
      return [
        (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            _,
            p
          ),
          h
        ),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: ue,
                attributes: a,
                key: ue,
                EmptyResponsePlaceholder: m,
              }))
            : g();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const pe = "ebox/ld-registration",
    _e = (0, a.__)("ebox Registration", "ebox");
  (0, l.registerBlockType)(pe, {
    title: _e,
    description: (0, a.__)("Shows the registration form", "ebox"),
    icon: "id-alt",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      width: { type: "string" },
      example_show: { type: "boolean", default: 1 },
      preview_show: { type: "boolean", default: !0 },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      const {
          attributes: { preview_show: l, example_show: n, width: i },
          setAttributes: d,
        } = e,
        c = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Styling", "ebox"), initialOpen: !0 },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Form Width", "ebox"),
              help: (0, a.__)(
                "Sets the width of the registration form.",
                "ebox"
              ),
              value: i || "",
              type: "string",
              onChange: (e) => d({ width: e }),
            })
          ),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Preview", "ebox"),
              checked: !!l,
              onChange: (e) => d({ preview_show: e }),
            })
          )
        );
      function p() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          _e
        );
      }
      function _(e) {
        return p();
      }
      return [
        c,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: pe,
                attributes: a,
                key: pe,
                EmptyResponsePlaceholder: _,
              }))
            : p();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const he = "ebox/ld-infobar",
    ge = (0, a.__)("ebox Infobar", "ebox");
  (0, l.registerBlockType)(he, {
    title: ge,
    description: (0, a.__)(
      "This block displays an Infobar for a specific ebox related post.",
      "ebox"
    ),
    icon: "welcome-widgets-menus",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      display_type: { type: "string", default: "" },
      course_id: { type: "string", default: "" },
      post_id: { type: "string", default: "" },
      team_id: { type: "string", default: "" },
      user_id: { type: "string", default: "" },
      preview_show: { type: "boolean", default: 1 },
      preview_user_id: { type: "string", default: "" },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
        attributes: {
          display_type: l,
          course_id: n,
          post_id: i,
          team_id: d,
          user_id: p,
          preview_show: _,
          preview_user_id: h,
        },
        setAttributes: m,
      } = e;
      var b, y;
      (b = (0, t.createElement)(s.SelectControl, {
        key: "display_type",
        label: (0, a.__)("Display Type", "ebox"),
        value: l,
        help: sprintf(
          // translators: placeholders: Course, Team.
          (0, a._x)(
            "Require if not used within a %1$s or %2$s. Or to override default display.",
            "placeholders: Course, Team",
            "ebox"
          ),
          c("course"),
          c("team")
        ),
        options: [
          { label: (0, a.__)("Select a Display Type", "ebox"), value: "" },
          { label: c("course"), value: "ebox-courses" },
          { label: c("team"), value: "teams" },
        ],
        onChange: (e) => m({ display_type: e }),
      })),
        "ebox-courses" === l
          ? (m({ team_id: "" }),
            (y = (0, t.createElement)(
              React.Fragment,
              null,
              (0, t.createElement)(s.TextControl, {
                label: sprintf(
                  // translators: placeholder: Course.
                  (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                  c("course")
                ),
                help: sprintf(
                  // translators: placeholders: Course, Course.
                  (0, a._x)(
                    "Enter single %1$s ID. Leave blank if used within a %2$s.",
                    "placeholders: Course, Course",
                    "ebox"
                  ),
                  c("course"),
                  c("course")
                ),
                value: n || "",
                type: "number",
                onChange: function (e) {
                  m("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
                },
              }),
              (0, t.createElement)(s.TextControl, {
                label: (0, a.__)("Step ID", "ebox"),
                help: sprintf(
                  // translators: placeholders: Course, Course.
                  (0, a._x)(
                    "Enter single Step ID. Leave blank if used within a %1$s step.",
                    "placeholders: Course, Course",
                    "ebox"
                  ),
                  c("course"),
                  c("course")
                ),
                value: i || "",
                type: "number",
                onChange: function (e) {
                  m("" != e && e < 0 ? { post_id: "0" } : { post_id: e });
                },
              })
            )))
          : "teams" === l &&
            (m({ course_id: "" }),
            m({ post_id: "" }),
            (y = (0, t.createElement)(s.TextControl, {
              label: sprintf(
                // translators: placeholder: Team.
                (0, a._x)("%s ID", "placeholder: Team", "ebox"),
                c("team")
              ),
              help: sprintf(
                // translators: placeholders: Team, Team.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Team, Team",
                  "ebox"
                ),
                c("team"),
                c("team")
              ),
              value: d || "",
              type: "number",
              onChange: function (e) {
                m("" != e && e < 0 ? { team_id: "0" } : { team_id: e });
              },
            })));
      const f = (0, t.createElement)(
        r.InspectorControls,
        { key: "controls" },
        g(),
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Settings", "ebox") },
          b,
          y,
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("User ID", "ebox"),
            help: (0, a.__)(
              "Enter specific User ID. Leave blank for current User.",
              "ebox"
            ),
            value: p || "",
            type: "number",
            onChange: function (e) {
              m("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
            },
          })
        ),
        (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!_,
            onChange: (e) => m({ preview_show: e }),
          }),
          (0, t.createElement)(
            s.PanelRow,
            { className: "ebox-block-error-message" },
            (0, a.__)("Preview settings are not saved.", "ebox")
          ),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Preview User ID", "ebox"),
            help: (0, a.__)("Enter a User ID for preview.", "ebox"),
            value: h || "",
            type: "number",
            onChange: function (e) {
              m(
                "" != e && e < 0
                  ? { preview_user_id: "0" }
                  : { preview_user_id: e }
              );
            },
          })
        )
      );
      function w() {
        return sprintf(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          ge
        );
      }
      function v(e) {
        return w();
      }
      return [
        f,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: he,
                attributes: a,
                key: he,
                EmptyResponsePlaceholder: v,
              }))
            : w();
          var a;
        }, [e.attributes]),
      ];
    },
    save: function (e) {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const me = "ebox/ld-materials",
    be = (0, a.__)("ebox Materials", "ebox");
  (0, l.registerBlockType)(me, {
    title: be,
    description: (0, a.__)(
      "This block displays the materials for a specific ebox related post.",
      "ebox"
    ),
    icon: "text",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      post_id: { type: "string", default: "" },
      autop: { type: "string", default: "true" },
      preview_show: { type: "boolean", default: 1 },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
          attributes: { post_id: l, autop: n, preview_show: i },
          setAttributes: d,
        } = e,
        c = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Post ID", "ebox"),
          help: (0, a.__)(
            "Enter a Post ID of the ebox post that you want to display materials for.",
            "ebox"
          ),
          value: l || "",
          type: "number",
          onChange: function (e) {
            d("" != e && e < 0 ? { post_id: "0" } : { post_id: e });
          },
        }),
        p = (0, t.createElement)(s.ToggleControl, {
          label: (0, a.__)("Auto Paragraph", "ebox"),
          help: (0, a.__)(
            "Whether to format materials content using wpautop.",
            "ebox"
          ),
          checked: !!n,
          onChange: (e) => d({ autop: e }),
        }),
        _ = (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!i,
            onChange: (e) => d({ preview_show: e }),
          })
        );
      function h() {
        return sprintf(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          be
        );
      }
      function m(e) {
        return h();
      }
      return [
        (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          g(),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            c,
            p
          ),
          _
        ),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: me,
                attributes: a,
                key: me,
                EmptyResponsePlaceholder: m,
              }))
            : h();
          var a;
        }, [e.attributes]),
      ];
    },
    save: function (e) {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const ye = "ebox/ld-user-status",
    fe = (0, a.__)("ebox User Status");
  (0, l.registerBlockType)(ye, {
    title: fe,
    description: (0, a.__)(
      "This block displays information of enrolled courses and their progress for a user. Defaults to current logged in user if no ID specified.",
      "ebox"
    ),
    icon: "analytics",
    category: "ebox-blocks",
    supports: { customClassName: !1 },
    attributes: {
      user_id: { type: "string", default: "" },
      registered_num: { type: "string", default: "" },
      registered_order_by: { type: "string" },
      registered_order: { type: "string" },
      preview_show: { type: "boolean", default: !0 },
      preview_user_id: { type: "string", default: "" },
      isblock: { type: "boolean", default: 1 },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
          attributes: {
            user_id: l,
            registered_num: n,
            registered_order_by: i,
            registered_order: d,
            preview_show: c,
            preview_user_id: p,
            isblock: _,
          },
          setAttributes: h,
        } = e,
        m = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("User ID", "ebox"),
          help: (0, a.__)("ID of the user to display information for.", "ebox"),
          value: l || "",
          type: "number",
          onChange: function (e) {
            h("" != e && e < 0 ? { user_id: "0" } : { user_id: e });
          },
        }),
        b = (0, t.createElement)(s.TextControl, {
          label: (0, a.__)("Courses per page", "ebox"),
          help: (0, a.__)(
            "Number of courses to display per page. Set to 0 for no pagination.",
            "ebox"
          ),
          value: n || "",
          type: "number",
          onChange: function (e) {
            h(
              "" != e && e < 0 ? { registered_num: "0" } : { registered_num: e }
            );
          },
        }),
        y = (0, t.createElement)(s.SelectControl, {
          key: "registered_order_by",
          value: i,
          label: (0, a.__)("Order By", "ebox"),
          options: [
            { label: (0, a.__)("Title", "ebox"), value: "post_title" },
            { label: (0, a.__)("ID", "ebox"), value: "post_id" },
            { label: (0, a.__)("Date", "ebox"), value: "post_date" },
            { label: (0, a.__)("Menu", "ebox"), value: "menu_order" },
          ],
          onChange: (e) => h({ registered_order_by: e }),
        }),
        f = (0, t.createElement)(s.SelectControl, {
          key: "registered_order",
          value: d,
          label: (0, a.__)("Order", "ebox"),
          options: [
            { label: (0, a.__)("ASC (default)", "ebox"), value: "ASC" },
            { label: (0, a.__)("DESC", "ebox"), value: "DESC" },
          ],
          onChange: (e) => h({ registered_order: e }),
        }),
        w = (0, t.createElement)(
          s.PanelBody,
          { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
          (0, t.createElement)(s.ToggleControl, {
            label: (0, a.__)("Show Preview", "ebox"),
            checked: !!c,
            onChange: (e) => h({ preview_show: e }),
          }),
          (0, t.createElement)(
            s.PanelRow,
            { className: "ebox-block-error-message" },
            (0, a.__)("Preview settings are not saved.", "ebox")
          ),
          (0, t.createElement)(s.TextControl, {
            label: (0, a.__)("Preview User ID", "ebox"),
            help: (0, a.__)("Enter a User ID to test preview", "ebox"),
            value: p || "",
            type: "number",
            onChange: function (e) {
              h(
                "" != e && e < 0
                  ? { preview_user_id: "0" }
                  : { preview_user_id: e }
              );
            },
          })
        );
      function v() {
        return sprintf(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          fe
        );
      }
      function C(e) {
        return v();
      }
      return [
        (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          g(),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            m,
            b,
            y,
            f
          ),
          w
        ),
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: ye,
                attributes: a,
                key: ye,
                EmptyResponsePlaceholder: C,
              }))
            : v();
          var a;
        }, [e.attributes]),
      ];
    },
    save: function (e) {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const we = "ebox/ld-navigation",
    ve = (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)("ebox %s Navigation", "placeholder: Course", "ebox"),
      c("course")
    );
  (0, l.registerBlockType)(we, {
    title: ve,
    description: (0, a.sprintf)(
      // translators: placeholder: Course.
      (0, a._x)(
        "This block displays the %s Navigation.",
        "placeholder: Course",
        "ebox"
      ),
      c("course")
    ),
    icon: "format-aside",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      course_id: { type: "string", default: "" },
      post_id: { type: "string", default: "" },
      preview_show: { type: "boolean", default: 1 },
      preview_post_id: { type: "string", default: "" },
      example_show: { type: "boolean", default: 0 },
      editing_post_meta: { type: "object" },
    },
    edit: (e) => {
      const {
          attributes: {
            course_id: l,
            post_id: n,
            preview_show: i,
            preview_post_id: d,
            example_show: p,
          },
          className: _,
          setAttributes: h,
        } = e,
        m = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          g(),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Settings", "ebox") },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)("%s ID", "placeholder: Course", "ebox"),
                c("course")
              ),
              help: (0, a.sprintf)(
                // translators: placeholders: Course, Course.
                (0, a._x)(
                  "Enter single %1$s ID. Leave blank if used within a %2$s.",
                  "placeholders: Course, Course",
                  "ebox"
                ),
                c("course"),
                c("course")
              ),
              value: l || "",
              type: "number",
              onChange: function (e) {
                h("" != e && e < 0 ? { course_id: "0" } : { course_id: e });
              },
            }),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Step ID", "ebox"),
              help: (0, a.sprintf)(
                // translators: placeholder: Course.
                (0, a._x)(
                  "Enter single Step ID. Leave blank if used within a %s.",
                  "placeholder: Course",
                  "ebox"
                ),
                c("course")
              ),
              value: n || "",
              type: "number",
              onChange: function (e) {
                h("" != e && e < 0 ? { post_id: "0" } : { post_id: e });
              },
            })
          ),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Preview", "ebox"),
              checked: !!i,
              onChange: (e) => h({ preview_show: e }),
            }),
            (0, t.createElement)(
              s.PanelRow,
              { className: "ebox-block-error-message" },
              (0, a.__)("Preview settings are not saved.", "ebox")
            ),
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Step ID", "ebox"),
              help: (0, a.__)("Enter a Step ID to test preview", "ebox"),
              value: d || "",
              type: "number",
              onChange: function (e) {
                h(
                  "" != e && e < 0
                    ? { preview_post_id: "0" }
                    : { preview_post_id: e }
                );
              },
            })
          )
        );
      function b() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          ve
        );
      }
      function y(e) {
        return b();
      }
      return [
        m,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: we,
                attributes: a,
                key: we,
                EmptyResponsePlaceholder: y,
              }))
            : b();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  const Ce = "ebox/ld-reset-password",
    Ee = (0, a.__)("ebox Reset Password", "ebox");
  (0, l.registerBlockType)(Ce, {
    title: Ee,
    description: (0, a.__)("Shows the reset password form", "ebox"),
    icon: "id-alt",
    category: "ebox-blocks",
    example: { attributes: { example_show: 1 } },
    supports: { customClassName: !1 },
    attributes: {
      width: { type: "string" },
      example_show: { type: "boolean", default: 1 },
      preview_show: { type: "boolean", default: !0 },
      editing_post_meta: { type: "object" },
    },
    edit: function (e) {
      const {
          attributes: { preview_show: l, example_show: n, width: i },
          setAttributes: d,
        } = e,
        c = (0, t.createElement)(
          r.InspectorControls,
          { key: "controls" },
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Styling", "ebox"), initialOpen: !0 },
            (0, t.createElement)(s.TextControl, {
              label: (0, a.__)("Form Width", "ebox"),
              help: (0, a.__)(
                "Sets the width of the reset password form.",
                "ebox"
              ),
              value: i || "",
              type: "string",
              onChange: (e) => d({ width: e }),
            })
          ),
          (0, t.createElement)(
            s.PanelBody,
            { title: (0, a.__)("Preview", "ebox"), initialOpen: !1 },
            (0, t.createElement)(s.ToggleControl, {
              label: (0, a.__)("Show Preview", "ebox"),
              checked: !!l,
              onChange: (e) => d({ preview_show: e }),
            })
          )
        );
      function p() {
        return (0, a.sprintf)(
          // translators: placeholder: block_title.
          (0, a._x)(
            "%s block output shown here",
            "placeholder: block_title",
            "ebox"
          ),
          Ee
        );
      }
      function _(e) {
        return p();
      }
      return [
        c,
        (0, t.useMemo)(() => {
          return 1 == (a = e.attributes).preview_show
            ? ((a.editing_post_meta = u()),
              (0, t.createElement)(o(), {
                block: Ce,
                attributes: a,
                key: Ce,
                EmptyResponsePlaceholder: _,
              }))
            : p();
          var a;
        }, [e.attributes]),
      ];
    },
    save: (e) => {
      delete e.attributes.example_show, delete e.attributes.editing_post_meta;
    },
  });
  var xe = window.wp.data,
    ke = window.React,
    Te = e.n(ke);
  const Pe = (0, ke.createContext)({});
  const De = {
    block_key: "ebox/ld-exam",
    block_title: (0, a.sprintf)(
      // translators: placeholder: Challenge Exam.
      (0, a._x)("ebox %s", "placeholder: Challenge Exam", "ebox"),
      c("exam")
    ),
    block_description: (0, a.sprintf)(
      // translators: placeholder: Create a Challenge Exam.
      (0, a._x)("Create a %s", "placeholder: Create a Challenge Exam", "ebox"),
      c("exam")
    ),
  };
  (0, l.registerBlockType)(De.block_key, {
    title: De.block_title,
    description: De.block_description,
    icon: "editor-help",
    category: "ebox-blocks",
    supports: { html: !1 },
    attributes: { ld_version: { type: "string" } },
    edit: (e) => {
      const {
          attributes: { ld_version: a = "" },
          setAttributes: l,
          clientId: s,
        } = e,
        n = (0, xe.useSelect)(
          (e) => e("core/block-editor").getBlockOrder(s),
          []
        ),
        o = (0, t.useMemo)(() => ({ blockOrder: n }), [s, n]);
      return (
        "" === a && l({ ld_version: ldlms_settings.version }),
        (0, t.createElement)(
          Pe.Provider,
          { value: o },
          (0, t.createElement)(r.InnerBlocks, {
            allowedBlocks: ["ebox/ld-exam-question"],
            template: [["ebox/ld-exam-question", {}]],
            renderAppender: () =>
              (0, t.createElement)(r.ButtonBlockAppender, {
                className: "ld-exam-block-appender",
                rootClientId: s,
              }),
            templateInsertUpdatesSelection: !0,
          })
        )
      );
    },
    save: () => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
  var Ie = {
      color: void 0,
      size: void 0,
      className: void 0,
      style: void 0,
      attr: void 0,
    },
    Se = Te().createContext && Te().createContext(Ie),
    ze = function () {
      return (
        (ze =
          Object.assign ||
          function (e) {
            for (var t, a = 1, l = arguments.length; a < l; a++)
              for (var r in (t = arguments[a]))
                Object.prototype.hasOwnProperty.call(t, r) && (e[r] = t[r]);
            return e;
          }),
        ze.apply(this, arguments)
      );
    };
  function qe(e) {
    return (
      e &&
      e.map(function (e, t) {
        return Te().createElement(e.tag, ze({ key: t }, e.attr), qe(e.child));
      })
    );
  }
  function Be(e) {
    return function (t) {
      return Te().createElement(
        Le,
        ze({ attr: ze({}, e.attr) }, t),
        qe(e.child)
      );
    };
  }
  function Le(e) {
    var t = function (t) {
      var a,
        l = e.attr,
        r = e.size,
        s = e.title,
        n = (function (e, t) {
          var a = {};
          for (var l in e)
            Object.prototype.hasOwnProperty.call(e, l) &&
              t.indexOf(l) < 0 &&
              (a[l] = e[l]);
          if (null != e && "function" == typeof Object.getOwnPropertySymbols) {
            var r = 0;
            for (l = Object.getOwnPropertySymbols(e); r < l.length; r++)
              t.indexOf(l[r]) < 0 &&
                Object.prototype.propertyIsEnumerable.call(e, l[r]) &&
                (a[l[r]] = e[l[r]]);
          }
          return a;
        })(e, ["attr", "size", "title"]),
        o = r || t.size || "1em";
      return (
        t.className && (a = t.className),
        e.className && (a = (a ? a + " " : "") + e.className),
        Te().createElement(
          "svg",
          ze(
            { stroke: "currentColor", fill: "currentColor", strokeWidth: "0" },
            t.attr,
            l,
            n,
            {
              className: a,
              style: ze(ze({ color: e.color || t.color }, t.style), e.style),
              height: o,
              width: o,
              xmlns: "http://www.w3.org/2000/svg",
            }
          ),
          s && Te().createElement("title", null, s),
          e.children
        )
      );
    };
    return void 0 !== Se
      ? Te().createElement(Se.Consumer, null, function (e) {
          return t(e);
        })
      : t(Ie);
  }
  function Oe(e) {
    return Be({
      tag: "svg",
      attr: { viewBox: "0 0 24 24" },
      child: [
        { tag: "path", attr: { fill: "none", d: "M0 0h24v24H0z" } },
        {
          tag: "path",
          attr: {
            d: "M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z",
          },
        },
      ],
    })(e);
  }
  function Ne(e) {
    return Be({
      tag: "svg",
      attr: { viewBox: "0 0 24 24" },
      child: [
        { tag: "path", attr: { fill: "none", d: "M0 0h24v24H0z" } },
        {
          tag: "path",
          attr: {
            d: "M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z",
          },
        },
      ],
    })(e);
  }
  function Ue(e) {
    return Be({
      tag: "svg",
      attr: { viewBox: "0 0 24 24" },
      child: [
        { tag: "path", attr: { fill: "none", d: "M0 0h24v24H0V0z" } },
        { tag: "path", attr: { d: "M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6z" } },
        {
          tag: "path",
          attr: {
            d: "M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-5.99 13c-.59 0-1.05-.47-1.05-1.05 0-.59.47-1.04 1.05-1.04.59 0 1.04.45 1.04 1.04-.01.58-.45 1.05-1.04 1.05zm2.5-6.17c-.63.93-1.23 1.21-1.56 1.81-.13.24-.18.4-.18 1.18h-1.52c0-.41-.06-1.08.26-1.65.41-.73 1.18-1.16 1.63-1.8.48-.68.21-1.94-1.14-1.94-.88 0-1.32.67-1.5 1.23l-1.37-.57C11.51 5.96 12.52 5 13.99 5c1.23 0 2.08.56 2.51 1.26.37.61.58 1.73.01 2.57z",
          },
        },
      ],
    })(e);
  }
  function Ae(e) {
    return Be({
      tag: "svg",
      attr: { viewBox: "0 0 24 24" },
      child: [
        { tag: "path", attr: { fill: "none", d: "M0 0h24v24H0z" } },
        {
          tag: "path",
          attr: {
            d: "M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z",
          },
        },
      ],
    })(e);
  }
  function $e(e) {
    return Be({
      tag: "svg",
      attr: { viewBox: "0 0 24 24" },
      child: [
        { tag: "path", attr: { fill: "none", d: "M0 0h24v24H0z" } },
        {
          tag: "path",
          attr: {
            d: "M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z",
          },
        },
      ],
    })(e);
  }
  function Ge(e) {
    return Be({
      tag: "svg",
      attr: { viewBox: "0 0 1024 1024" },
      child: [
        {
          tag: "path",
          attr: {
            d: "M955.7 856l-416-720c-6.2-10.7-16.9-16-27.7-16s-21.6 5.3-27.7 16l-416 720C56 877.4 71.4 904 96 904h832c24.6 0 40-26.6 27.7-48zM480 416c0-4.4 3.6-8 8-8h48c4.4 0 8 3.6 8 8v184c0 4.4-3.6 8-8 8h-48c-4.4 0-8-3.6-8-8V416zm32 352a48.01 48.01 0 0 1 0-96 48.01 48.01 0 0 1 0 96z",
          },
        },
      ],
    })(e);
  }
  const Re = [
      { label: (0, a.__)("Single", "ebox"), value: "single" },
      { label: (0, a.__)("Multiple", "ebox"), value: "multiple" },
    ],
    Qe = (0, a.__)("The Question is empty.", "ebox"),
    Me = sprintf(
      // translators: placeholder: Question type.
      (0, a._x)("%s type", "placeholder: Question type", "ebox"),
      c("question")
    );
  const We = {
    block_key: "ebox/ld-exam-question",
    block_title: (0, a.sprintf)(
      // translators: placeholder: Challenge Exam Question.
      (0, a._x)("%s Question", "placeholder: Challenge Exam Question", "ebox"),
      c("exam")
    ),
    block_description: (0, a.sprintf)(
      // translators: placeholder: Create a question for your Challenge Exam.
      (0, a._x)(
        "Create a question for your %s",
        "placeholder: Create a question for your Challenge Exam",
        "ebox"
      ),
      c("exam")
    ),
  };
  function je(e) {
    return Be({
      tag: "svg",
      attr: { viewBox: "0 0 24 24" },
      child: [
        {
          tag: "g",
          attr: {},
          child: [
            { tag: "path", attr: { fill: "none", d: "M0 0h24v24H0z" } },
            {
              tag: "path",
              attr: {
                d: "M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z",
              },
            },
          ],
        },
      ],
    })(e);
  }
  function Fe(e) {
    return Be({
      tag: "svg",
      attr: { viewBox: "0 0 24 24" },
      child: [
        {
          tag: "g",
          attr: {},
          child: [
            { tag: "path", attr: { fill: "none", d: "M0 0h24v24H0z" } },
            {
              tag: "path",
              attr: {
                d: "M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-.997-6l7.07-7.071-1.414-1.414-5.656 5.657-2.829-2.829-1.414 1.414L11.003 16z",
              },
            },
          ],
        },
      ],
    })(e);
  }
  (0, l.registerBlockType)(We.block_key, {
    title: We.block_title,
    description: We.block_description,
    icon: (0, t.createElement)(Oe, null),
    category: "ebox-blocks",
    parent: ["ebox/ld-exam"],
    supports: { html: !1 },
    providesContext: { "ebox/question_type": "question_type" },
    attributes: {
      question_title: { type: "string" },
      question_type: { type: "string" },
    },
    edit: (e) => {
      const {
          attributes: { question_title: l = "", question_type: n = "" },
          setAttributes: o,
          clientId: i,
        } = e,
        [d, u] = (0, t.useState)(!1),
        [c, p] = (0, t.useState)(!1),
        { innerBlocksClientIds: _, selectedBlockClientId: h } = (0,
        xe.useSelect)((e) => ({
          innerBlocksClientIds: e(
            "core/block-editor"
          ).getClientIdsOfDescendants([i]),
          selectedBlockClientId:
            e("core/block-editor").getSelectedBlockClientId(),
        })),
        g = _.includes(h) || h === i,
        { blockOrder: m } = (0, t.useContext)(Pe),
        b = m.lastIndexOf(i) === m.length - 1;
      !1 === c && !0 === g && p(!0),
        !0 === c && !1 === d && !1 === g && u(!0),
        "" === n && o({ question_type: "single" });
      const y = d ? "ebox-exam-question-allow-validations" : "";
      return (0, t.createElement)(
        t.Fragment,
        null,
        (0, t.createElement)(
          r.InspectorControls,
          null,
          (0, t.createElement)(
            s.PanelBody,
            { title: Me, initialOpen: !0 },
            (0, t.createElement)(
              s.PanelRow,
              null,
              (0, t.createElement)(s.SelectControl, {
                value: n,
                options: Re,
                onChange: (e) => o({ question_type: e }),
              })
            )
          )
        ),
        (0, t.createElement)(r.PlainText, {
          className: "ebox-exam-question",
          value: l,
          placeholder: (0, a.__)("Question", "ebox"),
          onChange: (e) => o({ question_title: e }),
        }),
        0 === l.length &&
          (0, t.createElement)(
            "div",
            { className: `${y} ebox-exam-question-empty-title` },
            (0, t.createElement)(Ge, { fill: "red" }),
            (0, t.createElement)("span", null, Qe)
          ),
        (0, t.createElement)(
          "div",
          { className: `${y} ebox-exam-question-flexbox` },
          (0, t.createElement)(r.InnerBlocks, {
            template: [
              ["ebox/ld-question-description", {}],
              ["ebox/ld-question-answers-block", {}],
              ["ebox/ld-correct-answer-message-block", {}],
              ["ebox/ld-incorrect-answer-message-block", {}],
            ],
            templateLock: "all",
          })
        ),
        !b && (0, t.createElement)("hr", null)
      );
    },
    save: () => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
  const Ve = (e) => {
    const {
      isMultiple: l,
      attributes: { answer_label: n = "", answer_correct: o = !1 },
      setAttributes: i,
    } = e;
    return (0, t.createElement)(
      t.Fragment,
      null,
      (0, t.createElement)(
        "span",
        { className: "ebox-exam-question-answer-select" },
        l
          ? o
            ? (0, t.createElement)($e, null)
            : (0, t.createElement)(Ae, null)
          : o
          ? (0, t.createElement)(Fe, null)
          : (0, t.createElement)(je, null)
      ),
      (0, t.createElement)(
        "span",
        { className: "ebox-exam-question-answer-input" },
        (0, t.createElement)(r.RichText, {
          value: n,
          placeholder: (0, a.__)("Add Answer", "ebox"),
          onChange: (e) => i({ answer_label: e }),
        })
      ),
      (0, t.createElement)(
        "span",
        { className: "ebox-exam-question-answer-toggle" },
        (0, t.createElement)(s.Button, {
          isSmall: !0,
          className: "ebox-exam-question-answer-toggle-button",
          variant: o ? "primary" : "secondary",
          disabled: 0 === n.length,
          onClick: () => i({ answer_correct: !o }),
          text: o
            ? (0, a.__)("Correct", "ebox")
            : (0, a.__)("Incorrect", "ebox"),
        })
      )
    );
  };
  var He = (e) => {
      const { type: l, attributes: r, setAttributes: n } = e,
        o = (0, a.__)("Answer is missing.", "ebox"),
        i = (0, a.__)("Required correct answer is missing.", "ebox"),
        d = "multiple" === l,
        u = r
          .map((e) => {
            let { answer_correct: t } = e;
            return null != t && t;
          })
          .lastIndexOf(!0);
      let c = 0,
        p = !1;
      const _ =
        Array.isArray(r) && r.length >= 1
          ? [
              ...r
                .filter((e) => "" !== e.label)
                .map(
                  (e, t) => (
                    (c = !0 === e.answer_correct ? c + 1 : c),
                    (p = !0),
                    d || t === u ? { ...e } : { ...e, answer_correct: !1 }
                  )
                ),
            ]
          : [
              { answer_label: "", answer_correct: !0 },
              { answer_label: "", answer_correct: !1 },
            ];
      !d && 1 < c && n(_),
        "" !== _[_.length - 1].answer_label &&
          _.push({ answer_label: "", answer_correct: !1 });
      const h = (e) => {
        if (e > _.length) return;
        const t = [..._];
        t.splice(e, 1),
          n(
            t.filter((e) => {
              let { answer_label: t } = e;
              return "" !== t;
            })
          );
      };
      return (0, t.createElement)(
        t.Fragment,
        null,
        (0, t.createElement)(
          "div",
          { className: "ebox-exam-question-answers-list" },
          (0, t.createElement)(
            "div",
            { className: "ebox-exam-question-single-answer" },
            !p &&
              (0, t.createElement)(
                "div",
                { className: "ebox-exam-question-empty-answers" },
                (0, t.createElement)(Ge, { fill: "red" }),
                (0, t.createElement)("span", null, o)
              ),
            p &&
              0 === c &&
              (0, t.createElement)(
                "div",
                { className: "ebox-exam-question-empty-correct" },
                (0, t.createElement)(Ge, { fill: "red" }),
                (0, t.createElement)("span", null, i)
              )
          ),
          _.length &&
            _.map((e, a) =>
              (0, t.createElement)(
                "div",
                { className: "ebox-exam-question-single-answer", key: a },
                (0, t.createElement)(
                  "span",
                  { className: "ebox-exam-question-single-answer-delete" },
                  _.length - 1 !== a &&
                    (0, t.createElement)(s.Button, {
                      isSmall: !0,
                      onClick: () => h(a),
                      icon: (0, t.createElement)(Ne, null),
                    })
                ),
                (0, t.createElement)(Ve, {
                  isMultiple: d,
                  attributes: e,
                  setAttributes: (e) =>
                    ((e, t) => {
                      if ("" === t.answer_label) h(e);
                      else {
                        const a = _.map((e) =>
                          !0 !== t.answer_correct || d
                            ? { ...e }
                            : { ...e, answer_correct: !1 }
                        );
                        (a[e] = { ..._[e], ...t }),
                          n(
                            a.filter((e) => {
                              let { answer_label: t } = e;
                              return "" !== t;
                            })
                          );
                      }
                    })(a, e),
                })
              )
            )
        )
      );
    },
    Ye = {
      single: (e) => (0, t.createElement)(He, e),
      multiple: (e) => (0, t.createElement)(He, e),
    };
  const Ze = (0, a.sprintf)(
      // translators: placeholder: Challenge Exam Question Answers.
      (0, a._x)(
        "%s Question Answers",
        "placeholder: Challenge Exam Question Answers",
        "ebox"
      ),
      c("exam")
    ),
    Je = (0, a.sprintf)(
      // translators: placeholder: Challenge Exam Question Answers.
      (0, a._x)(
        "%s Question Answers",
        "placeholder: Challenge Exam Question Answers",
        "ebox"
      ),
      c("exam")
    );
  (0, l.registerBlockType)("ebox/ld-question-answers-block", {
    title: Ze,
    description: Je,
    icon: (0, t.createElement)(Ue, null),
    category: "ebox-blocks",
    parent: ["ebox/ld-exam-question"],
    usesContext: ["ebox/question_type"],
    attributes: {
      question_type: { type: "string", default: "" },
      answers: { type: "array", default: [] },
    },
    supports: { inserter: !1, html: !1 },
    edit: (e) => {
      const {
          attributes: { answers: a },
          setAttributes: l,
          context: r,
          clientId: s,
        } = e,
        n =
          "ebox/question_type" in r && r["ebox/question_type"]
            ? r["ebox/question_type"]
            : "single",
        o = Ye[n];
      return (
        l({ question_type: n }),
        (0, t.createElement)(o, {
          clientId: s,
          type: n,
          attributes: [...a],
          setAttributes: (e) => l({ answers: [...e] }),
        })
      );
    },
    save: () => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
  const Ke = {
      icon: (0, t.createElement)(Ue, null),
      parent: ["ebox/ld-exam-question"],
      category: "ebox-blocks",
      supports: { inserter: !1, html: !1 },
      save: () => (0, t.createElement)(r.InnerBlocks.Content, null),
    },
    Xe = ["core/image", "core/paragraph"],
    et =
      ((0, l.registerBlockType)("ebox/ld-incorrect-answer-message-block", {
        ...Ke,
        title: (0, a.__)("Incorrect answer message", "ebox"),
        description: (0, a.__)("Incorrect answer message", "ebox"),
        edit: () => {
          const e = [
            [
              "core/paragraph",
              {
                placeholder: (0, a.__)(
                  "Add a message for incorrect answer (Optional)",
                  "ebox"
                ),
              },
            ],
          ];
          return (0, t.createElement)(
            t.Fragment,
            null,
            (0, t.createElement)(
              "div",
              null,
              (0, a.__)("Incorrect Answer Message", "ebox")
            ),
            (0, t.createElement)(r.InnerBlocks, {
              allowedBlocks: Xe,
              template: e,
              templateLock: !1,
            })
          );
        },
      }),
      (0, l.registerBlockType)("ebox/ld-correct-answer-message-block", {
        ...Ke,
        title: (0, a.__)("Correct answer message", "ebox"),
        description: (0, a.__)("Correct answer message", "ebox"),
        edit: () => {
          const e = [
            [
              "core/paragraph",
              {
                placeholder: (0, a.__)(
                  "Add a message for correct answer (Optional)",
                  "ebox"
                ),
              },
            ],
          ];
          return (0, t.createElement)(
            t.Fragment,
            null,
            (0, t.createElement)(
              "div",
              null,
              (0, a.__)("Correct Answer Message", "ebox")
            ),
            (0, t.createElement)(r.InnerBlocks, {
              allowedBlocks: Xe,
              template: e,
              templateLock: !1,
            })
          );
        },
      }),
      (0, a.__)("Question Notes", "ebox")),
    tt = (0, a.sprintf)(
      // translators: placeholder: Write a description for the Challenge Exam question.
      (0, a._x)(
        "Write a description for the %s question.",
        "placeholder: Write a description for the Challenge Exam question",
        "ebox"
      ),
      c("exam")
    );
  (0, l.registerBlockType)("ebox/ld-question-description", {
    title: et,
    description: tt,
    icon: (0, t.createElement)(Oe, null),
    parent: ["ebox/ld-exam-question"],
    category: "ebox-blocks",
    supports: { inserter: !1, html: !1 },
    edit: () => {
      const e = [
        [
          "core/paragraph",
          {
            placeholder: (0, a.__)(
              "Add a Description or type '/' to choose a block (Optional)",
              "ebox"
            ),
          },
        ],
      ];
      return (0, t.createElement)(r.InnerBlocks, {
        templateLock: !1,
        template: e,
      });
    },
    save: () => (0, t.createElement)(r.InnerBlocks.Content, null),
  });
})();
