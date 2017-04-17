<?php

namespace DyndnsUpdater\Controller;

use DyndnsUpdater\Application\Application;
use DyndnsUpdater\Updater\UpdaterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateController {
    protected $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function update(Request $request) {
        $domain = $request->query->get('domain', null);
        $ipv4 = $request->query->get('ipv4', null);
        $ipv6 = $request->query->get('ipv6', null);

        if (empty($domain)) {
            throw new BadRequestHttpException('You must specify a domain');
        }

        if (empty($ipv4) && empty($ipv6)) {
            throw new BadRequestHttpException('You must either specify an IPv4 or IPv6 parameter');
        }

        /** @var UpdaterInterface $updater */
        $updater = $this->app['updater'];

        if (!empty($ipv4)) {
            $updater->update($domain, $ipv4, UpdaterInterface::IPv4);
        }

        if (!empty($ipv6)) {
            $updater->update($domain, $ipv6, UpdaterInterface::IPv6);
        }

        return new Response();
    }
}