# Running eZ Platform's test

## Install selenium server
Download the last version of the selenium server on the [download page](http://www.seleniumhq.org/download/).

*Note: The selenium server must match the version of the browser you are using. Assuming you are using
a recent version of your browser, you should keep the selenium version up to date.*

Once downloaded, run the server: `java -jar selenium-server-standalone-2.48.2.jar`

## Create a dedicated vhost
Create a dedicated virtual host on your web server and set the environment to `behat`.

Once this is done, make sur this virtual hosts works in your browser.

## Create configuration file
At the root of ezplatform, copy the configuration file.
```
cp behat.yml.dist behat.yml
```

Edit the file and update the `base_url` and selenium's `wd_host` (if needed)

## Run the test
### Running all of the PlatformUI tests
`./bin/behat --profile=platformui`

### Running a dedicated test
`./bin/behat --profile=platformui ~/ezplatform/vendor/ezsystems/platform-ui-bundle/Features/Users/users.feature`
