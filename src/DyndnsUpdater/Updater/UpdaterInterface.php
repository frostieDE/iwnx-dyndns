<?php

namespace DyndnsUpdater\Updater;

interface UpdaterInterface {
    const IPv4 = 'IPv4';
    const IPv6 = 'IPv6';

    /**
     * @param string $domain
     * @param string $ipAddr
     * @param string $type
     */
    public function update($domain, $ipAddr, $type);
}