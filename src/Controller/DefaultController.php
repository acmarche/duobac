<?php

namespace AcMarche\Duobac\Controller;

use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private SituationFamilialeRepository $situationFamilialeRepository;

    public function __construct(SituationFamilialeRepository $situationFamilialeRepository)
    {
        $this->situationFamilialeRepository = $situationFamilialeRepository;
    }

    /**
     * @Route("/",name="duobac_home")
     */
    public function index(): Response
    {
        return $this->render('@AcMarcheDuobac/default/index.html.twig', [
        ]);
    }

    /**
     * @Route("/contact",name="duobac_contact")
     *
     */
    public function contact(): Response
    {
        return $this->render('@AcMarcheDuobac/default/contact.html.twig', []);
    }

    public function menuUser(): Response
    {
        $user = $this->getUser();
        $years = [];

        if ($user) {
            $years = $this->situationFamilialeRepository->getAllYears($user);
        }

        return $this->render(
            '@AcMarcheDuobac/default/_menu_user.html.twig',
            [
                'years' => $years,
            ]
        );
    }
}
