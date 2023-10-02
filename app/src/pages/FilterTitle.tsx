import { useEffect, useState, memo } from "react";
import axios from "axios";
import FilterGroup from "../components/filtering/filter-group/FilterGroup";
import {
  AuthorData,
  GenreData
} from "../components/titles-table/TitleData.inf";

function getAllAuthors(setAuthors: Function) {
  axios
    .get("http://localhost:5001/api/get/authors/*")
    .then((response) => {
      setAuthors(response.data);
    })
    .catch((error) => {
      console.log(error);
    });
}

function getAllGenre(setGenre: Function) {
  axios
    .get("http://localhost:5001/api/get/genre/*")
    .then((response) => {
      setGenre(response.data);
    })
    .catch((error) => {
      console.log(error);
    });
}
function FilterTitlePage() {
  let [authors, setAuthors] = useState<AuthorData[] | undefined>([]);
  let [genre, setGenre] = useState<GenreData[] | undefined>([]);

  useEffect(() => {
    if (!authors || authors.length === 0) {
      getAllAuthors(setAuthors);
    }

    if (!genre || genre.length === 0) {
      getAllGenre(setGenre);
    }
  }, [genre, authors]);
  if (authors && genre) {
    
    return (
      <FilterGroup
        identifier={"Filter Group"}
        authors={authors}
        genre={genre}
      ></FilterGroup>
    );
  }
  return <></>;
}

export default memo(FilterTitlePage);
