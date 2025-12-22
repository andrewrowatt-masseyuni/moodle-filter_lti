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
 * This is the filter itself.
 *
 * @package    filter_lti
 * @copyright  2022 Massey University
 * @author     Andrew Rowatt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    #[\Override]
    public function filter($text, array $options = []) {
        global $OUTPUT, $PAGE;

        // This part based on filter activitynames.
        $coursectx = $this->context->get_course_context(false);
        if (!$coursectx) {
            return $text;
        }
        $courseid = $coursectx->instanceid;

        // Get custom prefixes from settings.
        $allprefixes = 'lti';
        $customprefixes = get_config('filter_lti', 'customprefixes');
        if (empty($customprefixes)) {
            $allprefixes .= '|mediasite|padlet';
        } else {
            $allprefixes .= "|$customprefixes";
        }

        // Escape each prefix for use in regex pattern.
        $prefixes = explode('|', $allprefixes);
        $escapedprefixes = array_map('preg_quote', $prefixes, array_fill(0, count($prefixes), '/'));
        $prefixpattern = implode('|', $escapedprefixes);

        $pattern = '/\{(' . $prefixpattern . '):([^{|}]+)(?:\|(.+))?\}/';
        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER) === false) {
            return $text;
        }

        $modinfo = get_fast_modinfo($courseid);
        $cms = $modinfo->get_cms();

        foreach ($cms as $cm) {
            if ($cm->modname != 'page') {
                continue;
            }

            foreach ($matches as $match) {
                if (strcasecmp($cm->name, $match[2]) == 0) {
                    $options = isset($match[3]) ? $match[3] : '';
                    $params = (object) [
                        'type' => $match[1],
                        'options' => isset($match[3]) ? $match[3] : '',
                        'cmid' => $cm->id,
                        'title' => $cm->name,
                    ];

                    $embed = $OUTPUT->render_from_template('filter_lti/embed-lti', $params);

                    $text = str_ireplace("{{$match[1]}:{$cm->name}" . ($options != '' ? "|$options" : '') . "}", $embed, $text);
                }
            }
        }

        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            $unmatchednames = [];
            foreach ($matches as $match) {
                $unmatchednames[] = $match[2];
            }

            $unmatchednamescombined = implode(', ', $unmatchednames);

            if (has_capability('moodle/course:manageactivities', $PAGE->context)) {
                $unmatchedtagnotification = $OUTPUT->render_from_template(
                    'filter_lti/unmatched-tags',
                    ['message' => get_string(
                        'oneormoretagsunmatched',
                        'filter_lti',
                        trim($unmatchednamescombined)
                    ),
                    ]
                );
                $text .= $unmatchedtagnotification;
            }
        }

        return $text;
    }
}
