
import Stack from 'react-bootstrap/Stack'
import Form from 'react-bootstrap/Form'
import FloatingLabel from 'react-bootstrap/esm/FloatingLabel'
import Accordion from 'react-bootstrap/Accordion'
import './FilterRule.css'
import { useRef, useState } from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faCircle, faFloppyDisk, faHashtag, faTrash } from "@fortawesome/free-solid-svg-icons";
import Button from 'react-bootstrap/Button'

function FilterRule({ index, prev, setDeleteFilterIndex, updateFilterForm, unsetFilterRule, authors, genre }) {
    const DEFAULT_IDENTIFIER = `Rule ${index}`
    let [identifier, setIdentifier] = useState(DEFAULT_IDENTIFIER)
    let [isEqualityQueryCommand, setEqualityQueryCommand] = useState(false)
    let [columnType, setColumnType] = useState(null)
    let [isSaved, setSaveState] = useState(false)
    let [haltSave, setHaltSave] = useState(true)

    let form = useRef(null)

    const handleColumnTypeChage = (e) => {
        let columnType = e.target.value
        setColumnType(columnType)
    }

    const handleQueryCommandChange = (e) => {
        let queryCommand = e.target.value
        if (queryCommand === "IS" || queryCommand === "IS_NOT") {
            setEqualityQueryCommand(true)
        } else {
            setEqualityQueryCommand(false)
        }
    }

    const checkSelectionEnabled = () => {
        return isEqualityQueryCommand && (columnType === "AUTHOR" || columnType === "GENRE")
    }
    
    const onFormUpdate = () => {
        let formData = new FormData(form.current)

      
      
        if (haltSave && !checkSelectionEnabled()) {
            return;
        }

        let data = {
            index: index,
            boolean_operator: formData.get('boolean_operator'),
            column_type: formData.get('column_type'),
            query_command: formData.get('query_command'),
            query_value: formData.get('query_value')
        }
        console.log(JSON.stringify(data))

        updateFilterForm(index, data)
        setSaveState(true)
    }

    let queryValueField;

    if (isEqualityQueryCommand && columnType === "AUTHOR") {
        queryValueField = <QueryValueSelect index={5} data={authors} colType={"authors"}></QueryValueSelect>
    } else if (isEqualityQueryCommand && columnType === "GENRE") {
        queryValueField = <QueryValueSelect index={5} data={genre} colType={"genre"}></QueryValueSelect>
    } else {
        queryValueField = <QueryValueInput index={5} setHaltSave={setHaltSave}></QueryValueInput>
    }
    return (
        <Accordion>
            <Accordion.Item className='filter-rule' eventKey='0'>
                <Accordion.Header>
                    <FilterRuleIdentifier identifier={identifier}></FilterRuleIdentifier>
                    <FilterRuleSaveState isSaved={isSaved}></FilterRuleSaveState>
                </Accordion.Header>
                <Accordion.Body className='body'>
                    <Form ref={form} id={`rule-${index}`} onChange={() => {
                        setSaveState(false)
                        unsetFilterRule(index)
                    }}>
                        <Stack direction='vertical' gap={3}>

                            <BooleanOperatorSelection index={1} ruleindex={index} prev={prev}></BooleanOperatorSelection>
                            <IdentifierInput index={2} identifier={identifier} setIdentifier={setIdentifier}></IdentifierInput>
                            <ColumnTypeSelection index={3} handleColumnTypeChage={handleColumnTypeChage}></ColumnTypeSelection>
                            <QueryCommandSelection index={4} handleQueryCommandChange={handleQueryCommandChange}></QueryCommandSelection>
                            {queryValueField}
                            <Button variant='success' onClick={() => onFormUpdate()}><FontAwesomeIcon icon={faFloppyDisk}></FontAwesomeIcon><span className='px-2 rule-name'>Save Filter Rule</span></Button>
                            <Button variant='danger' onClick={() => setDeleteFilterIndex(index)} disabled={index === 1}><FontAwesomeIcon icon={faTrash}></FontAwesomeIcon><span className='px-2 rule-name'>Delete Filter Rule</span></Button>
                        </Stack>
                    </Form>
                </Accordion.Body>
            </Accordion.Item>
        </Accordion>
    )
}

function QueryValueSelect({ index, data, colType, setQueryValue }) {
    let options

    if (colType === "authors") {
        options = data.map((author, index) => {
            let value = author.name
            return (<option key={index} value={value}>{value}</option>)
        })
    } else {
        options = data.map((genre, index) => {
            let value = genre.genre
            return (<option key={index} value={value}>{value}</option>)
        })
    }

    return (<Form.Group key={index} about='Enter Query Value'>
        <FloatingLabel label='Enter query value'>
            <Form.Select name='query_value'>
                {options}</Form.Select>
        </FloatingLabel>
    </Form.Group>)
}

function FilterRuleIdentifier({ identifier }) {
    return (
        <div className='fw-bold text-truncate filter-rule-identifier'>
            <FontAwesomeIcon icon={faHashtag}>
            </FontAwesomeIcon>
            <span className='text-capitalize px-2'>{identifier}</span>
        </div>
    )
}

function FilterRuleSaveState({ isSaved }) {
    let statusClass = isSaved ? 'saved' : 'unsaved'
    return (<span className={`px-4 ${statusClass}`}><FontAwesomeIcon icon={faCircle} size='2xs'></FontAwesomeIcon></span>)
}

function BooleanOperatorSelection({ index, prev, ruleindex }) {
    return (<Form.Group key={index} about='Boolean Operator'>
        <FloatingLabel label='Boolean Operation'>
            <Form.Select defaultValue={ruleindex === 1 ? "DEFAULT" : "AND"} name='boolean_operator'>
                <option disabled={ruleindex !== 1} value={"DEFAULT"}>---</option>
                <option disabled={ruleindex === 1} value={"AND"}>AND</option>
                <option disabled={ruleindex === 1} value={"OR"}>OR</option>
            </Form.Select>
        </FloatingLabel>
    </Form.Group>)
}

function IdentifierInput({ index, identifier, setIdentifier }) {
    return (<Form.Group key={index} about='Filter Rule Name'>
        <FloatingLabel label='Filter Rule Name'>
            <Form.Control type='text' defaultValue={identifier} onChange={(e) => setIdentifier(e.target.value)}></Form.Control>
        </FloatingLabel>
    </Form.Group>)
}

function ColumnTypeSelection({ index, handleColumnTypeChage }) {
    return (
        <Form.Group key={index} about='Column Type Selection'>
            <FloatingLabel label='Select Column Type'>
                <Form.Select name='column_type' onChange={handleColumnTypeChage}>
                    <option value={"TITLE"}>Title</option>
                    <option value={"AUTHOR"}>Author</option>
                    <option value={"GENRE"}>Genre</option>
                </Form.Select>
            </FloatingLabel>
        </Form.Group>
    )
}

function QueryCommandSelection({ index, handleQueryCommandChange }) {
    return (
        <Form.Group key={index} about='Selecting Query Command'>
            <FloatingLabel label='Select Query Command'>
                <Form.Select name='query_command' onChange={handleQueryCommandChange} defaultValue={"STARTS_WITH"}>
                    <option value={"IS"}>is</option>
                    <option value={"IS_NOT"} disabled={true}>is not</option>
                    <option value={"STARTS_WITH"}>starts with</option>
                    <option value={"ENDS_WITH"}>ends with</option>
                </Form.Select>
            </FloatingLabel>
        </Form.Group>
    )
}

function QueryValueInput({ index, setHaltSave }) {

    let [isValidValue, confirmValueValid] = useState(false)

    const validateField = (e) => {
        let queryValue = e.target.value
        if (isEmpty(queryValue)) {
            confirmValueValid(false)
            setHaltSave(true)
        } else {
            confirmValueValid(true)
            setHaltSave(false)
        }

    }

    const isEmpty = (value) => {
        return value.trim().length === 0
    }
    return (<Form.Group key={index} about='Enter Query Value'>
        <FloatingLabel label='Enter query value'>
            <Form.Control type='text' placeholder='John Doe' name='query_value' required={true} onChange={validateField}></Form.Control>
            {!isValidValue && <Form.Text >
                Query value cannot just be whitespace
            </Form.Text>}
        </FloatingLabel>
    </Form.Group>)
}

export default FilterRule;