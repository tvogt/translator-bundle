<?php

namespace Calitarus\TranslatorBundle\Twig;

use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\TranslatorInterface;

class InlineTranslationExtension extends TranslationExtension {

    private $class = 'translated';

    public function __construct(TranslatorInterface $translator) {
        parent::__construct($translator);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return array(
            'i_trans' => new \Twig_SimpleFilter('i_trans', array($this, 'itrans'), array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'it_trans' => new \Twig_SimpleFilter('it_trans', array($this, 'itrans_title'), array('needs_environment' => true, 'pre_escape' => 'html', 'is_safe' => array('html'))),
            'i_transchoice' => new \Twig_SimpleFilter('i_transchoice', array($this, 'itranschoice'), array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'it_transchoice' => new \Twig_SimpleFilter('it_transchoice', array($this, 'itranschoice_title'), array('needs_environment' => true, 'pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    public function getName() {
        return 'inlinetranslator';
    }

    private function addtag($domain, $message, $string) {
        return '<span class="'.$this->class.'" domain="'.$domain.'" key="'.$message.'">'.$string.'</span>';
    }


    private function title(\Twig_Environment $env, $string) {
        return twig_title_string_filter($env, $string);
    }


    public function itrans($message, array $arguments = array(), $domain = null, $locale = null) {
        return $this->addtag($domain, $message, call_user_func_array('parent::trans', func_get_args()));
    }

    public function itrans_title(\Twig_Environment $env, $message, array $arguments = array(), $domain = null, $locale = null) {
        return $this->addtag($domain, $message, $this->title($env, parent::trans($message, $arguments, $domain, $locale)));
    }

    public function itranschoice($message, $count, array $arguments = array(), $domain = null, $locale = null) {
        return $this->addtag($domain, $message, call_user_func_array('parent::transchoice', func_get_args()));
    }

    public function itranschoice_title(\Twig_Environment $env, $message, $count, array $arguments = array(), $domain = null, $locale = null) {
        return $this->addtag($domain, $message, $this->title($env, call_user_func_array('parent::transchoice', func_get_args())));
    }

}

