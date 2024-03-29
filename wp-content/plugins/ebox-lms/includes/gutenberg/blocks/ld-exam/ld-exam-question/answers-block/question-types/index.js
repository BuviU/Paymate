/**
 * ebox Question Type Blocks
 *
 * @since 4.0.0
 * @package ebox
 */

/**
 * ebox block functions
 */
import SingleMultipleBlock from "./single-multiple";

const AnswerTypeBlock = {
  single: (props) => <SingleMultipleBlock {...props} />,
  multiple: (props) => <SingleMultipleBlock {...props} />,
  // etc...
};

export default AnswerTypeBlock;
