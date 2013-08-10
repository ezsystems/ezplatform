#!/bin/sh

if [ -d ezpublish_legacy ]; then
    echo "Legacy folder exists already, KTHXBYE"
    exit 1
fi

# Get eZ Publish Legacy
git clone --depth 1 https://github.com/ezsystems/ezpublish-legacy.git ezpublish_legacy

# Get package extensions
mkdir ezpublish_legacy_packages
cd ezpublish_legacy_packages
git clone --depth 1 https://github.com/ezsystems/ezdemo.git
git clone --depth 1 https://github.com/ezsystems/ezflow.git
git clone --depth 1 https://github.com/ezsystems/ezgmaplocation.git
git clone --depth 1 https://github.com/ezsystems/ezstarrating.git
git clone --depth 1 https://github.com/ezsystems/ezwt.git
#git clone --depth 1 https://github.com/ezsystems/ezcomments.git


# Symlink extensions
cd ../ezpublish_legacy/extension
ln -s ../../ezpublish_legacy_packages/ezdemo/packages/ezdemo_extension/ezextension/ezdemo
ln -s ../../ezpublish_legacy_packages/ezflow/packages/ezflow_extension/ezextension/ezflow
ln -s ../../ezpublish_legacy_packages/ezgmaplocation/packages/ezgmaplocation_extension/ezextension/ezgmaplocation
ln -s ../../ezpublish_legacy_packages/ezstarrating/packages/ezstarrating_extension/ezextension/ezstarrating
ln -s ../../ezpublish_legacy_packages/ezwt/packages/ezwt_extension/ezextension/ezwt
#ln -s ../../ezpublish_legacy_packages/ezcomments/packages/ezcomments_extension/ezextension/ezcomments

cd ..

# Fix folder permissions
./bin/modfix.sh

# Generate extensions autoload
php bin/php/ezpgenerateautoloads.php -e

# Create settings
mkdir settings/override
mkdir settings/siteaccess/behat_site
mkdir settings/siteaccess/behat_site_admin


echo "[SiteAccessSettings]
CheckValidity=false
RelatedSiteAccessList[]
RelatedSiteAccessList[]=behat_site
RelatedSiteAccessList[]=behat_site_admin

[SiteSettings]
SiteName=eZ Publish Behat Test Site
SiteURL=localhost

[Session]
SessionNameHandler=custom
SessionNamePerSiteAccess=disabled

[DebugSettings]
DebugRedirection=disabled

[ExtensionSettings]
ActiveExtensions[]=ezjscore
ActiveExtensions[]=ezoe
ActiveExtensions[]=ezformtoken
ActiveExtensions[]=ezdemo
ActiveExtensions[]=ezflow
ActiveExtensions[]=ezwt
ActiveExtensions[]=ezgmaplocation
ActiveExtensions[]=ezstarrating
#ActiveExtensions[]=ezcomments" > settings/override/site.ini


echo "[SiteAccessSettings]
RequireUserLogin=false
ShowHiddenNodes=false

[DesignSettings]
SiteDesign=ezdemo
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=ezflow
AdditionalSiteDesignList[]=base

[SiteSettings]
LoginPage=embedded" > settings/siteaccess/behat_site/site.ini

echo "[SiteSettings]
DefaultPage=content/dashboard

[SiteAccessSettings]
RequireUserLogin=true
ShowHiddenNodes=true

[DesignSettings]
SiteDesign=admin2
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=admin
AdditionalSiteDesignList[]=ezdemo" > settings/siteaccess/behat_site_admin/site.ini


