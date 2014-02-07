#!/usr/bin/env php
<?php

require_once 'CSVmaker.php';

/*
$csv = new CSVmaker();                          
$csv->table = $tableName;                               [required]
$csv->field = $fieldName;                               [required] 
$csv->condition = 'fieldName = :value';                 [optional] *require params
$csv->params = array('value' => 'string_to_match');     [optional]
$csv->exportPath = '/tmp'; // default is current dir.   [optional]
$csv->ExportFile();                                     export into file
$csv->ExportPrint();                                    print in std output
 */

$csv = new CSVmaker();
$csv->table = 'table_example';
$csv->field = 'id_example, user_example, pass_example';
$csv->ExportFile();


?>
