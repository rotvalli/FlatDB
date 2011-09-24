<?php

/*
 * Otto Salminen 2011, Public domain
 * 
 * Flat DB to read and modify csv flat file database
 * 
 * 
 * File format
 * ===========
 * http://en.wikipedia.org/wiki/Comma-separated_values
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
     * Absolute file path.
     */
    private $filename = "";

    /**
     * Rows in database.
     */
    private $rows = 0;

    /**
     * Columns in database row.
     */
    private $columns = 0;

    /**
     * List of column names.
     */
    private $column_names = array();

    /**
     * Database instance loaded in memory.
     */
    private $db = array();

    /**
     * Getter for database. Without arguments returns everything.
     * @param integer $row database row
     * @param integer $column database column
     * @return array|string
     */
    public function db($row=-1, &$column=-1) {
        if ($row != -1 && $column != -1) {
            return $this->db[$row][$column];
        } else if ($row != -1) {
            return $this->db[$row];
        } else {
            return $this->db;
        }
    }

    /**
     * Getter for database row count.
     * @return integer
     */
    public function rows() {
        return $this->rows;
    }

    /**
     * Getter for database column count.
     * @return integer
     */
    public function columns() {
        return $this->columns;
    }

    /**
     * Getter for database column names.
     * @return array
     */
    public function column_names($column) {
        return $this->column_names[$column];
    }

    /**
     * Method for reading database from file to memory.
     */
    public function fetch() {

        if ($this->filename == "") {
            echo "No database file set\n";
            return;
        }


        if (!($data = file($this->filename))) {
            echo "Cannot open database file\n";
            return;
        }

        if (function_exists("str_getcsv")) {
            $this->column_names = str_getcsv($data[0]);
        } else {
            $this->column_names = explode(",", trim($data[0]));
        }
        $this->columns = count($this->column_names);

        if (!$this->columns) {
            echo "No columns in database\n";
            return;
        }

        $db = array();
        //Rows minus columns row
        $this->rows = count($data) - 1;

        if (function_exists("str_getcsv")) {
            for ($r = 1; $r < $this->rows + 1; $r++) {
                $db[] = array_map(array(&$this, 'unescape'), str_getcsv($data[$r]));
            }
        } else {

            for ($r = 1; $r < $this->rows + 1; $r++) {
                $db[] = array_map(array(&$this, 'unescape'), explode(",", trim($data[$r])));
            }
        }

        $this->db = $db;
    }

    /**
     * Method to add new record to database.
     * @param integer $new_record array of column values for new row
     * @return boolean
     */
    public function add_record($new_record) {

        $new_record = array_map(array(&$this, 'escape'), $new_record);

        if (function_exists("str_getcsv")) {
            $db_file = fopen($this->filename, "a");
            flock($db_file, LOCK_EX);
            if (fputcsv($db_file, $new_record)) {
                $this->db[] = $new_record;
                $this->rows++;
            }
            flock($db_file, LOCK_UN);
            fclose($db_file);
        } else {
            $db_file_row = implode(",", $new_record) . "\n";

            $db_file = fopen($this->filename, "a");
            flock($db_file, LOCK_EX);
            if (fwrite($db_file, $db_file_row)) {
                $this->db[] = $new_record;
                $this->rows++;
            }
            flock($db_file, LOCK_UN);
            fclose($db_file);
        }
        return true;
    }

    /**
     * Modify record in database.
     * @param integer $row database row number
     * @param integer $column database column number
     * @param string $new_data new column data
     * @return boolean
     */
    public function modify_record($row, $column, $new_data) {

        $this->db[$row][$column] = $this->escape($new_data);

        $db_file_rows = array();

        $db_file_rows = implode(",", $this->column_names) . "\n";

        for ($r = 0; $r < $this->rows; $r++) {
            $db_file_rows .= implode(",", $this->db($r)) . "\n";
        }

        $db_file = fopen($this->filename, "w");
        flock($db_file, LOCK_EX);
        if (fwrite($db_file, $db_file_rows)) {
            
        }
        flock($db_file, LOCK_UN);
        fclose($db_file);

        return true;
    }

    /**
     * Remove record from database.
     * @param integer $row database row number
     * @return boolean
     */
    public function remove_record($row) {

        $db_file_rows = implode(",", $this->column_names) . "\n";

        for ($r = 0; $r < $this->rows; $r++) {
            if ($row != $r) {
                $db_file_rows .= implode(",", $this->db($r)) . "\n";
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

    /**
     * String escape method.
     * @param string $value data to escape
     * @return string
     */
    private function escape($value) {

        $value = str_replace("\r\n", "\n", $value);
        $value = str_replace("\n", "<br/>", $value);

        return $value;
    }

    /**
     * String unescape method.
     * @param string $value data to unescape
     * @return string
     */
    private function unescape($value) {

        $value = str_replace("<br/>", "\n", $value);

        return $value;
    }

    /**
     * Default constructor.
     * @param string $filename database file
     */
    public function __construct($filename) {
        $this->filename = $filename;
        $this->fetch();
    }

}