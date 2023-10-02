import FilterRule from "../filter-rule/FilterRule";
import Button from "react-bootstrap/Button";
import "./FilterGroup.css";
import Stack from "react-bootstrap/esm/Stack";
import { useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faAdd, faFilter, faHashtag } from "@fortawesome/free-solid-svg-icons";

import { useNavigate } from "react-router-dom";
import { AuthorData, GenreData } from "../../titles-table/TitleData.inf";
import {
    FilterRuleData,
    UpdateFilterGroupData
} from "../filter-rule/FilterRule.inf";
import axios from "axios";

function FilterGroup({
    identifier,
    authors,
    genre
}: {
    identifier: string;
    authors: AuthorData[];
    genre: GenreData[];
}) {
    let [numOfFilterRules, setNumOfFilterRules] = useState<number>(0);
    let [deleteFilterIndex, setDeleteFilterIndex] = useState<number>(-1);
    let [filterGroupData, setFilterGroupData] = useState(
        new Map<number, FilterRuleData>()
    );

    let navigate = useNavigate();

    const onSubmitForm = () => {
        let data = Array.from(filterGroupData.values());
        console.log(JSON.stringify(data));
        let config = {
            headers: {
                "Content-Type": "application/json",
                "Access-Control-Allow-Origin": "*"
            }
        };

        axios
            .post("http://localhost:5001/api/filter/title", data, config)
            .then((response) => {
                console.log(response.data);
                navigate("/filtered-titles", { state: { titles: response.data } });
            })
            .catch((error) => {
                console.log(error);
            });
    };

    const unsetFilterRule = (index: number) => {
        const temp = new Map<number, FilterRuleData>(filterGroupData);
        temp.delete(index);
        setFilterGroupData(temp);
    };

    const updateFilterGroupData: UpdateFilterGroupData = (
        index: number,
        filterRuleData: FilterRuleData
    ) => {
        const temp = new Map<number, FilterRuleData>(filterGroupData);
        temp.set(index, filterRuleData);
        setFilterGroupData(temp);
    };


    const addNewFilterRule = (
        filterRules: Map<number, JSX.Element>,
        setFilterRules: React.Dispatch<
            React.SetStateAction<Map<number, JSX.Element>>
        >,
        numOfFilterRules: number,
        setNumOfFilterRules: React.Dispatch<React.SetStateAction<number>>,
        authors: AuthorData[],
        genre: GenreData[]
    ) => {
        let currentNum: number = numOfFilterRules + 1;
        const temp = new Map<number, JSX.Element>(filterRules);
        authors.forEach((author) => console.log(author.name));
        temp.set(
            currentNum,
            <FilterRule
                key={currentNum}
                index={currentNum}
                setDeleteFilterIndex={setDeleteFilterIndex}
                updateFilterGroupData={updateFilterGroupData}
                unsetFilterRule={unsetFilterRule}
                genre={genre}
                authors={authors}
            ></FilterRule>
        );
        setNumOfFilterRules(currentNum);

        setFilterRules(temp);
    };

    let [filterRules, setFilterRules] = useState(
        new Map<number, JSX.Element>()
    );

    useEffect(() => {
        const deleteFilterRuleData = (index: number) => {
            unsetFilterRule(index);
        };

        const deleteFilterRule = (
            filterRules: Map<number, JSX.Element>,
            setFilterRules: React.Dispatch<
                React.SetStateAction<Map<number, JSX.Element>>
            >,
            index: number
        ) => {
            const temp = new Map<number, JSX.Element>(filterRules);
            temp.delete(index);
            setFilterRules(temp);
        };

        if (deleteFilterIndex !== -1) {
            deleteFilterRule(filterRules, setFilterRules, deleteFilterIndex);
            deleteFilterRuleData(deleteFilterIndex);
            setDeleteFilterIndex(-1);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [deleteFilterIndex, filterGroupData, filterRules]);

    return (
        <div className="filter-group">
            <div className="identifier mb-3">
                <FontAwesomeIcon icon={faHashtag}></FontAwesomeIcon>
                <span className="px-2">{identifier}</span>
            </div>
            <Stack gap={3} className="mb-3">
                {Array.from(filterRules.values())}
                <Button
                    type="button"
                    variant="secondary"
                    onClick={() =>
                        addNewFilterRule(
                            filterRules,
                            setFilterRules,
                            numOfFilterRules,
                            setNumOfFilterRules,
                            authors,
                            genre
                        )
                    }
                >
                    <FontAwesomeIcon icon={faAdd}></FontAwesomeIcon>
                    <span className="px-2">Attach New Filter Rule</span>
                </Button>
                <Button type="button" variant="primary">
                    <FontAwesomeIcon icon={faFilter}></FontAwesomeIcon>
                    <span className="px-2" onClick={onSubmitForm}>
                        Apply Filter
                    </span>
                </Button>
            </Stack>
        </div>
    );
}

export default FilterGroup;
