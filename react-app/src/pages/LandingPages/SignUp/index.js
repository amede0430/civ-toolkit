import { useState, useEffect, useContext } from "react";
import { useNavigate } from "react-router-dom";
import { Link } from "react-router-dom";
import Card from "@mui/material/Card";
import Grid from "@mui/material/Grid";
import MuiLink from "@mui/material/Link";
import MKBox from "components/MKBox";
import MKTypography from "components/MKTypography";
import MKInput from "components/MKInput";
import MKButton from "components/MKButton";
import SimpleFooter from "examples/Footers/SimpleFooter";
import bgImage from "assets/images/bg-sign-in-basic.jpeg";
import { AuthContext } from "context";
import AuthService from "services/auth-service"; // Importez votre service d'authentification ici.

function SignUpBasic() {
  const authContext = useContext(AuthContext);
  const navigate = useNavigate();

  const [alert, setAlert] = useState({ open: false, message: "", type: "" });
  const [inputs, setInputs] = useState({
    name: "",
    email: "",
    password: "",
    confirmPassword: "",
  });

  const [errors, setErrors] = useState({
    nameError: false,
    emailError: false,
    passwordError: false,
    confirmPasswordError: false,
    error: false,
    errorText: "",
  });

  useEffect(() => {
    if (alert.open) {
      const timer = setTimeout(() => setAlert({ open: false, message: "", type: "" }), 5000);
      return () => clearTimeout(timer);
    }
  }, [alert]);

  const changeHandler = (e) => {
    const { name, value } = e.target;
    setInputs({
      ...inputs,
      [name]: value,
    });
  };

  const submitHandler = async (e) => {
    e.preventDefault();

    const mailFormat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

    if (!inputs.name.trim()) return setErrors({ ...errors, nameError: true });
    if (!inputs.email.match(mailFormat)) return setErrors({ ...errors, emailError: true });
    if (inputs.password.length < 6) return setErrors({ ...errors, passwordError: true });
    if (inputs.password !== inputs.confirmPassword)
      return setErrors({ ...errors, confirmPasswordError: true });

    try {
      await AuthService.register({
        name: inputs.name,
        email: inputs.email,
        password: inputs.password,
      });
      setAlert({
        open: true,
        message: "Inscription réussie ! Un email de confirmation a été envoyé.",
        type: "success",
      });
      setInputs({ name: "", email: "", password: "", confirmPassword: "" });
      setErrors({});
      authContext.register();
    } catch (err) {
      setErrors({ ...errors, error: true, errorText: err.message });
      setAlert({
        open: true,
        message: "Une erreur est survenue. Veuillez réessayer.",
        type: "error",
      });
    }
  };

  const retour = () => navigate(-1);

  return (
    <>
      <MKBox
        position="absolute"
        top={0}
        left={0}
        zIndex={1}
        width="100%"
        minHeight="100vh"
        sx={{
          backgroundImage: ({ functions: { linearGradient, rgba }, palette: { gradients } }) =>
            `${linearGradient(
              rgba(gradients.dark.main, 0.6),
              rgba(gradients.dark.state, 0.6)
            )}, url(${bgImage})`,
          backgroundSize: "cover",
          backgroundPosition: "center",
          backgroundRepeat: "no-repeat",
        }}
      />
      <MKBox px={1} width="100%" height="100vh" mx="auto" position="relative" zIndex={2}>
        <Grid container spacing={1} justifyContent="center" alignItems="center" height="100%">
          <Grid item xs={11} sm={9} md={5} lg={4} xl={3}>
            <Card>
              <MKBox
                variant="gradient"
                bgColor="info"
                borderRadius="lg"
                coloredShadow="info"
                mx={2}
                mt={-3}
                p={2}
                mb={1}
                textAlign="center"
              >
                <MKTypography variant="h4" fontWeight="medium" color="white" mt={1}>
                  Sign up
                </MKTypography>
              </MKBox>
              <MKBox pt={4} pb={3} px={3}>
                <MKBox component="form" role="form" onSubmit={submitHandler}>
                  <MKBox mb={2}>
                    <MKInput
                      name="name"
                      type="text"
                      label="Name"
                      fullWidth
                      value={inputs.name}
                      onChange={changeHandler}
                      error={errors.nameError}
                    />
                  </MKBox>
                  <MKBox mb={2}>
                    <MKInput
                      name="email"
                      type="email"
                      label="Email"
                      fullWidth
                      value={inputs.email}
                      onChange={changeHandler}
                      error={errors.emailError}
                    />
                  </MKBox>
                  <MKBox mb={2}>
                    <MKInput
                      name="password"
                      type="password"
                      label="Password"
                      fullWidth
                      value={inputs.password}
                      onChange={changeHandler}
                      error={errors.passwordError}
                    />
                  </MKBox>
                  <MKBox mb={2}>
                    <MKInput
                      name="confirmPassword"
                      type="password"
                      label="Confirm Password"
                      fullWidth
                      value={inputs.confirmPassword}
                      onChange={changeHandler}
                      error={errors.confirmPasswordError}
                    />
                  </MKBox>
                  <MKBox mt={4} mb={1}>
                    <MKButton type="submit" variant="gradient" color="info" fullWidth>
                      Sign up
                    </MKButton>
                  </MKBox>
                  <MKBox mt={3} mb={1} textAlign="center">
                    <MKTypography variant="button" color="text">
                      Already have an account?{" "}
                      <MKTypography
                        component={Link}
                        to="/authentication/sign-in"
                        variant="button"
                        color="info"
                        fontWeight="medium"
                        textGradient
                      >
                        Sign in
                      </MKTypography>{" "}
                      |{" "}
                      <MKTypography
                        onClick={retour}
                        variant="button"
                        color="info"
                        fontWeight="medium"
                        textGradient
                        sx={{ cursor: "pointer" }}
                      >
                        Retour
                      </MKTypography>
                    </MKTypography>
                  </MKBox>
                </MKBox>
              </MKBox>
            </Card>
          </Grid>
        </Grid>
      </MKBox>
      <MKBox width="100%" position="absolute" zIndex={2} bottom="1.625rem">
        <SimpleFooter light />
      </MKBox>
    </>
  );
}

export default SignUpBasic;
