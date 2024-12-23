import Icon from "@mui/material/Icon";
import AboutUs from "layouts/pages/landing-pages/about-us";
import ContactUs from "layouts/pages/landing-pages/contact-us";
import Author from "layouts/pages/landing-pages/author";
import SignIn from "layouts/pages/authentification/sign-in";
import SignUp from "layouts/pages/authentification/sign-up";
import Dashboard from "layouts/dashboard";
import Tables from "layouts/tables";
import Billing from "layouts/billing";
import Notifications from "layouts/notifications";
import Profile from "layouts/profile";
// import PageHeaders from "layouts/sections/page-sections/page-headers";
// import Features from "layouts/sections/page-sections/featuers";
// import Navbars from "layouts/sections/navigation/navbars";
// import NavTabs from "layouts/sections/navigation/nav-tabs";
// import Pagination from "layouts/sections/navigation/pagination";
// import Inputs from "layouts/sections/input-areas/inputs";
// import Forms from "layouts/sections/input-areas/forms";
// import Alerts from "layouts/sections/attention-catchers/alerts";
// import Modals from "layouts/sections/attention-catchers/modals";
// import TooltipsPopovers from "layouts/sections/attention-catchers/tooltips-popovers";
// import Avatars from "layouts/sections/elements/avatars";
// import Badges from "layouts/sections/elements/badges";
// import BreadcrumbsEl from "layouts/sections/elements/breadcrumbs";
// import Buttons from "layouts/sections/elements/buttons";
// import Dropdowns from "layouts/sections/elements/dropdowns";
// import ProgressBars from "layouts/sections/elements/progress-bars";
// import Toggles from "layouts/sections/elements/toggles";
// import Typography from "layouts/sections/elements/typography";
import Presentation from "layouts/pages/presentation";
import Counters from "pages/Presentation/sections/Counters";
import History from "pages/LandingPages/history";
import Catalogue from "pages/LandingPages/Catalogue";

const routes = [
  {
    name: "Accueil",
    icon: <Icon>home</Icon>,
    route: "/presentation",
    component: <Presentation />,
    showInNavbar: true,
    columns: 1,
    rowsPerColumn: 2,
    collapse: [
      {
        name: "Accueil",
        collapse: [
          {
            name: "Counters",
            route: "/presentation#counter",
            component: <Counters />,
            showInNavbar: true,
          },
        ],
      },
      {
        name: "Contact",
        collapse: [
          {
            name: "Contactez-Nous",
            route: "/contact-us",
            component: <ContactUs />,
            showInNavbar: true,
          },
        ],
      },
    ],
  },
  {
    name: "Catalogue",
    icon: <Icon>folder</Icon>,
    route: "/catalog",
    component: <Catalogue />,
    showInNavbar: true,
  },
  {
    name: "Historique",
    icon: <Icon>view_list</Icon>,
    route: "/history",
    component: <History />,
    showInNavbar: true,
  },
  {
    name: "Qui sommes-nous ?",
    icon: <Icon>group</Icon>,
    route: "/about-us",
    component: <AboutUs />,
    showInNavbar: true,
  },
  {
    type: "collapse",
    name: "Dashboard",
    icon: <Icon fontSize="small">dashboard</Icon>,
    route: "/dashboard",
    component: <Dashboard />,
    showInNavbar: false,
  },
  {
    type: "collapse",
    name: "Tables",
    icon: <Icon fontSize="small">table_view</Icon>,
    route: "/tables",
    component: <Tables />,
    showInNavbar: false,
  },
  {
    type: "collapse",
    name: "Billing",
    icon: <Icon fontSize="small">receipt_long</Icon>,
    route: "/billing",
    component: <Billing />,
    showInNavbar: false,
  },
  {
    type: "collapse",
    name: "Notifications",
    icon: <Icon fontSize="small">notifications</Icon>,
    route: "/notifications",
    component: <Notifications />,
    showInNavbar: false,
  },
  {
    type: "collapse",
    name: "Profile",
    icon: <Icon fontSize="small">person</Icon>,
    route: "/profile",
    component: <Profile />,
    showInNavbar: false,
  },
  {
    type: "collapse",
    name: "Sign In",
    icon: <Icon fontSize="small">login</Icon>,
    route: "/authentication/sign-in",
    component: <SignIn />,
    showInNavbar: false,
  },
  {
    type: "collapse",
    name: "Sign Up",
    icon: <Icon fontSize="small">assignment</Icon>,
    route: "/authentication/sign-up",
    component: <SignUp />,
    showInNavbar: false,
  },
];

export default routes;
