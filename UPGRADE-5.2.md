# UPGRADE FROM eZ Publish 5.1 TO 5.2

When upgrading eZ Publish from 5.1 to 5.2, you need to do the following changes to the code that came from
the base distribution:

 * Change `EzPublishKernel`'s inheritance from `Symfony\Component\HttpKernel\Kernel` to `eZ\Bundle\EzPublishCoreBundle\Kernel`.
   For this, in `ezpublish/EzPublishKernel.php`, just replace:

       use Symfony\Component\HttpKernel\Kernel;

   By:

       use eZ\Bundle\EzPublishCoreBundle\Kernel;

