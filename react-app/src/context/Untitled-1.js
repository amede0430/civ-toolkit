const authContext = useContext(AuthContext);
  const [alert, setAlert] = useState({ open: false, message: "", type: "" });

  const [inputs, setInputs] = useState({
    name: "",
    email: "",
    address: "",
    academicPeriod: "",
    phone: "",
    logo: null,
    agree: false,
  });

  const [errors, setErrors] = useState({
    nameError: false,
    emailError: false,
    addressError: false,
    academicPeriodError: false,
    phoneError: false,
    logoError: false,
    agreeError: false,
    error: false,
    errorText: "",
  });

  const changeHandler = (e) => {
    const { name, value, type, files } = e.target;
    setInputs({
      ...inputs,
      [name]: type === "file" ? files[0] : value,
    });
  };

  useEffect(() => {
    if (alert.open) {
      const timer = setTimeout(() => setAlert({ open: false, message: "", type: "" }), 5000);
      return () => clearTimeout(timer);
    }
  }, [alert]);

  const submitHandler = async (e) => {
    e.preventDefault();

    const mailFormat = /^\w+([\.-]?\w+)@\w+([\.-]?\w+)(\.\w{2,3})+$/;
    const phoneFormat = /^[0-9]{10,15}$/;

    if (inputs.name.trim().length === 0) {
      setErrors({ ...errors, nameError: true });
      return;
    }

    if (inputs.email.trim().length === 0 || !inputs.email.trim().match(mailFormat)) {
      setErrors({ ...errors, emailError: true });
      return;
    }

    if (inputs.address.trim().length === 0) {
      setErrors({ ...errors, addressError: true });
      return;
    }

    if (inputs.academicPeriod.trim().length === 0) {
      setErrors({ ...errors, academicPeriodError: true });
      return;
    }

    if (!inputs.phone.match(phoneFormat)) {
      setErrors({ ...errors, phoneError: true });
      return;
    }

    if (inputs.logo === null) {
      setErrors({ ...errors, logoError: true });
      return;
    }

    if (inputs.agree === false) {
      setErrors({ ...errors, agreeError: true });
      return;
    }

    const formData = new FormData();
    formData.append("name", inputs.name);
    formData.append("email", inputs.email);
    formData.append("academic_period", inputs.academicPeriod);
    formData.append("address", inputs.address);
    formData.append("phone", inputs.phone);
    formData.append("logo", inputs.logo);

    try {
      const response = await AuthService.register(formData);
      setAlert({
        open: true,
        message: "Dossier envoyé avec succès ! Un mail vous a été envoyé à l'adresse fourni.",
        type: "success",
      });
      setInputs({
        name: "",
        email: "",
        academicPeriod: "",
        address: "",
        phone: "",
        logo: null,
        agree: false,
      });

      setErrors({
        nameError: false,
        emailError: false,
        academicPeriodError: false,
        addressError: false,
        phoneError: false,
        logoError: false,
        agreeError: false,
        error: false,
        errorText: "",
      });
    } catch (err) {
      setErrors({ ...errors, error: true, errorText: err.message });
      console.error(err);
      setAlert({
        open: true,
        message: "Une erreur est survenue. Veuillez réessayer.",
        type: "error",
      });
    }
  };