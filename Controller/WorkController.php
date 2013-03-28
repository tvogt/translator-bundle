<?php

namespace Calitarus\TranslatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;

use Calitarus\TranslatorBundle\Form;
use Calitarus\TranslatorBundle\Entity;


/**
 * @Route("/work")
 */
class WorkController extends Controller {
    
   /**
     * @Route("/")
     * @Template
     */
	public function indexAction() {

		return array();
	}

   /**
     * @Route("/translate/{language_id}", requirements={"language_id"="\d+"})
     * @Template
     */
	public function translateAction($language_id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$language = $em->getRepository('Calitarus\TranslatorBundle\Entity\Language')->find($language_id);

		$stats = array();
		$domains = $em->getRepository('Calitarus\TranslatorBundle\Entity\Domain')->findAll();
		// FIXME: there must be a better, DQL ? way of getting these simple statistics
		foreach ($domains as $domain) {
			$translated = 0;
			$messages = $domain->getMessages();
			foreach ($messages as $message) {
				$translated += $message->getTranslations()->filter(function($m) use ($language) { return $m->getLanguage() == $language; })->count();
			}

			$stats[$domain->getId()] = array(
				'id' => $domain->getId(),
				'name' => $domain->getName(),
				'total' => $messages->count(),
				'translated' => $translated
			);
		}

		return array(
			'language' => $language,
			'stats' => $stats
		);
	}

   /**
     * @Route("/domain/{language_id}/{domain_id}", requirements={"language_id"="\d+", "domain_id"="\d+"})
     * @Template
     */
	public function domainAction($language_id, $domain_id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$domain = $em->getRepository('Calitarus\TranslatorBundle\Entity\Domain')->find($domain_id);

		$language = $em->getRepository('Calitarus\TranslatorBundle\Entity\Language')->find($language_id);
		$query = $em->createQuery('SELECT t, m FROM Calitarus\TranslatorBundle\Entity\Translation t JOIN t.message m WHERE t.language = :language AND m.domain = :domain');
		$query->setParameters(array('domain'=>$domain, 'language'=>$language));
		$translations = new ArrayCollection($query->getResult());

		if ($language_id=='en') {
			$defaults = $translations;
		} else {
			$default_language = $em->getRepository('Calitarus\TranslatorBundle\Entity\Language')->findOneByCode('en');
			$messages = new ArrayCollection($em->getRepository('Calitarus\TranslatorBundle\Entity\Message')->findByDomain($domain_id));
			$query = $em->createQuery('SELECT t, m FROM Calitarus\TranslatorBundle\Entity\Translation t JOIN t.message m WHERE t.language = :language AND m.domain = :domain');
			$query->setParameters(array('domain'=>$domain, 'language'=>$default_language));
			$defaults = new ArrayCollection($query->getResult());			
		}

		$form = $this->createForm(new Form\TranslationsType($messages, $translations, $defaults));
		if ($request->isMethod('POST')) {
			$form->bind($request);
			if ($form->isValid()) {
				$data = $form->getData();
				$suggestions = 0;
				foreach ($data['strings'] as $message_id=>$set) {
					if ($set['changed']) {
						$translation_id = $set['translation_id'];
						if ($set['translation_id']==-1) {
							$message = $messages->filter(function($m) use ($message_id) { return $m->getId() == $message_id; })->first();
							if ($message) {
								$translation = new Entity\Translation();
								$translation->setContent($set['content']);
								$translation->setLanguage($language);
								$translation->setMessage($message);
								$translation->setBy($this->getUser());
								$em->persist($translation);
								$suggestions++;
							} else {
								echo "can't find message $message_id"; exit;
							}
						} else {
							$translation = $translations->filter(function($t) use ($translation_id) { return $t->getId() == $translation_id; })->first();
							if ($translation) {
								$suggestion = new Entity\Suggestion();
								$suggestion->setContent($set['content']);
								$suggestion->setTranslation($translation);
								$suggestion->setBy($this->getUser());
								$em->persist($suggestion);
								$suggestions++;
							} else {
								echo "can't find translation $translation_id"; exit;
							}
						}
					}
				}
				if ($suggestions>0) {
					$em->flush();
					$this->get('session')->getFlashBag()->add('notice', "$suggestions new suggestions added. Thank you.");
				}
			}
		}

		return array(
			'language' => $language,
			'domain' => $domain,
			'messages' => $messages,
			'form' => $form->createView(),
		);
	}

   /**
     * @Route("/suggestions")
     * @Template
     */
   public function suggestionsAction(Request $request) {
   	$translation_id = $request->query->get('translation_id');
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery('SELECT s.id, s.content, SUM(v.type) as votes FROM Calitarus\TranslatorBundle\Entity\Suggestion s LEFT JOIN s.votes v WHERE s.translation = :translation GROUP BY s');
		$query->setParameter('translation', $translation_id);
		$suggestions = $query->getArrayResult();
		return array('suggestions'=>$suggestions);
   }

   /**
     * @Route("/vote")
     */
   public function voteAction(Request $request) {
   	$type = $request->request->get('type');
   	$for = $request->request->get('for');

		$em = $this->getDoctrine()->getManager();
		$suggestion = $em->getRepository('Calitarus\TranslatorBundle\Entity\Suggestion')->find($for);
		if ($suggestion) {
	   	$vote = $em->getRepository('Calitarus\TranslatorBundle\Entity\Vote')->findOneBy(array('by'=>$this->getUser(), 'for'=>$suggestion));
	   	if (!$vote) {
	   		$vote = new Entity\Vote();
	   		$vote->setBy($this->getUser());
	   		$vote->setFor($suggestion);
	   		$em->persist($vote);
	   	}
   		switch ($type) {
   			case 'up':		$vote->setType(1); break;
   			case 'down':	$vote->setType(-1); break;
   		}
	   	$em->flush();
		}


		return new Response();
   }

   /**
     * @Route("/accept")
     */
   public function acceptAction(Request $request) {
   	$for = $request->request->get('for');
   	if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
			throw new AccessDeniedException('not admin');
   	}

		$em = $this->getDoctrine()->getManager();
		$suggestion = $em->getRepository('Calitarus\TranslatorBundle\Entity\Suggestion')->find($for);
		if ($suggestion) {
			$translation = $suggestion->getTranslation();
			$translation->setContent($suggestion->getContent());
			$translation->setBy($suggestion->getBy());
			foreach ($suggestion->getVotes() as $vote) {
				$em->remove($vote);
			} 
			$em->remove($suggestion);
	   	$em->flush();
		}

		return new Response();
   }

}
