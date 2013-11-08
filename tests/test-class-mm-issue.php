<?php

class Test_MM_Issue extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->test = new MM_Issue;
	}

	/**
	 * Test that these return values of the correct type
	 */
	function test_get_issue_content_heading() {
		$actual = $this->test->get_issue_content_heading();
		$this->assertInternalType('string', $actual);
	}

	function test_get_issue_download_link_text() {
		$actual = $this->test->get_issue_download_link_text();
		$this->assertInternalType('string', $actual);
	}

	// TODO
	function test_inject_download_link() {
	}

}
