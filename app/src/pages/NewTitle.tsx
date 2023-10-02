import { useEffect, useState, memo } from "react";
import axios from "axios";
import TitleForm from "../components/title-form/TitleForm";
import Container from 'react-bootstrap/Container'
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { AuthorData, GenreData } from "../components/titles-table/TitleData.inf";


function getAllAuthors(setAuthors: Function): void {
    axios
        .get("http://localhost:5001/api/get/authors/*")
        .then(response => {
            setAuthors(response.data)
        })
        .catch(error => {
            console.log(error);

        });


}

function getAllGenre(setGenre: Function): void {
    axios
        .get("http://localhost:5001/api/get/genre/*")
        .then(response => {

            setGenre(response.data)
        })
        .catch(error => {
            console.log(error);

        });
}

function NewTitlePage(): JSX.Element {
    const [authors, setAuthors] = useState<AuthorData[] | undefined>();

    const [genre, setGenre] = useState<GenreData[] | undefined>();
    const [isLoaded, setLoadedStatus] = useState<boolean>(false);

    useEffect(() => {
        if (!authors) {
            getAllAuthors(setAuthors)
        }
        if (!genre) {
            getAllGenre(setGenre)
        }

        if (authors && genre) {
            setLoadedStatus(true)
        } else {
            setLoadedStatus(false)
        }


    }, [authors, genre]);


    let form: JSX.Element = isLoaded && genre && authors ? <TitleForm selected={undefined} authors={authors} genre={genre} type="NEW" buttonText={"Create new title"}></TitleForm> : <></>
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
