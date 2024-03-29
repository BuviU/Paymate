/**
 * ebox Block ld-exam-question
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
import { ldlms_get_custom_label } from "../../ldlms.js";
import Edit from "./edit";

export const settings = {
  block_key: "ebox/ld-exam-question",
  block_title: sprintf(
    // translators: placeholder: Challenge Exam Question.
    _x("%s Question", "placeholder: Challenge Exam Question", "ebox"),
    ldlms_get_custom_label("exam")
  ),
  block_description: sprintf(
    // translators: placeholder: Create a question for your Challenge Exam.
    _x(
      "Create a question for your %s",
      "placeholder: Create a question for your Challenge Exam",
      "ebox"
    ),
    ldlms_get_custom_label("exam")
  ),
};

registerBlockType(settings.block_key, {
  title: settings.block_title,
  description: settings.block_description,
  icon: <MdAssignment />,
  category: "ebox-blocks",
  parent: ["ebox/ld-exam"],
  supports: {
    html: false,
  },
  providesContext: {
    "ebox/question_type": "question_type",
  },
  attributes: {
    question_title: {
      type: "string",
    },
    question_type: {
      type: "string",
    },
  },
  edit: Edit,
  save: () => <InnerBlocks.Content />,
});
