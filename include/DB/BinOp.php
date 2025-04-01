<?php

namespace DB;

/**
 * SQL Binary operator.
 */
enum BinOp: string
{
    case And = 'and';
    case Or = 'or';
    /** Greater than */
    case Gt = '>';
    /** Greater than or equal */
    case Ge = '>=';
    /** Less than */
    case Lt = '<';
    /** Less than or equal */
    case Le = '<=';
    /** Equal to */
    case Eq = '=';
    /** Not equal to */
    case Ne = '<>';
    /** In list */
    case In = 'in';
}