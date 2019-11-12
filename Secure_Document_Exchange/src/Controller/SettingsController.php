<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\UserRepository;
use App\Repository\ProfilesRepository;
use App\Repository\SettingsRepository;
use App\Entity\User;
use App\Form\UserType;
use App\Form\RegistrationType;
use App\Form\ImportPermissionsType;
use App\Form\ProfilesType;
use App\Service\Permissions;
use App\Entity\Profiles;

/**
 * Require ROLE_SUPER_ADMIN for *every* controller method in this class.
 * @IsGranted("ROLE_SUPER_ADMIN", message="Only super admins has access to settings !!")
 */
class SettingsController extends AbstractController
{
	private $passwordEncoder;
	private $manager;

	public function __construct(ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->manager = $manager;
		$this->passwordEncoder = $passwordEncoder;
	}

    /**
     * @Route("/settings/users", name="settings_users")
     */
    public function users(UserRepository $userRepo)
    {
    	$users = $userRepo->findAll();
        return $this->render('settings/users.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/settings/import/permissions", name="settings_import_permissions")
     */
    public function importPermissions(Request $request, Permissions $permissionsService, SettingsRepository $settingsRepo)
    {
        $importPermissions = $settingsRepo->findOneBy(['code' => 'IMPORT_PERMISSIONS']);
        if (empty($importPermissions)) {
            $importPermissions = $permissionsService->handleImportPermissions();
        }

        $form = $this->createForm(ImportPermissionsType::class, $importPermissions);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($importPermissions);
            $this->manager->flush();
            return $this->redirectToRoute("home_index");
        }
    	return $this->render('settings/import_permissions.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/settings/roles", name="settings_roles")
     */
    public function roles(ProfilesRepository $profilesRepo, Request $request)
    {
        $roles = $profilesRepo->findAll();
        $profile = new Profiles();
        $form = $this->createForm(ProfilesType::class, $profile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($profile);
            $this->manager->flush();
            return $this->redirectToRoute('settings_roles');
        }
        return $this->render('settings/roles.html.twig', [
            'roles' => $roles,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/settings/edit/user/{id}", name="settings_edit_user")
     */
    public function editUser(User $user, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'you haven\'t access to settings !!');
    	$form = $this->createForm(UserType::class, $user);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
    		$this->manager->persist($user);
    		$this->manager->flush();

    		return $this->redirectToRoute('settings_users');
    	}

    	return $this->render('settings/editUser.html.twig',[
    		'userForm' => $form->createView(),
    		'user' => $user
    	]);
    }

    /**
     * @Route("/settings/add/user", name="settings_add_user")
     */
    public function addUser(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', 'settings', 'you haven\'t access to settings !!');
    	$user = new User();
    	$form = $this->createForm(RegistrationType::class, $user);
    	$form->handleRequest($request);
    	if ($form->isSubmitted() && $form->isValid()) {
    		$user->setPassword($this->passwordEncoder->encodePassword(
    			$user,
    			$user->getPassword())
    		);
    		$this->manager->persist($user);
    		$this->manager->flush();
    		return $this->redirectToRoute('settings_users');
    	}
    	return $this->render('settings/addUser.html.twig', [
    		'form' => $form->createView()
    	]);
    }

}
