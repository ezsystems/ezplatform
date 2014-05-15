@contentTypeGroup @adminFeature
Feature: Read all Content Type Groups
    In order to prevent an unauthorized user to read Content Type Group
    As an anonymous
    I can't read any Content Type Groups

    Scenario: Can't read any Content Type Groups
        Given I am not logged in
        When I read Content Type Groups list
        Then I see an unauthorized error
