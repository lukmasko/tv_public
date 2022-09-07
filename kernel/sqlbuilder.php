<?php

namespace Kernel\SQLBuilder;
use Kernel\Model;


function getQuery_Where(Model\IModel $model, $table_name) : ?string
{
    $where = "";
    $vars = get_class_vars(get_class($model));

    foreach($vars as $index => $value){
        if(is_null($model->$index))
            continue;

        $where .= (empty($where)) ? sprintf("%s=:%s", $index, $index) : sprintf(" AND %s=:%s", $index, $index);
    }

    $res = sprintf("SELECT * FROM %s WHERE %s;", $table_name, $where);
    return $res;
}

function getQuery_WhereOrder(Model\IModel $model, $table_name, $orderByColumn, $asc=true) : ?string
{
    $where = "";
    $vars = get_class_vars(get_class($model));

    foreach($vars as $index => $value){
        if(is_null($model->$index))
            continue;

        $where .= (empty($where)) ? sprintf("%s=:%s", $index, $index) : sprintf(" AND %s=:%s", $index, $index);
    }

    $dir = ($asc) ? "ASC" : "DESC";

    $res = sprintf("SELECT * FROM %s WHERE %s ORDER BY %s %s;", $table_name, $where, $dir, $orderByColumn);
    return $res;
}

function getQuery_INSERT(Model\IModel $model, $table_name) : ?string
{
    return "";
}