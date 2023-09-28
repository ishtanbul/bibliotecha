import { useEffect, useState, memo } from "react";
import axios from "axios";
import TitleForm from "../components/title-form/TitleForm";
import Container from 'react-bootstrap/Container'
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

function getAllAuthors(setAuthors) {
    axios
        .get("http://localhost:5001/api/get/authors/*")
        .then(response => {
             setAuthors(response.data)
        })
        .catch(error => {
            console.log(error);

        });

    return [];
}

function getAllGenre(setGenre) {
    axios
        .get("http://localhost:5001/api/get/genre/*")
        .then(response => {
         
            setGenre(response.data)
        })
        .catch(error => {
            console.log(error);

        });

    return [];
}

function NewTitlePage() {
    const [authors, setAuthors] = useState(null);

    const [genre, setGenre] = useState(null);
    const [isLoaded, setLoadedStatus] = useState(false);

    useEffect(() => {
        if(!authors) {
        getAllAuthors(setAuthors)
        }
        if (!genre) {
            getAllGenre(setGenre)
        }
       
       if(authors && genre) {
        setLoadedStatus(true)
       } else {
        setLoadedStatus(false)
       }
        

    }, [authors, genre]);


    let form = isLoaded ? <TitleForm selected={null} authors={authors} genre={genre} type="NEW" buttonText={"Create new title"}></TitleForm> : <></>
    return (<>
        <Container>
            <Row>
                <Col xs="2"></Col>
                <Col xs="4">{form}</Col>

            </Row>
        </Container>

    </>)
}

export default memo(NewTitlePage);
