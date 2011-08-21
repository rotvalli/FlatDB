<?php
require_once(dirname(__FILE__) .'/../simpletest/autorun.php');
require_once('flatdb.php');

class TestRows extends UnitTestCase {

    function testRowsMethod() {
        $myDb = new FlatDB(dirname(__FILE__) .'/my.db');
        $this->assertFalse($myDb->rows() > 0);
    }

}