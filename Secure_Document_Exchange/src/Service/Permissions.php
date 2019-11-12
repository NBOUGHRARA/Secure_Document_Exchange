<?php

namespace App\Service;

use Symfony\Component\Security\Core\Security;
use App\Entity\Settings;

/**
 * 
 */
class Permissions 
{
	private $security;

	public function __construct(Security $security)
	{
		$this->security = $security;
	}

	public function handleFilePermissions($files)
	{
		$permissions = [];
		foreach ($files as $file) {
			$permissions[$file->getId()] = [
				'canWrite' => $this->handlePermission($file->getCanWrite()),
				'canRead' => $this->handlePermission($file->getCanRead()),
				'canDelete' => $this->handlePermission($file->getCanDelete())
			];
		}
		return $permissions;
	}

	public function handlePermission($requiredRoles): ?bool
	{
		if (!is_array($requiredRoles)) {
			return false;
		}
		
		$user = $this->security->getUser();
		if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
			return true;
		}

		foreach ($requiredRoles as $requiredRole) {
			if (in_array($requiredRole, $user->getRoles())) {
				return true;
			}
		}
		return false;
	}

	public function handleImportPermissions()
	{
		$importPermissions = new Settings();
        $importPermissions->setCode("IMPORT_PERMISSIONS");
        return $importPermissions;
	}
}