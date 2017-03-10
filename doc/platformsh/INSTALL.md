# Install eZ Platform on Platform.sh

> **Beta**: Instructions and Tools *(Platform.sh configuration files, scripts, ...)* described on this page are currently in Beta for community testing & contribution, and may change without notice.

## Installation using eZ Platform template
This is the simplest approach, but may be less up to date with current development than the other, more manual approach.

1. Login or create an account at [Platform.sh](https://platform.sh)
1. Create a Platform.sh project, using the "Create a blank site from a template" option. Select the "eZ Platform" stack template.
1. Complete the setup wizard, and your eZ Platform site will be created.

## Installation using an eZ Platform clone
This requires more manual steps, but may be more up to date with current development than the template-based approach.

**NB:** Some optional aspects of the installation require you to be project owner on a Platform.sh project. If you create a new project now, you will be.

1. Login or create an account at [Platform.sh](https://platform.sh)
1. Create a Platform.sh project, using the "Import your existing code" option. Follow the setup wizard, but halt at the end, before clicking "Finish".
1. Fork [eZ Platform](https://github.com/ezsystems/ezplatform/) and clone your fork locally.
1. Add the platform remote of your project, and push your branch. The Platform.sh setup wizard tells you the command to use. Example:  
   `git remote add platform my_project@git.eu.platform.sh:my_project.git`  
   1. Optional: Set the SYMFONY_ENV environment variable to 'prod' or 'dev':  
      `export SYMFONY_ENV=dev`  
      If you don't do this, the local build will default to 'prod'.  
      **NB:** For this to affect remote builds, it must also be set as a Platform.sh project variable, see below.
1. Push your branch. The Platform.sh setup wizard tells you the command to use. Example:  
   `git push -u platform master`  
   This starts the build process. Now, finish the Platform.sh setup wizard.
   1. The build may fail due to mismatching SSH keys. If you are project administrator, verify that your Platform.sh project "Deploy key" (under "Configure project") is included among your GitHub SSH keys: https://github.com/settings/keys If not, copy the deploy key and add it on GitHub using the "New SSH key" button. Then push an empty commit to trigger a Platform.sh rebuild:  
      `git commit --allow-empty -m'rebuild' && git push`
1. Optional: Install the Platform.sh CLI and set the env:symfony_env project variable
   1. Install the Platform.sh CLI according to https://docs.platform.sh/overview/cli.html
   1. Run `platform`  
      Run `platform get <your project id>`
   1. Optional: Set the SYMFONY_ENV environment variable to 'prod' or 'dev':  
      `platform project:variable:set env:symfony_env prod`  
      If you don't do this, remote builds will default to 'prod'.  
      Then push an empty commit to trigger a Platform.sh rebuild:  
      `git commit --allow-empty -m'rebuild' && git push`  
      This will take a while, as it runs the full composer install.

> **NB:** If you have installed eZ Platform or the Enterprise Edition on this Platform.sh instance before, you may need to remove the web/var/.platform.installed file to ensure the installation is performed in the deploy stage.
>
> The symptom for this is when, in the backend, you go to Content -> Form Manager and get an error message, or when the "My Drafts Scheduled for Future Publication" and "All Drafts Scheduled for Future Publication" sections on "My Dashboard" will not load.
>
> To do this, go to the Platform.sh web interface -> "Access site" and copy the "SSH access" command. Then, run the SSH command from a terminal, and:
> `rm web/var/.platform.installed`
> The next time you trigger a rebuild (see above), the full install will run.
