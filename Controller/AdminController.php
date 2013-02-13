<?php

namespace Calitarus\TranslatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Calitarus\TranslatorBundle\Form;
use Calitarus\TranslatorBundle\Entity;


/**
 * @Route("/admin")
 */
class AdminController extends Controller {
    
   /**
     * @Route("/")
     * @Template
     */
	public function indexAction() {

		return array();
	}

	/**
     * @Route("/domains")
     * @Template
     */
	public function domainsAction(Request $request) {
		$em = $this->getDoctrine()->getManager();

		$form = $this->createForm(new Form\DomainType());
		if ($request->isMethod('POST')) {
			$form->bind($request);
			if ($form->isValid()) {
				$data = $form->getData();
				$domain = new Entity\Domain();
				$domain->setName($data['name']);
				$em->persist($domain);
				$em->flush();
			}
		}

		$domains = $em->getRepository('Calitarus\TranslatorBundle\Entity\Domain')->findAll();

		return array(
			'domains' => $domains,
			'form' => $form->createView()
		);
	}

	/**
     * @Route("/languages")
     * @Template
     */
	public function languagesAction(Request $request) {
		$em = $this->getDoctrine()->getManager();

		$form = $this->createForm(new Form\LanguageType());
		if ($request->isMethod('POST')) {
			$form->bind($request);
			if ($form->isValid()) {
				$data = $form->getData();
				$lang = new Entity\Language();
				$lang->setCode($data['code']);
				$lang->setName($data['name']);
				$em->persist($lang);
				$em->flush();
			}
		}

		$languages = $em->getRepository('Calitarus\TranslatorBundle\Entity\Language')->findAll();

		return array(
			'languages' => $languages,
			'form' => $form->createView()
		);
	}

	/**
     * @Route("/messages/{domain_id}", requirements={"domain_id"="\d+"})
     * @Template
     */
	public function messagesAction($domain_id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$domain = $em->getRepository('Calitarus\TranslatorBundle\Entity\Domain')->find($domain_id);
		$default_language = $em->getRepository('Calitarus\TranslatorBundle\Entity\Language')->findOneByCode('en');

		$form = $this->createForm(new Form\NewMessageType());
		if ($request->isMethod('POST')) {
			$form->bind($request);
			if ($form->isValid()) {
				$data = $form->getData();
				$message = new Entity\Message();
				$message->setKey($data['key']);
				$message->setLong($data['long']);
				$message->setRemarks($data['remarks']);
				$message->setDomain($domain);
				$em->persist($message);
				$translation = new Entity\Translation();
				$translation->setContent($data['translation']);
				$translation->setLanguage($default_language);
				$translation->setMessage($message);
				$em->persist($translation);
				$em->flush();
			}
		}

		$query = $em->createQuery('SELECT m.key, m.remarks, t.content FROM Calitarus\TranslatorBundle\Entity\Message m JOIN m.translations t WHERE t.language = :lang');
		$query->setParameter('lang', $default_language);
		$messages = $query->getResult();

		return array(
			'domain' => $domain,
			'messages' => $messages,
			'form' => $form->createView()
		);
	}


}
