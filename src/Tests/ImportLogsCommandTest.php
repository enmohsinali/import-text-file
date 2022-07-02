<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ImportLogsCommandTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $application = new Application($kernel);

        $command = $application->find('app:import-logs');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $commandTester->assertCommandIsSuccessful();

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('File logs.txt has been proccesed.', $output);
    }
}
