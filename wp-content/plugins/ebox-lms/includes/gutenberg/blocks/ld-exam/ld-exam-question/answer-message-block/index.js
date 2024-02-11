/**
 * ebox Block ld-incorrect-answer-message-block ld-incorrect-answer-message-block
 *
 * @since 4.0.0
 * @package ebox
 */

import { __ } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { InnerBlocks } from "@wordpress/block-editor";
import { Fragment } from "@wordpress/element";
import { MdQuiz } from "react-icons/md";

const settings = {
  icon: <MdQuiz />,
  parent: ["ebox/ld-exam-question"],
  category: "ebox-blocks",
  supports: {
    inserter: false,
    html: false,
  },
  save: () => <InnerBlocks.Content />,
};

const allowedBlocks = ["core/image", "core/paragraph"];

export const IncorrectAnswerMessage = registerBlockType(
  "ebox/ld-incorrect-answer-message-block",
  {
    ...settings,
    title: __("Incorrect answer message", "ebox"),
    description: __("Incorrect answer message", "ebox"),
    edit: () => {
      const template = [
        [
          "core/paragraph",
          {
            placeholder: __(
              "Add a message for incorrect answer (Optional)",
              "ebox"
            ),
          },
        ],
      ];
      return (
        <Fragment>
          <div>{__("Incorrect Answer Message", "ebox")}</div>
          <InnerBlocks
            allowedBlocks={allowedBlocks}
            template={template}
            templateLock={false}
          />
        </Fragment>
      );
    },
  }
);

export const CorrectAnswerMessage = registerBlockType(
  "ebox/ld-correct-answer-message-block",
  {
    ...settings,
    title: __("Correct answer message", "ebox"),
    description: __("Correct answer message", "ebox"),
    edit: () => {
      const template = [
        [
          "core/paragraph",
          {
            placeholder: __(
              "Add a message for correct answer (Optional)",
              "ebox"
            ),
          },
        ],
      ];
      return (
        <Fragment>
          <div>{__("Correct Answer Message", "ebox")}</div>
          <InnerBlocks
            allowedBlocks={allowedBlocks}
            template={template}
            templateLock={false}
          />
        </Fragment>
      );
    },
  }
);
