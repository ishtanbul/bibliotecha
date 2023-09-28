
import Form from 'react-bootstrap/Form'


import './TitleForm.css'
import Button from 'react-bootstrap/Button'
import { useRef} from 'react'
import axios from 'axios'
import {useNavigate } from 'react-router-dom'

function TitleForm({ selected, authors, genre, type, buttonText }) {

    let navigate = useNavigate()
 

    let form = useRef(null)


    const handleSubmit = (e) => {
        e.preventDefault()

        let formData = new FormData(form.current)


        let data = JSON.stringify({
            title_id: selected?.id,
            title: formData.get("title"),
            author_id: formData.getAll("author_id[]"),
            genre_id: formData.getAll("genre_id[]")
        })

        let config = {
            headers: {
                "Content-Type": "application/json",
                "Access-Control-Allow-Origin": "*"
            }
        }


       let endpoint = type === "UPDATE" ? "http://localhost:5001/api/update/title" : "http://localhost:5001/api/post/title"

        axios.post(endpoint, data, config).then((response) => {

          
               navigate("/")

        }).catch((error) => {
            console.log(error)
        })
    }

   
    return (<>
        <Form id="form" ref={form} onSubmit={handleSubmit}>
            <TitleField selectedTitle={selected?.title}></TitleField>
            <AuthorSelect selectedAuthors={selected?.authors} authors={authors} ></AuthorSelect>
            <GenreSelect selectedGenre={selected?.genre} genre={genre} ></GenreSelect>
            <Button type="submit">{buttonText}</Button>
        </Form>
    </>)
}

function TitleField({ selectedTitle }) {
    let field;
    if (selectedTitle) {
   
        field = <Form.Control type="text" placeholder="Pride and Prejudice" name='title' form="form" defaultValue={selectedTitle} />
    } else {
        field = <Form.Control type="text" placeholder="Pride and Prejudice" name='title' form="form" />
    }
    return (<> <Form.Label><span className='fw-bold'>Title</span></Form.Label>
        {field}
    </>)
}

function AuthorSelect({ selectedAuthors, authors }) {
    let selectedAuthorIDs = selectedAuthors ? selectedAuthors.map((author) => author.id) : null

    let options = authors.map((author, index) => {
        return (<option key={index} value={author.id}>{author.name}</option>)
    })
    return (<>
        <Form.Label><span className='fw-bold'>Author</span></Form.Label>
        <Form.Select className='mb-3' multiple htmlSize='5' name='author_id[]' form="form" defaultValue={selectedAuthorIDs}>
            {options}
        </Form.Select>

    </>)
}

function GenreSelect({ selectedGenre, genre }) {
    let selectedGenreIDs = selectedGenre ? selectedGenre.map((genre) => genre.id) : null
    let options = genre.map((genre, index) => {
        return (<option key={index} value={genre.id}>{genre.genre}</option>)
    })
    return (<>
        <Form.Label><span className='fw-bold'>Genre</span></Form.Label>
        <Form.Select className='mb-3' multiple htmlSize='5' name='genre_id[]' form="form" defaultValue={selectedGenreIDs}>
            {options}
        </Form.Select>

    </>)
}


export default TitleForm