import { useEffect, useState, memo } from "react";
import axios from "axios";
import TitleForm from "../components/title-form/TitleForm";
import Container from 'react-bootstrap/Container'
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useParams } from "react-router-dom";
import { AuthorData, GenreData, TitleData } from "../components/titles-table/TitleData.inf";

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


function getSelectedValues(titleID: string, setSelectedValues: Function) {
    axios
    .get(`http://localhost:5001/api/get/titles/${titleID}`)
    .then(response => {
        setSelectedValues(response.data)
    })
    .catch(error => {
        console.log(error);

    });
}

function EditTitlePage(): JSX.Element {
    const {titleID} = useParams()
    const [authors, setAuthors] = useState<AuthorData[] | undefined>();
    const [selectedValues, setSelectedValues] = useState<TitleData | undefined>()
    const [genre, setGenre] = useState<GenreData[] | undefined>();
    const [isLoaded, setLoadedStatus] = useState(false);

    if(!titleID) {
        throw new Error("Title ID not found")
    }

    useEffect(() => {
        if(!authors) {
        getAllAuthors(setAuthors)
        }
        if (!genre) {
            getAllGenre(setGenre)
        }

        if(!selectedValues) {
            getSelectedValues(titleID, setSelectedValues)
        }
       
       if(authors && genre && selectedValues) {
        setLoadedStatus(true)
       } else {
        setLoadedStatus(false)
       }
        

    }, [authors, genre, selectedValues, titleID]);


    let form = isLoaded && authors && genre ? <TitleForm selected={selectedValues} authors={authors} genre={genre} type="UPDATE" buttonText={"Confirm changes"}></TitleForm> : <></>
    return (<>
        <Container>
            <Row>
                <Col xs="2"></Col>
                <Col xs="4">{form}</Col>

            </Row>
        </Container>

    </>)
}

export default memo(EditTitlePage);