import { useEffect, useState, memo } from "react";
import axios from "axios";
import TitleForm from "../components/title-form/TitleForm";
import Container from 'react-bootstrap/Container'
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import FilterRule from "../components/filtering/filter-rule/FilterRule";
import Accordion from 'react-bootstrap/Accordion'
import FilterGroup from "../components/filtering/filter-group/FilterGroup";

function getAllAuthors(setAuthors) {
    axios.get("http://localhost:5001/api/get/authors/*").then((response) => {
        setAuthors(response.data)
    }).catch((error) => {
        console.log(error)
    })
}

function getAllGenre(setGenre) {
    axios.get("http://localhost:5001/api/get/genre/*").then((response) => {
        setGenre(response.data)
    }).catch((error) => {
        console.log(error)
    })
} 
function FilterTitlePage() {
    let [authors, setAuthors] = useState(null)
    let [genre, setGenre] = useState(null)
    let [isLoaded, setLoaded] = useState(false)
    useEffect(() => {
        if(!authors) {
            getAllAuthors(setAuthors)
        }

        if (!genre) {
            getAllGenre(setGenre)
        }

        if(genre && authors) {
            setLoaded(true)
        }

    }, [genre, authors])
   
    if(!isLoaded) {
        return (<></>)
    }
    return (<FilterGroup identifier={"Filter Group"} authors={authors} genre={genre}></FilterGroup>)
}

export default memo(FilterTitlePage);
