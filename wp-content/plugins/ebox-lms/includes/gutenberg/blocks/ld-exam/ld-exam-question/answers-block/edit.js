/**
 * ebox Block ld-question-answers-block Edit
 *
 * @since 4.0.0
 * @package ebox
 */

/**
 * ebox block functions
 */
import AnswerTypeBlock from "./question-types";

const Edit = (props) => {
  const {
    attributes: { answers },
    setAttributes,
    context,
    clientId,
  } = props;

  const questionType =
    "ebox/question_type" in context && context["ebox/question_type"]
      ? context["ebox/question_type"]
      : "single";

  const RenderBlock = AnswerTypeBlock[questionType];
  setAttributes({ question_type: questionType });

  return (
    <RenderBlock
      clientId={clientId}
      type={questionType}
      attributes={[...answers]}
      setAttributes={(newAnswers) =>
        setAttributes({ answers: [...newAnswers] })
      }
    />
  );
};

export default Edit;
