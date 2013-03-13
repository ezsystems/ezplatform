Feature: Start page

    @javascript
    Scenario: Start page displays welcome text
        Given I am on the homepage
        Then I should see "Welcome to eZ Publish"

    @javascript
    Scenario: Search works from the start page
        Given I am on the homepage
         When I search for "welcome"
         Then I am on the "Search Page"
          And I see search 1 result

