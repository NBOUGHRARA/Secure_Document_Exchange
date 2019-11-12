<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\Profiles;
use App\Entity\Settings;

class UserFixtures extends Fixture
{

	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
	}

    public function load(ObjectManager $manager)
    {
        $rolesCode = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER'];
        $roles = ['super-admin', 'admin', 'user'];

        /*adding 3 users and 3 profiles*/
        foreach ($roles as $key => $role) {

            $user = new User();
            $user->setEmail($role . "@exemple.fr")
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'password'))
            ->setRoles([$rolesCode[$key]]);
            $manager->persist($user);

            $profiles = new Profiles();
            $profiles->setProfileCode($rolesCode[$key])
                    ->setProfileLabel($role);
            $manager->persist($profiles);
        }

        /*Adding permission for importFile case */
        $settings = new Settings();
        $settings->setCode('IMPORT_PERMISSIONS')
                ->setData(['ROLE_SUPER_ADMIN']);
        $manager->persist($settings);

        $manager->flush();
    }
}
