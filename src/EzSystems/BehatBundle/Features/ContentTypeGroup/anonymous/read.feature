@contentTypeGroup @adminFeature
Feature: Read a Content Type Groups
    In order to prevent an unauthorized user to read a Content Type Group
    As an anonymous
    I'm not allowed to read a Content Type Group

    Scenario: Can't read a Content Type Group
        Given I am not logged in
        And I have a Content Type Group with identifier "some_string"
        When I read Content Type Group with identifier "some_string"
        Then I see an unauthorized error
