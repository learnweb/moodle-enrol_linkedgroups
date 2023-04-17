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
 * Strings for plugin 'enrol_linkedgroups'.
 *
 * @package    enrol_linkedgroups
 * @copyright  2023 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Linked courses and groups enrolment';
$string['pluginname_desc'] = 'Self enrolment that allows to be automatically enrolled into linked courses and groups.';

$string['linkedcourses'] = 'Linked courses';

$string['linkedgroups:config'] = 'Configure self enrol instances';
$string['linkedgroups:enrolself'] = 'Self enrol in course';
$string['linkedgroups:holdkey'] = 'Appear as the self enrolment key holder';
$string['linkedgroups:manage'] = 'Manage enrolled users';
$string['linkedgroups:unenrol'] = 'Unenrol users from course';
$string['linkedgroups:unenrolself'] = 'Unenrol self from the course';

$string['expirymessageenrollersubject'] = 'Self enrolment expiry notification';
$string['expirymessageenrollerbody'] = 'Self enrolment in the course \'{$a->course}\' will expire within the next {$a->threshold} for the following users:

{$a->users}

To extend their enrolment, go to {$a->extendurl}';
$string['expirymessageenrolledsubject'] = 'Self enrolment expiry notification';
$string['expirymessageenrolledbody'] = 'Dear {$a->user},

This is a notification that your enrolment in the course \'{$a->course}\' is due to expire on {$a->timeend}.

If you need help, please contact {$a->enroller}.';

$string['privacy:metadata'] = 'The Linked groups enrolment plugin does not store any personal data.';