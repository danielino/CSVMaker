<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * @category   Csv Maker
 * @package    CSVmaker
 * @author     Daniele Marcocci <danielinor0x@hotmail.com>
 * @link       https://github.com/danielino/CSVMaker
 *
 *
 * This class extract data from mysql
 * and create csv with field comma separated
 * example:
 *      "field1","field2","field3"
 *
 * 
 * see readme.php for help
 */

class CSVmaker
{

    // private variable for db connection
    
    private $_db_host;
    private $_db_user;
    private $_db_pass;
    private $_db_name;
    private $_instance;
    private $_dsn;
    private $_db_adapter;
    
    public $max_row;                // int            
    public $table;                  // string
    public $field = array();        
    public $condition;              // 'field_id_table = :id'
    public $params = array();       // array('id'=>$id)
    
    // public var for manipulate path and filename
    public $exportPath;             // extract file into path
    public $fileName;               // name for file
    
    
    
    public function __construct()
    {
        $this->_db_adapter = 'mysql';
        $this->_db_host = '';
        $this->_db_user = '';
        $this->_db_pass = '';
        $this->_db_name = '';
        
        $this->__init();    // initialize pdo 
    }




    /**
     * destroy pdo instance 
     */
    public function ___destruct()
    {
        $this->_instance = null;
    }



    
    /**
     * this function print the data in csv format to the standard output
     * 
     */
    public function ExportPrint()
    {
        echo $this->Export();
    }
    
    
    
    /**
     * this function export the data into file 
     */
    public function ExportFile()
    {
        $this->writeFile($this->Export());
    }
    
    
    
    
    
    /***** private function *****/
    
    
    /**
     * Initialize mysql db connection
     */
    private function __init()
    {
        // set exportPath with current absolute path
        $this->exportPath = getcwd();
        
        $this->_dsn = $this->_db_adapter.':host=' . $this->_db_host .
                     ';dbname=' . $this->_db_name;
        $this->_instance = new PDO($this->_dsn, $this->_db_user, $this->_db_pass);
    }
    
    
    /**
     * query the db with PDO library 
     * @return string or boolean false if can't do query
     */
    private function Export()
    {
        
        $allRes = $this->selectAll();
        
        // create the header of file with the name of db field 
        $data = "";
        for($i = 0; $i < count($this->field) ; $i++)
            $data .= ($i < (count($this->field) - 1)) ? "\"". $this->field[$i] ."\"," : "\"". $this->field[$i] ."\"";
        $data .= "\n";
               
        // remove the header before iterate array
        unset($allRes[0]);
        
        foreach($allRes as $value)
        {
            if( !is_array($value) )
                continue;
            
            $j = 0; // column counter
            $arr = array();
            
            foreach($value as $value2)  // create sequential array
                $arr[] = rtrim($value2, '0x0D');     // remove ^M
            
            // create row
            while($j < count($arr)){
                $data .=  '"'. $arr[$j] . (($j < (count($arr) - 1 )) ? "\"," : "\"");
                $j++;
            }
            
            // close line with line feed
            $data .= "\n";
        }
        
        // return data or false
        return $data ? $data : false;
    }
    
    
    
    /**
     * write data into file
     * @param string $data
     * @return boolean
     * @throws Exception "can't write into $path" | path is not writable
     * @throws Exception "can't open file for write. " | can't open file
     */
    private function writeFile($data)
    {
        if(!is_writable($this->exportPath))
            throw new Exception ("can't write into ".$this->exportPath);
        
        $file = $this->exportPath."/";
        
        $tmpfname = ($this->fileName != "" && $this->fileName != NULL) ? 
                    $this->fileName :
                    $this->createRandomFileName($this->exportPath, $this->table, '.csv');
        
        $this->fp = fopen($tmpfname, 'w+');
        
        if(!$this->fp)
            throw new Exception("can't open file for write.");
        
        if( !fwrite($this->fp, $data) )
            return false;
        
        fclose($this->fp);
    }
    
    
    
    /**
     *
     * @param string $path
     * @param string $suffix
     * @param string $extension - default .csv
     * @return string fileName
     */
    private function createRandomFileName($path, $suffix = '', $extension = '.csv')
    {
        // default suffix is the name of the db table
        $suffix = $suffix != "" ? $suffix : $this->table;

        if($path == "" || $path == NULL)
            return false;

        $randName = substr(mt_rand(), 0, 3);    // select only 3 int

        // absolute_path/suffixName-randomNumber.extension
        $file = $path."/export-".$suffix."-".$randName.$extension;

        return $file;
    }
    
    
    /**
     * select all row from MySQL
     * @return boolean or array $row
     */
    private function selectAll()
    {
        $this->field = ( $this->field != "" && $this->field != NULL ) ? $this->field : '*';
        if( !$this->table )
            return false;
        
        // create select
        $sql = "SELECT ";
        for($i = 0; $i < count($this->field) ; $i++)
            $sql .= ($i < (count($this->field) - 1)) ? " ".$this->field[$i].", " : " ". $this->field[$i]." ";
        $sql .= "FROM ". $this->table .";";
        
        // format field=:field
        if( $this->condition )
            $sql .= " WHERE ".$this->condition;
        
        // limit results
        if( is_int($this->max_row) && $this->max_row > 0 )
            $sql .= " LIMIT ".$this->max_row;
        
        // check for params
        if( $this->condition && (!is_array($this->params) || count($this->params) == 0) )
            throw new Exception ("you've specified condition without params");
        
        // prepare and execute query with or without params
        $res = $this->_instance->prepare($sql);
        is_array($this->params) ? $res->execute($this->params) : $res->execute();
        
        if($res->rowCount() > 0)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $obj = $res->fetchAll();
            if(count($obj) === 1)
                return $obj[0];

            return $obj;
        }
        
        return false;
    }
    
    
}
