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
 * Linkedgroups enrol plugin implementation.
 *
 * @package    enrol_linkedgroups
 * @copyright  2023 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_linkedgroups;

use context_course;
use context_system;
use context_user;
use core_text;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/enrol/locallib.php');

class enrol_form extends \moodleform {
    protected $instance;
    protected $toomany = false;

    /**
     * Overriding this function to get unique form id for multiple self enrolments.
     *
     * @return string form identifier
     */
    protected function get_form_identifier() {
        $formid = $this->_customdata->id.'_'.get_class($this);
        return $formid;
    }

    public function definition() {
        global $USER, $OUTPUT, $CFG;
        $mform = $this->_form;
        $instance = $this->_customdata;
        $this->instance = $instance;
        $plugin = enrol_get_plugin('linkedgroups');

        $heading = $plugin->get_instance_name($instance);
        $mform->addElement('header', 'selfheader', $heading);

        if ($instance->password) {
            // Change the id of self enrolment key input as there can be multiple self enrolment methods.
            $mform->addElement('password', 'enrolpassword', get_string('password', 'enrol_self'),
                    array('id' => 'enrolpassword_'.$instance->id));
            $context = context_course::instance($this->instance->courseid);
            $userfieldsapi = \core_user\fields::for_userpic();
            $ufields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
            $keyholders = get_users_by_capability($context, 'enrol/self:holdkey', $ufields);
            $keyholdercount = 0;
            foreach ($keyholders as $keyholder) {
                $keyholdercount++;
                if ($keyholdercount === 1) {
                    $mform->addElement('static', 'keyholder', '', get_string('keyholder', 'enrol_self'));
                }
                $keyholdercontext = context_user::instance($keyholder->id);
                if ($USER->id == $keyholder->id || has_capability('moodle/user:viewdetails', context_system::instance()) ||
                        has_coursecontact_role($keyholder->id)) {
                    $profilelink = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $keyholder->id . '&amp;course=' .
                            $this->instance->courseid . '">' . fullname($keyholder) . '</a>';
                } else {
                    $profilelink = fullname($keyholder);
                }
                $profilepic = $OUTPUT->user_picture($keyholder, array('size' => 35, 'courseid' => $this->instance->courseid));
                $mform->addElement('static', 'keyholder'.$keyholdercount, '', $profilepic . $profilelink);
            }

        } else {
            $mform->addElement('static', 'nokey', '', get_string('nopassword', 'enrol_self'));
        }

        $this->add_action_buttons(false, get_string('enrolme', 'enrol_self'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $instance->courseid);

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', $instance->id);
    }

    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        $instance = $this->instance;

        if ($this->toomany) {
            $errors['notice'] = get_string('error');
            return $errors;
        }

        if ($instance->password) {
            if ($data['enrolpassword'] !== $instance->password) {
                if ($instance->customint7) {
                    $originalinstance = $DB->get_record('enrol', ['id' => $instance->customint7]);
                } else {
                    $originalinstance = $instance;
                }
                if ($originalinstance) {
                    // Check group enrolment key.
                    if (!enrol_self_check_group_enrolment_key($originalinstance->courseid, $data['enrolpassword'])) {
                        // We can not hint because there are probably multiple passwords.
                        $errors['enrolpassword'] = get_string('passwordinvalid', 'enrol_self');
                    }

                } else {
                    $plugin = enrol_get_plugin('self');
                    if ($plugin->get_config('showhint')) {
                        $hint = core_text::substr($instance->password, 0, 1);
                        $errors['enrolpassword'] = get_string('passwordinvalidhint', 'enrol_self', $hint);
                    } else {
                        $errors['enrolpassword'] = get_string('passwordinvalid', 'enrol_self');
                    }
                }
            }
        }

        return $errors;
    }
}