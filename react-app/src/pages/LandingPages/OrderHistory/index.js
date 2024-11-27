import React, { useState } from "react";
import {
  Box,
  Card,
  Grid,
  Typography,
  Button,
  Select,
  MenuItem,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
} from "@mui/material";

function OrderHistoryBasic() {
  // Échantillon de données de commandes/téléchargements
  const [orders, setOrders] = useState([
    { id: 1, name: "Plan de maison 3 chambres", date: "2023-10-05", type: "Téléchargement", status: "Complété" },
    { id: 2, name: "Plan de villa moderne", date: "2023-09-12", type: "Commande", status: "En cours" },
    { id: 3, name: "Plan de studio compact", date: "2023-08-21", type: "Téléchargement", status: "Complété" },
    { id: 4, name: "Plan de duplex", date: "2023-07-15", type: "Commande", status: "Annulé" },
  ]);

  const [filterStatus, setFilterStatus] = useState("");
  const [filterType, setFilterType] = useState("");

  // Fonction de filtrage
  const filteredOrders = orders.filter((order) => {
    return (
      (filterStatus ? order.status === filterStatus : true) &&
      (filterType ? order.type === filterType : true)
    );
  });

  return (
    <Box sx={{ padding: "20px" }}>
      {/* En-tête de la page */}
      <Typography variant="h4" fontWeight="bold" sx={{ mb: 3 }}>
        Historique des Commandes et Téléchargements
      </Typography>

      {/* Filtres */}
      <Grid container spacing={2} sx={{ mb: 4 }}>
        <Grid item xs={12} md={6}>
          <Select
            fullWidth
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value)}
            displayEmpty
            sx={{ backgroundColor: "#f5f5f5" }}
          >
            <MenuItem value="">Filtrer par Statut</MenuItem>
            <MenuItem value="Complété">Complété</MenuItem>
            <MenuItem value="En cours">En cours</MenuItem>
            <MenuItem value="Annulé">Annulé</MenuItem>
          </Select>
        </Grid>
        <Grid item xs={12} md={6}>
          <Select
            fullWidth
            value={filterType}
            onChange={(e) => setFilterType(e.target.value)}
            displayEmpty
            sx={{ backgroundColor: "#f5f5f5" }}
          >
            <MenuItem value="">Filtrer par Type</MenuItem>
            <MenuItem value="Téléchargement">Téléchargement</MenuItem>
            <MenuItem value="Commande">Commande</MenuItem>
          </Select>
        </Grid>
      </Grid>

      {/* Tableau de l'historique */}
      <TableContainer component={Paper}>
        <Table>
          <TableHead sx={{ backgroundColor: "#1976d2" }}>
            <TableRow>
              <TableCell sx={{ color: "white" }}>Nom du Plan</TableCell>
              <TableCell sx={{ color: "white" }}>Date</TableCell>
              <TableCell sx={{ color: "white" }}>Type</TableCell>
              <TableCell sx={{ color: "white" }}>Statut</TableCell>
              <TableCell sx={{ color: "white" }}>Actions</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {filteredOrders.map((order) => (
              <TableRow key={order.id}>
                <TableCell>{order.name}</TableCell>
                <TableCell>{order.date}</TableCell>
                <TableCell>{order.type}</TableCell>
                <TableCell>
                  <Typography
                    sx={{
                      color:
                        order.status === "Complété"
                          ? "green"
                          : order.status === "En cours"
                          ? "orange"
                          : "red",
                    }}
                  >
                    {order.status}
                  </Typography>
                </TableCell>
                <TableCell>
                  <Button
                    variant="contained"
                    color="primary"
                    onClick={() => alert(`Détails de la commande ${order.id}`)}
                  >
                    Voir Détails
                  </Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Box>
  );
}

export default OrderHistoryBasic;
