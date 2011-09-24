<?php
require_once(dirname(__FILE__) .'/../../simpletest/autorun.php');
require_once(dirname(__FILE__) .'/../flatdb.php');

class TestRows extends UnitTestCase {

    function testRowsMethod() {
        $fp = fopen(dirname(__FILE__) .'/../my.csv', 'w');
        fputcsv($fp, array("one"));
        fclose($fp);

        $myDb = new FlatDB(dirname(__FILE__) .'/../my.csv');
                
        $this->assertFalse($myDb->rows() > 0);

        $myDb->add_record(array("row"));

        $myDb = new FlatDB(dirname(__FILE__) .'/../my.csv');
                
        $this->assertTrue($myDb->rows() > 0);
    }

}