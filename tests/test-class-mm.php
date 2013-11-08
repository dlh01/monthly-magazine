<?php

class Test_MM extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->test = MM::get_instance();
	}

	/**
	 * Test that these return values of the correct type
	 */
	function test_get_textdomain() {
		$actual = $this->test->get_textdomain();
		$this->assertInternalType('string', $actual);
	}

	function test_get_meta_prefix() {
		$actual = $this->test->get_meta_prefix();
		$this->assertInternalType('string', $actual);
	}

	function test_get_post_type_slug() {
		$actual = $this->test->get_issue_post_type_slug();
		$this->assertInternalType('string', $actual);
	}

	function test_get_post_type_supports() {
		$actual = $this->test->get_post_type_supports();
		$this->assertInternalType('array', $actual);
	}

	function test_get_issue_file_postmeta_key() {
		$actual = $this->test->get_issue_file_postmeta_key();
		$this->assertInternalType('string', $actual);
	}

	function test_get_post_type_label() {
		$actual = $this->test->get_post_type_label();
		$this->assertInternalType('string', $actual);
	}

	function test_get_issue_content_connection_name() {
		$actual = $this->test->get_issue_content_connection_name();
		$this->assertInternalType('string', $actual);
	}

	function test_get_issue_content_connection_from_title() {
		$actual = $this->test->get_issue_content_connection_from_title();
		$this->assertInternalType('string', $actual);
	}

	function test_get_issue_content_connection_to_title() {
		$actual = $this->test->get_issue_content_connection_to_title();
		$this->assertInternalType('string', $actual);
	}

	function test_issue_content_post_types() {
		$actual = $this->test->get_issue_content_post_types();
		$this->assertInternalType('array', $actual);

		// test the filter with valid data
		$good_types = array( 'post', 'page', 'foo' );
		add_filter( 'mm_issue_content_post_types', function() { return $good_types; } );
		$actual = $this->test->get_issue_content_post_types();
		$this->assertInternalType('array', $actual);

		// test the filter with invalid string data
		// the return value should not be a string
		// and should equal the default value of
		// $mm->issue_content_post_types;
		$bad_types = rand_str();
		add_filter( 'mm_issue_content_post_types', function() { return $bad_types; } );
		$actual = $this->test->get_issue_content_post_types();
		$this->assertInternalType('array', $actual);
		$this->assertEquals(array('post'), $actual);
	}

	function test_mm_issue_init() {
		// the post type is created on init, so it should exist
		$this->assertTrue(post_type_exists('mm_issue'));
	}
	
}

