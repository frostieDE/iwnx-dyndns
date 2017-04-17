<?php

namespace DyndnsUpdater\Updater;

class FakeUpdater implements UpdaterInterface {

    private $domains;

    public function __construct(array $domains) {
        $this->domains = $domains;
    }

    public function update($domain, $ipAddr, $type) {
        $domainInfo = $this->getDomain($domain);

        if($domainInfo === null) {
            throw new DomainNotFoundException(sprintf('Domain "%s" was not found', $domain));
        }
    }

    private function getDomain($domain) {
        foreach($this->domains as $domainInfo) {
            if($domainInfo['domain'] === $domain) {
                return $domainInfo;
            }
        }

        return null;
    }
}