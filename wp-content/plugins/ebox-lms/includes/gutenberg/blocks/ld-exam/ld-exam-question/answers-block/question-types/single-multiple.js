/**
 * ebox Single-Multiple Question Block
 *
 * @since 4.0.0
 * @package ebox
 */

import { __ } from "@wordpress/i18n";
import { Button } from "@wordpress/components";
import { RichText } from "@wordpress/block-editor";
import { Fragment } from "@wordpress/element";
import { AiFillWarning } from "react-icons/ai";
import { MdCheckBoxOutlineBlank, MdCheckBox, MdDelete } from "react-icons/md";
import {
  RiCheckboxBlankCircleLine,
  RiCheckboxCircleFill,
} from "react-icons/ri";

const AnswerRow = (props) => {
  const {
    isMultiple,
    attributes: { answer_label = "", answer_correct = false },
    setAttributes,
  } = props;

  return (
    <Fragment>
      <span className="ebox-exam-question-answer-select">
        {isMultiple ? (
          answer_correct ? (
            <MdCheckBox />
          ) : (
            <MdCheckBoxOutlineBlank />
          )
        ) : answer_correct ? (
          <RiCheckboxCircleFill />
        ) : (
          <RiCheckboxBlankCircleLine />
        )}
      </span>
      <span className="ebox-exam-question-answer-input">
        <RichText
          value={answer_label}
          placeholder={__("Add Answer", "ebox")}
          onChange={(newLabel) => setAttributes({ answer_label: newLabel })}
        />
      </span>
      <span className="ebox-exam-question-answer-toggle">
        <Button
          isSmall
          className="ebox-exam-question-answer-toggle-button"
          variant={answer_correct ? "primary" : "secondary"}
          disabled={0 === answer_label.length ? true : false}
          onClick={() => setAttributes({ answer_correct: !answer_correct })}
          text={
            answer_correct ? __("Correct", "ebox") : __("Incorrect", "ebox")
          }
        />
      </span>
    </Fragment>
  );
};

// ToDo: Drag and Drop to reorder answers
const SingleMultipleBlock = (props) => {
  const { type, attributes, setAttributes } = props;
  const required_answer = __("Answer is missing.", "ebox");
  const required_correct = __("Required correct answer is missing.", "ebox");
  const isMultiple = type === "multiple";
  const lastCorrect = attributes
    .map(({ answer_correct }) => answer_correct ?? false)
    .lastIndexOf(true);
  let countCorrect = 0;
  let hasAnswers = false;
  const answers =
    Array.isArray(attributes) && attributes.length >= 1
      ? [
          ...attributes
            .filter((row) => row.label !== "")
            .map((row, index) => {
              countCorrect =
                true === row.answer_correct ? countCorrect + 1 : countCorrect;
              hasAnswers = true;
              if (!isMultiple && index !== lastCorrect) {
                return {
                  ...row,
                  answer_correct: false,
                };
              }
              return { ...row };
            }),
        ]
      : [
          { answer_label: "", answer_correct: true },
          { answer_label: "", answer_correct: false },
        ];

  if (!isMultiple && 1 < countCorrect) {
    setAttributes(answers);
  }

  if (answers[answers.length - 1].answer_label !== "") {
    answers.push({ answer_label: "", answer_correct: false });
  }

  const updateAnswer = (index, newValue) => {
    if (newValue.answer_label === "") {
      deleteAnswer(index);
    } else {
      const newAnswers = answers.map((row) => {
        if (true === newValue.answer_correct && !isMultiple) {
          return {
            ...row,
            answer_correct: false,
          };
        }
        return { ...row };
      });
      newAnswers[index] = { ...answers[index], ...newValue };
      setAttributes(
        newAnswers.filter(({ answer_label }) => "" !== answer_label)
      );
    }
  };

  const deleteAnswer = (index) => {
    if (index > answers.length) {
      return;
    }
    const newAnswers = [...answers];
    newAnswers.splice(index, 1);
    setAttributes(newAnswers.filter(({ answer_label }) => "" !== answer_label));
  };

  return (
    <Fragment>
      <div className="ebox-exam-question-answers-list">
        <div className="ebox-exam-question-single-answer">
          {!hasAnswers && (
            <div className="ebox-exam-question-empty-answers">
              <AiFillWarning fill="red" />
              <span>{required_answer}</span>
            </div>
          )}
          {hasAnswers && 0 === countCorrect && (
            <div className="ebox-exam-question-empty-correct">
              <AiFillWarning fill="red" />
              <span>{required_correct}</span>
            </div>
          )}
        </div>
        {answers.length &&
          answers.map((singleAnswer, index) => {
            return (
              <div className="ebox-exam-question-single-answer" key={index}>
                <span className="ebox-exam-question-single-answer-delete">
                  {answers.length - 1 !== index && (
                    <Button
                      isSmall
                      onClick={() => deleteAnswer(index)}
                      icon={<MdDelete />}
                    />
                  )}
                </span>
                <AnswerRow
                  isMultiple={isMultiple}
                  attributes={singleAnswer}
                  setAttributes={(newValue) => updateAnswer(index, newValue)}
                />
              </div>
            );
          })}
      </div>
    </Fragment>
  );
};

export default SingleMultipleBlock;
