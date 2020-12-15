<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use App\Repository\AvatarRepository;
use App\Repository\UsersRepository;
use App\Repository\VideosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FollowingController extends AbstractController
{
    /**
     * @Route("/following/{id}", name="following")
     * //permet de s'abonner à un user
     */
    public function follow(Users $userToFollow, EntityManagerInterface $entity)
    {
        $currentUser = $this->getUser();

        if($userToFollow->getId() !== $currentUser) {

            $currentUser->getFollowing()->add($userToFollow);

            $entity->flush();
            
            return $this->json([
                'code' => 200, 
                'message' => 'user ajouté'
            ], 200);
        


        }

        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/unfollowing/{id}", name="unfollowing")
     * //permet de se désabonner
     */
    public function unfollow(Users $userToUnFollow, EntityManagerInterface $entity)
    {
        $currentUser = $this->getUser();

            $currentUser->getFollowing()
            ->removeElement($userToUnFollow);

            $entity->flush();

            return $this->json([
                'code' => 200, 
                'message' => 'user ajouté'
            ], 200);
    }

     /**
     * @Route("/followed_videos_list", name="followed_videos_list")
     * //permet d'accéder à la liste des vidéos des chaînes auxquelles on s'est abonné
     */
    public function showFollowingVideo(TokenStorageInterface $tokenStorage, VideosRepository $videorepo, PaginatorInterface $paginator, Request $request) {

        $currentUser = $tokenStorage->getToken()->getUser();

        if($currentUser instanceof Users) {
            
            //recupère liste des vidéos des utilisateurs auxquels il est abonné
            
            $videos = $paginator->paginate(
                $videorepo->findAllByUsers($currentUser->getFollowing()),
                $request->query->getInt('page', 1), /*page number*/
                20 /*limit per page*/

            );
        
        } else {

            return $this->redirectToRoute('home');
        }

        $following_videos = $videorepo->findAllByUsers($currentUser->getFollowing());

        if(empty($following_videos)) {

            $this->addFlash('no_channels', 'Vous n\'etes abonné à aucune chaine pour le moment, veuillez vous abonnez afin de suivre vos contenus préférés.');
            return $this->redirectToRoute('allvideos');

        }


        return $this->render('following/following_videos.html.twig', [
            'videos' => $videos,
     
            ]);
    
    }

    /**
     * @Route("/channels", name="channels_list")
     * //permet d'accéder à la liste des chaînes auxquelles on s'est abonné
     */
    public function listChannel(AvatarRepository $repoAvatar, UsersRepository $repoUser, Request $request, PaginatorInterface $paginator){

        $username = new Users();
        
        $user = $this->getUser();

        $follow = $user->getFollowing();
        
        $videos = new Videos();

        $avatars = $repoAvatar->findByUser($follow);

        $userChannel = $paginator->paginate(
            $repoUser->findUser($follow),
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/

        );

        $channelUser = $repoUser->findUser($user);

        //Si user n'a aucune chaine renvoie message flash pour l'informer
        if(empty($channelUser)) {

            $this->addFlash('no_channels', 'Vous n\'etes abonné à aucune chaine pour le moment, veuillez vous abonnez afin de suivre vos contenus préférés.');
            return $this->redirectToRoute('allvideos');

        }

        return $this->render('following/following_channel.html.twig', [
            "userChannel" => $userChannel,
            "user" => $user,
            "videos" => $videos,
            "avatars" => $avatars
        ]);
    }

}
