Feature: Verify that Admin Panel is available only for authenticated users

  @javascript @common
  Scenario: Should be redirected to Dashboard after successful login
    Given I open Login page
    When I log in as admin with password publish
    Then I should be on Dashboard page

  @javascript @common
  Scenario: Should be redirected to Login page from Dashboard when not logged in
    When I try to open Dashboard page
    Then I should be on Login page

  @javascript @common
  Scenario: Should be redirected to Login page after unsuccessful login
    Given I open Login page
    When I log in as admin with password notpublish
    Then I should be on Login page
