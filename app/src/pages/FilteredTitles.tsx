import { memo } from "react";

import Table from "../components/titles-table/TitlesTable";
import Button from "react-bootstrap/esm/Button";
import { Location, NavigateFunction, useLocation, useNavigate } from "react-router-dom";
import { TitleData } from "../components/titles-table/TitleData.inf";


function FilteredTitlesPage() {
    let location: Location = useLocation()
    let navigate: NavigateFunction = useNavigate()

    const onReset = () => {
        location.state = null
        navigate("/")
    }

    let titles: TitleData[] = location.state?.titles

    return (<>
        <Button variant="primary" href="/new-title">Create New Title</Button>
        <Button variant="primary" href="/filter-title">Filter Titles</Button>
        <Button variant="primary" onClick={onReset}>Reset</Button>
        <Table loaded={!!titles} tableData={titles} />
    </>);
}

export default memo(FilteredTitlesPage);
