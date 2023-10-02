import { useEffect, useState, memo } from "react";
import axios from "axios";

import Table from "../components/titles-table/TitlesTable";
import Button from "react-bootstrap/esm/Button";

function HomePage(): JSX.Element {
  const [titles, setTitles] = useState([]);
  const [isLoaded, setLoadedStatus] = useState(false);
  useEffect(() => {
    axios
      .get("http://localhost:5001/api/get/titles/*")
      .then(response => {
        setTitles(response.data);
        setLoadedStatus(true);
      })
      .catch(error => {
        console.log(error);
        setLoadedStatus(false);
      });
  });

  let table: JSX.Element = !isLoaded ? <Table loaded={false} tableData={[]} /> : <Table loaded={true} tableData={titles} />

  return (<>
    <Button variant="primary" href="/new-title">Create New Title</Button>
    <Button variant="primary" href="/filter-title">Filter Titles</Button>
    {table}
  </>);
}

export default memo(HomePage);
