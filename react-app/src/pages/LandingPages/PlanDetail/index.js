import React, { useState } from "react";
import { Card, Grid, Button, Typography, Link, Box, TextField, Select, MenuItem } from "@mui/material";
import istoc from "assets/images/products/istoc.jpg";

function PlanDetailBasic() {
  const [bedrooms, setBedrooms] = useState("2");
  const [bathrooms, setBathrooms] = useState("1");

  const handleDownload = () => {
    alert("Téléchargement du plan en cours...");
  };

  const handleOrder = () => {
    alert("Commande en cours...");
  };

  return (
    <Box>
      {/* En-tête de navigation */}
      <Box
        sx={{
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          padding: "20px",
          backgroundColor: "#f5f5f5",
        }}
      >
       
        <Typography variant="h4">Civ-Toolkit</Typography>
        <Box>
          <Link href="/" underline="none" sx={{ mx: 2 }}>Accueil</Link>
          <Link href="/plans" underline="none" sx={{ mx: 2 }}>Plans de maisons</Link>
          <Link href="/how-it-works" underline="none" sx={{ mx: 2 }}>Comment ça marche</Link>
          <Link href="/contact" underline="none" sx={{ mx: 2 }}>Contact</Link>
          <Button variant="contained" color="primary" sx={{ mx: 1 }} >Sign in</Button>
          <Button variant="contained" color="info" component={Link}
                        to="/pages/authentication/sign-up"
                        >Register</Button>
        </Box>
      </Box>

      {/* Section principale */}
      <Grid container spacing={2} sx={{ padding: "20px" }}>
        <Grid item xs={12} md={6}>
          <Box
             minHeight="75vh"
             width="100%"
             sx={{
               backgroundImage: `url(${ istoc})`,
               backgroundSize: "cover",
               backgroundPosition: "top",
               display: "grid",
               placeItems: "center",
             }}
          />
        </Grid>

        <Grid item xs={12} md={6}>
          <Card sx={{ padding: "20px" }}>
            <Typography variant="h5" fontWeight="bold">Nom du Plan de Construction</Typography>
            <Typography variant="h6" color="primary" sx={{ my: 2 }}>$50</Typography>

            <Box sx={{ mb: 3 }}>
              <Typography variant="subtitle1">Nombre de chambres :</Typography>
              <Select value={bedrooms} onChange={(e) => setBedrooms(e.target.value)} fullWidth>
                <MenuItem value="1">1</MenuItem>
                <MenuItem value="2">2</MenuItem>
                <MenuItem value="3">3</MenuItem>
                <MenuItem value="4">4</MenuItem>
              </Select>
            </Box>

            <Box sx={{ mb: 3 }}>
              <Typography variant="subtitle1">Salles de bain :</Typography>
              <Select value={bathrooms} onChange={(e) => setBathrooms(e.target.value)} fullWidth>
                <MenuItem value="1">1</MenuItem>
                <MenuItem value="2">2</MenuItem>
                <MenuItem value="3">3</MenuItem>
              </Select>
            </Box>

            <Button variant="contained" color="primary"  onClick={handleDownload} sx={{ mb: 2 }}>
              Télécharger
            </Button>
            <Button variant="contained" color="info" onClick={handleOrder}>
              Commander
            </Button>

            <Typography variant="body1" sx={{ mt: 2, color: "#666" }}>
              Description du plan : répondez à la question fréquemment posée en une phrase simple ou un paragraphe plus long.
            </Typography>
          </Card>
        </Grid>
      </Grid>

      {/* Section des avis */}
      <Box sx={{ padding: "20px" }}>
        <Typography variant="h5">Derniers avis</Typography>
        <Card sx={{ padding: "10px", marginTop: "10px" }}>
          <Typography variant="h6">Titre de l'avis</Typography>
          <Typography variant="body2">Corps de l'avis</Typography>
          <Typography variant="caption">Auteur - Date</Typography>
        </Card>
      </Box>

      {/* Inscription à la newsletter */}
      <Box sx={{ padding: "20px", textAlign: "center" }}>
        <Typography variant="h5">Suivez les dernières tendances</Typography>
        <Box component="form" sx={{ mt: 2, display: "flex", justifyContent: "center" }}>
          <TextField type="email" placeholder="Entrez votre email" sx={{ mr: 2 }} />
          <Button variant="contained" color="primary">S'inscrire</Button>
        </Box>
      </Box>
    </Box>
  );
}

export default PlanDetailBasic;
