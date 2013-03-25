<?php

namespace Calitarus\TranslatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Collections\ArrayCollection;

use Calitarus\TranslatorBundle\Entity;


class SetupCommand extends ContainerAwareCommand {

    private $domains = array('messages', 'validators');

    // 25 most common languages, ordered by number of speakers
    private $languages = array(
        'zh' => '中文',          // mandarin / chinese
        'en' => 'english',     // english
        'es' => 'español',     // spanish
        'hi' => 'हिन्दी',      // hindi
        'ru' => 'язык',        // russian
        'ar' => 'العربية',     // arabic
        'pt' => 'português',    // portuguese
        'bn' => 'বাংলা',       // bengali
        'fr' => 'français',     // french
        'ms' => 'بهاس ملايو‎',      // malay / indonesian
        'de' => 'deutsch',      // german
        'ja' => '日本語',       // japanese
        'fa' => 'فارسی',        // farsi/persian
        'ur' => 'اردو',         // urdu 
        'pa' => 'ਪੰਜਾਬੀ',       // punjabi
        'vi' => 'Tiếng Việt', // vietnamese
        'jv' => 'basa Jawa',  // javanese
        'ta' => 'தமிழ்',     // tamil
        'ko' => '한국어',      // korean 
        'tr' => 'Türkçe',     // turkish
        'te' => 'తెలుగు',   // telugu
        'mr' => 'मराठी',      // marathi
        'it' => 'italiano',   // italian
        'th' => 'ไทย',        // thai
        'my' => 'ဗမာစာ',    // burmese
    );

    protected function configure() {
        $this
            ->setName('trans:setup')
            ->setDescription('Initialize the Translator database with default data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // TODO: truncate tables

        $output->writeln("Adding default domains");
        foreach ($this->domains as $name) {
            $domain = new Entity\Domain();
            $domain->setName($name);
            $em->persist($domain);
        }

        $output->writeln("Adding default language list");
        foreach ($this->languages as $code=>$name) {
            $language = new Entity\Language();
            $language->setCode($code);
            $language->setName($name);
            $em->persist($language);
        }

        $em->flush();

        $default_domain = $em->getRepository('Calitarus\TranslatorBundle\Entity\Domain')->findOneByName($this->domains[0]);
        if (!$default_domain) { $output->writeln("error in default domain"); exit(1); }
        $default_language = $em->getRepository('Calitarus\TranslatorBundle\Entity\Language')->findOneByCode('en');
        if (!$default_language) { $output->writeln("error in default language"); exit(1); }


        $output->writeln("Initializing english language set");
        $message = new Entity\Message();
        $message->setKey('__self');
        $message->setLong(false);
        $message->setDomain($default_domain);
        $message->setLastChange(new \DateTime("now"));
        $em->persist($message);
        $translation = new Entity\Translation();
        $translation->setContent('english');
        $translation->setLanguage($default_language);
        $translation->setMessage($message);
        $translation->setLastChange(new \DateTime("now"));
        $em->persist($translation);

        $em->flush();
        $output->writeln("Database initialized");
    }

}
