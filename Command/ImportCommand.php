<?php

namespace Calitarus\TranslatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\Collections\ArrayCollection;

use Calitarus\TranslatorBundle\Entity;


class ImportCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('trans:import')
            ->setDescription('Import a file into Translator database')
            ->addArgument('file', InputArgument::REQUIRED, 'file to load')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $file = $input->getArgument('file');
        $base = strtolower(basename($file));
        $parts = explode('.', $base);
        $file_domain = $parts[0];
        $file_language = $parts[1];
        $file_format = $parts[2];

        switch ($file_format) {
            case 'yml':
                $yaml = Yaml::parse($file);
                break;
            default:
                $output->writeln("only YAML supported at this time.");
                exit(1);
        }

        $em = $this->getContainer()->get('doctrine')->getManager();
        $domain = $em->getRepository('Calitarus\TranslatorBundle\Entity\Domain')->findOneByName($file_domain);
        if (!$domain) {
            $output->writeln("can't find domain $file_domain.");
            exit(1);
        }
        $language = $em->getRepository('Calitarus\TranslatorBundle\Entity\Language')->findOneByCode($file_language);
        if (!$language) {
            $output->writeln("can't find language code $file_language.");
            exit(1);
        }

        $count = 0; $newmsgs = 0;

        $strings = $this->parse($yaml);
        foreach ($strings as $key => $content) {
            $message = $em->getRepository('Calitarus\TranslatorBundle\Entity\Message')->findOneByKey($key);
            if (!$message) {
                $newmsgs++;
                $message = new Entity\Message();
                $message->setKey($key);
                if (strlen($content)>120) {
                    $message->setLong(true);
                } else {
                    $message->setLong(false);
                }
                $message->setDomain($domain);
                $message->setLastchange(new \DateTime("now"));
                $em->persist($message);                
            }

            $translation = new Entity\Translation();
            $translation->setContent($content);
            $translation->setLanguage($language);
            $translation->setMessage($message);
            $translation->setLastchange(new \DateTime("now"));
            $em->persist($translation);

            $count++;
        }

        $em->flush();
        $output->writeln("$count records imported, $newmsgs new messages");
    }

    private function parse($data, $path=array()) {
        $strings = array();
        foreach ($data as $key=>$value) {
            if (is_array($value)) {
                $mypath = $path; $mypath[] = $key;
                $result = $this->parse($value, $mypath);
            } else {
                if (!empty($path)) {
                    $key = implode('.', $path).'.'.$key;
                } 
                $result = array($key => $value);
            }
            $strings = array_merge($strings, $result);
        }
        return $strings;
    }

}
