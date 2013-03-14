Feature: Sections can be managed through the admin interface.

    @javascript
    Scenario: Default sections are listed.
        Given I am logged in as "admin" with password "publish"
         When I go to the "Admin Section List Page"
         Then I see 5 "Section" elements listed


