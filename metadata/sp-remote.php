<?php

$metadata['http(s)://{laravel_url}/mytestidp1/metadata'] = array(
    'AssertionConsumerService' => 'http(s)://{laravel_url}/mytestidp1/acs',
    'SingleLogoutService' => 'http(s)://{laravel_url}/mytestidp1/sls',
    //the following two affect what the $Saml2user->getUserId() will return
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
    'simplesaml.nameidattribute' => 'uid' 
);