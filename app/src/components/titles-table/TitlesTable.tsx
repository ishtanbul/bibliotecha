import React, { useEffect, useState } from "react";
import Table from "react-bootstrap/Table";
import Button from "react-bootstrap/Button";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faPenToSquare, faTrash } from "@fortawesome/free-solid-svg-icons";
import Modal from "react-bootstrap/Modal";
import "./TitlesTable.css";
import axios from "axios";
import {
  Location,
  NavigateFunction,
  useLocation,
  useNavigate
} from "react-router-dom";
import { TitleData } from "./TitleData.inf";

type BasicTitle = { title: string; id: string | number };
type VoidLambda = () => void;

const EMPTY_TITLE: BasicTitle = { title: "", id: -1 };

function TitlesTable({
  loaded,
  tableData
}: {
  loaded: boolean;
  tableData: TitleData[];
}) {
  let navigate: NavigateFunction = useNavigate();
  let location: Location = useLocation();
  let [deleteTitle, setDeleteTitle] = useState(EMPTY_TITLE);
  let [show, setShow] = useState(false);
  let [deleteConfirmed, setDeleteConfirmed] = useState(false);

  const handleClose = () => setShow(false);
  const handleShow = () => setShow(true);

  const initiateDelete = () => setDeleteConfirmed(true);
  const exitDelete = () => setDeleteConfirmed(false);

  useEffect(() => {
    if (deleteTitle.id !== -1 && deleteConfirmed) {
      axios
        .get(`http://localhost:5001/api/delete/title/${deleteTitle.id}`)
        .then((response) => {
          setDeleteTitle(EMPTY_TITLE);
          setDeleteConfirmed(false);
          handleClose();
          navigate(location.pathname);
        })
        .catch((error) => {
          console.log(error);
        });
    }
  }, [deleteTitle, deleteConfirmed, navigate, location.pathname]);

  const colHeaders = ["#", "Title", "Authors", "Genre", "Options"];
  let body = loaded ? (
    <TableBody
      setDeleteTitle={setDeleteTitle}
      handleShow={handleShow}
      tableData={tableData}
    />
  ) : (
    <></>
  );

  return (
    <>
      <DeleteModal
        show={show}
        handleClose={handleClose}
        exitDelete={exitDelete}
        initiateDelete={initiateDelete}
        deleteTitle={deleteTitle}
      ></DeleteModal>
      <Table striped bordered hover>
        <TableHeader colHeaders={colHeaders} />
        {body}
      </Table>
    </>
  );
}

const TableHeader = ({ colHeaders }: { colHeaders: string[] }) => {
  let headers = colHeaders.map((colHeader: string, index: number) => {
    return <th key={index}>{colHeader}</th>;
  });
  return (
    <thead>
      <tr>{headers}</tr>
    </thead>
  );
};

const TableRow = ({
  index,
  setDeleteTitle,
  row,
  handleShow
}: {
  index: number;
  setDeleteTitle: React.Dispatch<React.SetStateAction<BasicTitle>>;
  row: TitleData;
  handleShow: VoidLambda;
}) => {
  let names: string[] = row.authors.map((author) => {
    return author.name;
  });

  let genre: string[] = row.genre.map((genre) => {
    return genre.genre;
  });

  const enableModal: VoidFunction = () => {
    setDeleteTitle({ title: row.title, id: row.id });
    handleShow();
  };

  return (
    <tr>
      <td>{index + 1}</td>
      <td>{row.title}</td>
      <td>{names.join(", ")}</td>
      <td>{genre.join(", ")}</td>
      <td>
        <Button className="orange" size="lg" href={`/edit-title/${row.id}`}>
          <FontAwesomeIcon icon={faPenToSquare}></FontAwesomeIcon>
        </Button>
        <Button className="black" size="lg" onClick={enableModal}>
          <FontAwesomeIcon icon={faTrash}></FontAwesomeIcon>
        </Button>
      </td>
    </tr>
  );
};

const TableBody = ({
  setDeleteTitle,
  tableData,
  handleShow
}: {
  setDeleteTitle: React.Dispatch<React.SetStateAction<BasicTitle>>;
  tableData: TitleData[];
  handleShow: VoidLambda;
}) => {
  let rows = tableData.map((row, index) => {
    return (
      <TableRow
        setDeleteTitle={setDeleteTitle}
        key={index}
        index={index}
        row={row}
        handleShow={handleShow}
      />
    );
  });
  return <tbody>{rows}</tbody>;
};

const DeleteModal = ({
  show,
  handleClose,
  exitDelete,
  initiateDelete,
  deleteTitle
}: {
  show: boolean;
  handleClose: VoidLambda;
  exitDelete: VoidLambda;
  initiateDelete: VoidLambda;
  deleteTitle: BasicTitle;
}) => {
  const closeModal = () => {
    handleClose();
    exitDelete();
  };

  return (
    <Modal show={show} onHide={handleClose}>
      <Modal.Header closeButton>
        <Modal.Title>Delete title</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <div>
          Are you sure you want to delete selected title labelled "
          {deleteTitle.title}"?
        </div>
        <div className="fw-bold">
          Please note that this action is irreversible.
        </div>
      </Modal.Body>
      <Modal.Footer>
        <Button variant="secondary" onClick={closeModal}>
          Close
        </Button>
        <Button variant="danger" onClick={initiateDelete}>
          <FontAwesomeIcon icon={faTrash}></FontAwesomeIcon>&ensp;Delete
        </Button>
      </Modal.Footer>
    </Modal>
  );
};

export default TitlesTable;
