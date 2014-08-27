<?php namespace Npmweb\ComposerService\Laravel;

use Illuminate\Support\ServiceProvider;
use GitWrapper\GitWrapper;
use NpmWeb\ComposerService\CompositeComposerService;
use NpmWeb\ComposerService\GitRepoComposerService;
use NpmWeb\ComposerService\PackagistComposerService;

class ComposerServiceServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // @see https://coderwall.com/p/svocrg
        $this->package('npmweb/composer-service', null, __DIR__.'/../../../');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $packagist = $this->registerPackagist();
        $gitRepo = $this->registerGitRepo();
        $this->registerComposite([ $packagist, $gitRepo ]);
    }

    protected function registerPackagist()
    {
        $packagist = new PackagistComposerService( new \Packagist\Api\Client() );
        $this->app->bindShared( PackagistComposerService::class, function() use($packagist) {
            return $packagist;
        });
        return $packagist;
    }

    protected function registerGitRepo()
    {
        $gitRepo = new GitRepoComposerService( new GitWrapper() );
        $this->app->bindShared( GitRepoComposerService::class, function() use($gitRepo) {
            return $gitRepo;
        });
        return $gitRepo;
    }

    protected function registerComposite( $composerServices )
    {
        $composite = new CompositeComposerService( $composerServices );
        $this->app->bindShared( CompositeComposerService::class, function() use($composite) {
            return $composite;
        });
        return $composite;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
