<?php

namespace Calitarus\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;


class NewMessageType extends AbstractType {

/*
	private $default_language;

	public function __construct($default_language) {
		$this->default_language = $default_language;
	}
*/

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('key', 'text', array('label'=>'name', 'required'=>true, 'attr' => array('size'=>20, 'maxlength'=>40)));
		$builder->add('long', 'checkbox', array('label'=>'long (textarea)'));
/*
		$builder->add('language', 'entity', array(
			'label'=>'origin language',
			'required'=>true,
			'class'=>'CalitarusTranslatorBundle:language', 'property'=>'name',
			'data' => $this->default_language
			//'preferred_choices' => array($this->default_language)
		));
*/
		$builder->add('translation', 'textarea', array('label'=>'english text', 'required'=>true));
		$builder->add('remarks', 'textarea', array('label'=>'optional remarks', 'required'=>false));
	}

	public function getName() {
		return 'newmessage';
	}
}
