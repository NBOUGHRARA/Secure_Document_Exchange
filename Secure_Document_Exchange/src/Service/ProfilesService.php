<?php

namespace App\Service;

use App\Repository\ProfilesRepository;

class ProfilesService
{
	private $profilesRepo;

	public function __construct(ProfilesRepository $profilesRepo)
	{
		$this->profilesRepo = $profilesRepo;
	}

	public function getProfilesForFormBuilder()
	{
		$choices = [];
        $profiles = $this->profilesRepo->findAll();
        foreach ($profiles as $profile) {
            $choices[$profile->getProfileLabel()] = $profile->getProfileCode();
        }

        return $choices;
	}
}