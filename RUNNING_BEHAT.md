# Running the behat features

*Note: If you want there is a way to run Behat using Docker setup, where you won't have to install any software other
then Docker. See `doc/docker/README.md` for further info.*

## Install selenium server
Download the last version of the selenium server on the [download page](http://www.seleniumhq.org/download/).

*Note: The selenium server must match the version of the browser you are using. Assuming you are using
a recent version of your browser, you should keep the selenium version up to date.*

Once downloaded, run the server: `java -jar selenium-server-standalone-2.48.2.jar`

## Create a dedicated vhost
Create a dedicated virtual host on your web server and set the environment to `behat`.

Once this is done, make sur this virtual hosts works in your browser.

## Customize the configuration
Behat needs to run HTTP calls on the project. By default, it uses http://localhost.

You can either create a configuration file, or use environment variables:

```
cp behat.yml.dist behat.yml
```
Edit the file and update the `base_url` and selenium's `wd_host` (if needed)

Or customize the settings using the BEHAT_PARAMS environment variable:
```
export BEHAT_PARAMS='{"extensions" : {"Behat\\MinkExtension" : {"base_url" : "https://www.example.com/"}}}'
```

See http://docs.behat.org/en/v3.0/guides/6.profiles.html#environment-variable-behat-params.

## Run the features

```
./bin/behat
```

### Tags
Features tagged with `common` are executed on every pull request. Other, such as `edge`, are only executed on occasions.

Add ` --tags='common'` to the behat command line to restrict execution to features tagged as "common".

### Running all of the PlatformUI features
`./bin/behat --profile=platformui`

### Running a specific feature
`./bin/behat --profile=platformui ~/ezplatform/vendor/ezsystems/platform-ui-bundle/Features/Users/users.feature`
