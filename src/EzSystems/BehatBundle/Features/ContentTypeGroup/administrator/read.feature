@contentTypeGroup @adminFeature
Feature: Read a Content Type Groups
    In order to read a Content Type Groups
    As an administrator
    I need to be able to read a Content Type Group

    Scenario: Read a Content Type Group
        Given I am logged as an "administrator"
        And I have a Content Type Group with identifier "some_string"
        When I read Content Type Group with identifier "some_string"
        Then I see a Content Type Group with identifier "some_string"
