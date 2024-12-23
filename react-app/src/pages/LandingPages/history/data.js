import React, { useState, useMemo } from "react";
import PropTypes from "prop-types";
import Box from "@mui/material/Box";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableContainer from "@mui/material/TableContainer";
import TableHead from "@mui/material/TableHead";
import TablePagination from "@mui/material/TablePagination";
import TableRow from "@mui/material/TableRow";
import TableSortLabel from "@mui/material/TableSortLabel";
import Toolbar from "@mui/material/Toolbar";
import Typography from "@mui/material/Typography";
import IconButton from "@mui/material/IconButton";
import Tooltip from "@mui/material/Tooltip";
import FilterListIcon from "@mui/icons-material/FilterList";
import { visuallyHidden } from "@mui/utils";

function createData(id, date, action, user) {
  return { id, date, action, user };
}

const rows = [
  createData(1, "2024-11-15", "Connexion", "Alice"),
  createData(2, "2024-11-14", "Déconnexion", "Bob"),
  createData(3, "2024-11-12", "Modification", "Charlie"),
  createData(4, "2024-11-10", "Suppression", "Dave"),
  createData(5, "2024-11-09", "Ajout", "Eve"),
  createData(6, "2024-11-08", "Connexion", "Frank"),
  createData(7, "2024-11-07", "Modification", "Grace"),
  createData(8, "2024-11-06", "Connexion", "Hannah"),
  createData(9, "2024-11-05", "Déconnexion", "Ivy"),
  createData(10, "2024-11-04", "Ajout", "Jack"),
];

const headCells = [
  { id: "date", numeric: false, disablePadding: true, label: "Date" },
  { id: "action", numeric: false, disablePadding: false, label: "Action" },
  { id: "user", numeric: false, disablePadding: false, label: "Utilisateur" },
];

function descendingComparator(a, b, orderBy) {
  if (b[orderBy] < a[orderBy]) {
    return -1;
  }
  if (b[orderBy] > a[orderBy]) {
    return 1;
  }
  return 0;
}

function getComparator(order, orderBy) {
  return order === "desc"
    ? (a, b) => descendingComparator(a, b, orderBy)
    : (a, b) => -descendingComparator(a, b, orderBy);
}

function EnhancedTableHead(props) {
  const { order, orderBy, onRequestSort } = props;
  const createSortHandler = (property) => (event) => {
    onRequestSort(event, property);
  };

  return (
    <TableHead>
      <TableRow>
        {headCells.map((headCell) => (
          <TableCell
            key={headCell.id}
            align={headCell.numeric ? "right" : "left"}
            padding={headCell.disablePadding ? "none" : "normal"}
            sortDirection={orderBy === headCell.id ? order : false}
            sx={{
              border: "1px solid #ddd",
              fontWeight: "bold",
              textTransform: "uppercase",
            }}
          >
            <TableSortLabel
              active={orderBy === headCell.id}
              direction={orderBy === headCell.id ? order : "asc"}
              onClick={createSortHandler(headCell.id)}
            >
              {headCell.label}
              {orderBy === headCell.id ? (
                <Box component="span" sx={visuallyHidden}>
                  {order === "desc" ? "sorted descending" : "sorted ascending"}
                </Box>
              ) : null}
            </TableSortLabel>
          </TableCell>
        ))}
      </TableRow>
    </TableHead>
  );
}

EnhancedTableHead.propTypes = {
  onRequestSort: PropTypes.func.isRequired,
  order: PropTypes.oneOf(["asc", "desc"]).isRequired,
  orderBy: PropTypes.string.isRequired,
};

function EnhancedTableToolbar() {
  return (
    <Toolbar
      sx={[
        {
          pl: { sm: 2 },
          pr: { xs: 1, sm: 1 },
        },
      ]}
    >
      <Typography
        sx={{ flex: "1 1 100%", fontSize: "30px", fontWeight: "bold" }}
        variant="h4"
        id="tableTitle"
        component="div"
      >
        Historique
      </Typography>
      <Tooltip title="Filter list">
        <IconButton>
          <FilterListIcon />
        </IconButton>
      </Tooltip>
    </Toolbar>
  );
}

export default function HistoryTable() {
  const [order, setOrder] = useState("asc");
  const [orderBy, setOrderBy] = useState("date");
  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(10);

  const handleRequestSort = (event, property) => {
    const isAsc = orderBy === property && order === "asc";
    setOrder(isAsc ? "desc" : "asc");
    setOrderBy(property);
  };

  const handleChangePage = (event, newPage) => {
    setPage(newPage);
  };

  const handleChangeRowsPerPage = (event) => {
    setRowsPerPage(parseInt(event.target.value, 10));
    setPage(0);
  };

  const emptyRows = page > 0 ? Math.max(0, (1 + page) * rowsPerPage - rows.length) : 0;

  const visibleRows = useMemo(
    () =>
      [...rows]
        .sort(getComparator(order, orderBy))
        .slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage),
    [order, orderBy, page, rowsPerPage]
  );

  return (
    <Box sx={{ width: "100%" }}>
      <EnhancedTableToolbar />
      <TableContainer sx={{ paddingInline: "20px" }}>
        <Table sx={{ minWidth: 750, tableLayout: "fixed", padding: "10px" }}>
          <EnhancedTableHead order={order} orderBy={orderBy} onRequestSort={handleRequestSort} />
          <TableBody sx={{ padding: "10px" }}>
            {visibleRows.map((row) => (
              <TableRow hover tabIndex={-1} key={row.id}>
                <TableCell
                  component="th"
                  scope="row"
                  padding="none"
                  sx={{
                    border: "1px solid #ddd",
                  }}
                >
                  {row.date}
                </TableCell>
                <TableCell
                  align="left"
                  sx={{
                    border: "1px solid #ddd",
                  }}
                >
                  {row.action}
                </TableCell>
                <TableCell
                  align="left"
                  sx={{
                    border: "1px solid #ddd",
                  }}
                >
                  {row.user}
                </TableCell>
              </TableRow>
            ))}
            {emptyRows > 0 && (
              <TableRow
                style={{
                  height: 53 * emptyRows,
                }}
              >
                <TableCell colSpan={3} />
              </TableRow>
            )}
          </TableBody>
        </Table>
      </TableContainer>
      <TablePagination
        rowsPerPageOptions={[5, 10, 25]}
        component="div"
        count={rows.length}
        rowsPerPage={rowsPerPage}
        page={page}
        onPageChange={handleChangePage}
        onRowsPerPageChange={handleChangeRowsPerPage}
      />
    </Box>
  );
}
