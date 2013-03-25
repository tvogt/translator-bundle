<?php

namespace Calitarus\TranslatorBundle\Tests\Controller;

use Calitarus\TranslatorBundle\Tests\SimpleTestCase;

use Calitarus\TranslatorBundle\Entity\Language;
use Calitarus\TranslatorBundle\Entity\Translation;


class LanguageEntityTest extends SimpleTestCase {
	// NOTE: this is mostly to complete paths that the normal game code never takes

	public function testBasics() {
		$test = new Language;
		$test->setName('german')->setCode('de');

		$this->assertNull($test->getId());
		$this->assertEquals('german', $test->getName());
		$this->assertEquals('de', $test->getCode());

		$trans = new Translation;
		$test->addTranslation($trans);
		$this->assertTrue($test->getTranslations()->contains($trans));
		$test->removeTranslation($trans);
		$this->assertTrue($test->getTranslations()->isEmpty());
	}


}

