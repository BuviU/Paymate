/**
 * ebox Block ld-exam Edit
 *
 * @since 4.0.0
 * @package ebox
 */

import { InnerBlocks, ButtonBlockAppender } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { useMemo } from "@wordpress/element";

/**
 * ebox block functions
 */
import { ExamContext } from "./exam-context";

const Edit = (props) => {
  const {
    attributes: { ld_version = "" },
    setAttributes,
    clientId,
  } = props;

  const template = [["ebox/ld-exam-question", {}]];

  const blockOrder = useSelect((select) => {
    return select("core/block-editor").getBlockOrder(clientId);
  }, []);

  const examContext = useMemo(
    () => ({
      blockOrder,
    }),
    [clientId, blockOrder]
  );

  if (ld_version === "") {
    setAttributes({ ld_version: ldlms_settings.version });
  }

  return (
    <ExamContext.Provider value={examContext}>
      <InnerBlocks
        allowedBlocks={["ebox/ld-exam-question"]}
        template={template}
        renderAppender={() => (
          <ButtonBlockAppender
            className="ld-exam-block-appender"
            rootClientId={clientId}
          />
        )}
        templateInsertUpdatesSelection={true}
      />
    </ExamContext.Provider>
  );
};

export default Edit;
