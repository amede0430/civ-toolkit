import { forwardRef } from "react";
import PropTypes from "prop-types";
import MKInputRoot from "components/MKInput/MKInputRoot";

const MKInput = forwardRef(({ error, success, disabled, ...rest }, ref) => (
  <MKInputRoot {...rest} ref={ref} ownerState={{ error, success, disabled }} />
));

MKInput.defaultProps = {
  error: false,
  success: false,
  disabled: false,
};

MKInput.propTypes = {
  error: PropTypes.bool,
  success: PropTypes.bool,
  disabled: PropTypes.bool,
};

export default MKInput;
