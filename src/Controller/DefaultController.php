<?php

namespace AcMarche\Duobac\Controller;

use AcMarche\Duobac\Manager\SituationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var SituationManager
     */
    private $situationManager;

    public function __construct(SituationManager $situationManager)
    {
        $this->situationManager = $situationManager;
    }

    /**
     * @Route("/",name="duobac_home")
     *
     */
    public function index()
    {
        return $this->render('@AcMarcheDuobac/default/index.html.twig', []);
    }

    /**
     * @Route("/contact",name="duobac_contact")
     *
     */
    public function contact()
    {
        return $this->render('@AcMarcheDuobac/default/contact.html.twig', []);
    }

    public function menuUser()
    {
        $user = $this->getUser();
        $years = [];

        if ($user) {
            $years = $this->situationManager->getAllYears($user);
        }

        return $this->render(
            '@AcMarcheDuobac/default/_menu_user.html.twig',
            [
                'years' => $years,
            ]
        );
    }
}
