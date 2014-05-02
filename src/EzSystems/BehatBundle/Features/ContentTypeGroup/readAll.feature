@contentTypeGroup @adminFeature
Feature: Read all Content Type Groups
  Read all Content Type Groups
  As an administrator or anonymous user
  I want to know all existing Content Type Groups

  Scenario: Read all Content Type Groups
    Given I am logged as an "administrator"
    And I have the following Content Type Groups:
      | groups     |
      | some       |
      | dif3rent   |
      | id_nt.fier |
    When I read Content Type Groups list
    Then I see the following Content Type Groups:
      | groups     |
      | some       |
      | dif3rent   |
      | id_nt.fier |

  Scenario: Attempt to read all Content Type Groups with a non authorized user
    Given I am not logged in
    When I read Content Type Groups list
    Then I see an unauthorized error
