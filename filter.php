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

defined('MOODLE_INTERNAL') || die;

/**
 * This is the filter itself.
 *
 * @package    filter_lti
 * @copyright  2022 Massey University
 * @author     Andrew Rowatt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_lti extends moodle_text_filter {
    /**
     * Function filter replaces any lti-sources.
     */
    public function filter($text, array $options = array()) {
        global $CFG, $DB, $COURSE, $OUTPUT;

        if (empty($COURSE->id) || $COURSE->id == 0) {
            return $text;
        }
        if (strpos($text, '{lti:') === false) {
            return $text;
        }

        $modinfo = get_fast_modinfo($COURSE);
        $cms = $modinfo->get_cms();

        foreach ($cms as $cm) {
            if ($cm->modname != 'lti') {
                continue;
            }
            $params = (object) array(
                'id' => $cm->id,
                'name' => $cm->modname,
                'url' => $cm->url,
                'wwwroot' => $CFG->wwwroot,
            );
            switch ($cm->modname) {
                case 'lti':
                    $embed = $OUTPUT->render_from_template('filter_lti/embed-lti', $params);
                    //$embed_mediasite = $OUTPUT->render_from_template('filter_lti/embed-lti-mediasite', $params);
                    //$embed_padlet = $OUTPUT->render_from_template('filter_lti/embed-lti-padlet', $params);
                break;
            }
            //$text = str_replace('{lti:mediasite:' . $cm->name . '}', $embed_mediasite, $text);
            //$text = str_replace('{lti:padlet:' . $cm->name . '}', $embed_padlet, $text);
            $text = str_replace('{lti:' . $cm->name . '}', $embed, $text);
        }

        return $text;
    }
}
