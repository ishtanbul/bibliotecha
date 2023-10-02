import { BrowserRouter, Routes, Route } from "react-router-dom";
import 'bootstrap/dist/css/bootstrap.min.css';
import "./App.css";

import { Component } from "react";
import HomePage from "./pages/Home";
import NewTitlePage from "./pages/NewTitle";
import EditTitlePage from "./pages/EditTitle";
import FilterTitlePage from "./pages/FilterTitle"
import FilteredTitlesPage from "./pages/FilteredTitles";
import { getEndpoint } from "./global/config";
import React from "react";

class App extends Component {
  render() {
    console.log(getEndpoint())
    return (
      <BrowserRouter>
        <Routes>
          <Route path="/" index={true} element={<HomePage />} />
          <Route path="/new-title" element={<NewTitlePage />} />
          <Route path="/edit-title/:titleID" element={<EditTitlePage />}></Route>
          <Route path="/filter-title" element={<FilterTitlePage></FilterTitlePage>}></Route>
          <Route path="/filtered-titles" element={<FilteredTitlesPage></FilteredTitlesPage>}></Route>
        </Routes>
      </BrowserRouter>
    );
  }
}

export default App;
