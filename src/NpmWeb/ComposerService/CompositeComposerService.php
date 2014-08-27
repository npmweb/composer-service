<?php namespace NpmWeb\ComposerService;

class CompositeComposerService implements ComposerServiceInterface {

    protected $composerServices;

    public function __construct( array $composerServices ) {
        $this->composerServices = $composerServices;
    }

    public function getPackageInfo( $packageName, array $extraInfo = array() )
    {
        foreach( $this->composerServices as $composerService ) {
            if( $packageInfo = $composerService->getPackageInfo( $packageName, $extraInfo )) {
                return $packageInfo;
            }
        }
        return null;
    }

}
