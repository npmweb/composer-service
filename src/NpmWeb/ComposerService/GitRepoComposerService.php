<?php namespace NpmWeb\ComposerService;

use GitWrapper\GitWrapper;

class GitRepoComposerService implements ComposerServiceInterface {

    protected $git;

    public function __construct( GitWrapper $git ) {
        $this->git = $git;
    }

    public function getPackageInfo( $packageName, array $extraInfo = array() )
    {
        // TODO don't hard-code the Git URL
        if( isset($extraInfo['repo']) ) {
            $repoUrl = $extraInfo['repo'];
        } else {
            foreach( $extraInfo['repos'] as $repo ) {
                // TODO more reliable way to do this; pull down all repos I guess
                // TODO not matching bbgrid b/c case?
                $result = strpos( $repo->url, $packageName );
                if( false !== $result ) {
                    $repoUrl = $repo->url;
                    break;
                }
            }
        }
        if( !isset($repoUrl) ) {
            return null; // not found
        }

        $wcPath = $this->getWorkingCopy( $packageName, $repoUrl );

        // check composer.json for it
        $composerConfig = $this->getComposerConfig( $wcPath );
        if( !$composerConfig ) {
            return null;
        } else {
            $result = [];
            foreach( ['type','description','require','repositories'] as $field ) {
                if( isset($composerConfig->$field) ) {
                    $result[$field] = $composerConfig->$field;
                }
            }

            // get homepage
            if( preg_match('/^git@(.+):(.+\/.+)/', $repoUrl, $matches) ) {
                $result['homepage'] = 'https://'.$matches[1].'/'.$matches[2];
            }

            return (object)$result;
        }
    }

    protected function getWorkingCopy( $packageName, $gitUrl )
    {
        $wcPath = storage_path().'/git/'.$packageName;

        // if already have working copy, pull latest
        if( is_dir($wcPath) ) {
            echo 'pulling '.$packageName."\n";
            $wc = $this->git->workingCopy($wcPath);
            $wc->pull();
        } else {
            echo 'checking out '.$packageName."\n";
            // otherwise, get working copy
            $wc = $this->git->clone($gitUrl, $wcPath);
        }

        return $wcPath;
    }

    protected function getComposerConfig( $wcPath )
    {
        $composerPath = $wcPath.'/composer.json';
        if( !file_exists($composerPath) ) {
            return null;
        } else {
            $composerJson = file_get_contents($composerPath);
            $decoded = json_decode($composerJson);
            return $decoded;
        }
    }

}
