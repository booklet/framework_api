<?php
Config::set('env', 'development');

// SETUP ENV FOR TESTS REQUETS
// ==============================================================================
$headers = new Headers;
if ($headers->isTesterTestRequest()) {
    Config::set('env', 'test');
}
