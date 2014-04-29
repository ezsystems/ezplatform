@contenttypegroup @adminFeature
Feature: Read all Content Type Groups
  Read all Content Type Groups
  As an administrator or anonymous user
  I want to know all existing Content Type Groups

  Scenario: Read all ContentTypeGroups
    Given I am logged as an "administrator"
    And I have the following ContentTypeGroups:
      | groups     |
      | some       |
      | dif3rent   |
      | id_nt.fier |
    When I read ContentTypeGroups list
    Then I see the following ContentTypeGroups:
      | groups     |
      | some       |
      | dif3rent   |
      | id_nt.fier |

  Scenario: Attempt to read all ContentTypeGroups with a non authorized user
    Given I am not logged in
    When I read ContentTypeGroups list
    Then I see an unauthorized error
