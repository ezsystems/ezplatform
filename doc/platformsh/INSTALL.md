# Install eZ Platform on Platform.sh

## Installation using eZ Platform template
This is the simplest approach, but may be less up to date with current development than the other, more manual approach.

### Using eZ Platform Cloud
With eZ Platform Cloud subscription, you are able to create an instance of both open source and Enterprise using templates. In the future it will also enable you to create an instance of eZ Commerce.

To use eZ Platform Cloud:
1. Log in to [cloud.ezplatform.com](https://cloud.ezplatform.com/)
2. Click Add a project, during setup wizard you'll get the choice to pick a flavor of eZ Platform.
3. Complete the setup wizard, and your eZ Platform site will be created.

### Using vanilla platform.sh
Using a platform.sh account, you can create an instance of open source version of eZ Platform from template:
1. Login or create an account at [Platform.sh](https://platform.sh)
2. Create a Platform.sh project, using the "Create a blank site from a template" option. Select one of the "eZ Platform" stack templates.
3. Complete the setup wizard, and your eZ Platform site will be created.

## Manual installation using an eZ Platform Git clone
This requires more manual steps, but may be more up to date with current development than the template-based approach.

**NB:** Some optional aspects of the installation require you to be project owner on a Platform.sh project. If you create a new project now, you will be.

1. Log in or create an account at [Platform.sh](https://platform.sh) _(or login to [cloud.ezplatform.com](https://cloud.ezplatform.com/) if you have an subscription)_
2. Unless this has already been done for you, create a new project by using the **Import your existing code** option. Follow the setup wizard, but halt at the end, before clicking **Finish**.
3. Fork [eZ Platform](https://github.com/ezsystems/ezplatform/) _(or [eZ Platform Enterprise](https://github.com/ezsystems/ezplatform-ee/)/[eZ Commerce](https://github.com/ezsystems/ezcommerce/) if you have a subscription)_ and clone your fork locally.
4. Add the platform remote of your project, and push your branch. The Platform.sh setup wizard provides the command to use. Example:
   `git remote add platform my_project@git.eu.platform.sh:my_project.git`
5. [Optional] Authentication against Github/Bitbucket/Gitlab/updates.ez.no for locale development:
   If you have private packages from your own git repositories or use eZ Platform Enterprise, you can use Composer's
   [auth.json](https://getcomposer.org/doc/articles/http-basic-authentication.md) file or [COMPOSER_AUTH](https://getcomposer.org/doc/03-cli.md#composer-auth) environment variable for this.
   Here's an example of `COMPOSER_AUTH` usage to authenticate for eZ Enterprise packages:
   `export COMPOSER_AUTH='{"http-basic":{"updates.ez.no":{"username":"network-id","password":"token-key"}}}'`

   Further reading also on [docs.platform.sh](https://docs.platform.sh/tutorials/composer-auth.html#set-the-envcomposerauth-project-variable)
6. [Optional] Set the `env:symfony_env` or `env:COMPOSER_AUTH` project variables by performing the following steps:
   1. Install the Platform.sh CLI according to https://docs.platform.sh/gettingstarted/own-code/cli-install.html
   2. Run `platform`
      Run `platform get <your project id>`
   3. Authentication against Github/Bitbucket/Gitlab/updates.ez.no
       For example set the project variables for your eZ Network installation ID and token:
      `platform project:variable:create env:COMPOSER_AUTH '{"http-basic":{"updates.ez.no":{"username":"network-id","password":"token-key"}}}' --no-visible-runtime --sensitive true`
   4. If you have the need to debug things remotely, set the `SYMFONY_ENV` environment variable to 'dev':
      `platform project:variable:set env:SYMONY_ENV dev`.
7. Push your branch. The Platform.sh setup wizard provides the command to use. Example:
   `git push -u platform master`  
   This starts the build process. Now, finish the Platform.sh setup wizard.
   1. The build may fail due to mismatching SSH keys. If you are project administrator, verify that your Platform.sh project "Deploy key" (under "Configure project") is included among your GitHub SSH keys: https://github.com/settings/keys If not, copy the deploy key and add it on GitHub using the "New SSH key" button. Then push an empty commit to trigger a Platform.sh rebuild:  
      `git commit --allow-empty -m'rebuild' && git push`

# FAQ

## Can I adjust the platform.sh config?

Yes, like all configuration (YML/VCL/..) bundled with the eZ Platform installation, all config in the "root project" is yours, and for you to customize for your needs.
The bundled config is a recommended safe default that you can start from, just make sure to make it possible to merge in future config changes when you later need to upgrade.

## Using Enterprise Dedicated Cluster

If you are on a Platform.sh Enterprise Dedicated Cluster setup, typically named PE-6 / PE-12 / (...), you'll need to do some adjustment on the platform.sh config and you'll
have dedicated on-boarding where topics around using eZ Platform on this cluster will be gone true before you can deploy to it.

If you want to read up a bit, look in the bundled platform.sh config. There is a lot of config commented out by default, some of these are explicitly for Dedicated Cluster setup.

It's recommended to:
- Get platform.sh Support to setup `var/cache` and `var/log` as locale mounts to avoid performance issues _(default on PE Cluster is shared)_
- Either:
   A. Enable eZ Clustered DFS setup _(A **requirement** if you use eZ Publish legacy and legacy bridge, due to shared cache files)_.
   B. Stay with a shared mount on `web/var` for storage files _(images, videos, files)_.
- Setup own persisted redis service for sessions, to avoid sessions risking being evicted with cache.

Tips:
- _As always, with help from Platform.sh Support tune memory/disk size for services (Redis, Solr, ..) to make sure it can handle your traffic and data needs._
- _For use with Redis and Solr we recommend at a minimum a PE-6+ ("ME-6") instance with additional memory available._

## Downgrading services

Note that if you downgrade certain services like mysql, this can cause your deploy to hang. Contact platform.sh support if this happens and they will downgrade it for you.

## Re-install Demo

If you use default config, where database is installed on first deploy and thus have installed eZ Platform or the Enterprise Edition on this Platform.sh instance before, you may need to remove the `web/var/.platform.installed` file to ensure the installation is performed in the deploy stage.

The symptom for this is when, in the Back Office, you go to **Content** -> **Form** Manager and get an error message or when the "My Drafts Scheduled for Future Publication" and "All Drafts Scheduled for Future Publication" sections on "My Dashboard" do not load.

To do this, you'll need to remove `web/var/.platform.installed`, either using SSH or using `platform` command:
```
platform ssh -e [env] 'rm web/var/.platform.installed'
```

The next time you trigger a rebuild _(can be done in UI)_, the full install will run.
