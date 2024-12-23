import Container from "@mui/material/Container";
import Grid from "@mui/material/Grid";
import Divider from "@mui/material/Divider";
import MKBox from "components/MKBox";
import DefaultCounterCard from "examples/Cards/CounterCards/DefaultCounterCard";

function Counters() {
  return (
    <MKBox component="section" py={3}>
    <Container>
      <Grid container item xs={12} lg={9} sx={{ mx: "auto" }}>
        <Grid item xs={12} md={4}>
          <DefaultCounterCard
            count={70}
            suffix="+"
            title="Plan de contructions"
            description="Conçus par les professionnels du bâtiment pour les exigences aux exigences. Consultez la base et trouvez toutes les informations nécessaires dans notre documentation détaillée."
          />
        </Grid>
        <Grid item xs={12} md={4} display="flex">
          <Divider orientation="vertical" sx={{ display: { xs: "none", md: "block" }, mx: 0 }} />
          <DefaultCounterCard
            count={15}
            suffix="+"
            title="Ebooks"
            description="Le guide touristique pour les plans et rapports de construction, regroupant toutes les données essentielles pour une compréhension complète de votre projet."
          />
          <Divider orientation="vertical" sx={{ display: { xs: "none", md: "block" }, ml: 0 }} />
        </Grid>
        <Grid item xs={12} md={4}>
          <DefaultCounterCard
            count={4}
            title="Feuille de Calcul"
            description="Réaliser des calculs précis pour le dimensionnement des structures peut être coûteux. Démarrez avec notre système de dimensionnement pour les résultats fiables et efficaces."
          />
        </Grid>
      </Grid>
    </Container>
  </MKBox>
);
}

export default Counters;
