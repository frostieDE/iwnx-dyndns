<?php

namespace DyndnsUpdater\ServiceProvider;

use DyndnsUpdater\Updater\DomRobotUpdater;
use DyndnsUpdater\Updater\FakeUpdater;
use INWX\Domrobot;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UpdaterServiceProvider implements ServiceProviderInterface {
    public function register(Container $app) {
        $app['domrobot.language'] = 'en';

        $app['domrobot'] = function($app) {
            $domrobot = new Domrobot($app['config']['domrobot']['url']);
            $domrobot->setDebug(false);
            $domrobot->setLanguage($app['config']['domrobot']['language'] ?? $app['domrobot.language']);

            return $domrobot;
        };

        $app['updater'] = function($app) {
            $useFake = $app['config']['domrobot']['fake'] ?? false;

            if($useFake === true) {
                return new FakeUpdater($app['config']['domains']);
            }

            return new DomRobotUpdater(
                $app['config']['domains'],
                $app['domrobot'],
                $app['config']['domrobot']['username'],
                $app['config']['domrobot']['password'],
                $app['config']['domrobot']['shared_secret'],
                $app['request_stack'],
                $app['logger']
            );
        };
    }
}