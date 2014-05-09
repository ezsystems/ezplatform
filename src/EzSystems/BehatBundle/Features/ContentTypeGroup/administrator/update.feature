@contentTypeGroup @adminFeature
Feature: Update a Content Type Group
    In order to update a Content Type Group
    As an administrator
    I want to be able to change Content Type Group

    Scenario: Update the Content Type Group identifier
        Given I am logged as an "administrator"
        And I have a Content Type Group with identifier "some_string"
        And I don't have a Content Type Group with identifier "another_string"
        When I update Content Type Group with identifier "some_string" to "another_string"
        Then I see a Content Type Group with identifier "another_string"
        And I don't see a Content Type Group with identifier "some_string"

    Scenario: Can't update the Content Type Group identifier to an existing one
        Given I am logged as an "administrator"
        And I have the following Content Type Groups:
            | groups         |
            | some_string    |
            | another_string |
        When I update Content Type Group with identifier "some_string" to "another_string"
        Then I see an invalid field error
        And I see 1 Content Type Group with identifier "some_string"
        And I see 1 Content Type Group with identifier "another_string"
