CSVMaker
========

export mysql table into csv 

# example
======== 
```php
initialize object
$csv = new CSVmaker();                          

set table name
$csv->table = $tableName;                               [required]

set field comma separated
$csv->field = $fieldName;                               [required] 

add condition
$csv->condition = 'fieldName = :value';                 [optional] *require params

set params [required when condition is defined]
$csv->params = array('value' => 'string_to_match');     [optional]

path to save the file generated
$csv->exportPath = '/tmp'; // default is current dir.   [optional]

export into file
$csv->ExportFile();                                     export into file

print into stdout
$csv->ExportPrint();                                    print in std output


Export Table
```
