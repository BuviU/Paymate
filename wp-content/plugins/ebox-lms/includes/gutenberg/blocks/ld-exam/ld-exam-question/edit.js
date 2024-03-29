/**
 * ebox Block ld-exam-question Edit
 *
 * @since 4.0.0
 * @package ebox
 */

import { __, _x } from "@wordpress/i18n";
import {
  InnerBlocks,
  PlainText,
  InspectorControls,
} from "@wordpress/block-editor";
import { PanelBody, PanelRow, SelectControl } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { Fragment, useContext, useState } from "@wordpress/element";
import { AiFillWarning } from "react-icons/ai";

/**
 * ebox block functions
 */
import { ldlms_get_custom_label } from "../../ldlms.js";
import { ExamContext } from "../exam-context";

const question_types = [
  { label: __("Single", "ebox"), value: "single" },
  { label: __("Multiple", "ebox"), value: "multiple" },
];

const empty_title = __("The Question is empty.", "ebox");

const panel_title = sprintf(
  // translators: placeholder: Question type.
  _x("%s type", "placeholder: Question type", "ebox"),
  ldlms_get_custom_label("question")
);

const Edit = (props) => {
  const {
    attributes: { question_title = "", question_type = "" },
    setAttributes,
    clientId,
  } = props;
  const [allowValidations, setAllowValidations] = useState(false);
  const [hasBeenSelected, setHasBeenSelected] = useState(false);

  /*
   * Note: this is kind of a hack since
   * hasSelectedInnerBlock is not working properly.
   * It is not detecting when description-block is selected
   * even with deep as true|false
   */
  const { innerBlocksClientIds, selectedBlockClientId } = useSelect(
    (select) => {
      return {
        innerBlocksClientIds: select(
          "core/block-editor"
        ).getClientIdsOfDescendants([clientId]),
        selectedBlockClientId:
          select("core/block-editor").getSelectedBlockClientId(),
      };
    }
  );
  const hasSelectedInnerBlock =
    innerBlocksClientIds.includes(selectedBlockClientId) ||
    selectedBlockClientId === clientId;

  const { blockOrder } = useContext(ExamContext);
  const isLastQuestionBlock =
    blockOrder.lastIndexOf(clientId) === blockOrder.length - 1;

  const template = [
    ["ebox/ld-question-description", {}],
    ["ebox/ld-question-answers-block", {}],
    ["ebox/ld-correct-answer-message-block", {}],
    ["ebox/ld-incorrect-answer-message-block", {}],
  ];

  if (hasBeenSelected === false) {
    if (hasSelectedInnerBlock === true) {
      setHasBeenSelected(true);
    }
  }

  if (hasBeenSelected === true && allowValidations === false) {
    if (hasSelectedInnerBlock === false) {
      setAllowValidations(true);
    }
  }

  if (question_type === "") {
    setAttributes({ question_type: "single" });
  }

  const classAllowValidations = allowValidations
    ? "ebox-exam-question-allow-validations"
    : "";

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={panel_title} initialOpen={true}>
          <PanelRow>
            <SelectControl
              value={question_type}
              options={question_types}
              onChange={(question_type) => setAttributes({ question_type })}
            />
          </PanelRow>
        </PanelBody>
      </InspectorControls>
      <PlainText
        className="ebox-exam-question"
        value={question_title}
        placeholder={__("Question", "ebox")}
        onChange={(question_title) => setAttributes({ question_title })}
      />
      {0 === question_title.length && (
        <div
          className={`${classAllowValidations} ebox-exam-question-empty-title`}
        >
          <AiFillWarning fill="red" />
          <span>{empty_title}</span>
        </div>
      )}
      <div className={`${classAllowValidations} ebox-exam-question-flexbox`}>
        <InnerBlocks template={template} templateLock={"all"} />
      </div>
      {!isLastQuestionBlock && <hr />}
    </Fragment>
  );
};

export default Edit;
