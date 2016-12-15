<?php

require_once __DIR__.'/_config.php';

$all = SpConfig::current()->getBuildContainer()->getPartyContainer()->getIdpEntityDescriptorStore()->all();
switch (count($all)) {
    case 0:
        print 'None IDP configured';
        exit;
    case 1:
        header('Location: login.php?idp='.$all[0]->getEntityID());
        exit;
}

echo "<h1>Following IDPs are configured</h1>\n";
echo "<p><small>Choose one</small></p>\n";
foreach ($all as $idp) {
    if ($idp->getAllIdpSsoDescriptors()) {
        echo "<p><a href=\"login.php?idp={$idp->getEntityID()}\">{$idp->getEntityID()}</a></p>\n";
    }
}
echo "\n<p>LigthSAML</p>\n";
