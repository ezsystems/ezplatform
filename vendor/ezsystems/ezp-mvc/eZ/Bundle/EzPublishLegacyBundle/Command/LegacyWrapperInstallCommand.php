<?php
/**
 * File containing the LegacyWrapperInstallCommand class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishLegacyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ezcPhpGenerator;
use ezcPhpGeneratorParameter;

class LegacyWrapperInstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName( 'ezpublish:legacy:assets_install' )
            ->addArgument( 'webroot', InputArgument::OPTIONAL, 'The webroot directory (usually "web")', 'web' )
            ->setDescription( 'Installs assets from eZ Publish legacy installation and wrapper scripts for front controllers (like index_cluster.php).' )
            ->setHelp( <<<EOT
The command <info>%command.name%</info> installs <info>assets</info> from eZ Publish legacy installation
and wrapper scripts for <info>front controllers</info> (like <info>index_cluster.php</info>).
<info>Assets folders:</info> Symlinks will be created from your eZ Publish legacy directory.
<info>Front controllers:</info> Wrapper scripts will be generated.
EOT
            )
        ;
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        /**
         * @var \Symfony\Component\Filesystem\Filesystem
         */
        $filesystem = $this->getContainer()->get( 'filesystem' );
        $webroot = rtrim( $input->getArgument( 'webroot' ), '/' );
        $legacyRootDir = rtrim( $this->getContainer()->getParameter( 'ezpublish_legacy.root_dir' ), '/' );

        $output->writeln( "Installing assets eZ Publish legacy, located at $legacyRootDir" );
        foreach ( array( 'design', 'extension', 'share', 'var' ) as $folder )
        {
            $webrootFolder = "$webroot/$folder";
            $filesystem->remove( $webrootFolder );
            $filesystem->symlink( "$legacyRootDir/$folder", $webrootFolder );
        }

        $output->writeln( "Installing wrappers for eZ Publish legacy front controllers" );
        foreach ( array( 'index_treemenu.php', 'index_rest.php', 'index_cluster.php' ) as $frontController )
        {
            $newFrontController = "$webroot/$frontController";
            $filesystem->remove( $newFrontController );
            $generator = new ezcPhpGenerator( $newFrontController, false );
            $generator->lineBreak = "\n";
            $generator->appendCustomCode( <<<EOT
<?php
/**
 * File containing the wrapper around the legacy $frontController file
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */
EOT
            );
            $generator->appendValueAssignment( 'legacyRoot', $legacyRootDir );
            $generator->appendFunctionCall(
                'chdir',
                array(
                     new ezcPhpGeneratorParameter( 'legacyRoot' )
                )
            );
            $generator->appendCustomCode( 'require $legacyRoot . "/index_rest.php";' );
            $generator->appendEmptyLines();
            $generator->finish();
        }
    }
}
