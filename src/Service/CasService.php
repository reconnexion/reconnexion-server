<?php

namespace App\Service;

class CasService
{
    public function __construct(string $casHost, int $casPort, string $casPath)
    {
        \phpCAS::setDebug('cas-debug.log');
        \phpCAS::setVerbose(true);

        \phpCAS::client(CAS_VERSION_2_0, $casHost, $casPort, $casPath, true);
        \phpCAS::setNoCasServerValidation();
        \phpCAS::setLang(PHPCAS_LANG_FRENCH);
    }

    public function forceAuthentication() : bool
    {
        \phpCAS::forceAuthentication();
        return \phpCAS::isAuthenticated();
    }

    public function getUserAttributes() : array
    {
        if( !\phpCAS::isAuthenticated() ) {
            throw new \Exception("The user is not authenticated !");
        }
        return \phpCAS::getAttributes();
    }
}