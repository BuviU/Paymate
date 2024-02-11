/**
 * ebox Block ld-question-description
 *
 * @since 4.0.0
 * @package ebox
 */

import { __, _x, sprintf } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { InnerBlocks } from "@wordpress/block-editor";
import { MdAssignment } from "react-icons/md";

/**
 * ebox block functions
 */
import { ldlms_get_custom_label } from "../../../ldlms.js";

const block_key = "ebox/ld-question-description";
const block_title = __("Question Notes", "ebox");
const block_description = sprintf(
  // translators: placeholder: Write a description for the Challenge Exam question.
  _x(
    "Write a description for the %s question.",
    "placeholder: Write a description for the Challenge Exam question",
    "ebox"
  ),
  ldlms_get_custom_label("exam")
);

registerBlockType(block_key, {
  title: block_title,
  description: block_description,
  icon: <MdAssignment />,
  parent: ["ebox/ld-exam-question"],
  category: "ebox-blocks",
  supports: {
    inserter: false,
    html: false,
  },
  edit: () => {
    const template = [
      [
        "core/paragraph",
        {
          placeholder: __(
            "Add a Description or type '/' to choose a block (Optional)",
            "ebox"
          ),
        },
      ],
    ];
    return <InnerBlocks templateLock={false} template={template} />;
  },
  save: () => <InnerBlocks.Content />,
});
