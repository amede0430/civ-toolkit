import PropTypes from "prop-types";
import Flatpickr from "react-flatpickr";
import "flatpickr/dist/flatpickr.css";
import MKInput from "components/MKInput";

function MKDatePicker({ input, ...rest }) {
  return (
    <Flatpickr
      {...rest}
      render={({ defaultValue }, ref) => (
        <MKInput {...input} defaultValue={defaultValue} inputRef={ref} />
      )}
    />
  );
}

MKDatePicker.defaultProps = {
  input: {},
};

MKDatePicker.propTypes = {
  input: PropTypes.objectOf(PropTypes.oneOfType([PropTypes.any])),
};

export default MKDatePicker;
