<?php

namespace BM2\SiteBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Bundle\FrameworkBundle\Client;

use Calitarus\TranslatorBundle\Tests\SimpleTestCase;
use Calitarus\TranslatorBundle\Command\TurnCommand;


class SetupCommandTest extends SimpleTestCase {

    public function runCommand(Client $client, $command) {
       $application = new Application($client->getKernel());
       $application->setAutoExit(false);

       $fp = tmpfile();
       $input = new StringInput($command);
       $output = new StreamOutput($fp);

       $application->run($input, $output);

       fseek($fp, 0);
       $output = '';
       while (!feof($fp)) {
           $output = fread($fp, 4096);
       }
       fclose($fp);

       return $output;
   }


   public function testExecute() {
        $output = $this->runCommand($this->client, "trans:setup");

        $this->assertContains('Adding default domains', $output);
        $this->assertContains('Database initialized', $output);
    }
}