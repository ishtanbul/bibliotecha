import Stack from "react-bootstrap/Stack";
import Form from "react-bootstrap/Form";
import FloatingLabel from "react-bootstrap/esm/FloatingLabel";
import Accordion from "react-bootstrap/Accordion";
import "./FilterRule.css";
import React, { ChangeEvent, useRef, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faCircle,
  faFloppyDisk,
  faHashtag,
  faTrash
} from "@fortawesome/free-solid-svg-icons";
import Button from "react-bootstrap/Button";
import {
  Column,
  FilterRuleData,
  QueryCommand,
  UnsetFilterRule,
  UpdateFilterGroupData,
  getColumnFromString,
  getQueryCommandFromString
} from "./FilterRule.inf";
import { AuthorData, GenreData } from "../../titles-table/TitleData.inf";

function FilterRule({
  index,
  setDeleteFilterIndex,
  updateFilterGroupData,
  unsetFilterRule,
  authors,
  genre
}: {
  index: number;
  setDeleteFilterIndex: React.Dispatch<React.SetStateAction<number>>;
  updateFilterGroupData: UpdateFilterGroupData;
  unsetFilterRule: UnsetFilterRule;
  authors: AuthorData[];
  genre: GenreData[];
}) {
  const DEFAULT_IDENTIFIER = `Rule ${index}`;
  let [identifier, setIdentifier] = useState<string>(DEFAULT_IDENTIFIER);
  let [isEqualityQueryCommand, setEqualityQueryCommand] =
    useState<boolean>(false);
  let [columnType, setColumnType] = useState<Column>(Column.NONE);
  let [isSaved, setSaveState] = useState<boolean>(false);
  let [haltSave, setHaltSave] = useState<boolean>(true);

  let form: React.MutableRefObject<null | any> = useRef(null);

  const handleColumnTypeChange: React.ChangeEventHandler<HTMLSelectElement> = (
    e: React.ChangeEvent<HTMLSelectElement>
  ) => {
    let columnType: Column = getColumnFromString(e.target.value);
    setColumnType(columnType);
  };

  const handleQueryCommandChange: React.ChangeEventHandler<
    HTMLSelectElement
  > = (e: React.ChangeEvent<HTMLSelectElement>) => {
    let queryCommand: QueryCommand = getQueryCommandFromString(e.target.value);
    if (
      queryCommand === QueryCommand.IS ||
      queryCommand === QueryCommand.IS_NOT
    ) {
      setEqualityQueryCommand(true);
    } else {
      setEqualityQueryCommand(false);
    }
  };



  const checkSelectionEnabled = () => {
    return (
      isEqualityQueryCommand &&
      (columnType === Column.AUTHORS || columnType === Column.GENRE)
    );
  };

  const onFormUpdate = () => {
    let formData = new FormData(form.current);

    if (haltSave && !checkSelectionEnabled()) {
      return;
    }
  
    let data: FilterRuleData = {
      index: index, 
      boolean_operator: formData.get("boolean_operator")?.toString() || "NONE",
      column_type:  formData.get("column_type")?.toString() || "NONE",
      query_command: formData.get("query_command")?.toString() || "NONE",
      query_value: formData.get("query_value")?.toString() || ""
    };
    console.log(JSON.stringify(data));

    updateFilterGroupData(index, data);
    setSaveState(true);
  };

  let queryValueField;

  if (isEqualityQueryCommand && columnType === Column.AUTHORS) {
    queryValueField = (
      <QueryValueSelect
        index={5}
        data={authors}
        colType={columnType}
      ></QueryValueSelect>
    );
  } else if (isEqualityQueryCommand && columnType === Column.GENRE) {
    queryValueField = (
      <QueryValueSelect
        index={5}
        data={genre}
        colType={columnType}
      ></QueryValueSelect>
    );
  } else {
    queryValueField = (
      <QueryValueInput index={5} setHaltSave={setHaltSave}></QueryValueInput>
    );
  }
  return (
    <Accordion>
      <Accordion.Item className="filter-rule" eventKey="0">
        <Accordion.Header>
          <FilterRuleIdentifier identifier={identifier}></FilterRuleIdentifier>
          <FilterRuleSaveState isSaved={isSaved}></FilterRuleSaveState>
        </Accordion.Header>
        <Accordion.Body className="body">
          <Form
            ref={form}
            id={`rule-${index}`}
            onChange={() => {
              setSaveState(false);
              unsetFilterRule(index);
            }}
          >
            <Stack direction="vertical" gap={3}>
              <BooleanOperatorSelection
                index={1}
                ruleIndex={index}
              
              ></BooleanOperatorSelection>
              <IdentifierInput
                index={2}
                identifier={identifier}
                setIdentifier={setIdentifier}
              ></IdentifierInput>
              <ColumnTypeSelection
                index={3}
                handleColumnTypeChange={handleColumnTypeChange}
              ></ColumnTypeSelection>
              <QueryCommandSelection
                index={4}
                handleQueryCommandChange={handleQueryCommandChange}
              ></QueryCommandSelection>
              {queryValueField}
              <Button variant="success" onClick={() => onFormUpdate()}>
                <FontAwesomeIcon icon={faFloppyDisk}></FontAwesomeIcon>
                <span className="px-2 rule-name">Save Filter Rule</span>
              </Button>
              <Button
                variant="danger"
                onClick={() => setDeleteFilterIndex(index)}
                disabled={index === 1}
              >
                <FontAwesomeIcon icon={faTrash}></FontAwesomeIcon>
                <span className="px-2 rule-name">Delete Filter Rule</span>
              </Button>
            </Stack>
          </Form>
        </Accordion.Body>
      </Accordion.Item>
    </Accordion>
  );
}

function QueryValueSelect({
  index,
  data,
  colType
}: {
  index: number;
  data: AuthorData[] | GenreData[];
  colType: Column;
}) {
  let options;

  if (colType === Column.AUTHORS) {
    options = (data as AuthorData[]).map(
      (author: AuthorData, index: number) => {
        let value: string = author.name;
        return (
          <option key={index} value={value}>
            {value}
          </option>
        );
      }
    );
  } else if (colType === Column.GENRE) {
    options = (data as GenreData[]).map((genre: GenreData, index: number) => {
      let value: string = genre.genre;
      return (
        <option key={index} value={value}>
          {value}
        </option>
      );
    });
  }

  return (
    <Form.Group key={index} about="Enter Query Value">
      <FloatingLabel label="Enter query value">
        <Form.Select name="query_value">{options}</Form.Select>
      </FloatingLabel>
    </Form.Group>
  );
}

function FilterRuleIdentifier({ identifier }: { identifier: string }) {
  return (
    <div className="fw-bold text-truncate filter-rule-identifier">
      <FontAwesomeIcon icon={faHashtag} />
      <span className="text-capitalize px-2">{identifier}</span>
    </div>
  );
}

function FilterRuleSaveState({ isSaved }: { isSaved: boolean }) {
  let statusClass = isSaved ? "saved" : "unsaved";
  return (
    <span className={`px-4 ${statusClass}`}>
      <FontAwesomeIcon icon={faCircle} size="2xs"></FontAwesomeIcon>
    </span>
  );
}

function BooleanOperatorSelection({ index, ruleIndex }: {index: number, ruleIndex: number}) {
  return (
    <Form.Group key={index} about="Boolean Operator">
      <FloatingLabel label="Boolean Operation">
        <Form.Select
          defaultValue={ruleIndex === 1 ? "DEFAULT" : "AND"}
          name="boolean_operator"
        >
          <option disabled={ruleIndex !== 1} value={"DEFAULT"}>
            ---
          </option>
          <option disabled={ruleIndex === 1} value={"AND"}>
            AND
          </option>
          <option disabled={ruleIndex === 1} value={"OR"}>
            OR
          </option>
        </Form.Select>
      </FloatingLabel>
    </Form.Group>
  );
}

function IdentifierInput({ index, identifier, setIdentifier }: {index: number, identifier: string, setIdentifier: React.Dispatch<React.SetStateAction<string>>}) {
  return (
    <Form.Group key={index} about="Filter Rule Name">
      <FloatingLabel label="Filter Rule Name">
        <Form.Control
          type="text"
          defaultValue={identifier}
          onChange={(e) => setIdentifier(e.target.value)}
        ></Form.Control>
      </FloatingLabel>
    </Form.Group>
  );
}

function ColumnTypeSelection({ index, handleColumnTypeChange }: {index: number, handleColumnTypeChange: React.ChangeEventHandler<HTMLSelectElement>}) {
  return (
    <Form.Group key={index} about="Column Type Selection">
      <FloatingLabel label="Select Column Type">
        <Form.Select name="column_type" onChange={handleColumnTypeChange}>
          <option value={"TITLE"}>Title</option>
          <option value={"AUTHORS"}>Author</option>
          <option value={"GENRE"}>Genre</option>
        </Form.Select>
      </FloatingLabel>
    </Form.Group>
  );
}

function QueryCommandSelection({ index, handleQueryCommandChange }: {index: number, handleQueryCommandChange: React.ChangeEventHandler<HTMLSelectElement>}) {
  return (
    <Form.Group key={index} about="Selecting Query Command">
      <FloatingLabel label="Select Query Command">
        <Form.Select
          name="query_command"
          onChange={handleQueryCommandChange}
          defaultValue={"STARTS_WITH"}
        >
          <option value={"IS"}>is</option>
          <option value={"IS_NOT"} disabled={true}>
            is not
          </option>
          <option value={"STARTS_WITH"}>starts with</option>
          <option value={"ENDS_WITH"}>ends with</option>
        </Form.Select>
      </FloatingLabel>
    </Form.Group>
  );
}

function QueryValueInput({ index, setHaltSave }: {index: number, setHaltSave: React.Dispatch<React.SetStateAction<boolean>>}) {
  let [isValidValue, confirmValueValid] = useState(false);

  const validateField: React.ChangeEventHandler<any> = (e: ChangeEvent<any>) => {
    let queryValue: string = e.target.value as string;
    if (isEmpty(queryValue)) {
      confirmValueValid(false);
      setHaltSave(true);
    } else {
      confirmValueValid(true);
      setHaltSave(false);
    }
  };

  const isEmpty = (value: string) => {
    return value.trim().length === 0;
  };
  return (
    <Form.Group key={index} about="Enter Query Value">
      <FloatingLabel label="Enter query value">
        <Form.Control
          type="text"
          placeholder="John Doe"
          name="query_value"
          required={true}
          onChange={validateField}
        ></Form.Control>
        {!isValidValue && (
          <Form.Text>Query value cannot just be whitespace</Form.Text>
        )}
      </FloatingLabel>
    </Form.Group>
  );
}

export default FilterRule;
