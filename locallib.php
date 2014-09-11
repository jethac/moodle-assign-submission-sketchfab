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

define('SKETCHFAB_DB_TABLE', 'assignsubmission_sketchfab');
define('SKETCHFAB_OEMBED_ENDPOINT', 'https://sketchfab.com/oembed');
define('SKETCHFAB_MODELPAGE_URL', 'http://sketchfab.com/models');

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

    public function is_empty(stdClass $submission) {
        return false;
    }


    /**
     * Display a count of the submission items and a link to a more detailed
     * list.
     *
     * @param stdClass $submission
     * @param bool $showviewlink Enable view link.
     * @return string
     */
    public function view_summary(stdClass $submission, & $showviewlink) {
        global $DB;

        $showviewlink = true;

        $count = $DB->count_records(
            SKETCHFAB_DB_TABLE,
            array(
                'assignment' => $this->assignment->get_instance()->id,
                'submission' => $submission->id
            )
        );
        return get_string(
            $count == 1 ? 'countitem_single' : 'countitem_plural',
            'assignsubmission_sketchfab',
            $count
        );
    }

    /**
     * Display a more detailed view of the submission items.
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission) {
        global $OUTPUT, $DB;

        // Get the items.
        $items = $DB->get_records(
            SKETCHFAB_DB_TABLE,
            array(
                'assignment' => $this->assignment->get_instance()->id,
                'submission' => $submission->id
            )
        );

        $responses = array();
        foreach ($items as $item) {

            $curl = new curl();
            // SKETCHFAB_OEMBED_ENDPOINT
            $sketchfab_data = array(
                'url' => SKETCHFAB_MODELPAGE_URL . '/' . $item->model,
                'maxwidth' => 480,
                'maxheight' => 360
            );
            $curlsuccess = $curl->get(
                SKETCHFAB_OEMBED_ENDPOINT,
                $sketchfab_data,
                array(
                    'CURLOPT_RETURNTRANSFER' => 1
                )
            );

            $curlmetadata = new stdClass();
            $curlmetadata->success = $curlsuccess;
            $curlmetadata->errno = $curl->errno;
            $curlmetadata->error = $curl->error;
            $curlmetadata->response = $curl->response;

            // Did the request succeed?
            $oembediframe = "";
            if (!empty($curlsuccess)) {
                // Parse JSON for the unique ID.
                $oembediframe = html_writer::tag(
                    'div',
                    json_decode($curlsuccess, true)["html"],
                    array(
                        'class' => 'sketchfab-oembed'
                    )
                );
            }


            $responses[] = $oembediframe;
        }


        return implode($responses);//'<pre>'.var_dump($responses).'</pre>';
    }


    public function save(stdClass $submission, stdClass $data) {
        global $USER, $DB, $OUTPUT;

        $assignmentid = $this->assignment->get_instance()->id;
        $submissionid = $submission->id;


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
        $requests = array();
        $filenames = array();

        $records = array();
        $updaterecords = array();
        foreach ($files as $file) {

            $filenamenoext = pathinfo($file->get_filename())['filename'];

            $sketchfab_data = array(
                'token' => $data->sketchfab_api_token,
                'modelFileName' => $file->get_filename(),
                'modelFile' => $file,
                'name' => $filenamenoext,
                'description' => 'Automatically uploaded from Moodle.'
            );
            $requests[$file->get_filename()] = $sketchfab_data;

            $curlrequest = new curl();
            $curlsuccess = $curlrequest->post(
                $data->sketchfab_api_url,
                $sketchfab_data,
                array(
                    'CURLOPT_RETURNTRANSFER' => 1
                )
            );

            $curlmetadata = new stdClass();
            $curlmetadata->success = $curlsuccess;
            $curlmetadata->errno = $curlrequest->errno;
            $curlmetadata->error = $curlrequest->error;
            $curlmetadata->response = $curlrequest->response;

            $sketchfabUID = "";

            // Did the request succeed?
            if (!empty($curlsuccess)) {
                // Parse JSON for the unique ID.
                $sketchfabUID = json_decode($curlsuccess, true)["uid"];
            }
            $result[$file->get_filename()] = $sketchfabUID;

            // Build a record and add it to a list.
            $record = array(
                'assignment' => $assignmentid,
                'submission' => $submissionid,
                'model' => $sketchfabUID
            );
            $records[] = $record;
        }


        // @todo: Stop making the baby robot Jesus cry.
        // Delete existing rows for this user's submission.
        $DB->delete_records(
            SKETCHFAB_DB_TABLE,
            array(
                'assignment' => $assignmentid,
                'submission' => $submissionid
            )
        );

        // Add new rows for this user's submission.
        $DB->insert_records(SKETCHFAB_DB_TABLE, $records);


        return true;
    }

}