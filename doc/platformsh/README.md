# Use eZ Platform on Platform.sh

## What is Platform.sh?
*Platform.sh* is a continuous deployment cloud hosting solution which can replicate a live production setup in seconds and create byte-level clones of throwaway dev and staging environments, which makes human testing and validation easy.

## Install
For installation instructions, see [INSTALL.md](https://github.com/ezsystems/ezplatform/blob/master/doc/platformsh/INSTALL.md).

## Platform.sh configuration files
You need to tweak these files before pushing to platform.sh setup:
- [.platform.app.yaml](https://docs.platform.sh/configuration/app-containers.html) controls your application, including dependencies, build and deployment.
- The `.platform` directory contains Platform.sh service and route settings.
- `app/config/env/platformsh.php` ensures that database, cache, and sessions provided by or configured for Platform.sh is applied in eZ Platform. You should normally not need to change this, but there may be special cases where it is required.

There are inline comments in these files for choices you should consider, and as stated in [INSTALL.md](https://github.com/ezsystems/ezplatform/blob/master/doc/platformsh/INSTALL.md)
FAQ there are also specific hints if you plan to use Platform.sh Enterprise Dedicated Cluster setup.
