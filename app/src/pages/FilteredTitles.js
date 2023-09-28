import { memo } from "react";

import Table from "../components/titles-table/TitlesTable";
import Button from "react-bootstrap/esm/Button";
import { useLocation, useNavigate } from "react-router-dom";

function FilteredTitlesPage() {
    let location = useLocation()
    let navigate = useNavigate()

    const onReset = () => {
        location.state = null
        navigate("/")
    }
    let titles = location.state?.titles

    return (<>
        <Button variant="primary" href="/new-title">Create New Title</Button>
        <Button variant="primary" href="/filter-title">Filter Titles</Button>
        <Button variant="primary" onClick={onReset}>Reset</Button>
        <Table loaded={!!titles} tableData={titles} />
    </>);
}

export default memo(FilteredTitlesPage);
