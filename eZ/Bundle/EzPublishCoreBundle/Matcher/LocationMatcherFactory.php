<?php
/**
 * File containing the LocationMatcherFactory class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishCoreBundle\Matcher;

use eZ\Publish\Core\MVC\Symfony\Matcher\LocationMatcherFactory as BaseMatcherFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LocationMatcherFactory extends BaseMatcherFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct( ContainerInterface $container )
    {
        $this->container = $container;
        parent::__construct(
            $this->container->get( 'ezpublish.api.repository' ),
            $this->container->get( 'ezpublish.config.resolver' )->getParameter( 'location_view' )
        );
    }

    /**
     * @param string $matcherIdentifier
     *
     * @return \eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface
     */
    protected function getMatcher( $matcherIdentifier )
    {
        if ( $this->container->has( $matcherIdentifier ) )
            return $this->container->get( $matcherIdentifier );

        return parent::getMatcher( $matcherIdentifier );
    }
}
