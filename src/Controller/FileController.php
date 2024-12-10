<?php

namespace App\Controller;

use DateTime;
use App\Pagination;
use App\Entity\FileUpload;
use App\Form\FileUploadType;
use App\Repository\FileUploadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileController extends AbstractController
{
    private $_files;

    public function __construct(
        FileUploadRepository $files
    ) {
        $this->_files = $files;
    }

    #[Route('/dashboard/files', name: 'app_dashboard_file')]
    public function files(
        Request $request,
        Pagination $pagination
    ): Response {
        $page = $request->get('page') ?? 1;
        $listedFiles = $pagination->getEntityForPage($this->_files, $page);
        
        return $this->render('dashboard/files/index.html.twig', [
            'files' => $listedFiles,
            'maxPages' => $pagination->getMaxPages($this->_files->count())
        ]);
    }
    
    #[Route('/dashboard/files/create', name: 'app_dashboard_file_create')]
    public function create(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager
    ): Response
    {
        $post = new FileUpload();
        $form = $this->createForm(FileUploadType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $fileDatabaseEntry = $form->getData();

            $uploadedFile = $form->get('file')->getData();
            if($uploadedFile === null) {
                return $this->handleError('No file data was uploaded.');
            }

            $saveFileName = $slugger->slug($form->get('name')->getData());
            $newFileName = $saveFileName . '-' . uniqid(). '.' . $uploadedFile->guessExtension();

            try {
                /* move background image to a different folder */
                if($form->get('loginBackground')->getData() === true) {
                    $uploadedFile->move(
                        $this->getParameter('files_uploads_backgrounds_directory'),
                        $newFileName
                    );
                } else {
                    $uploadedFile->move(
                        $this->getParameter('files_uploads_directory'),
                        $newFileName
                    );
                }
            } catch(FileException $e) {
                return $this->handleError('Error while uploading file.');
            }

            /* save the file info in database */
            $fileDatabaseEntry->setName($newFileName);
            $fileDatabaseEntry->setCreatedOn(new DateTime());
            $fileDatabaseEntry->setCreatedBy($this->getUser());
            $entityManager->persist($fileDatabaseEntry);
            $entityManager->flush();

            $this->addFlash('success', sprintf('File "%s" has been uploaded.', $fileDatabaseEntry->getName()));

            return $this->redirectToRoute('app_dashboard_file');
        }

        return $this->render('dashboard/files/create.html.twig', [
            'form' => $form
        ]);
    }
    
    #[Route('/dashboard/files/delete', name: 'app_dashboard_file_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        if($request->get('object_id') === null) {
            return $this->handleError('No file was found.');
        }
        
        $file = $this->_files->find($request->get('object_id'));

        if($file === null) {
            return $this->handleError('No file was found.');
        }

        /* delete file in upload folder */
        $uploadDirectory = $this->getParameter('files_uploads_directory');
        if( file_exists($uploadDirectory . $file->getFileName()) &&
            is_writable($uploadDirectory . $file->getFileName())
        ) {
            unlink($uploadDirectory . $file->getFileName());
        }

        /* delete file in background folder */
        $backgroundUploadDirectory = $this->getParameter('files_uploads_backgrounds_directory');
        if( file_exists($backgroundUploadDirectory . $file->getFileName()) &&
            is_writable($backgroundUploadDirectory . $file->getFileName())
        ) {
            unlink($backgroundUploadDirectory . $file->getFileName());
        }

        $entityManager->remove($file);
        $entityManager->flush();

        $this->addFlash('success', sprintf('File "%s" has been deleted.', $file->getName()));

        return $this->redirect($request->headers->get('referer'));
    }
    
    protected function handleError(string $message): RedirectResponse 
    {
        $this->addFlash('error', $message);
        
        return $this->redirectToRoute('app_dashboard_file', [
            'files' => $this->_files->findAll()
        ]);
    }
}
