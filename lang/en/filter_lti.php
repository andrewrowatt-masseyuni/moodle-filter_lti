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

/**
 * String file for filter_lti.
 *
 * @package    filter_lti
 * @copyright  2022 Massey University
 * @author     Andrew Rowatt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['customprefixes'] = 'Custom prefixes';
$string['customprefixes_desc'] = 'Custom prefixes for LTI filter tags, separated by pipe character (e.g., mediasite|padlet)';
$string['customprefixcss'] = 'Custom prefix CSS';
$string['customprefixcss_desc'] = 'Custom CSS for custom prefixes';
$string['filtername'] = 'LTI (External tool) Filter';
$string['oneormoretagsunmatched'] = '<div><b>Warning: </b>The following name(s) were were not able to be matched: <b>{$a}</b>. Please ensure that the corresponding LTI activity exists in this course.</div><div><small>Students will not see this message.</small></div>';
$string['pluginname'] = 'LTI (External tool) Filter';
$string['privacy:metadata'] = 'This plugin does not store any data at all.';
