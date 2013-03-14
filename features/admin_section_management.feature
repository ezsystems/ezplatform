Feature: Sections can be managed through the admin interface.

    @javascript
    Scenario: Default sections are listed.
        Given I am logged in as "admin" with password "publish"
         When I go to the "Admin Section List Page"
         Then I see 5 "Section" elements listed

    @javascript
        Scenario: Default sections are listed.
            Given I am logged in as "admin" with password "publish"
              And I go to the "Admin Section List Page"
             When I follow "Create a new section"
             Then I am on the "Admin Section Create Page"

    @javascript
    Scenario: When I create a section, I see it listed.
        Given I am logged in as "admin" with password "publish"
          And I go to the "Admin Section Create Page"
         When I fill in "Name" with "FooSection"
          And I fill in "Identifier" with "foo"
          And I press "Create the section"
         Then I am on the "Admin Section List Page"
          And I see 6 "Section" elements listed
          And I should see "FooSection"


