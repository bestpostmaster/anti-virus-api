<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UsersController extends AbstractController
{
    protected const DEFAULT_LIMIT = 10;
    protected const DEFAULT_OFFSET = 0;
    protected const RESPONSE_ONE = '19';
    protected const RESPONSE_TWO = '17';
    private UserPasswordHasherInterface $passwordEncoder;
    private DenormalizerInterface $denormalizer;
    private string $webSiteName;
    private string $webSiteDomainName;
    private string $webSiteHomeUrl;
    private string $webSiteEmailAddress;
    private MailerInterface $mailer;
    private LoggerInterface $logger;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, DenormalizerInterface $denormalizer, string $webSiteName,
                                string $webSiteDomainName, string $webSiteHomeUrl, string $webSiteEmailAddress,
                                MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->denormalizer = $denormalizer;
        $this->webSiteName = $webSiteName;
        $this->webSiteDomainName = $webSiteDomainName;
        $this->webSiteHomeUrl = $webSiteHomeUrl;
        $this->webSiteEmailAddress = $webSiteEmailAddress;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @Route("/api/admin/user/{userId}", name="app_user_infos")
     */
    public function getUserInfos(Request $request, UserRepository $userRepository): Response
    {
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw new \Exception('Admin only !');
        }

        $result = $userRepository->findOneBy(['id' => $request->get('userId')]);
        if (!$result) {
            throw $this->createNotFoundException('Unknown user id : '.$request->get('userId'));
        }

        return $this->json($result, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/user/user/{userId}", name="get_my_infos")
     */
    public function getMyInfos(Request $request, UserRepository $userRepository): Response
    {
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('Not authorized !');
        }

        $currentUser = $this->getUser();

        if (!$currentUser || $currentUser->getId() !== (int) $request->get('userId')) {
            throw new \Exception('Not authorized !');
        }

        $result = $userRepository->findOneBy(['id' => $request->get('userId')]);
        if (!$result) {
            throw $this->createNotFoundException('Unknown user id : '.$request->get('userId'));
        }

        return $this->json($result, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/admin/edit-user/{userId}", name="edit_user")
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function editUserInfos(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine): Response
    {
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw new \Exception('Admin only !');
        }

        $user = $userRepository->findOneBy(['id' => $request->get('userId')]);
        if (!$user) {
            throw $this->createNotFoundException('Unknown user id : '.$request->get('userId'));
        }

        $user = $this->hydrateUserByAdmin($request, $user);
        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/user/edit-user/{userId}", name="edit_my_infos")
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function editMyInfos(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine): Response
    {
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('Not authorized !');
        }

        $currentUser = $this->getUser();

        if (!$currentUser || $currentUser->getId() !== (int) $request->get('userId')) {
            throw new \Exception('Not authorized !');
        }

        $user = $userRepository->findOneBy(['id' => $request->get('userId')]);

        if (!$user) {
            throw $this->createNotFoundException('Unknown user id : '.$request->get('userId'));
        }

        $user = $this->hydrateUser($request, $user);
        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/user/change-my-password/{userId}", name="api_change_my_password")
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function changeMyPassword(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine): Response
    {
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('Not authorized !');
        }

        $currentUser = $this->getUser();
        $data = (array) json_decode($request->getContent());
        if (!isset($data['password1'], $data['password2']) || $data['password1'] !== $data['password2']) {
            return $this->json(['error' => '151', 'field' => '', 'message' => 'Please check all fields', 'data' => $data], 200);
        }

        if (!$currentUser || $currentUser->getId() !== (int) $request->get('userId')) {
            throw new \Exception('Not authorized !');
        }

        $user = $userRepository->findOneBy(['id' => $request->get('userId')]);

        if (!$user) {
            throw $this->createNotFoundException('Unknown user id : '.$request->get('userId'));
        }

        $user = $this->hydrateUser($request, $user);
        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json(['status' => 'ok'], 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/user/delete-my-account/{userId}", name="api_delete_my_account")
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function deleteMyAccount(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine): Response
    {
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new \Exception('Not authorized !');
        }

        $currentUser = $this->getUser();
        $data = (array) json_decode($request->getContent());
        if (!isset($data['password1']) || $data['password1'] === '') {
            return $this->json(['error' => '151', 'field' => '', 'message' => 'Please check all fields', 'data' => $data], 200);
        }

        if (!$currentUser || $currentUser->getId() !== (int) $request->get('userId')) {
            throw new \Exception('Not authorized !');
        }

        $user = $userRepository->findOneBy(['id' => $request->get('userId')]);

        if (!$user) {
            throw $this->createNotFoundException('Unknown user id : '.$request->get('userId'));
        }
        if (!$this->passwordEncoder->isPasswordValid($user, $data['password1'])) {
            return $this->json(['error' => '207', 'field' => '', 'message' => 'Please check all fields', 'data' => $data], 200);
        }

        $this->sendDeleteAccountMail($user, $data['lang']);

        $user->setDeleteAccountRequested(true);
        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json(['status' => 'ok'], 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route(
     *     "/{_locale}/users/confirm-email-address/{secret}",
     *     name="confirm_email_address",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi",
     *     }
     * )
     */
    public function confirmEmailAddress(Request $request, ManagerRegistry $doctrine, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['secretTokenForValidation' => $request->get('secret')]);

        if (!$user) {
            throw $this->createNotFoundException('Unknown user id : '.$request->get('userId'));
        }

        $user->setEmailConfirmed(true);
        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return $this->render('app/confirm-email.html.twig', [
            'user' => $user,
            'lang' => $request->get('_locale'),
        ]);
    }

    /**
     * @Route("/api/admin/users/add", name="app_users_add")
     */
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return new Response('Admin only !', 401);
        }

        $data = json_decode($request->getContent());

        $user = new User();
        $user->setLogin($data->username);
        $user->setRoles($data->roles);
        $user->setRegistrationDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $user->setSecretTokenForValidation(md5(uniqid((string) mt_rand(), true)).md5(uniqid((string) mt_rand(), true)));
        $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            $data->password
        ));

        $manager = $doctrine->getManager();
        $manager->persist($user);
        $manager->flush($user);

        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route(
     *     "/{_locale}/users/register",
     *     name="app_users_register",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi",
     *     }
     * )
     */
    public function registerFromFront(Request $request, ManagerRegistry $doctrine, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        $data = (array) json_decode($request->getContent());

        if (!isset($data['email'], $data['password1'], $data['password2'], $data['response1'], $data['response2']) || $data['response1'] !== $this::RESPONSE_ONE || $data['response2'] !== $this::RESPONSE_TWO) {
            return $this->json(['error' => '140', 'field' => '', 'message' => 'Please check all fields', 'data' => $data], 200, [], ['status' => 'ko', 'message' => 'This email address already exists in our database']);
        }

        $usersWithSameMail = $userRepository->findBy(['email' => strtolower($data['email'])]);
        if (!empty($usersWithSameMail)) {
            return $this->json(['error' => '145', 'field' => 'email', 'message' => 'This email address already exists in our database', 'data' => $data], 200, [], ['status' => 'ko', 'message' => 'This email address already exists in our database']);
        }

        $user = new User();
        $user->setLogin($data['email']);
        $user->setEmail($data['email']);
        $user->setRoles([
            'ROLE_USER',
        ]);

        $user->setRegistrationDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $user->setPasswordDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $user->setSecretTokenForValidation(md5(uniqid((string) mt_rand(), true)).md5(uniqid((string) mt_rand(), true)));
        $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            $data['password1']
        ));

        $manager = $doctrine->getManager();
        $manager->persist($user);
        $manager->flush($user);

        $link = $this->webSiteHomeUrl.'/'.$request->get('_locale').'/users/confirm-email-address/'.$user->getSecretTokenForValidation();

        $email = (new TemplatedEmail())
            ->from($this->webSiteEmailAddress)
            ->to($user->getEmail())
            // ->cc('cc@example.com')
            // ->bcc('bcc@example.com')
            // ->replyTo('fabien@example.com')
            // ->priority(Email::PRIORITY_HIGH)
            ->subject('Activate your account')
            ->htmlTemplate('app/mails/confirm-email.html.twig')
            ->context([
                'link' => $link,
                'lang' => $request->get('_locale'),
            ]);

        $mailer->send($email);

        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/admin/users/{limit?}/{offset?}", name="app_users")
     */
    public function getUsers(Request $request, UserRepository $userRepository): Response
    {
        $limit = (int) ($request->get('limit') ?? self::DEFAULT_LIMIT);
        $offset = (int) ($request->get('offset') ?? self::DEFAULT_OFFSET);

        $users = $userRepository->findBy([], null, $limit, $offset);
        $this->denyAccessUnlessGranted('USER_VIEW_ALL', $users);

        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @return void
     */
    public function hydrateUserByAdmin(Request $request, User $user): User
    {
        $data = json_decode($request->getContent(), true);
        empty($data['email']) ? true : $user->setEmail($data['email']);
        empty($data['login']) ? true : $user->setLogin($data['login']);
        empty($data['roles']) ? true : $user->setRoles($data['roles']);

        empty($data['password']) ? true : $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            $data['password']
        ));

        empty($data['totalSpaceUsedMo']) ? true : $user->setTotalSpaceUsedMo($data['totalSpaceUsedMo']);
        empty($data['authorizedSizeMo']) ? true : $user->setAuthorizedSizeMo($data['authorizedSizeMo']);
        empty($data['phoneNumber']) ? true : $user->setPhoneNumber($data['phoneNumber']);
        empty($data['city']) ? true : $user->setCity($data['city']);
        empty($data['country']) ? true : $user->setCountry($data['country']);
        empty($data['zipCode']) ? true : $user->setZipCode($data['zipCode']);
        empty($data['preferredLanguage']) ? true : $user->setPreferredLanguage($data['preferredLanguage']);
        empty($data['typeOfAccount']) ? true : $user->setTypeOfAccount($data['typeOfAccount']);
        empty($data['description']) ? true : $user->setDescription($data['description']);
        empty($data['avatarPicture']) ? true : $user->setAvatarPicture($data['avatarPicture']);
        empty($data['dateOfBirth']) ? true : $user->setDateOfBirth($data['dateOfBirth']);
        empty($data['isBanned']) ? true : $user->setIsBanned($data['isBanned']);

        return $user;
    }

    public function hydrateUser(Request $request, User $user): User
    {
        $data = json_decode($request->getContent(), true);

        empty($data['password']) ? true : $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            $data['password']
        ));

        if (isset($data['password1']) && $data['password1'] !== '') {
            $user->setPassword($this->passwordEncoder->hashPassword(
                $user,
                $data['password1']
            ));

            $user->setPasswordDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        }

        empty($data['phoneNumber']) ? true : $user->setPhoneNumber($data['phoneNumber']);
        empty($data['city']) ? true : $user->setCity($data['city']);
        empty($data['country']) ? true : $user->setCountry($data['country']);
        empty($data['zipCode']) ? true : $user->setZipCode($data['zipCode']);
        empty($data['preferredLanguage']) ? true : $user->setPreferredLanguage($data['preferredLanguage']);
        empty($data['avatarPicture']) ? true : $user->setAvatarPicture($data['avatarPicture']);
        empty($data['dateOfBirth']) ? true : $user->setDateOfBirth($data['dateOfBirth']);

        if (isset($data['sendEmailAfterEachAction']) && is_bool($data['sendEmailAfterEachAction'])) {
            $user->setSendEmailAfterEachAction($data['sendEmailAfterEachAction']);
        }

        if (isset($data['sendEmailIfFileIsInfected']) && is_bool($data['sendEmailIfFileIsInfected'])) {
            $user->setSendEmailIfFileIsInfected($data['sendEmailIfFileIsInfected']);
        }

        if (isset($data['postUrlAfterAction']) && trim($data['postUrlAfterAction']) !== '') {
            $user->setPostUrlAfterAction(strtolower($data['postUrlAfterAction']));
        }

        if (isset($data['sendPostToUrlAfterEachAction']) && is_bool($data['sendPostToUrlAfterEachAction'])) {
            $user->setSendPostToUrlAfterEachAction($data['sendPostToUrlAfterEachAction']);
        }

        if (isset($data['sendPostToUrlIfFileIsInfected']) && is_bool($data['sendPostToUrlIfFileIsInfected'])) {
            $user->setSendPostToUrlIfFileIsInfected($data['sendPostToUrlIfFileIsInfected']);
        }

        return $user;
    }

    private function sendDeleteAccountMail(User $user, string $lang): void
    {
        $subject = 'Delete your account';
        $link = $this->webSiteHomeUrl.'/'.$lang.'/user/delete-my-account-confirmation/'.$user->getSecretTokenForValidation();

        if ($user->isSendEmailAfterEachAction()) {
            $email = (new TemplatedEmail())
                ->from($this->webSiteEmailAddress)
                ->to($user->getEmail())
                // ->cc('cc@example.com')
                // ->bcc('bcc@example.com')
                // ->replyTo('fabien@example.com')
                // ->priority(Email::PRIORITY_HIGH)
                ->subject($subject)
                ->htmlTemplate('app/mails/delete-account.html.twig')
                ->context([
                    'lang' => 'en',
                    'link' => $link,
                ]);

            try {
                $this->mailer->send($email);
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
    }
}
