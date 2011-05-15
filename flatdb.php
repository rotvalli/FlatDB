<?php

/*
 * Otto Salminen 2011, Public domain
 * 
 * Flat DB to read and modify flat file database
 * 
 * 
 * File format
 * ===========
 * Line 0 defines column names. Names are separated by | character and line ends
 * with \n.
 * 
 * name1|name2|name3\n
 * 
 * Following lines are data records. Same rules apply for column separator and 
 * line ending.
 * 
 * column1|column2|column3\n
 * ...
 * 
 * Data type
 * =========
 * Data is red from file to an array.
 * 
 * Array([0] => ( name1 => column1, name2 => column2, name3 => column3 ),
 *       [1] => ( ... ),
 *       [2] => ( ... ));
 *  
 */

class FlatDB {

    /**
     * Absolute file path
     */
    private $filename = "";
    
    /**
     * Rows in database
     */
    private $rows = 0;

    /**
     * Columns in database row
     */
    private $columns = 0;

    /**
     * List of column names
     */
    private $column_names = array();

    /**
     * Database instance loaded in memory
     */
    private $db = array();

    public function db($row=-1, &$column=-1) {
        if ($row != -1 && $column != -1) {
            return $this->db[$row][$column];
        } else if ($row != -1) {
            return $this->db[$row];
        } else {
            return $this->db;
        }
    }

    public function rows() {
        return $this->rows;
    }

    public function columns() {
        return $this->columns;
    }

    public function column_names($column) {
        return $this->column_names[$column];
    }

    public function fetch() {

        if ($this->filename == "") {
            echo "No database file set\n";
            return;
        }

        if (!($data = file($this->filename))) {
            echo "Cannot open database file\n";
            return;
        }

        $columns = explode("|", trim($data[0]));
        $this->column_names = $columns;
        $this->columns = count($columns);
        if (!$this->columns) {
            echo "No columns in database\n";
            return;
        }

        $db = array();
        //Rows minus columns row
        $this->rows = count($data) - 1;

        for ($r = 1; $r < $this->rows + 1; $r++) {
            $db[] = explode("|", trim($data[$r]));
        }

        $this->db = $db;
    }

    public function add_record($new_record) {

        $db_file_row = implode("|", $new_record) . "\n";

        $db_file = fopen($this->filename, "a");
        flock($db_file, LOCK_EX);
        if (fwrite($db_file, $db_file_row)) {
            $this->db[] = $new_record;
            $this->rows++;
        }
        flock($db_file, LOCK_UN);
        fclose($db_file);

        return true;
    }

    public function modify_record($row, $column, $new_data) {

        $this->db[$row][$column] = $new_data;

        $db_file_rows = implode("|", $this->column_names) . "\n";

        for ($r = 0; $r < $this->rows; $r++) {
            $db_file_rows += implode("|", $this->db($r)) . "\n";
        }

        $db_file = fopen($this->filename, "w");
        flock($db_file, LOCK_EX);
        if (fwrite($db_file, $db_file_rows)) {
            
        }
        flock($db_file, LOCK_UN);
        fclose($db_file);

        return true;
    }

    public function remove_record($row) {

        $db_file_rows = implode("|", $this->column_names) . "\n";

        for ($r = 0; $r < $this->rows; $r++) {
            if ($row != $r) {
                $db_file_rows .= implode("|", $this->db($r)) . "\n";
            }
        }

        $db_file = fopen($this->filename, "w");
        flock($db_file, LOCK_EX);
        if (fwrite($db_file, $db_file_rows)) {
            $this->fetch();
        }
        flock($db_file, LOCK_UN);
        fclose($db_file);

        return true;
    }

    public function __construct($filename) {
        $this->filename = $filename;
        $this->fetch();
    }

}