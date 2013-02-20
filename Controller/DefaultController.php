<?php

namespace Calitarus\TranslatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Calitarus\TranslatorBundle\Form;
use Calitarus\TranslatorBundle\Entity;


/**
 * @Route("/")
 */
class DefaultController extends Controller {
    
   /**
     * @Route("/")
     * @Template
     */
	public function indexAction(Request $request) {
		$em = $this->getDoctrine()->getManager();

		$query = $em->createQuery('SELECT count(m) FROM Calitarus\TranslatorBundle\Entity\Message m');
		$total = $query->getSingleScalarResult();

		$query = $em->createQuery('SELECT l.id, l.code, l.name, count(t) as number FROM Calitarus\TranslatorBundle\Entity\Language l JOIN l.translations t GROUP BY l');
		$progress = $query->getResult();

		return array(
			'total' => $total,
			'progress' => $progress,
		);
	}

   /**
     * @Route("/contact")
     * @Template
     */
	public function contactAction() {
		return array();
	}

}
