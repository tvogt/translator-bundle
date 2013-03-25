<?php

namespace Calitarus\TranslatorBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


abstract class SimpleTestCase extends WebTestCase {

	protected $em;
	protected $doctrine;

	protected $client;

	public function setUp() { 
		$kernel = static::createKernel();
		$kernel->boot();

		$this->doctrine = $kernel->getContainer()->get('doctrine');
		$this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
		$this->em->beginTransaction(); // doesn't really work, though
		$this->client = static::createClient();
	}

	public function tearDown() {
		$this->em->rollback(); // see above - would've been too easy, wouldn't it?
		$this->em->close();
		$this->doctrine->getConnection()->close();
		parent::tearDown();
	}

}
