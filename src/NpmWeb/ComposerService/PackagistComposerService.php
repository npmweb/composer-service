<?php namespace NpmWeb\ComposerService;

use Packagist\Api\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

class PackagistComposerService implements ComposerServiceInterface {

    protected $packagist;

    public function __construct( Client $packagist ) {
        $this->packagist = $packagist;
    }

    public function getPackageInfo( $packageName, array $extraInfo = array() )
    {
        try {
            $packagistInfo = $this->packagist->get($packageName);
            return (object)array(
                'description' => $packagistInfo->getDescription(),
            );
        } catch( ClientErrorResponseException $e ) {
            return null;
        }
    }

}
