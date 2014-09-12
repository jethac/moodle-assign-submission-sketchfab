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

// This should be off by default, I'm pretty sure.
$settings->add(new admin_setting_configcheckbox('assignsubmission_sketchfab/default',
                   new lang_string('default', 'assignsubmission_sketchfab'),
                   new lang_string('default_help', 'assignsubmission_sketchfab'), 0));

$settings->add(
	new admin_setting_configtext(
		'assignsubmission_sketchfab/endpointurl',
		new lang_string('endpointurl', 'assignsubmission_sketchfab'),
		new lang_string('endpointurl_help', 'assignsubmission_sketchfab'),
		'https://api.sketchfab.com/v2/models'
	)
);

$settings->add(
	new admin_setting_configtext(
		'assignsubmission_sketchfab/defaultpolycount',
		new lang_string('defaultpolycount', 'assignsubmission_sketchfab'),
		new lang_string('defaultpolycount_help', 'assignsubmission_sketchfab'),
		'500'
	)
);
$settings->add(
	new admin_setting_configtext(
		'assignsubmission_sketchfab/defaultmatcount',
		new lang_string('defaultmatcount', 'assignsubmission_sketchfab'),
		new lang_string('defaultmatcount_help', 'assignsubmission_sketchfab'),
		'1'
	)
);