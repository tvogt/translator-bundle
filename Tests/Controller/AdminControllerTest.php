<?php

namespace Calitarus\TranslatorBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AdminControllerTest extends WebTestCase {

	private $pages = array(
		array('url' => '/en/translator/admin/domains', 'content'=>'Create New Domain'),
		array('url' => '/en/translator/admin/languages', 'content'=>'Start A New Translation'),
	);

	private function login($client) {
		$client->followRedirects();
		$crawler = $client->request('GET', '/en/login');
		$form = $crawler->selectButton('_submit')->form(array(
			'_username'  => 'admin',
			'_password'  => 'admin'
			));		
		$client->submit($form);
	}

	public function testBasics() {
		$client = static::createClient();
		$this->login($client);

		foreach ($this->pages as $page) {
			$crawler = $client->request('GET', $page['url']);
			$this->assertTrue($client->getResponse()->isSuccessful(), "page ".$page['url']." failed to load");
			$this->assertTrue($crawler->filter('html:contains("'.$page['content'].'")')->count() > 0, "page ".$page['url']." content failure");
		}
	}
}
