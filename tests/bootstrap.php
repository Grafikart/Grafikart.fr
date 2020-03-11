<?php

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

require dirname(__DIR__).'/vendor/autoload.php';

// On charge la configuration de symfony
require dirname(__DIR__).'/config/bootstrap.php';

// On crée le base de données
$kernel = new \App\Kernel('test', true);
$kernel->boot();
$application = new Application($kernel);
$application->setAutoExit(false);
$databaseDoesNotExists = $application->run(new StringInput('doctrine:query:sql "SELECT username FROM user;"'), new NullOutput());
if ($databaseDoesNotExists) {
    $application->run(new StringInput('doctrine:database:drop --if-exists --force -q'));
    $application->run(new StringInput('doctrine:database:create -q'));
    $application->run(new StringInput('doctrine:schema:update --force -q'));
}
$kernel->shutdown();
