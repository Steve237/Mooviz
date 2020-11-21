<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Avatar;
use App\Form\UsersType;
use App\Form\AvatarType;
use App\Repository\UsersRepository;
use App\Repository\AvatarRepository;
use App\Repository\VideosRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    public function userProfile(UsersRepository $repo, PlaylistRepository $repository, VideosRepository $videorepo, AvatarRepository $repoAvatar)
    {

        $user = $repo->findAll();

        $username = new Users();
        $username = $this->getUser();

        $avatar = new Avatar();

        $avatars = $repoAvatar->findByUser($username);

        $playlists = $repository->showVideoByUser($username);
        $videos = $videorepo->getVideos();

        return $this->render('user/mainprofile.html.twig', [
            'avatars' => $avatars,
            'user' => $user,
            'playlists' => $playlists,
            'videos' => $videos
        ]);
    }

   

    /**
     * @Route("/main/user", name="userprofile")
     */
    public function showNewVideo(VideosRepository $videorepo)
    {

        $videos = $videorepo->getVideos();

        return $this->render('user/userprofile.html.twig', [
            
            'videos' => $videos
        ]);
    }


    /**
     * @Route("/main/userplaylist", name="user_playlist")
     */
    public function showPlaylist(PlaylistRepository $repository, PaginatorInterface $paginator, Request $request)
    {    
            $user = new Users();
            $user = $this->getUser();
    
            $playlists = $paginator->paginate(
            $repository->showVideoByUser($user),
            $request->query->getInt('page', 1), /*page number*/
                20 /*limit per page*/
            );
            
            return $this->render('user/userplaylist.html.twig', [
                "playlists" => $playlists
            ]);
        
    }

    
    /**
     * @Route("/main/update_avatar", name="avatar_update")
     * //permet d'ajouter un avatar
     */
    public function userAvatar(Request $request, EntityManagerInterface $entity, AvatarRepository $repoAvatar) { 


        $avatar = new Avatar();

        $user = new Users();
        $user = $this->getUser();

        $form = $this->createForm(AvatarType::class, $avatar);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $avatarExist = $repoAvatar->findByUser($user);
            
            $file = $avatar->getAvatar();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $fileName);

            $avatar->setAvatar($fileName);
            $avatar->setUser($user);

            $entity->persist($avatar);
            $entity->flush();


            return $this->redirectToRoute("userprofile");

        }

        return $this->render('user/updateavatar.html.twig', [
            
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/main/update_avatar/{id}", name="update_image")
     * //permet de modifier un avatar
     */
    public function updateUser(Avatar $avatar, Request $request, EntityManagerInterface $entity) { 


        $form = $this->createForm(AvatarType::class, $avatar);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $file = $avatar->getAvatar();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $fileName);

            $avatar->setAvatar($fileName);
            $entity->persist($avatar);
            $entity->flush();


            return $this->redirectToRoute("userprofile");

        }

        return $this->render('user/updateavatar.html.twig', [
            
            'form' => $form->createView(),
            'avatar' => $avatar
        ]);
    }





    /**
     * @Route("/main/user_account/{id}", name="user_account")
     * //permet d'accéder et de modifier les infos du compte user
    */
    public function userAccount(Users $user, Request $request, EntityManagerInterface $entity, AvatarRepository $repoAvatar, UsersRepository $repoUser, UserPasswordEncoderInterface $encoder) { 

        
        $form = $this->createForm(UsersType::class, $user);

        $form->handleRequest($request);
        
        $avatars = $repoAvatar->findByUser($user);

        if($form->isSubmitted() && $form->isValid()) {
            
            $passwordCrypte = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordCrypte);
            $user->setConfirmPassword($passwordCrypte);

            $entity->persist($user);
            $entity->flush();

            return $this->redirectToRoute("userprofile");

        }

        return $this->render('user/useraccount.html.twig', [

            'form' => $form->createView(),
            'avatars' => $avatars
        ]);
    }


    /**
     *permet d'afficher avatar
     */
    public function showAvatar(AvatarRepository $repoAvatar)
    {

        $username = new Users();
        $username = $this->getUser();

        $avatars = $repoAvatar->findByUser($username);

        return $this->render('user/avatar.html.twig', [
            'avatars' => $avatars,
        ]);
    }
}
