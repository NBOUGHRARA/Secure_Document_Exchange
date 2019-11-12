<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\PasswordUpdateType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{

	private $passwordEncoder;
	private $manager;

	public function __construct(ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->manager = $manager;
		$this->passwordEncoder = $passwordEncoder;
	}

    /**
     * @Route("/profile/edit/pass", name="profile_editPass")
     */
    public function editPass(Request $request)
    {
    	$user = $this->getUser();

    	$form = $this->createForm(PasswordUpdateType::class, $user);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
    		$user->setPassword($this->passwordEncoder->encodePassword(
    			$user,
    			$user->getPassword())
    		);
    		$this->manager->persist($user);
    		$this->manager->flush();
    		return $this->redirectToRoute('home_index');
    	}
        return $this->render('profile/editPass.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
