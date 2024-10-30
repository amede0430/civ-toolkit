/*
=========================================================
* Material Kit 2 React - v2.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-kit-react
* Copyright 2023 Creative Tim (https://www.creative-tim.com)

Coded by www.creative-tim.com

 =========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
*/

// @mui material components
import Container from "@mui/material/Container";
import Grid from "@mui/material/Grid";

// Material Kit 2 React components
import MKBox from "components/MKBox";

// Material Kit 2 React examples
import RotatingCard from "examples/Cards/RotatingCard";
import RotatingCardFront from "examples/Cards/RotatingCard/RotatingCardFront";
import RotatingCardBack from "examples/Cards/RotatingCard/RotatingCardBack";
import DefaultInfoCard from "examples/Cards/InfoCards/DefaultInfoCard";

// Images
import bgFront from "assets/images/logo2.png";
import bgBack from "assets/images/bg1.jpg";

function Information() {
  return (
    <MKBox component="section" py={6} my={6}>
      <Container>
        <Grid container item xs={11} spacing={3} alignItems="center" sx={{ mx: "auto" }}>
          <Grid item xs={12} lg={4} sx={{ mx: "auto" }}>
            <RotatingCard>
              <RotatingCardFront
                image={bgFront}
                icon="touch_app"
                title={
                  <>
                    Civ
                    <br />
                    ToolKit
                  </>
                }
                description="Des plans de constructions et des ebooks disponibles. Dimensionner aussi vos dommaines  avec nous"
              />
              <RotatingCardBack
                image={bgBack}
                title="En savoir plus"
                description="Inscrivez-vous sur notre platforme pour découvrir nos oeuvres."
                action={{
                  type: "internal",
                  route: "/pages/authentication/sign-up",
                  label: "s'inscrire",
                }}
              />
            </RotatingCard>
          </Grid>
          <Grid item xs={12} lg={7} sx={{ ml: "auto" }}>
            <Grid container spacing={3}>
              <Grid item xs={12} md={6}>
                <DefaultInfoCard
                  icon="content_copy"
                  title="Plans Architecturaux et Structuraux"
                  description="Des plans complets et détaillés conçus par des experts du bâtiment, avec toutes les informations essentielles en un seul endroit."
                />
              </Grid>
              <Grid item xs={12} md={6}>
                <DefaultInfoCard
                  icon="flip_to_front"
                  title="Document Synthèse"
                  description="Un guide essentiel regroupant les données clés pour une vision claire et complète de votre projet."
                />
              </Grid>
            </Grid>
            <Grid container spacing={3} sx={{ mt: { xs: 0, md: 6 } }}>
              <Grid item xs={12} md={6}>
                <DefaultInfoCard
                  icon="devices"
                  title="Dimensionnement de Bâtiments"
                  description="Simplifiez vos calculs de dimensionnement avec un système précis et fiable, prêt à l’emploi."
                />
              </Grid>
              <Grid item xs={12} md={6}>
                <DefaultInfoCard
                  icon="price_change"
                  title="Feuille de Calcul"
                  description="Des feuilles de calcul dynamiques, parfaitement adaptées à toutes les résolutions et formats d’écran."
                />
              </Grid>
            </Grid>
          </Grid>
        </Grid>
      </Container>
    </MKBox>
  );
}

export default Information;
