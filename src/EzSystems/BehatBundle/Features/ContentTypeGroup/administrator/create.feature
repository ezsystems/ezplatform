@contentTypeGroup @adminFeature
Feature: Creating a Content Type Group
    In order to create Content Type Group
    As an administrator
    I need to create a Content Type Group

    Scenario: Create a valid Content Type Group
        Given I am logged as an "administrator"
        And I don't have a Content Type Group with identifier "some_string"
        When I create a Content Type Group with identifier "some_string"
        Then I see a Content Type Group with identifier "some_string"

    Scenario: Can't create a Content Type Group with same identifier of an existing group
        Given I am logged as an "administrator"
        And I have a Content Type Group with identifier "some_string"
        When I create a Content Type Group with identifier "some_string"
        Then I see an invalid field error
        And I see 1 Content Type Group with identifier "some_string"
