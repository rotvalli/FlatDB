<h1>FlatDB test suite</h1>
<?php
require_once(dirname(__FILE__) .'/../simpletest/autorun.php');

class AllTests extends TestSuite {
    function AllTests() {
        $this->TestSuite('FlatDB tests - Simple test'.SimpleTest::getVersion());
        $this->addFile(dirname(__FILE__) .'/test_rows.php');
        $this->addFile(dirname(__FILE__) .'/test_columns.php');
    }
}