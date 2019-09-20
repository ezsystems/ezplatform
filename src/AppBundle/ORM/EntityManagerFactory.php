<?php
declare( strict_types=1 );

namespace AppBundle\ORM;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use eZ\Bundle\EzPublishCoreBundle\ApiLoader\RepositoryConfigurationProvider;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class EntityManagerFactory
{

    use ContainerAwareTrait;

    /**
     * @var \eZ\Bundle\EzPublishCoreBundle\ApiLoader\RepositoryConfigurationProvider
     */
    protected $repositoryConfigurationProvider;

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    protected $doctrineRegistry;

    /**
     * EntityManagerFactory constructor.
     * @param RepositoryConfigurationProvider $repositoryConfigurationProvider
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrineRegistry
     */
    public function __construct( RepositoryConfigurationProvider $repositoryConfigurationProvider, Registry $doctrineRegistry )
    {
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
        $this->doctrineRegistry = $doctrineRegistry;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        $repositoryConfig = $this->repositoryConfigurationProvider->getRepositoryConfig();

        if ( isset( $repositoryConfig['storage']['connection'] ) )
        {
            return $this->doctrineRegistry->getManager($repositoryConfig['storage']['connection'] );
        }

        throw new InvalidArgumentException(
            "Invalid Doctrine entity manager '{$repositoryConfig['storage']['connection']}' for repository '{$repositoryConfig['alias']}'."
        );
    }
}
