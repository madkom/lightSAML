<?php

require_once __DIR__.'/_config.php';

$buildContainer = SpConfig::current()->getBuildContainer();

$ssoState = $buildContainer->getStoreContainer()->getSsoStateStore()->get();

foreach ($ssoState->getSsoSessions() as $ssoSession) {
    echo "<ul>\n";
    echo '<li>IDP: '.$ssoSession->getIdpEntityId()."</li>\n";
    echo '<li>SP: '.$ssoSession->getSpEntityId()."</li>\n";
    echo '<li>NameID: '.$ssoSession->getNameId()."</li>\n";
    echo '<li>NameIDFormat: '.$ssoSession->getNameIdFormat()."</li>\n";
    echo '<li>SessionIndex: '.$ssoSession->getSessionIndex()."</li>\n";
    echo '<li>AuthnInstant: '.$ssoSession->getSessionInstant()->format('Y-m-d H:i:s P')."</li>\n";
    echo '<li>FirstAuthOn: '.$ssoSession->getFirstAuthOn()->format('Y-m-d H:i:s P')."</li>\n";
    echo '<li>LastAuthOn: '.$ssoSession->getLastAuthOn()->format('Y-m-d H:i:s P')."</li>\n";
    echo "</ul>\n";
}

if (empty($ssoState->getSsoSessions())) {
    echo "<p>No sessions established</p>\n";
}
