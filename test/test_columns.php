<?php
require_once(dirname(__FILE__) .'/../simpletest/autorun.php');
require_once('flatdb.php');

class TestColumns extends UnitTestCase {

    function testColumnsMethod() {
        $myDb = new FlatDB(dirname(__FILE__) .'/my.db');
        $this->assertTrue($myDb->columns() > 0);
    }
}