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

// File areas for file submission assignment.
define('ASSIGNSUBMISSION_SKETCHFAB_MAXFILES', 20);
define('ASSIGNSUBMISSION_SKETCHFAB_MAXSUMMARYFILES', 5);
define('ASSIGNSUBMISSION_SKETCHFAB_FILEAREA', 'submission_sketchfab');

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

        $fileoptions = $this->get_file_options();
        $submissionid = $submission ? $submission->id : 0;

        $data = file_prepare_standard_filemanager($data,                                                                            
                                                  'files',                                                                          
                                                  $fileoptions,                                                                     
                                                  $this->assignment->get_context(),                                                 
                                                  'assignsubmission_sketchfab',                                                          
                                                  ASSIGNSUBMISSION_SKETCHFAB_FILEAREA,                                                   
                                                  $submissionid);                                                                   
        $mform->addElement('filemanager', 'files_filemanager', html_writer::tag('span', $this->get_name(),                          
            array('class' => 'accesshide')), null, $fileoptions);

        return true;
    }

}