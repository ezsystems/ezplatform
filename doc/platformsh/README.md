# Use eZ Platform on Platform.sh

> **Beta**: Instructions and Tools *(Platform.sh configuration files, scripts, ...)* described on this page are currently in Beta for community testing & contribution, and may change without notice.

## What is Platform.sh?
*Platform.sh* is a continuous deployment cloud hosting solution which can replicate a live production setup in seconds and create byte-level clones of throwaway dev and staging environments, which makes human testing and validation easy.

## Current limitations
- Configuration is limited to eZ Platform 1.13 and higher setups, if you intend to run legacy bridge or pure eZ Publish legacy on platform.sh
  you should ideally get in contact with a eZ Partner who has experience with this already, possible also get some consulting hours
  by eZ Professional Services to help on your migration path.

## Install
For installation instructions, see [INSTALL.md](https://github.com/ezsystems/ezplatform/blob/master/doc/platformsh/INSTALL.md).

## Platform.sh configuration files
You may need to tweak these files after completing the installation.
- [.platform.app.yaml](https://docs.platform.sh/configuration/app-containers.html) controls your application, including dependencies, build and deployment.
- The `.platform` directory contains Platform.sh service and route settings.
- `app/config/env/platformsh.php` ensures that database, cache, and sessions provided by or configured for Platform.sh is applied in eZ Platform. You should normally not need to change this, but there may be special cases where it is required.
