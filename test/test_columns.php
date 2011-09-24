<?php
require_once(dirname(__FILE__) .'/../../simpletest/autorun.php');
require_once(dirname(__FILE__) .'/../flatdb.php');

class TestColumns extends UnitTestCase {

    function testColumnsMethod() {
        file_put_contents(dirname(__FILE__) .'/../my.csv', "");

        $myDb = new FlatDB(dirname(__FILE__) .'/../my.csv');
                
        $this->assertFalse($myDb->columns() > 0);

        $fp = fopen(dirname(__FILE__) .'/../my.csv', 'w');
        fputcsv($fp, array("one", "two"));
        fclose($fp);

        $myDb = new FlatDB(dirname(__FILE__) .'/../my.csv');
                
        $this->assertTrue($myDb->columns() > 0);

        
    }
    
}