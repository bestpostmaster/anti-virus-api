<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Action;
use App\Entity\ActionRequested;
use App\Entity\HostedFile;
use App\Entity\User;
use App\Message\CommandRunnerMessage;
use App\Repository\ActionRepository;
use App\Repository\ActionRequestedRepository;
use App\Repository\HostedFileRepository;
use App\Service\FileConverterService;
use App\Service\VirusScannerService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class FilesController extends AbstractController
{
    protected const DEFAULT_LIMIT = 10;
    protected const MAX_LIMIT = 200;
    protected const DEFAULT_OFFSET = 0;
    private string $hostingDirectory;
    private string $actionsResultsDirectory;
    private ManagerRegistry $doctrine;

    public function __construct(string $hostingDirectory, ManagerRegistry $doctrine, string $actionsResultsDirectory)
    {
        $this->hostingDirectory = $hostingDirectory;
        $this->actionsResultsDirectory = $actionsResultsDirectory;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/api/files/upload", name="app_files_upload")
     */
    public function upload(Request $request, LoggerInterface $logger, MessageBusInterface $bus, ActionRepository $actionRepository, HostedFileRepository $hostedFileRepository): Response
    {
        if (empty($request->files) || !$request->files->get('file')) {
            throw new \Exception('No file sent');
        }

        $receivedFile = $request->files->get('file');
        $logger->info('Try to upload file : '.$receivedFile->getClientOriginalName());

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('No user logged in');
        }

        $name = md5(uniqid((string) mt_rand(), true)).'.'.strtolower($receivedFile->getClientOriginalExtension());
        $receivedFile->move($this->hostingDirectory, $name);

        if (!file_exists($this->hostingDirectory.$name)) {
            throw new \Exception('Upload error...');
        }

        $currentTime = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $currentUser = $this->getUser();
        $manager = $this->doctrine->getManager();
        $currentUser = $manager->find(User::class, $currentUser->getId());

        $fileSize = round(filesize($this->hostingDirectory.$name) / 1000000, 4);

        if (!$this->checkUserCanUpload($currentUser, $fileSize)) {
            return $this->json(['error' => '0073', 'message' => 'NotEnoughStorageSpace Exception']);
        }

        $file = new HostedFile();
        $file->setName($name);
        $file->setClientName($receivedFile->getClientOriginalName());
        $file->setUploadDate($currentTime);
        $file->setUser($this->getUser());
        $file->setSize($fileSize);
        $file->setScaned(false);
        $file->setDescription(($request->get('description') && $request->get('description') !== '') ? $request->get('description') : $receivedFile->getClientOriginalName());
        $file->setFilePassword($request->get('filePassword') ?? '');
        $file->setDownloadCounter(0);
        $file->setUrl(md5(uniqid((string) mt_rand(), true)).md5(uniqid((string) mt_rand(), true)));
        $file->setUploadLocalisation($_SERVER['REMOTE_ADDR'] ?? '');
        $file->setCopyrightIssue(false);
        $file->setConversionsAvailable('');
        $file->setVirtualDirectory('/');

        $scanAction = $actionRepository->findOneBy(['actionName' => 'Scan']);
        if (!$scanAction) {
            throw new \Exception('Please create scan action raw!');
        }

        $manager = $this->doctrine->getManager();
        $manager->persist($file);
        $manager->flush($file);

        $actionRequested = $this->createActionRequested($currentTime, $file, $scanAction);

        $manager = $this->doctrine->getManager();
        $manager->persist($actionRequested);
        $manager->flush($actionRequested);

        $this->increaseUserSpace($currentUser, $fileSize);

        // Without Messenger
        // $virusScannerService->scan($file);

        $bus->dispatch(new CommandRunnerMessage($actionRequested->getId()));

        $this->addActionsRequestedList($file, $hostedFileRepository);

        return $this->json($file, 200, [], ['groups' => 'file:read']);
    }

    /**
     * @Route("/api/files/edit-file-info/{fileId}", name="edit_file_info")
     */
    public function edit(Request $request, HostedFileRepository $hostedFileRepository, ManagerRegistry $doctrine): Response
    {
        $userId = $this->getUser()->getId();
        $id = $request->get('fileId');

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('No user logged in');
        }

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $file = $hostedFileRepository->findOneBy(['id' => $id]);
        } else {
            $file = $hostedFileRepository->findOneBy(['id' => $id, 'user' => $userId]);
        }

        if (!$file) {
            throw $this->createNotFoundException('Unknown file id : '.$request->get('fileId'));
        }

        $file = $this->hydrateFile($request, $file);
        $em = $doctrine->getManager();
        $em->persist($file);
        $em->flush();

        return $this->json($file, 200, [], ['groups' => 'file:read']);
    }

    /**
     * @Route("/api/files/add-action-on-files", name="add_action_on_files")
     */
    public function addAction(Request $request, HostedFileRepository $hostedFileRepository, ManagerRegistry $doctrine, ActionRepository $actionRepository, MessageBusInterface $bus): Response
    {
        $userId = $this->getUser()->getId();
        $data = json_decode($request->getContent(), true);
        $files = $data['files'] ?? null;
        $actionName = $data['actionName'] ?? null;
        $parameters = $data['parameters'] ?? null;

        if (!$files || !$actionName) {
            throw new \Exception('Please provide files and actionName');
        }

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('No user logged in');
        }

        $action = $actionRepository->findOneBy(['actionName' => $actionName]);

        if (!$action) {
            throw $this->createNotFoundException('Unknown action name : '.$request->get('actionName'));
        }

        foreach ($files as $id) {
            if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $file = $hostedFileRepository->findOneBy(['id' => $id]);
            } else {
                $file = $hostedFileRepository->findOneBy(['id' => $id, 'user' => $userId]);
            }

            if (!$file) {
                throw $this->createNotFoundException('Unknown file id : '.$request->get('fileId'));
            }

            $currentTime = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
            $actionRequested = $this->createActionRequested($currentTime, $file, $action, $parameters);

            $manager = $this->doctrine->getManager();
            $manager->persist($actionRequested);
            $manager->flush($actionRequested);

            $bus->dispatch(new CommandRunnerMessage($actionRequested->getId()));
        }

        return $this->json(['actionId' => $actionRequested->getId(), 'files' => $files, 'actionName' => $actionName]);
    }

    /**
     * @Route(
     *     "/{_locale}/free/{serviceDescription}",
     *     name="service",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi",
     *     }
     * )
     */
    public function service(Request $request): Response
    {
        return $this->render('app/index.html.twig', [
            'lang' => $request->get('_locale'),
        ]);
    }

    /**
     * @Route("/api/files/download/{url}", name="app_files_download")
     */
    public function download(Request $request, HostedFileRepository $hostedFileRepository, ManagerRegistry $doctrine): BinaryFileResponse
    {
        $userId = $this->getUser()->getId();
        $url = $request->get('url');

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('No user logged in');
        }

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $result = $hostedFileRepository->findOneBy(['url' => $url]);
        } else {
            $result = $hostedFileRepository->findOneBy(['url' => $url, 'user' => $userId]);
        }

        if (!$result) {
            throw $this->createNotFoundException('The file does not exist');
        }

        $response = new BinaryFileResponse($this->hostingDirectory.$result->getName());

        $extension = explode('.', $result->getName())[1] ?? '';
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $this->clean($result->getDescription()).'.'.$extension
        );

        $em = $doctrine->getManager();
        $result->setDownloadCounter($result->getDownloadCounter() + 1);
        $em->persist($result);
        $em->flush();

        return $response;
    }

    public function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    /**
     * @Route("/api/files/upload-from-url", name="app_files_upload_from_url")
     */
    public function uploadFromUrl(Request $request, LoggerInterface $logger, VirusScannerService $virusScannerService, MessageBusInterface $bus, ActionRepository $actionRepository, HostedFileRepository $hostedFileRepository): Response
    {
        if (!$request->get('url')) {
            throw new \Exception('No url sent');
        }

        $url = $request->get('url');
        $logger->info('Try to upload file : '.$url);

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('No user logged in');
        }

        $fileName = basename($url);

        // Url from AWS-S3
        if (isset(explode('?', $fileName)[1]) && explode('?', $fileName)[1] !== '') {
            $fileName = explode('?', $fileName)[0];
        }

        $name = md5(uniqid((string) mt_rand(), true)).'.'.strtolower(explode('.', $fileName)[1] ?? 'html');

        try {
            file_put_contents($this->hostingDirectory.$name, file_get_contents($url));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        if (!file_exists($this->hostingDirectory.$name)) {
            throw new \Exception('Upload error...');
        }

        $currentUser = $this->getUser();
        $manager = $this->doctrine->getManager();
        $currentUser = $manager->find(User::class, $currentUser->getId());
        $currentTime = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $fileSize = round(filesize($this->hostingDirectory.$name) / 1000000, 4);

        if (!$this->checkUserCanUpload($currentUser, $fileSize)) {
            return $this->json(['error' => '0073', 'message' => 'NotEnoughStorageSpace Exception']);
        }

        $file = new HostedFile();
        $file->setName($name);
        $file->setClientName($fileName ?? $url);
        $file->setUploadDate($currentTime);
        $file->setUser($this->getUser());
        $file->setSize($fileSize);
        $file->setScaned(false);
        $file->setDescription(empty($request->get('description')) ? $url : $request->get('description'));
        $file->setFilePassword($request->get('filePassword') ?? '');
        $file->setDownloadCounter(0);
        $file->setUrl(md5(uniqid((string) mt_rand(), true)).md5(uniqid((string) mt_rand(), true)));
        $file->setUploadLocalisation($_SERVER['REMOTE_ADDR'] ?? '');
        $file->setCopyrightIssue(false);
        $file->setConversionsAvailable('');
        $file->setVirtualDirectory('/');

        $scanAction = $actionRepository->findOneBy(['actionName' => 'Scan']);
        if (!$scanAction) {
            throw new \Exception('Please create scan action raw!');
        }

        $manager = $this->doctrine->getManager();
        $manager->persist($file);
        $manager->flush($file);

        $actionRequested = $this->createActionRequested($currentTime, $file, $scanAction);

        $manager = $this->doctrine->getManager();
        $manager->persist($actionRequested);
        $manager->flush($actionRequested);

        $this->increaseUserSpace($currentUser, $fileSize);

        // Without Messenger
        // $virusScannerService->scan($file);

        $bus->dispatch(new CommandRunnerMessage($actionRequested->getId()));

        $this->addActionsRequestedList($file, $hostedFileRepository);

        return $this->json($file, 200, [], ['groups' => 'file:read']);
    }

    /**
     * @Route("/api/files/file-info/{fileId}", name="app_file_info")
     */
    public function fileInfo(Request $request, HostedFileRepository $hostedFileRepository): Response
    {
        $userId = $this->getUser()->getId();
        $id = $request->get('fileId');

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('No user logged in');
        }

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $result = $hostedFileRepository->findOneBy(['id' => $id]);
        } else {
            $result = $hostedFileRepository->findOneBy(['id' => $id, 'user' => $userId]);
        }

        if (!$result) {
            throw $this->createNotFoundException('The file does not exist');
        }

        $this->addActionsRequestedList($result, $hostedFileRepository);

        return $this->json($result, 200, [], ['groups' => 'file:read']);
    }

    /**
     * @Route("/api/files/delete/{fileId}", name="app_files_delete", methods={"DELETE"})
     */
    public function deleteById(Request $request, HostedFileRepository $hostedFileRepository, ManagerRegistry $doctrine, ActionRepository $actionRepository, ActionRequestedRepository $actionRequestedRepository): Response
    {
        $userId = $this->getUser()->getId();
        $id = $request->get('fileId');

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('No user logged in');
        }

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $file = $hostedFileRepository->findOneBy(['id' => $id]);
        } else {
            $file = $hostedFileRepository->findOneBy(['id' => $id, 'user' => $userId]);
        }

        if (!$file) {
            throw $this->createNotFoundException('The file does not exist');
        }

        $this->deleteRelatedActions($file, $actionRequestedRepository);

        $manager = $doctrine->getManager();
        $currentUser = $manager->find(User::class, $this->getUser());
        $manager->remove($file);
        $manager->flush();

        $fullPath = $this->hostingDirectory.$file->getName();

        if (file_exists($fullPath)) {
            unlink($this->hostingDirectory.$file->getName());
        }

        $this->decreaseUserSpace($currentUser, $file->getSize());

        return $this->json(['fileId' => $id], 200);
    }

    /**
     * @Route("/api/files/convert/{fileId}/{convertTo}", name="app_files_convert")
     */
    public function convert(Request $request, FileConverterService $converter): Response
    {
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('No user logged in');
        }

        $fileId = $request->get('fileId');
        $convertTo = $request->get('convertTo');

        $resultPath = $converter->convert($fileId, $convertTo);

        return $this->json(['resultPath' => $resultPath], 200, [], ['groups' => 'file:read']);
    }

    /**
     * @Route("/api/files/search/{limit?}/{offset?}", name="search_files")
     */
    public function search(Request $request, HostedFileRepository $hostedFileRepository, string $hostingDirectory): Response
    {
        $data = json_decode($request->getContent(), true);
        $key = $data['key'];

        if (!$key || $key === '' || strlen($key) === 1) {
            throw new \Exception('Please provide a valid key. Exemple {"key":"File description"}');
        }

        $userId = $this->getUser()->getId();
        $this->hostingDirectory = $hostingDirectory;
        $limit = (int) ($request->get('limit') ?? self::DEFAULT_LIMIT);
        if ($limit > self::MAX_LIMIT) {
            $limit = self::DEFAULT_LIMIT;
        }
        $offset = (int) ($request->get('offset') ?? self::DEFAULT_OFFSET);
        $orderBy = ['id' => 'DESC'];

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->json($hostedFileRepository->searchBy(['key' => $key], $orderBy, $limit, $offset), 200, [], ['groups' => 'file:read']);
        }

        return $this->json($hostedFileRepository->searchBy(['userId' => $userId, 'key' => $key], $orderBy, $limit, $offset), 200, [], ['groups' => 'file:read']);
    }

    /**
     * @Route("/api/files/{limit?}/{offset?}", name="app_files")
     */
    public function index(Request $request, HostedFileRepository $hostedFileRepository, string $hostingDirectory): Response
    {
        $userId = $this->getUser()->getId();
        $this->hostingDirectory = $hostingDirectory;
        $limit = (int) ($request->get('limit') ?? self::DEFAULT_LIMIT);
        if ($limit > self::MAX_LIMIT) {
            $limit = self::DEFAULT_LIMIT;
        }
        $offset = (int) ($request->get('offset') ?? self::DEFAULT_OFFSET);
        $orderBy = ['id' => 'DESC'];

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $hostedFiles = $this->addActionsRequestedListToMultipleFiles($hostedFileRepository->findBy([], $orderBy, $limit, $offset), $hostedFileRepository);

            return $this->json($hostedFiles, 200, [], ['groups' => 'file:read']);
        }

        $hostedFiles = $this->addActionsRequestedListToMultipleFiles($hostedFileRepository->findBy(['user' => $userId], $orderBy, $limit, $offset), $hostedFileRepository);

        return $this->json($hostedFiles, 200, [], ['groups' => 'file:read']);
    }

    private function checkUserCanUpload(User $user, float $fileSize): bool
    {
        return $fileSize + $user->getTotalSpaceUsedMo() <= $user->getAuthorizedSizeMo();
    }

    private function increaseUserSpace(User $user, float $sizeToAdd): void
    {
        $manager = $this->doctrine->getManager();
        $user->setTotalSpaceUsedMo($user->getTotalSpaceUsedMo() + $sizeToAdd);
        $manager->persist($user);
        $manager->flush($user);
    }

    private function decreaseUserSpace(User $user, float $sizeToDeduct): void
    {
        $manager = $this->doctrine->getManager();
        $user->setTotalSpaceUsedMo($user->getTotalSpaceUsedMo() - $sizeToDeduct);
        $manager->persist($user);
        $manager->flush($user);
    }

    public function createActionRequested(\DateTime $currentTime, HostedFile $file, Action $scanAction, ?string $parameters = null): ActionRequested
    {
        $actionRequested = new ActionRequested();
        $actionRequested->setDateOfDemand($currentTime);
        $actionRequested->setActionParameters($parameters ?? '');
        $actionRequested->setHostedFileIds([['fileId' => $file->getId(), 'parameters' => '']]);
        $actionRequested->setAction($scanAction);
        $actionRequested->setUser($this->getUser());
        $actionRequested->setActionResults([]);

        return $actionRequested;
    }

    private function hydrateFile(Request $request, HostedFile $file)
    {
        $data = json_decode($request->getContent(), true);
        empty($data['description']) ? true : $file->setDescription($data['description']);
        empty($data['virtualDirectory']) ? true : $file->setVirtualDirectory($data['virtualDirectory']);
        empty($data['description']) ? true : $file->setDescription($data['description']);

        return $file;
    }

    private function addActionsRequestedList(HostedFile $hostedFile, HostedFileRepository $hostedFileRepository)
    {
        $result = $hostedFileRepository->findRelatedActions($hostedFile->getId());
        $hostedFile->setRelatedActions($result);
    }

    private function addActionsRequestedListToMultipleFiles(array $hostedFiles, HostedFileRepository $hostedFileRepository): array
    {
        foreach ($hostedFiles as $file) {
            $this->addActionsRequestedList($file, $hostedFileRepository);
        }

        return $hostedFiles;
    }

    private function deleteRelatedActions(HostedFile $file, ActionRequestedRepository $actionRepository)
    {
        $relatedActions = $actionRepository->findRelatedActions($file->getId());
        if (empty($relatedActions)) {
            return;
        }

        $actionRepository->deleteRelatedActions($file->getId());

        foreach ($relatedActions as $actionRequested) {
            $actionResultsDir = $this->actionsResultsDirectory.DIRECTORY_SEPARATOR.$actionRequested->getAction()->getActionName();
            if (is_dir($this->actionsResultsDirectory) && is_dir($actionResultsDir) && is_dir($actionResultsDir.DIRECTORY_SEPARATOR.$actionRequested->getId())) {
                $this->deleteDirectory($actionResultsDir.DIRECTORY_SEPARATOR.$actionRequested->getId());
            }
        }
    }

    private function deleteDirectory(string $dir): bool
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}
