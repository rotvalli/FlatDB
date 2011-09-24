<?php
require_once(dirname(__FILE__) .'/../../simpletest/autorun.php');
require_once(dirname(__FILE__) .'/../flatdb.php');

class TestFlatToCsv extends UnitTestCase {

    function testFlatCsv() {
        

        $fp = fopen(dirname(__FILE__) .'/../my.csv', 'w');
        fputcsv($fp, array("'o,ne'","t\wo","three"));
        fclose($fp);

        $flatDb = new FlatDB(dirname(__FILE__) .'/../my.csv');

        $flatDb->add_record(array("ki,ssa","ka\nni","koira"));                    

        $csvDb = new FlatDB(dirname(__FILE__) .'/../my.csv');
        
        for($r=0;$r<$flatDb->rows();$r++){
            $csvDb->add_record($flatDb->db($r));                    
        }
        
        $csvDb2 = new FlatDB(dirname(__FILE__) .'/../my.csv');
        
        $this->assertTrue($csvDb2->rows() == 2);

    }
    
    
}