<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace filter_lti;

/**
 * Tests for LTI (External tool) Filter
 *
 * @package    filter_lti
 * @category   test
 * @copyright  2025 Andrew Rowatt <A.J.Rowatt@massey.ac.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class text_filter_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Enable filter at top level.
        filter_set_global_state('lti', TEXTFILTER_ON);
        filter_set_global_state('activitynames', TEXTFILTER_DISABLED);
    }

    /**
     * Check all valid scenarios for LTI filter links.
     *
     * @covers \filter_lti
     * @return void
     */
    public function test_links(): void {
        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create lti modules.
        $lti1 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'lti 1']
        );

        $padlet1 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'padlet 1']
        );

        $mediasite1 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'mediasite 1']
        );

        $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'page 1']
        );

        // Format text with all three entries in HTML.
        $html = '<p>Test 1 - lti</p>
            <p>{lti:lti 1}</p>
            <p>Test 2 - padlet</p>
            <p>{padlet:padlet 1}</p>
            <p>Test 3 - lti - name not found</p>
            <p>{lti:lti 2}</p>
            <p>Test 4 - mediasite</p>
            <p>{mediasite:mediasite 1}</p>
            <p>Test 5 - mediasite with options</p>
            <p>{mediasite:mediasite 1|16x9}</p>
            <p>Test 6 - other module - not implemented</p>
            <p>{page:page 1}</p>';

        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);

        $this->assertStringContainsString("title=\"{$lti1->name}\"", $filtered);
        $this->assertStringContainsString("data-filter_lti-cmid=\"{$lti1->cmid}\"", $filtered);

        $this->assertStringContainsString("title=\"{$padlet1->name}\"", $filtered);
        $this->assertStringContainsString("data-filter_lti-cmid=\"{$padlet1->cmid}\"", $filtered);
        $this->assertStringContainsString("title=\"{$mediasite1->name}\"", $filtered);
        $this->assertStringContainsString("data-filter_lti-cmid=\"{$mediasite1->cmid}\"", $filtered);
        $this->assertStringContainsString("filter_lti_options_16x9", $filtered);

        $this->assertStringContainsString("{lti:lti 2}", $filtered);
        $this->assertStringContainsString(
            "<b>Warning: </b>The following name(s) were were not able to be matched: <b>lti 2</b>",
            $filtered
        );

        $this->assertStringContainsString("{page:page 1}", $filtered);
    }

    /**
     * Test custom prefixes setting.
     *
     * @covers \filter_lti
     * @return void
     */
    public function test_custom_prefixes(): void {
        // Set custom prefixes.
        set_config('customprefixes', 'custom1|custom2', 'filter_lti');

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create modules.
        $custom1 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'custom 1']
        );

        $custom2 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'custom 2']
        );

        // Format text with custom prefixes.
        $html = '<p>Test custom1</p>
            <p>{custom1:custom 1}</p>
            <p>Test custom2</p>
            <p>{custom2:custom 2}</p>
            <p>Test old prefix should not work</p>
            <p>{lti:custom 1}</p>';

        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);

        // Custom prefixes should work.
        $this->assertStringContainsString("title=\"{$custom1->name}\"", $filtered);
        $this->assertStringContainsString("data-filter_lti-cmid=\"{$custom1->cmid}\"", $filtered);
        $this->assertStringContainsString("title=\"{$custom2->name}\"", $filtered);
        $this->assertStringContainsString("data-filter_lti-cmid=\"{$custom2->cmid}\"", $filtered);
    }

    /**
     * Test default prefixes when setting is not configured.
     *
     * @covers \filter_lti
     * @return void
     */
    public function test_default_prefixes(): void {
        // Ensure custom prefixes is empty or not set.
        set_config('customprefixes', '', 'filter_lti');

        echo get_config('filter_lti', 'customprefixes');

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create modules.
        $lti1 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'lti 1']
        );

        $padlet1 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'padlet 1']
        );

        $mediasite1 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'mediasite 1']
        );

        // Format text with default prefixes.
        $html = '<p>Test lti</p>
            <p>{lti:lti 1}</p>
            <p>Test padlet</p>
            <p>{padlet:padlet 1}</p>
            <p>Test mediasite</p>
            <p>{mediasite:mediasite 1}</p>';

        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);

        // Default prefixes should work.
        $this->assertStringContainsString("title=\"{$lti1->name}\"", $filtered);
        $this->assertStringContainsString("data-filter_lti-cmid=\"{$lti1->cmid}\"", $filtered);
        $this->assertStringContainsString("title=\"{$padlet1->name}\"", $filtered);
        $this->assertStringContainsString("data-filter_lti-cmid=\"{$padlet1->cmid}\"", $filtered);
        $this->assertStringContainsString("title=\"{$mediasite1->name}\"", $filtered);
        $this->assertStringContainsString("data-filter_lti-cmid=\"{$mediasite1->cmid}\"", $filtered);
    }

    /**
     * Test that prefixes with special regex characters are properly escaped.
     *
     * @covers \filter_lti
     * @return void
     */
    public function test_prefixes_with_special_characters(): void {
        // Set custom prefixes with regex special characters.
        set_config('customprefixes', 'test.prefix|test+tool|test(app)', 'filter_lti');

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create modules.
        $test1 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'test 1']
        );

        // Format text with the prefixes.
        $html = '<p>Test with special chars</p>
            <p>{test.prefix:test 1}</p>
            <p>{test+tool:test 1}</p>
            <p>{test(app):test 1}</p>';

        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);

        // All prefixes should work despite special characters.
        $this->assertStringContainsString("title=\"{$test1->name}\"", $filtered);
        $this->assertStringContainsString("data-filter_lti-cmid=\"{$test1->cmid}\"", $filtered);
    }
}
