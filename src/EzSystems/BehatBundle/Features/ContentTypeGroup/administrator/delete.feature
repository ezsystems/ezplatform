@contentTypeGroup @adminFeature
Feature: Delete a Content Type Group
    In order to delete a Content Type Group
    As an administrator
    I want to be able to delete a Content Type Group

    Scenario: Delete the Content Type Group
        Given I am logged as an "administrator"
        And I have a Content Type Group with identifier "some_string"
        When I delete Content Type Group with identifier "some_string"
        Then I don't see a Content Type Group with identifier "some_string"
