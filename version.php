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
 * IDM notify
 *
 * @package   LOCAL_IDMNOTIFY
 * @copyright 2012 Carleton College
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2019040103;
$plugin->requires = 2018120303; // See http://docs.moodle.org/dev/Moodle_Versions
$plugin->cron = 1;
$plugin->component = 'local_idmnotify';
$plugin->maturity = MATURITY_STABLE;
$plugin->release = 'v2.0';

// More information
// https://wiki.carleton.edu/display/itskb/COURSES+-+Granting+Rights%2C+explained+-+Restricted