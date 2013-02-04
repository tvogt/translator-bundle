<?php

namespace Calitarus\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;


class NewTranslationType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('language', 'entity', array(
			'label'=>'language',
			'required'=>true,
			'class'=>'CalitarusTranslatorBundle:Language', 'property'=>'name', 
			'query_builder'=>function(EntityRepository $er) {
			return $er->createQueryBuilder('l')->leftJoin('l.translations', 't')->where('t IS NULL');
		}));
	}

	public function getName() {
		return 'newtranslation';
	}
}
