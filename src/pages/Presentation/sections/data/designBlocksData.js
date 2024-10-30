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

import carte_ville from "assets/images/products/carte-ville-londres-est-posee-table-bois_1143726-16528.jpg";
import morceau_papier_bleu from "assets/images/products/morceau-papier-bleu-tasse-cafe-tasse-cafe-dessus_1143726-16485.jpg";
import plans_architecturaux from  "assets/images/products/plans-architecturaux-plans-conception-technique-presentant-plans-construction-complexes-ingenieur_1143726-16443.jpg";
import vue from "assets/images/products/vue-ingenieur-au-travail-pour-celebration-journee-ingenieurs_23-2151615020.jpg";
import projet_architecture from "assets/images/products/projet-architecture-maquette-tablette_23-2148252114.jpg";
import collaboration_plan from "assets/images/products/collaboration-plan_1207264-6398.jpg";
import illustration from "assets/images/products/illustration-commerciale-donnees-statistiques_24908-58410.avif";
import illustrations from "assets/images/products/illustration-conception-sites-web-reactifs_335657-4708.avif";
import papier from "assets/images/products/fichier-recherche_869423-988.avif";
import fourniture from "assets/images/products/fournitures-scolaires-education_24877-59360.jpg";
import calculatrice from "assets/images/products/calculatrice-papier_23-2148148303.avif";
import cahier from "assets/images/products/cahier-rouge-blanc-stylo-au-dessus_979014-10338.jpg";
import bleu from "assets/images/products/calculatrice-bleue-crayon-vert-stylo-dessus_806553-38241.jpg";


export default [
  {
    title: "Plans de Constructions ",
    description: "A selection of 45 page sections that fit perfectly in any combination",
    items: [
      {
        image: carte_ville,  // Chemin vers l'image locale
        name: "Page Headers",
        count: 10,
        route: "/sections/page-sections/page-headers",
      },
      {
        image: morceau_papier_bleu,  // Chemin vers l'image locale
        name: "Features",
        count: 14,
        route: "/sections/page-sections/features",
      },
      {
        image: plans_architecturaux,  // Chemin vers l'image locale
        name: "Pricing",
        count: 8,
        pro: true,
      },
      {
        image: vue,  // Chemin vers l'image locale
        name: "FAQ",
        count: 1,
        pro: true,
      },
      {
        image: projet_architecture,  // Chemin vers l'image locale
        name: "Blog Posts",
        count: 11,
        pro: true,
      },
      {
        image: collaboration_plan,  // Chemin vers l'image locale
        name: "Testimonials",
        count: 11,
        pro: true,
      },
    ],
  },
  {
    title: "Documents Synthèses",
    description: "30+ components that will help go through the pages",
    items: [
      {
        image: illustration,  // Chemin vers l'image locale
        name: "Navbars",
        count: 4,
        route: "/sections/navigation/navbars",
      },
      {
        image: illustrations,  // Chemin vers l'image locale
        name: "Nav Tabs",
        count: 2,
        route: "/sections/navigation/nav-tabs",
      },
      {
        image: papier,  // Chemin vers l'image locale
        name: "Pagination",
        count: 3,
        route: "/sections/navigation/pagination",
      },
    ],
  },
  {
    title: "Feuilles de Calcul",
    description: "50+ elements that you need for text manipulation and insertion",
    items: [
      {
        image: fourniture,  // Chemin vers l'image locale
        name: "Newsletters",
        count: 6,
        pro: true,
      },
      {
        image: calculatrice,  // Chemin vers l'image locale
        name: "Contact Sections",
        count: 8,
        pro: true,
      },
      {
        image: cahier,  // Chemin vers l'image locale
        name: "Forms",
        count: 3,
        route: "/sections/input-areas/forms",
      },
      {
        image: bleu,  // Chemin vers l'image locale
        name: "Inputs",
        count: 6,
        route: "/sections/input-areas/inputs",
      },
    ],
  }
];
