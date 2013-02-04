<?php

namespace Calitarus\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;


class TranslationsType extends AbstractType {

	private $messages;
	private $translations;
	private $defaults;

	public function __construct($messages, $translations, $defaults) {
		$this->messages = $messages;
		$this->translations = $translations;
		$this->defaults = $defaults;
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('strings', 'collection');
		foreach ($this->messages as $message) {
			$id = $message->getId();
			$default = $this->defaults->filter(function($default) use ($message) {
                return $default->getMessage() == $message;
            })->first();
			$translation = $this->translations->filter(function($translation) use ($message) {
                return $translation->getMessage() == $message;
            })->first();

			if ($translation) {
				$tid = $translation->getId();
				$content = $translation->getContent();
			} else {
				$tid=-1;
				$content = '';
			}
			$builder->get('strings')->add((string)$id, 'collection');
			$set = $builder->get('strings')->get((string)$id);
			$set->add("translation_id", 'hidden', array('data'=>$tid, 'label'=>$default->getContent()));
			$set->add("changed", 'checkbox', array('required'=>false, 'attr'=>array('class'=>'hidden')));
			if ($message->getLong()) {
				$set->add("content",'textarea', array(
					'label'=>$message->getKey(), 'data'=>$content, 'required'=>false, 'attr'=>array(
					'class'=>'edit', 'data-mid'=>$id, 'data-tid'=>$tid)));
			} else {
				$set->add("content",'text', array(
					'label'=>$message->getKey(), 'data'=>$content, 'required'=>false, 'attr'=>array(
					'class'=>'edit', 'data-mid'=>$id, 'data-tid'=>$tid, 'size'=>60, 'maxlength'=>200)));
			}
		}

	}

	public function getName() {
		return 'translations';
	}
}
