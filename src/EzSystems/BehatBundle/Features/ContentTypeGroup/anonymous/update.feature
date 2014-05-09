@contentTypeGroup @adminFeature
Feature: Update a Content Type Group
    In order to prevent non authorized users to update a Content Type Group
    As an anonymous
    I can't be able to change Content Type Group

    Scenario: Can't update a Content Type Group
        Given I am not logged in
        And I have a Content Type Group with identifier "some_string"
        And I don't have a Content Type Group with identifier "another_string"
        When I update Content Type Group with identifier "some_string" to "another_string"
        Then I see an unauthorized error
        And I see a Content Type Group with identifier "some_string"
        And I don't see a Content Type Group with identifier "another_string"
