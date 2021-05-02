<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PlaylistController extends AbstractController
{
    
  
   
    /**
     * @Route("/main/playlist", name="playlistuser")
     */
    public function userPlaylist(PlaylistRepository $repository) {   
    
        
        $user = $this->getUser();

        // on récupère les vidéos de la playlist
        $playlists = $repository->showVideoByUser($user);

            // si aucune vidéo on affiche ce message
            if(empty($playlists)){

                $this->addFlash('no_channels', 'Vous n\'avez ajouté aucune vidéo à votre playlist.');
                return $this->redirectToRoute('allvideos'); 

            }

            // minimum de vidéos pour afficher button load more
            $loadMoreStart = 20;

        return $this->render('playlist/playlist.html.twig', [
            
            "playlists" => $playlists,
            "loadMoreStart" => $loadMoreStart
        ]);
    
    } 
    
    /**
     * @Route("/main/updateplaylist/user/{id}/video/{idvideo}", name="playlist")
     * @ParamConverter("user", options={"mapping": {"id" : "id"}})
    *  @ParamConverter("video", options={"mapping": {"idvideo" : "id"}})
     */
    public function removeToPlaylist(Users $user, Videos $video, EntityManagerInterface $entity, PlaylistRepository $playlistRepo) {

        
        $user = $this->getUser();

        //Permet de retirer vidéo d'une playlist
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
        
        //permet d'ajouter une vidéo à une playlist;
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

     /**
     * Permet de charger plus de vidéos dans la playlist
     * @Route("/main/loadMoreVideosInPlaylist/{start}", name="loadMoreVideosInPlaylist", requirements={"start": "\d+"})
     */
    public function loadMoreVideosInPlaylist(PlaylistRepository $repository, $start = 20)
    {   
        $user = $this->getUser();

        // on récupère les 20 prochaines vidéos
        $playlists = $repository->showVideoByUser($user);

        return $this->render('playlist/loadMoreInPlaylist.html.twig', [
            
            'playlists' => $playlists,
            'start' => $start
        ]);
    }
}
