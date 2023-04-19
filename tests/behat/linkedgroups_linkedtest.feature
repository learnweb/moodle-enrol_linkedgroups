@enrol @enrol_linkedgroups
Feature: Users can auto-enrol themself in courses where linkedgroups enrolment is allowed
  In order to participate in courses
  As a user
  I need to auto enrol me in courses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
      | Course 2 | C2 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Manage enrol plugins" in site administration
    And I click on "Enable" "link" in the "Linked courses and groups enrolment" "table_row"
    And I log out

  @javascript
  Scenario: Self-enrolment with linked course from main course.
    Given I log in as "teacher1"
    And I add "Linked courses and groups enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
      | Linked courses       | Course 2               |
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I am on "Course 2" course homepage
    And I should see "Topic 1"

  @javascript
  Scenario: Self-enrolment from linked course. Afterwards unenrol from main course.
    Given I log in as "teacher1"
    And I add "Linked courses and groups enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
      | Linked courses       | Course 2               |
    And I log in as "student1"
    When I am on "Course 2" course homepage
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I am on "Course 1" course homepage
    And I should see "Topic 1"
    When I navigate to "Unenrol me from C1" in current page administration
    And I click on "Continue" "button" in the "Confirm" "dialogue"
    Then I should see "You are unenrolled from the course \"Course 1\""
    And I am on "Course 1" course homepage
    And I should not see "Topic 1"
    And I am on "Course 2" course homepage
    And I should not see "Topic 1"

  @javascript
  Scenario: Self-enrolment with linked course and group enrolment key. Afterwards unenrol from linked course.
    Given I log in as "teacher1"
    And I add "Linked courses and groups enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
      | Linked courses       | Course 2               |
      | Enrolment key        | moodle_rulez           |
    And I am on the "Course 1" "groups" page
    And I press "Create group"
    And I set the following fields to these values:
      | Group name    | Group 1     |
      | Enrolment key | Group1_rulez |
    And I press "Save changes"
    When I log in as "student1"
    And I am on "Course 2" course homepage
    And I set the following fields to these values:
      | Enrolment key | Group1_rulez |
    And I press "Enrol me"

    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I edit the section "2"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "(Any group)"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save changes" "button"
    And I am on "Course 2" course homepage with editing mode on
    And I edit the section "2"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "(Any group)"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save changes" "button"

    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Topic 1"
    And I should see "Topic 2"
    And I am on "Course 2" course homepage
    And I should see "Topic 1"
    And I should see "Topic 2"
    When I navigate to "Unenrol me from C2" in current page administration
    And I click on "Continue" "button" in the "Confirm" "dialogue"
    Then I should see "You are unenrolled from the course \"Course 2\""
    And I am on "Course 1" course homepage
    And I should see "Topic 1"
    And I am on "Course 2" course homepage
    And I should not see "Topic 1"

