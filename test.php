<h1>FlatDB test suite</h1>
<?php
require_once('../simpletest/autorun.php');
require_once('flatdb.php');

class TestFlatDB extends UnitTestCase {

    function testDbColumnCount() {
        $myDb = new FlatDB('/var/www/FlatDB/my.db');
        $this->assertTrue($myDb->columns() > 0);
    }

    function testDbRowCount() {
        $myDb = new FlatDB('/var/www/FlatDB/my.db');
        $this->assertFalse($myDb->rows() > 0);
    }

}