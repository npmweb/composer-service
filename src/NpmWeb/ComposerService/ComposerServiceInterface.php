<?php namespace NpmWeb\ComposerService;

interface ComposerServiceInterface {

    public function getPackageInfo( $packageName, array $extraInfo = array() );

}