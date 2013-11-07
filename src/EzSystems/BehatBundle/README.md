# eZ Publish BDD Testing

This as a fast summary of the possible sentences,
and the guidelines for in case you want to add new sentences.

For the following documentation you should remember:

* Words inside angular brackets (**[]**) are sentence definitions
* Words inside less and greater characters (**<>**) are user input



## Single sentences


### Given

```Cucumber
    I am on "<name>" page
    I am on <name> page for "<special-location>"
    I am <action> <what>
    I am logged in as "<username>" with "<password>"
    I am logged in as a <type> user
    I have "<what>" [type]
    test is pending <some-reason>
```


### When

Notice that several When sentences can and should also be used in Given sentences.

```Cucumber
    I [action] "<what>" [type]
    I [action] "<what>" [type] to "<where>"
    I on [place] [action] "<what>" [type]
    I follow "<link>"
    I go to "<page>"
    I go to "<page>" at "<special-case>"
    I press "<button>"
    I search for "<what>"
    I attach "<what>"
    I attach "<what>" in "<where>"
    I fill in "<key>" with "<value>"
    I fill a valid <which> form
```


### Then

```Cucumber
    I see "<what>" [type]
    I see "<what>" [type] with "<value>"
    I see "<what>" [type] emphasized
    I see search <total> results
    I see a "<block>" on the page
    on [place] I see "<what>" [type]
    I see key "<key>" with value "<value>"
    I check <name> page for "<special>" Location
    I see [type] for Content object
```

```Cucumber
    I don't see "<what>" [type]
    I con't see "<what>" [type] with "<value>"
    I don't see key "<key>" with value "<value>"
    on [place] I don't see "<what>" [type]
```



## Tabled sentences

About the tables, you shouldn't forget that the first row is always informative,
it means that it will be discarded by implementation, is only for user
readability.


### Given

```Cucumber
    I have "<what>" with:
```

### When

```Cucumber
    I fill the form with:
        | Key | Value |
    I fill <which> form with:
```


### Then

```Cucumber
    I see [type]:
    I see [type] with:
    I see [type] in following order:
    on [place] I see "<what>" [type] in following order:
    I see form filled with:
    I see "<what>" [type] with attributes:
    I see [type] for [eZ Content]:
    I see [type] for [eZ Content] in following order:
```

```Cucumber
    I don't see [type]:
    I don't see [type] with:
    I don't see form filled with:
```


## Possible system definitions

Following are the possible values for the words inside angular brackets ([]).


### Action
* click
* go to
* attach

There are some actions that have a specific tab, for example **go to** action
must/can have **page** type:

    I go to "<name>" page


### Place
* main (this is the main content)
* menu
    * main menu
    * sub menu
    * side menu
* footer
* header
* breadcrumb


### Type
* page
* table
* link / links
* title / topic
* message / text
* error / warning
* button
* place / block / element
* node

There are some types that are only for a specific step, like **extension** type
that should be used only for preparing the system, ie. used at Given steps.

For more information on **pages**, **places** and **blocks** see each bundle,
since these are defined in tested bundle itself


### eZ Content

These are content specific for the eZ Publish:
* Content object
* Content Type
* Location
* Role
* Policy
* ...
( these should be presented with Camel Case, with the exception of "object" from
Content object )



## Addicional information

In general the sentences can have the appropriated sentence construction,
since it has many optional words like:
* ```(?:the |an |a |)```
* ```(?:on|at)```
* ```(?:don\'t|do not)```
* ```(?:\:|)```
* ```(?:s|)```
* ```['"](.+)["']```
* ```(?:word1|word2)``` (in some cases you can choose from several words,
ex: ```/^I see (?:the |an |a |)["'](.+)["'] (?:title|topic)$/``` here we have
2 possible options)

In Then sentences for almost each positive sentence, there is/should be/exist a
negative sentence also.


## Usefull links

Complete list for avaliable sentences - [BehatBundle/Sentences.md](https://github.com/ezsystems/ezpublish-community/blob/master/src/EzSystems/BehatBundle/Sentences.md)

Content manager - [BehatBundle/ContentManager.md](https://github.com/ezsystems/ezpublish-community/blob/master/src/EzSystems/BehatBundle/ContentManager.md)

System Manager - [BehatBundle/SystemManager.md](https://github.com/ezsystems/ezpublish-community/blob/master/src/EzSystems/BehatBundle/SystemManager.md)
