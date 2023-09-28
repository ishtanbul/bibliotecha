
import FilterRule from '../filter-rule/FilterRule'
import Button from 'react-bootstrap/Button'
import './FilterGroup.css'
import Stack from 'react-bootstrap/esm/Stack';
import { useEffect, useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faAdd, faFilter, faHashtag } from '@fortawesome/free-solid-svg-icons';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';

function FilterGroup({ identifier, authors, genre }) {
    let [numOfFilterRules, setNumOfFilterRules] = useState(1)
    let [deleteFilterIndex, setDeleteFilterIndex] = useState(-1)
    let [filterData, setFilterData] = useState({})

    let navigate = useNavigate()


    const onSubmitForm = () => {

        let data = JSON.stringify(Object.values(filterData))
        let config = {
            headers: {
                "Content-Type": "application/json",
                "Access-Control-Allow-Origin": "*"
            }
        }


        axios.post("http://localhost:5001/api/filter/title", data, config).then((response) => {

            navigate("/filtered-titles", { state: { titles: response.data } })
        }).catch((error) => {
            console.log(error)
        })
    }
    const updateFilterForm = (index, formData) => {
        filterData[index] = formData
        setFilterData(filterData)

    }

    const unsetFilterRule = (index) => {
        if (!(index in filterData)) { return }
        delete filterData[index]
        setFilterData(filterData)
    }


    let [filterRules, setFilterRules] = useState({ 1: <FilterRule key={1} index={1} prev={null} setDeleteFilterIndex={setDeleteFilterIndex} updateFilterForm={updateFilterForm} unsetFilterRule={unsetFilterRule} authors={authors} genre={genre}></FilterRule> })



    const addNewFilterRule = (filterRules, setFilterRules, numOfFilterRules, setNumOfFilterRules) => {
        let currentNum = numOfFilterRules + 1
        setNumOfFilterRules(currentNum)
        filterRules[currentNum] = <FilterRule key={currentNum} index={currentNum} prev={null} setDeleteFilterIndex={setDeleteFilterIndex} updateFilterForm={updateFilterForm} unsetFilterRule={unsetFilterRule} genre={genre} authors={authors}></FilterRule>
        setFilterRules(filterRules)
    }



    useEffect(() => {
        const deleteFilterForm = (index) => {
            delete filterData[index]
            setFilterData(filterData)
        }


        const deleteFilterRule = (filterRules, setFilterRules, index) => {
            delete filterRules[index]
            setFilterRules(filterRules)
        }

        if (deleteFilterIndex !== -1) {
            deleteFilterRule(filterRules, setFilterRules, deleteFilterIndex)
            deleteFilterForm(deleteFilterIndex)
            setDeleteFilterIndex(-1)
        }
    }, [deleteFilterIndex, filterRules, setFilterData, filterData])

    return (<div className='filter-group'>
        <div className='identifier mb-3'>
            <FontAwesomeIcon icon={faHashtag}></FontAwesomeIcon><span className='px-2'>{identifier}</span></div>
        <Stack gap={3} className='mb-3'>
            {Object.values(filterRules)}
            <Button type='button' variant='secondary' onClick={() => addNewFilterRule(filterRules, setFilterRules, numOfFilterRules, setNumOfFilterRules)}><FontAwesomeIcon icon={faAdd}></FontAwesomeIcon><span className='px-2'>Attach New Filter Rule</span></Button>
            <Button type='button' variant='primary'><FontAwesomeIcon icon={faFilter}></FontAwesomeIcon><span className='px-2' onClick={onSubmitForm}>Apply Filter</span></Button>
        </Stack>

    </div>)
}

export default FilterGroup;