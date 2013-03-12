Feature: Start page

    @javascript
    Scenario: Start page displays welcome text
        Given I am on the homepage
        Then I should see "Welcome to eZ Publish"
