<?php

namespace Calitarus\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class DomainType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('name', 'text', array('label'=>'name', 'required'=>true, 'attr' => array('size'=>20, 'maxlength'=>40)));
	}

	public function getName() {
		return 'domain';
	}
}
