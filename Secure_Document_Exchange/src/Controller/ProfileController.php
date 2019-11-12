<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\PasswordUpdateType;
use App\Form\UserLocaleType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProfileController extends AbstractController
{

	private $passwordEncoder;
	private $manager;
    private $session;

	public function __construct(ObjectManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        SessionInterface $session
    )
	{
		$this->manager = $manager;
		$this->passwordEncoder = $passwordEncoder;
        $this->session = $session;
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

    /**
     * @Route("/profile/language", name="profile_language")
    */
    public function language(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserLocaleType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $manager->persist($user);
            $manager->flush();
            $this->session->set('_locale', $user->getLocale());
            return $this->redirectToRoute("profile_language");
        }
        return $this->render('profile/language.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
