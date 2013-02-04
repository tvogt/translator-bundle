<?php

namespace Calitarus\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class LanguageType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('code', 'text', array('label'=>'language code', 'required'=>true, 'attr' => array('size'=>2, 'maxlength'=>2)));
		$builder->add('name', 'text', array('label'=>'name', 'required'=>true, 'attr' => array('size'=>20, 'maxlength'=>40)));
	}
	
	public function getName() {
		return 'language';
	}
}
