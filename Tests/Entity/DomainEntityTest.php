<?php

namespace Calitarus\TranslatorBundle\Tests\Controller;

use Calitarus\TranslatorBundle\Tests\SimpleTestCase;

use Calitarus\TranslatorBundle\Entity\Domain;
use Calitarus\TranslatorBundle\Entity\Message;


class DomainEntityTest extends SimpleTestCase {
	// NOTE: this is mostly to complete paths that the normal game code never takes

	public function testBasics() {
		$test = new Domain;
		$test->setName('testname');

		$this->assertNull($test->getId());
		$this->assertTrue($test->getMessages()->isEmpty());
		$this->assertEquals('testname', $test->getName());

		$message = new Message;
		$test->addMessage($message);
		$this->assertTrue($test->getMessages()->contains($message));
		$test->removeMessage($message);
		$this->assertTrue($test->getMessages()->isEmpty());
	}


}

