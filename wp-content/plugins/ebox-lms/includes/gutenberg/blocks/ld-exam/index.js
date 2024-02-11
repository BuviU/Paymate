/**
 * ebox Block ld-exam
 *
 * @since 4.0.0
 * @package ebox
 */

import { __, _x, sprintf } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { InnerBlocks } from "@wordpress/block-editor";

/**
 * ebox block functions
 */
import { ldlms_get_custom_label } from "../ldlms.js";
import Edit from "./edit";

export const settings = {
  block_key: "ebox/ld-exam",
  block_title: sprintf(
    // translators: placeholder: Challenge Exam.
    _x("ebox %s", "placeholder: Challenge Exam", "ebox"),
    ldlms_get_custom_label("exam")
  ),
  block_description: sprintf(
    // translators: placeholder: Create a Challenge Exam.
    _x("Create a %s", "placeholder: Create a Challenge Exam", "ebox"),
    ldlms_get_custom_label("exam")
  ),
};

registerBlockType(settings.block_key, {
  title: settings.block_title,
  description: settings.block_description,
  icon: "editor-help",
  category: "ebox-blocks",
  supports: {
    html: false,
  },
  attributes: {
    ld_version: {
      type: "string",
    },
  },
  edit: Edit,
  save: () => <InnerBlocks.Content />,
});
