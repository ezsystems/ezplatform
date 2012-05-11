# Installation instructions

1. Clone the repository

       ```bash
       git clone git@github.com:ezsystems/ezp-next-mvc.git
       ```
2. Install the dependencies with [Composer](http://getcomposer.org) :

       ```bash
       cd /path/to/ezp-next-mvc/
       php bin/composer.phar install
       ```
3. Initialize and update git submodules (like public API) :

       ```bash
       git submodule init
       git submodule update
       ```

Note that your document root must be the `web/` folder.
