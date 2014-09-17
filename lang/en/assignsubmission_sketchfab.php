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
$string['criteria'] = '3D criteria';
$string['allownpot'] = 'Allow NPOT?';
$string['allownpot_help'] = 'Whether to allow NPOT (non-power-of-two) textures.';
$string['apitoken'] = 'Sketchfab API token';
$string['apitoken_help'] = 'Your personal Sketchfab API token.';
$string['apitoken_required'] = 'Your API token is required in order to upload to Sketchfab.';
$string['countitem_single'] = '{$a} item';
$string['countitem_plural'] = '{$a} items';
$string['default'] = 'Enabled by default';
$string['default_help'] = 'If set, this submission method will be enabled by default for all new assignments.';
$string['defaultpolycount'] = 'Default polygon count';
$string['defaultpolycount_help'] = 'The polygon count new assignments will target by default.';
$string['defaultmatcount'] = 'Default material count';
$string['defaultmatcount_help'] = 'The number of materials new assignments will target by default.';
$string['defaulttexsize'] = 'Default texture size';
$string['defaulttexsize_help'] = 'The default largest dimension of a texture; setting this to 1024, for example, will allow 1024x512 and 1024x1024 textures.';
$string['defaultallownpot'] = 'Allow NPOT textures';
$string['defaultallownpot_help'] = 'Whether to allow NPOT (non-power-of-two) textures by default.';
$string['enabled'] = 'Sketchfab assignments enabled';
$string['enabled_help'] = 'If enabled, students are able to submit 3D files using Sketchfab.';
$string['endpointurl'] = 'API endpoint';
$string['endpointurl_help'] = 'The default API endpoint.';
$string['pluginname'] = 'Sketchfab submissions';
$string['matlimit'] = 'Material count';
$string['matlimit_help'] = 'The target material count for this assignment.';
$string['metamaterials'] = 'Materials';
$string['metapolygons'] = 'Polygons';
$string['metatextures'] = 'Textures';
$string['metavertexes'] = 'Vertexes';
$string['nomaterialswarning'] = 'No materials detected; applying material count target to textures.';
$string['npotallowed'] = 'NPOT textures allowed';
$string['npotallowed_help'] = 'Whether non-power-of-two textures (e.g. 1024x512) are allowed.';
$string['polylimit'] = 'Polygon count';
$string['polylimit_help'] = 'The target polygon count for this assignment.';
$string['quotientdisplay'] = '{$a->value} (target: {$a->target})';
$string['texsize'] = 'Texture size';
$string['texsize_help'] = 'The maximum texture size for this assignment.';
$string['texsizegood'] = "Size OK";
$string['texsizebad'] = "Oversize";