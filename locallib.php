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
 * Sketchfab Assignment Submission Plugin
 *
 * @package    assignsubmission
 * @subpackage sketchfab
 * @copyright 2014 Jetha Chan
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/../mod/assign/submission/file/locallib.php');

class assign_submission_sketchfab extends assign_submission_plugin {

	/** 
	 * Returns the name of this plugin.
	 *
	 * @return string The name of this plugin.
	 */
	public function get_name() {
		return get_string('pluginname', 'assignsubmission_sketchfab');
	}

    /**
     * Add form elements to the submission form.
     *
     * @param mixed $submission can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return true if elements were added to the form
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        global $CFG, $COURSE, $PAGE, $OUTPUT;

        $submissionid = $submission ? $submission->id : 0;
        
        $mform->addElement(
            'hidden',
            'sketchfab_api_url',
            get_config(
                'assignsubmission_sketchfab',
                'endpointurl'
            )
        );
        $mform->setType(
            'sketchfab_api_url',
            PARAM_TEXT
        );

        // API token.
        $mform->addElement(
            'text',
            'sketchfab_api_token',
            get_string('apitoken', 'assignsubmission_sketchfab')
        );
        $mform->setType(
            'sketchfab_api_token',
            PARAM_TEXT
        );
        $mform->addHelpButton(
            'sketchfab_api_token',
            'apitoken',
            'assignsubmission_sketchfab'
        );
        $mform->addRule(
            'sketchfab_api_token',
            get_string('apitoken_required', 'assignsubmission_sketchfab'),
            'required',
            '',
            'client'
        );

        return true;
    }

    protected function count_files($submissionid, $area) { 

        return count($files);
    }


    public function save(stdClass $submission, stdClass $data) {
        global $USER, $DB, $OUTPUT;

        // Rely upon the files uploaded by the mod_assign_submission_file; let's not
        // reinvent the wheel.
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id,
                                     'assignsubmission_file',
                                     ASSIGNSUBMISSION_FILE_FILEAREA,
                                     $submission->id,
                                     'id',
                                     false);


        $count = count($files);

        echo $OUTPUT->notification("derp: $count", "notifysuccess");

        $result = array();
        $filenames = array();
        foreach ($files as $file) {
            $result[$file->get_filename()] = $file;
            $filenames[] = $file->get_filename();
        }

        ?>
        <pre><?php var_dump($filenames); ?></pre>
        <hr />
        <pre><?php var_dump($submission); ?></pre>
        <hr />
        <pre><?php var_dump($data); ?></pre>
        <?php


        return true;
    }

}