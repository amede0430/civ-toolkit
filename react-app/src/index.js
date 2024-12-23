/**
=========================================================
* Material Dashboard 2 React - v2.2.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard-react
* Copyright 2023 Creative Tim (https://www.creative-tim.com)

Coded by www.creative-tim.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
*/

import React from "react";
import * as ReactDOMClient from "react-dom/client"; // Maintenir votre méthode existante
import { BrowserRouter } from "react-router-dom";
import App from "App";

// Importation du contexte Material Dashboard
import { MaterialUIControllerProvider } from "context";

const container = document.getElementById("root"); // Garder "root" pour correspondre à votre projet actuel
const root = ReactDOMClient.createRoot(container);

root.render(
  <BrowserRouter>
    <MaterialUIControllerProvider>
      <App />
    </MaterialUIControllerProvider>
  </BrowserRouter>
);
