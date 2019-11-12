<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Form\DocumentType;
use App\Entity\Files;
use App\Entity\Settings;
use App\Service\FileUploader;
use App\Service\Permissions;
use App\Repository\FilesRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\File\File;

class HomeController extends AbstractController
{

    private $session;
    private $manager;
    private $filesRepo;
    private $permissionsService;
    private $settingsRepo;

    public function __construct(
        SessionInterface $session,
        FilesRepository $filesRepo,
        Permissions $permissionsService,
        SettingsRepository $settingsRepo,
        ObjectManager $manager

    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->filesRepo = $filesRepo;
        $this->permissionsService = $permissionsService;
        $this->settingsRepo = $settingsRepo;
    }

    /**
     * @Route("/", name="home_index")
     */
    public function index()
    {
        /*To control "Import File" in navBar*/
        if (!$this->session->get('canImportFile')) {
            $importFilePermissions = $this->settingsRepo->findOneBy(['code' => 'IMPORT_PERMISSIONS'])->getData();
            $this->session->set(
                'canImportFile',
                $this->permissionsService->handlePermission($importFilePermissions)
            );
        }

        $files = $this->filesRepo->getAvailableFiles();
        $permissions = $this->permissionsService->handleFilePermissions($files);
        return $this->render('home/index.html.twig', [
            'files' => $files,
            'permissions' => $permissions
        ]);
    }

    /**
     * @Route("/import", name="home_import")
     * @Route("/edit/{id}", name="home_edit")
     */
    public function import(Files $file = null, Request $request, FileUploader $fileUploader)
    {
        $isEdit = false;
        if ($file === null) {
            $importPermissions = $this->settingsRepo->findOneBy(['code' => 'IMPORT_PERMISSIONS'])->getData();
            $this->denyAccessWithoutPermission($importPermissions, 'Access Denied: You haven\'t access to import files !');
            $file = new Files();
        } else {
            $this->denyAccessWithoutPermission($file->getCanWrite(), 'Access Denied: You can\'t modify this file !');
            $isEdit = true;
        }

    	$form = $this->createForm(DocumentType::class, $file);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
    		$file->setUpdatedAt(new \DateTime())
    			->setUpdatedBy($this->getUser());

    		if (!$file->getId()) {
    		    /** @var UploadedFile $fileData */
                $fileData = $form["fileDirectory"]->getData();
                $newFilename = $fileUploader->upload($fileData);
                $file->setFileDirectory($newFilename);

    			$file->setCreatedAt(new \DateTime())
    				->setCreatedBy($this->getUser());
    		}

    		$this->manager->persist($file);
    		$this->manager->flush();

    		return $this->redirectToRoute('home_index');
    	}

    	return $this->render('home/import.html.twig',[
    		'file' => $form->createView(),
            'isEdit' => $isEdit
    	]);
    }

    /**
     * @Route("/delete/{id}", name="home_delete")
     */
    public function delete(Files $file)
    {
        $this->denyAccessWithoutPermission($file->getCanDelete(), 'Access Denied: You can\'t delete this file !');
        try {
            $file->setIsDeleted(true)
                ->setUpdatedAt(new \DateTime())
                ->setUpdatedBy($this->getUser());

            $this->manager->persist($file);
            $this->manager->flush();

            return $this->json([
                "code" => 200,
                "message" => "File successfully deleted : " . $file->getFileName()
            ], 200);
        } catch (Exception $e) {
            return $this->json([
                "code" => 403,
                "message" => "Something went wrong"
            ], 403);
        }
    }
}
