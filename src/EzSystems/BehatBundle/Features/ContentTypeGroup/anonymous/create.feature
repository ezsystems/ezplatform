@contentTypeGroup @adminFeature
Feature: Creating a Content Type Group
    In order to prevent an unauthorized user to create a Content Type Group
    As an anonymous
    I can't create a Content Type Group

    Scenario: Can't create a Content Type Group
        Given I am not logged in
        And I don't have a Content Type Group with identifier "some_string"
        When I create a Content Type Group with identifier "some_string"
        Then I see not authorized error
        And I don't see a Content Type Group with identifier "some_string"
