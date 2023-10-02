export enum Column {
    TITLE = "TITLE",
    AUTHORS = "AUTHORS",
    GENRE = "GENRE",
    NONE = 0
}

export function getColumnFromString(column: string): Column {
    column = column.toUpperCase()
    switch (column) {
        case "TITLE":
            return Column.TITLE
        case "AUTHORS":
            return Column.AUTHORS
        case "GENRE":
            return Column.GENRE
    }
    return Column.NONE
}

export enum QueryCommand {
    IS = "IS",
    IS_NOT = "IS_NOT",
    STARTS_WITH = "STARTS_WITH",
    ENDS_WITH = "ENDS_WITH",
    NONE = 0
}

export function getQueryCommandFromString(queryCommand: string): QueryCommand {
    queryCommand = queryCommand.toUpperCase()
    switch (queryCommand) {
        case "IS":
            return QueryCommand.IS
        case "IS_NOT":
            return QueryCommand.IS_NOT
        case "STARTS_WITH":
            return QueryCommand.STARTS_WITH
        case "ENDS_WITH":
            return QueryCommand.ENDS_WITH
    }
    return QueryCommand.NONE
}

export enum BooleanOperator {
    AND = "AND",
    OR = "OR",
    DEFAULT = "DEFAULT",
    NONE = 0
}

export function getBooleanOperatorFromString(booleanOperator: string): BooleanOperator {
    booleanOperator = booleanOperator.toUpperCase()
    switch (booleanOperator) {
        case "AND":
            return BooleanOperator.AND
        case "OR":
            return BooleanOperator.OR
        case "DEFAULT":
            return BooleanOperator.DEFAULT

    }
    return BooleanOperator.NONE
}

export interface FilterRuleData {
    index: number
    boolean_operator: BooleanOperator | string;
    column_type: Column | string; 
    query_command: QueryCommand | string;
    query_value: string;
}

export type UpdateFilterGroupData = (index: number, filterRuleData: FilterRuleData) => void;

export type UnsetFilterRule = (index: number) => void;

export interface FilterRuleValue {
    index: number
    element: JSX.Element
    data: FilterRuleData
}