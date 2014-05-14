@contentTypeGroup @adminFeature
Feature: Delete a Content Type Group
    In order to prevent an unauthorized user to delete a Content Type Group
    As an anonymous
    I can't be able to delete a Content Type Group

    Scenario: Can't delete the Content Type Group
        Given I am not logged in
        And I have a Content Type Group with identifier "some_string"
        When I delete Content Type Group with identifier "some_string"
        Then I see an unauthorized error
