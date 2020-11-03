<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PlaylistController extends AbstractController
{
    
  
   
    /**
     * @Route("/main/playlist", name="playlistuser")
     */
    public function userPlaylist(PlaylistRepository $repository) {   
    
        $user = new Users();
        $user = $this->getUser();

        $playlists = $repository->showVideoByUser($user);
        return $this->render('playlist/index.html.twig', [
            "playlists" => $playlists
        ]);
    
    } 
    
    /**
     * @Route("/main/addplaylist/user/{id}/video/{idvideo}", name="playlist")
     * @ParamConverter("user", options={"mapping": {"id" : "id"}})
    *  @ParamConverter("video", options={"mapping": {"idvideo" : "id"}})
     */
    public function index(Users $user, Videos $video, EntityManagerInterface $entity, PlaylistRepository $playlistRepo) {

        
        $user = $this->getUser();

        //Permet de supprimer le like d'un utilisateur
        if ($video->isSelectedByUser($user)) {

            $selectedVideo = $playlistRepo->findOneBy([
                
                'video' => $video,
                'user' => $user
            ]);

            $entity->remove($selectedVideo);
            $entity->flush();

            return $this->json([

                'code' => 200,
                'message' => 'vidéo bien supprimé'
            ], 200);

        }
        
        
        $selectedVideo = new Playlist();
        $selectedVideo->setVideo($video);
        $selectedVideo->setUser($user);

        $entity->persist($selectedVideo);
        $entity->flush();

        return $this->json([
            'code' => 200, 
            'message' => 'video ajouté'
        ], 200);
    
    
    }



}
