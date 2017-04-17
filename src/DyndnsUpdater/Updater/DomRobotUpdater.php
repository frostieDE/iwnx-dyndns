<?php

namespace DyndnsUpdater\Updater;

use INWX\Domrobot;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RequestStack;

class DomRobotUpdater implements UpdaterInterface {
    private $logger;

    private $domains;
    private $domrobot;

    private $username;
    private $password;
    private $sharedSecret;

    private $requestStack;

    public function __construct(array $domains, Domrobot $domrobot, $username, $password, $sharedSecret, RequestStack $requestStack, LoggerInterface $logger) {
        $this->domains = $domains;
        $this->domrobot = $domrobot;

        $this->username = $username;
        $this->password = $password;
        $this->sharedSecret = $sharedSecret;

        $this->requestStack = $requestStack;

        $this->logger = $logger ?? new NullLogger();
    }

    public function update($domain, $ipAddr, $type) {
        $res = $this->domrobot->login(
            $this->username,
            $this->password,
            empty($this->sharedSecret) ? null : $this->sharedSecret
        );

        if($res['code'] == 1000) {
            $domainInfo = $this->getDomain($domain);

            if($domainInfo === null) {
                throw new DomainNotFoundException(sprintf('Domain "%s" was not found', $domain));
            }

            ## IPv4
            if(isset($domainInfo['ipv4']) && !empty($ipAddr) && $type === UpdaterInterface::IPv4) {
                $this->updateDomain($domainInfo['ipv4'], $ipAddr);
            }

            ## IPv6
            if(isset($domainInfo['ipv6']) && !empty($ipAddr) && $type === UpdaterInterface::IPv6) {
                $this->updateDomain($domainInfo['ipv6'], $ipAddr);
            }
        } else {
            $this->logger->critical('Domrobot login failed', [
                'res' => $res
            ]);
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

    private function updateDomain($id, $ipAddr) {
        $res = $this->domrobot->call('nameserver', 'updateRecord', [
            'id' => $id,
            'content' => $ipAddr
        ]);

        if($res['code'] === 1000) {
            $request = $this->requestStack->getMasterRequest();

            $this->logger->info(
                sprintf('Updated record %d to %s [requested from: %s]', $id, $ipAddr, $request->getClientIp())
            );
        } else {
            $this->logger->critical('Failed to update record', [
                'id' => $id,
                'ipaddr' => $ipAddr,
                'res' => $res
            ]);

            throw new \Exception('Failed to update record');
        }
    }
}