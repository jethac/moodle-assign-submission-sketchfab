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
     * Get new settings to add to the assignment settings page.
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform) {
        global $CFG, $COURSE;

        // Get limits stored for this activity.
        $assignmentpolycount = $this->get_config('polylimit');
        $assignmentmatcount = $this->get_config('matlimit');
        $assignmenttexsize = $this->get_config('texsize');
        $assignmentallownpot = $this->get_config('allownpot');

        // Get defaults.
        $adminpolycount = get_config('assignsubmission_sketchfab', 'defaultpolycount');
        $adminmatcount = get_config('assignsubmission_sketchfab', 'defaultmatcount');
        $admintexsize = get_config('assignsubmission_sketchfab', 'defaulttexsize');
        $adminallownpot = get_config('assignsubmission_sketchfab', 'defaultallownpot');

        $polycount = $assignmentpolycount == 0 ? $adminpolycount : $assignmentpolycount;
        $matcount = $assignmentmatcount == 0 ? $adminmatcount : $assignmentmatcount;
        $texsize = $assignmenttexsize == 0 ? $admintexsize : $assignmenttexsize;
        $allownpot = $assignmentallownpot == 0 ? $adminallownpot : $assignmentallownpot;

        // Create a header to group our 3d-related things together.
        $mform->addElement(
            'header', 'sketchfab_header', get_string('criteria', 'assignsubmission_sketchfab')
        );

        // Polycount limit.
        $polylimitgrp = array();
        $polylimitgrp[] = $mform->createElement('text', 'assignsubmission_sketchfab_polylimit', '', array('size' => '6'));
        $polylimitgrp[] = $mform->createElement('advcheckbox', 'assignsubmission_sketchfab_polylimit_enabled',
                '', get_string('enable'));
        $mform->addGroup(
            $polylimitgrp,
            'assignsubmission_sketchfab_polylimit_group',
            get_string('polylimit', 'assignsubmission_sketchfab'),
            ' ',
            false
        );
        $mform->addHelpButton('assignsubmission_sketchfab_polylimit_group',
                              'polylimit',
                              'assignsubmission_sketchfab');
        $mform->disabledIf('assignsubmission_sketchfab_polylimit',
                           'assignsubmission_sketchfab_polylimit_enabled',
                           'notchecked');

        // Material count limit.
        $matlimitgrp = array();
        $matlimitgrp[] = $mform->createElement('text', 'assignsubmission_sketchfab_matlimit', '', array('size' => '6'));
        $matlimitgrp[] = $mform->createElement('advcheckbox', 'assignsubmission_sketchfab_matlimit_enabled',
                '', get_string('enable'));
        $mform->addGroup(
            $matlimitgrp,
            'assignsubmission_sketchfab_matlimit_group',
            get_string('matlimit', 'assignsubmission_sketchfab'),
            ' ',
            false
        );
        $mform->addHelpButton('assignsubmission_sketchfab_matlimit_group',
                              'matlimit',
                              'assignsubmission_sketchfab');
        $mform->disabledIf('assignsubmission_sketchfab_matlimit',
                           'assignsubmission_sketchfab_matlimit_enabled',
                           'notchecked');

        // Texture size.
        $texsizegrp = array();
        $texsizegrp[] = $mform->createElement('text', 'assignsubmission_sketchfab_texsize', '', array('size' => '6'));
        $texsizegrp[] = $mform->createElement('advcheckbox', 'assignsubmission_sketchfab_texsize_enabled',
                '', get_string('enable'));
        $mform->addGroup(
            $texsizegrp,
            'assignsubmission_sketchfab_texsize_group',
            get_string('texsize', 'assignsubmission_sketchfab'),
            ' ',
            false
        );
        $mform->addHelpButton('assignsubmission_sketchfab_texsize_group',
                              'texsize',
                              'assignsubmission_sketchfab');
        $mform->disabledIf('assignsubmission_sketchfab_texsize',
                           'assignsubmission_sketchfab_texsize_enabled',
                           'notchecked');

        // Allow NPOT?
        $mform->addElement('advcheckbox', 'assignsubmission_sketchfab_allownpot', get_string('allownpot', 'assignsubmission_sketchfab'));
        $mform->addHelpButton('assignsubmission_sketchfab_allownpot',
                              'allownpot',
                              'assignsubmission_sketchfab');

        // Add numeric rule to text fields.
        $polylimitgrprules = array();
        $polylimitgrprules['assignsubmission_sketchfab_polylimit'][] = array(null, 'numeric', null, 'client');
        $mform->addGroupRule('assignsubmission_sketchfab_polylimit_group', $polylimitgrprules);
        $matlimitgrprules = array();
        $matlimitgrprules['assignsubmission_sketchfab_matlimit'][] = array(null, 'numeric', null, 'client');
        $mform->addGroupRule('assignsubmission_sketchfab_matlimit_group', $polylimitgrprules);
        $texsizegrprules = array();
        $texsizegrprules['assignsubmission_sketchfab_texsize'][] = array(null, 'numeric', null, 'client');
        $mform->addGroupRule('assignsubmission_sketchfab_texsize_group', $texsizegrprules);

        // Rest of group setup.
        $mform->setDefault('assignsubmission_sketchfab_polylimit', $polycount);
        $mform->setDefault('assignsubmission_sketchfab_polylimit_enabled', $this->get_config('polylimitenabled'));
        $mform->setType('assignsubmission_sketchfab_polylimit', PARAM_INT);
        $mform->disabledIf('assignsubmission_sketchfab_polylimit_group',
                           'assignsubmission_sketchfab_enabled',
                           'notchecked');
        $mform->setDefault('assignsubmission_sketchfab_matlimit', $matcount);
        $mform->setDefault('assignsubmission_sketchfab_matlimit_enabled', $this->get_config('matlimitenabled'));
        $mform->setType('assignsubmission_sketchfab_matlimit', PARAM_INT);
        $mform->disabledIf('assignsubmission_sketchfab_matlimit_group',
                           'assignsubmission_sketchfab_enabled',
                           'notchecked');
        $mform->setDefault('assignsubmission_sketchfab_texsize', $texsize);
        $mform->setDefault('assignsubmission_sketchfab_texsize_enabled', $this->get_config('texsizeenabled'));
        $mform->setType('assignsubmission_sketchfab_texsize', PARAM_INT);
        $mform->disabledIf('assignsubmission_sketchfab_texsize_group',
                           'assignsubmission_sketchfab_enabled',
                           'notchecked');

        $mform->setDefault('assignsubmission_sketchfab_allownpot', $this->get_config('allownpot'));
    }

    /**
     * Save settings.
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data) {
        global $CFG;
        if (empty($data->assignsubmission_sketchfab_polylimit) || empty($data->assignsubmission_sketchfab_polylimit_enabled)) {
            $polylimit = 0;
            $polylimitenabled = 0;
        } else {
            $polylimit = $data->assignsubmission_sketchfab_polylimit;
            $polylimitenabled = 1;
        }
        if (empty($data->assignsubmission_sketchfab_matlimit) || empty($data->assignsubmission_sketchfab_matlimit_enabled)) {
            $matlimit = 0;
            $matlimitenabled = 0;
        } else {
            $matlimit = $data->assignsubmission_sketchfab_matlimit;
            $matlimitenabled = 1;
        }
        if (empty($data->assignsubmission_sketchfab_texsize) || empty($data->assignsubmission_sketchfab_texsize_enabled)) {
            $texsize = 0;
            $texsizeenabled = 0;
        } else {
            $texsize = $data->assignsubmission_sketchfab_texsize;
            $texsizeenabled = 1;
        }
        $this->set_config('polylimit', $polylimit);
        $this->set_config('polylimitenabled', $polylimitenabled);
        $this->set_config('matlimit', $matlimit);
        $this->set_config('matlimitenabled', $matlimitenabled);
        $this->set_config('texsize', $texsize);
        $this->set_config('texsizeenabled', $texsizeenabled);
        $this->set_config('allownpot', $data->assignsubmission_sketchfab_allownpot);
        return true;
    }

    /**
     * Talk to Sketchfab, get a bunch of objects back ready for embedding.
     *
     * @param stdClass $submission
     * return array An array of stdClasses, ready for embedding.
     */
    private function get_models(stdClass $submission) {
        global $DB;

        // Get the items.
        $items = $DB->get_records(
            SKETCHFAB_DB_TABLE,
            array(
                'assignment' => $this->assignment->get_instance()->id,
                'submission' => $submission->id
            )
        );

        // Create an empty array to populate with models.
        $models = array();

        // Create an instance of the Moodle cURL class to use.
        $curl = new curl();

        // Iterate over each item then make the three cURL requests
        // that we'll need for full metadata.
        foreach ($items as $item) {

            // Create an empty stdClass.
            $thismodel = new stdClass();

            // 1. OEMBED
            $oembed = $curl->get(
                SKETCHFAB_OEMBED_ENDPOINT,
                array(
                    'url' => SKETCHFAB_MODELPAGE_URL . '/' . $item->model
                ),
                array(
                    'CURLOPT_RETURNTRANSFER' => 1
                )
            );
            if (!empty($oembed)) {
                $thismodel->oembed = html_writer::tag(
                    'div',
                    json_decode($oembed, true)["html"],
                    array(
                        'class' => 'sketchfab-oembed'
                    )
                );
            }

            // 2. METADATA
            $metaurl = get_config('assignsubmission_sketchfab', 'endpointurl') . '/' . $item->model;
            $meta = $curl->get($metaurl);
            if (!empty($meta)) {
                // Parse JSON for the metadata we care about.
                $json = json_decode($meta, true);
                $thismodel->facecount = $json["faceCount"];
                $thismodel->vertexcount = $json["vertexCount"];
                $thismodel->matcount = count($json["options"]["materials"]);
            }

            // 3. TEXTURE METADATA
            $metaurl .= '/textures';
            $meta = $curl->get($metaurl);
            $texturedata = array();
            if (!empty($meta)) {
                // Parse JSON for texture metadata.
                $json = json_decode($meta, true);
                foreach ($json['results'] as $result) {
                    $thistex = array();

                    // Image name.
                    $thistex['name'] = $result['name'];

                    // Dimensions.
                    $h = $result['images'][0]['height'];
                    $w = $result['images'][0]['width'];
                    $thistex['h'] = $h;
                    $thistex['w'] = $w;

                    // Largest image dimension.
                    $largestdimension = intval($h);
                    if ($largestdimension < intval($w)) {
                        $largestdimension = intval($w);
                    }
                    $thistex['largestdimension'] = $largestdimension;

                    // Image aspect ratio.
                    $aspectratio = $h / $w;
                    $thistex['aspectratio'] = $aspectratio;

                    // Image POT.
                    $isPOT = ($largestdimension & ($largestdimension - 1)) == 0;
                    $thistex['poweroftwo'] = $isPOT;

                    // Image URL.
                    $thistex['url'] = $result['images'][0]['url'];

                    $texturedata[] = $thistex;
                }
            }
            $thismodel->textures = $texturedata;
            $thismodel->texcount = count($texturedata);

            // Push the model onto the end of the array.
            $models[] = $thismodel;

        }


        return $models;
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
        $items = $this->get_models($submission);

        // Build the html.
        $responses = array();

        foreach ($items as $item) {

            // Output metadata tags.
            $metahtml = html_writer::start_tag('table', array('class' => 'sketchfab-stats table table-bordered table-striped'));

            // Polygons.
            $metahtml .= html_writer::start_tag('tr');
            $metahtml .= html_writer::tag(
                'th',
                get_string('metapolygons', 'assignsubmission_sketchfab')
            );
            $metahtml .= html_writer::tag(
                'td',
                html_writer::tag(
                    'span',
                    $item->facecount,
                    array('class' => 'value')
                ) .
                $this->build_meta_label(
                    $item->facecount,
                    intval($this->get_config('polylimitenabled')) === 1,
                    $this->get_config('polylimit')
                )
            );
            $metahtml .= html_writer::end_tag('tr');

            // Vertexes.
            $metahtml .= html_writer::start_tag('tr');
            $metahtml .= html_writer::tag(
                'th',
                get_string('metavertexes', 'assignsubmission_sketchfab')
            );
            $metahtml .= html_writer::tag(
                'td',
                html_writer::tag(
                    'span',
                    $item->vertexcount,
                    array('class' => 'value')
                )
            );
            $metahtml .= html_writer::end_tag('tr');

            // Materials.
            $metahtml .= html_writer::start_tag('tr');
            $metahtml .= html_writer::tag(
                'th',
                get_string('metamaterials', 'assignsubmission_sketchfab'),
                array(
                    'rowspan' => $item->matcount + 1
                )
            );

            $matinner = $item->matcount;
            $usematcount = intval($item->matcount) > 0;
            if ($usematcount) {
                $matinner .= $this->build_meta_label(
                    $item->matcount,
                    intval($this->get_config('matlimitenabled')) === 1,
                    $this->get_config('matlimit')
                );
            }

            $metahtml .= html_writer::tag(
                'td',
                html_writer::tag(
                    'span',
                    $item->matcount,
                    array('class' => 'value')
                )
            );
            $metahtml .= html_writer::end_tag('tr');

            // Textures.
            $metahtml .= html_writer::start_tag('tr');
            $metahtml .= html_writer::tag(
                'th',
                get_string('metatextures', 'assignsubmission_sketchfab'),
                array(
                    'rowspan' => $item->texcount + 1
                )
            );

            $texinner = $item->texcount;
            if (!$usematcount) {
                $texinner .= $this->build_meta_label(
                    $item->texcount,
                    intval($this->get_config('matlimitenabled')) === 1,
                    $this->get_config('matlimit')
                );
            }

            $metahtml .= html_writer::tag(
                'td',
                html_writer::tag(
                    'span',
                    $texinner,
                    array('class' => 'value')
                )
            );
            $metahtml .= html_writer::end_tag('tr');

            $allownpot = intval($this->get_config('allownpot')) === 1;
            $texsizeenabled = intval($this->get_config('texsizeenabled')) === 1;
            $texsizelimit = intval($this->get_config('texsize'));
            foreach ($item->textures as $key => $tex) {
                $metahtml .= html_writer::start_tag('tr');

                $inner = html_writer::tag(
                    'a',
                    $tex['name'],
                    array(
                        'href' => $tex['url']
                    )
                );
                $inner .= " ($tex[w]x$tex[h])";

                $inner .= html_writer::start_tag('div', array('class' => 'flags'));

                // If texture size is restricted, show a flag as to whether its
                // size is over the limit.
                if (!$texsizeenabled) {
                    $inner .= $this->build_meta_label_boolean(
                        get_string("texsizegood", "assignsubmission_sketchfab"),
                        get_string("texsizebad", "assignsubmission_sketchfab"),
                        $tex['largestdimension'] <= $texsizelimit
                    );
                }

                // If NPOT textures are not allowed, show a flag as to whether
                // or not this texture is POT.
                if (!$allownpot) {
                    $inner .= $this->build_meta_label_boolean("POT", "NPOT", $tex['poweroftwo']);
                }

                $inner .= html_writer::end_tag('div');

                $metahtml .= html_writer::tag(
                    'td',
                    html_writer::tag(
                        'small',
                        $inner,
                        array('class' => 'value')
                    )
                );
                $metahtml .= html_writer::end_tag('tr');
            }


            $metahtml .= html_writer::end_tag('table');

            $thishtml = html_writer::tag(
                'div',
                $item->oembed . $metahtml,
                array('class' => 'sketchfab-oembed-outer')
            );

            $responses[] = $thishtml;
        }


        return implode($responses);
    }


    /**
     * Build a model metadata label.
     *
     * @param string $quantity The quantity to display.
     * @param bool $limited Whether a limit is enforced on the quantity.
     * @param string $limit The enforced limit, as a floating-point value passed as a string.
     * @return string An HTML fragment.
     */
    private function build_meta_label($quantity, $limited = false, $limit = 0) {

        $label = "";

        $metatagclasses = 'label';
        if ($limited) {

            $quotient = 100.0 * (floatval($quantity) / floatval($limit) - 1.0);
            $metatagclasses .= ($quotient > 0.0 ? ' label-important' : ' label-success');

            $quotientstring = format_float($quotient, 2);
            if ($quotientstring[0] != '-') {
                $quotientstring = '+' . $quotientstring;
            }
            $quotientstring .= '%';

            $a = new stdClass();
            $a->value = $quotientstring;
            $a->target = $limit;

            $label .= get_string('quotientdisplay', 'assignsubmission_sketchfab', $a);
        }

        $rval = html_writer::tag(
                'span',
                $label,
                array(
                    'class' => $metatagclasses
                )
            );

        return $rval;
    }

    private function build_meta_label_boolean($label_good, $label_bad, $okay = false) {


        $label = $okay? $label_good : $label_bad;
        $okayclass = $okay? 'icon-ok' : 'icon-remove';
        $labelclasses = 'label' . ($okay ? ' label-success' : ' label-important');

        $rval = html_writer::tag(
            'span',
            '<i class="' . $okayclass . '"></i> '  . $label,
            array(
                'class' => $labelclasses
            )
        );

        return $rval;
    }

    /**
     * Process uploaded files and upload them to Sketchfab.
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
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