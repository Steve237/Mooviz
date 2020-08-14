<?php

namespace App\DataFixtures;

use App\Entity\Videos;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class VideosFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {   

        $category1 = new Category();
        $category1->setCategoryname("Action");
        $category1->setImageCategory("genres/action.png");
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setCategoryname("Drame");
        $category2->setImageCategory("genres/drama.png");
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setCategoryname("Comedy");
        $category3->setImageCategory("genres/comedy.png");
        $manager->persist($category3);

        $category4 = new Category();
        $category4->setCategoryname("Romance");
        $category4->setImageCategory("genres/romance.png");
        $manager->persist($category4);

        $category5 = new Category();
        $category5->setCategoryname("Fantaisie");
        $category5->setImageCategory("genres/fantasy.png");
        $manager->persist($category5);

        $category6 = new Category();
        $category6->setCategoryname("Sci-Fi");
        $category6->setImageCategory("genres/sci-fi.png");
        $manager->persist($category6);

        $category7 = new Category();
        $category7->setCategoryname("Thriller");
        $category7->setImageCategory("genres/thriller.png");
        $manager->persist($category7);

        $category8 = new Category();
        $category8->setCategoryname("Western");
        $category8->setImageCategory("genres/western.png");
        $manager->persist($category8);

        $category9 = new Category();
        $category9->setCategoryname("Aventure");
        $category9->setImageCategory("genres/adventure.png");
        $manager->persist($category9);

        $category10 = new Category();
        $category10->setCategoryname("Animation");
        $category10->setImageCategory("genres/animation.png");
        $manager->persist($category10);

        $category11 = new Category();
        $category11->setCategoryname("Documentaire");
        $category11->setImageCategory("genres/documentary.png");
        $manager->persist($category11);

        // films action
        $video1 = new Videos();
        $video1->setVideoTitle("Birds of Prey et la fantabuleuse histoire de Harley Quinn")
               ->setVideoDescription("Lorsque Roman Sionis, l'ennemi le plus abominable de Gotham, et son fidèle acolyte Zsasz décident de s'en prendre à une certaine Cass, la ville est passée au peigne fin pour retrouver la trace de la jeune fille. Les parcours de Harley, de la Chasseuse, de Black Canary et de Renee Montoya se télescopent et ce quatuor improbable n'a d'autre choix que de faire équipe pour éliminer Roman.")
               ->setPublicationDate(new \DateTime('2020/06/06'))
               ->setVideoImage("action/birdsofprey.jpg")
               ->setVideoLink("films/action/birdsofprey.mp4")
               ->setVideoDuration(new \DateTime('01:49:00'))
               ->setCategory($category1);
                $manager->persist($video1);


        $video2 = new Videos();
        $video2->setVideoTitle("Bloodshot")
               ->setVideoDescription("Bloodshot est un ancien soldat doté de pouvoirs de régénération et de meta-morphing suite à l'injection de nanites dans son sang. Après avoir vu sa mémoire effacée à plusieurs reprises, il finit par découvrir qui il est et décide de se venger de ceux qui lui ont infligé cette expérience.")
               ->setPublicationDate(new \DateTime('2020/06/06'))
               ->setVideoImage("action/bloodshot.jpg")
               ->setVideoLink("films/action/bloodshot.mp4")
               ->setVideoDuration(new \DateTime('01:49:00'))
               ->setCategory($category1);
                
               $manager->persist($video2);
               
               
               
        $video3 = new Videos();
        $video3->setVideoTitle("Tyler Rake")
            ->setVideoDescription("Tyler Rake est un mercenaire intrépide qui travaille dans l'ombre. Il a été envoyé au Bangladesh par un puissant caïd mafieux incarcéré, pour sauver son fils qui a été enlevé. Bien qu'il soit un guerrier d'expérience, il réalise rapidement à quel point sa mission est bouleversante et dangereuse.")
            ->setPublicationDate(new \DateTime('2020/06/06'))
            ->setVideoImage("action/tyler_rake.jpg")
            ->setVideoLink("films/action/tyler_rake.mp4")
            ->setVideoDuration(new \DateTime('01:57:00'))
            ->setCategory($category1);
                       
            $manager->persist($video3);       
               
        $video4 = new Videos();
        $video4->setVideoTitle("Justice League Dark: Apokolips")
            ->setVideoDescription("Après ses deux tentatives infructueuses de conquérir la Terre, Darkseid et ses armées ont conquis d'innombrables mondes. Pour contrer cela, la Justice League, rejointe par de nouveaux membres Lex Luthor, John Constantine et son amant Zatanna, se rend à Apokolips pour le tuer tandis que les Teen Titans restent sur Terre pour le défendre.")
            ->setPublicationDate(new \DateTime('2020/06/06'))
            ->setVideoImage("animation/justiceleaguedark.jpg")
            ->setVideoLink("films/animation/justiceleaguedark.mp4")
            ->setVideoDuration(new \DateTime('01:30:00'))
            ->setCategory($category10);
                           
            $manager->persist($video4);             
               
            
        $video5 = new Videos();
        $video5->setVideoTitle("Polar")
            ->setVideoDescription("Un tueur à gages sur le point de prendre sa retraite doit reprendre du service quand son ancien boss envoie un jeune groupe de tueurs sans pitié à sa poursuite.")
            ->setPublicationDate(new \DateTime('2020/06/06'))
            ->setVideoImage("action/polar.jpg")
            ->setVideoLink("films/action/polar.mp4")
            ->setVideoDuration(new \DateTime('01:59:00'))
            ->setCategory($category1);
                               
            $manager->persist($video5);                  
            
            
        $video6 = new Videos();
        $video6->setVideoTitle("Mission : Impossible 6 - Fallout")
            ->setVideoDescription("Les meilleures intentions finissent souvent par se retourner contre vous. Dans ce film, Ethan Hunt, accompagné de son équipe de l'IMF Impossible Mission Force et de quelques fidèles alliées sont lancés dans une course contre la montre, suite au terrible échec d'une mission.")
            ->setPublicationDate(new \DateTime('2020/06/06'))
            ->setVideoImage("action/missionfallout6.jpg")
            ->setVideoLink("films/action/mission6.mp4")
            ->setVideoDuration(new \DateTime('02:28:00'))
            ->setCategory($category1);
                                   
            $manager->persist($video6);    
            

        $video7 = new Videos();
        $video7->setVideoTitle("IpMan 4")
            ->setVideoDescription("Ip Man 4 est un film biographique d'arts martiaux hongkongais 
            réalisé par Wilson Yip et produit par Raymond Wong. Il s'agit du quatrième film 
            de la série de films Ip Man, basé sur la vie du grand maître Wing Chun du même nom, 
            et mettant en vedette Donnie Yen.")
            ->setPublicationDate(new \DateTime('2020/06/06'))
            ->setVideoImage("action/ipman4.jpg")
            ->setVideoLink("films/action/ipman4.mp4")
            ->setVideoDuration(new \DateTime('01:45:00'))
            ->setCategory($category1);
            $manager->persist($video7);  

        
        $video8 = new Videos();
        $video8->setVideoTitle("Bad Boys For Life")
            ->setVideoDescription("Marcus Burnett est maintenant inspecteur de police. Mike Lowery est, quant à lui, en pleine crise de la quarantaine. Ils s'unissent à nouveau lorsqu'un mercenaire albanais, dont ils ont tué le frère, leur promet une importante prime.")
            ->setPublicationDate(new \DateTime('2020/06/06'))
            ->setVideoImage("action/badboysforlife.jpg")
            ->setVideoLink("films/action/badboysforlife.mp4")
            ->setVideoDuration(new \DateTime('01:45:00'))
            ->setCategory($category1);
            $manager->persist($video8);  
    

        $video9 = new Videos();
        $video9->setVideoTitle("John Wick 3 : Parabellum")
        ->setVideoDescription("John Wick a transgressé une règle fondamentale : il a tué à l’intérieur même de l’Hôtel Continental. « Excommunié », tous les services liés au Continental lui sont fermés et sa tête mise à prix. John se retrouve sans soutien, traqué par tous les plus dangereux tueurs du monde.")
            ->setPublicationDate(new \DateTime('2020/06/07'))
            ->setVideoImage("action/johnwick3.jpg")
            ->setVideoLink("films/action/johnwick3.mp4")
            ->setVideoDuration(new \DateTime('02:10:00'))
            ->setCategory($category1)
            ->setSliderImage("slider/johnwick3.jpg");
            $manager->persist($video9);  
                
            
        $video10 = new Videos();
        $video10->setVideoTitle("The Last Days of American Crime")
            ->setVideoDescription("Dans un avenir pas trop lointain, où en réponse finale au crime et au terrorisme, le gouvernement américain prévoit de diffuser un signal qui empêchera quiconque de sciemment enfreindre la loi.")
            ->setPublicationDate(new \DateTime('2020/06/15'))
            ->setVideoImage("action/lastdaysofcrime.jpg")
            ->setVideoLink("films/action/lastdaysofcrime.mp4")
            ->setVideoDuration(new \DateTime('02:30:00'))
            ->setCategory($category1);
            $manager->persist($video10);  
            
        $video11 = new Videos();
        $video11->setVideoTitle("Fast & Furious : Hobbs & Shaw")
            ->setVideoDescription("Depuis que Hobbs et Shaw se sont affrontés, les deux hommes font tout pour se nuire l'un à l'autre. Mais lorsque Brixton, un anarchiste génétiquement modifié, met la main sur une arme de destruction massive après avoir battu le meilleur agent du MI6 qui se trouve être la soeur de Shaw. Les deux ennemis de longue date vont devoir alors faire équipe pour faire tomber le seul adversaire capable de les anéantir.")
            ->setPublicationDate(new \DateTime('2020/06/15'))
            ->setVideoImage("action/fastandfurioushobbsandshaw.jpg")
            ->setVideoLink("films/action/fastandfurioushobbsandshaw.mp4")
            ->setVideoDuration(new \DateTime('02:16:00'))
            ->setCategory($category1);
            $manager->persist($video11);  
            
        $video12 = new Videos();
        $video12->setVideoTitle("Disturbing the Peace")
            ->setVideoDescription("Un chef de police d'une petite ville, qui n'a pas porté d'arme depuis qu'il a quitté les Rangers du Texas après une fusillade tragique, doit reprendre son arme pour combattre un gang de motards hors la loi qui a envahi la ville pour réaliser une série de braquages bien préparé et violent.")
            ->setPublicationDate(new \DateTime('2020/06/15'))
            ->setVideoImage("action/disturbingthepeace.jpg")
            ->setVideoLink("films/action/disturbingthepeace.mp4")
            ->setVideoDuration(new \DateTime('01:30:00'))
            ->setCategory($category1);
            $manager->persist($video12);  
                    
        $manager->flush();
    
        $video13 = new Videos();
        $video13->setVideoTitle("X-Men : Dark Phœnix")
            ->setVideoDescription("Lors d'une périlleuse mission spatiale, Jean est frappée par une force qui la change en l'un des mutants les plus puissants qui soient. En lutte contre elle-même, Jean Grey déchaîne ses pouvoirs, incapable de les comprendre ou de les maîtriser. Devenue incontrôlable et dangereuse pour ses proches, elle défait peu à peu les liens qui unissent les X-Men. Voici le point d'orgue de vingt années d'histoire des X-Men, et l'ultime confrontation entre la famille de mutants que nous avons appris à aimer et son pire ennemi : l'une des leurs..")
            ->setPublicationDate(new \DateTime('2020/06/15'))
            ->setVideoImage("action/xmandarkphenix.jpg")
            ->setVideoLink("films/action/darkphenix.mp4")
            ->setVideoDuration(new \DateTime('01:54:00'))
            ->setCategory($category1);
            $manager->persist($video13);  
                    
        $video14 = new Videos();
        $video14->setVideoTitle("John Wick 2")
            ->setVideoDescription("John Wick est forcé de sortir de sa retraite volontaire par un de ses ex-associés qui cherche à prendre le contrôle d’une mystérieuse confrérie de tueurs internationaux. Parce qu’il est lié à cet homme par un serment, John se rend à Rome, où il va devoir affronter certains des tueurs les plus dangereux du monde..")
            ->setPublicationDate(new \DateTime('2020/06/16'))
            ->setVideoImage("action/johnwick2.jpg")
            ->setVideoLink("films/action/johnwick2.mp4")
            ->setVideoDuration(new \DateTime('02:03:00'))
            ->setCategory($category1);
            $manager->persist($video14);  
                        
        $video15 = new Videos();
        $video15->setVideoTitle("The Predator")
            ->setVideoDescription("Les pires prédateurs de l'univers sont maintenant plus forts et plus intelligents que jamais, ils se sont génétiquement perfectionnés grâce à l'ADN d'autres espèces. Quand un jeune garçon déclenche accidentellement leur retour sur Terre, seul un équipage hétéroclite d'anciens soldats et un professeur de science contestataire peuvent empêcher l’extinction de la race humaine..")
            ->setPublicationDate(new \DateTime('2020/06/17'))
            ->setVideoImage("scifi/thepredator.jpg")
            ->setVideoLink("films/scifi/thepredator.mp4")
            ->setVideoDuration(new \DateTime('01:58:00'))
            ->setCategory($category6);
            $manager->persist($video15);  

        $video16 = new Videos();
        $video16->setVideoTitle("Lucy")
            ->setVideoDescription("Lucy, une jeune étudiante ordinaire, se fait kidnapper. À son réveil, elle découvre que les membres d'une organisation criminelle lui ont inséré un paquet de drogue dans l'estomac dans le but de lui faire passer la frontière. Mais lorsqu'à la suite d'un coup porté à son ventre le produit se déverse dans son corps et s'implante dans son système, la jeune femme en subit les étonnants effets. Cette substance synthétique lui permet de décupler ses capacités intellectuelles et physiques. Devenue un génie autant qu'une véritable machine de guerre, Lucy dispose désormais de pouvoirs illimités et surhumains....")
            ->setPublicationDate(new \DateTime('2020/06/17'))
            ->setVideoImage("scifi/lucy.jpg")
            ->setVideoLink("films/scifi/lucy.mp4")
            ->setVideoDuration(new \DateTime('01:30:00'))
            ->setCategory($category6);
            $manager->persist($video16);  
                                
        
        $video17 = new Videos();
        $video17->setVideoTitle("Venom")
            ->setVideoDescription("Eddie Brock est un journaliste d'enquête très connu à San Francisco. Après avoir fait déraper une entrevue avec Carlton Drake, le fondateur de la puissante compagnie Life Foundation, il perd son emploi. Quelques mois plus tard, quand une employée de Drake le rejoint afin de lui faire part des dangereuses expériences qui sont menées dans les laboratoires de la Life Foundation, Eddie se rend sur place pour investiguer. Il entrera alors en contact avec un symbiote venu d'une autre planète.")
            ->setPublicationDate(new \DateTime('2020/06/17'))
            ->setVideoImage("scifi/venom.jpg")
            ->setVideoLink("films/scifi/venom.mp4")
            ->setVideoDuration(new \DateTime('02:20:00'))
            ->setCategory($category6);
            $manager->persist($video17); 

        
        $video18 = new Videos();
        $video18->setVideoTitle("Terminator Dark Fate")
            ->setVideoDescription("Sarah Connor et la cyborg Grace doivent protéger la vie de Dani Ramos, une jeune femme persécutée par le Rev-9, un nouveau Terminator capable de séparer son squelette de l'armure liquide dont elle est équipée.")
            ->setPublicationDate(new \DateTime('2020/06/17'))
            ->setVideoImage("scifi/terminatordarkfate.jpg")
            ->setVideoLink("films/scifi/terminatordark.mp4")
            ->setVideoDuration(new \DateTime('02:08:00'))
            ->setCategory($category6);
            $manager->persist($video18); 
                            
        $video19 = new Videos();
        $video19->setVideoTitle("Captain Marvel")
            ->setVideoDescription("Carol Danvers va devenir l'une des super-héroïnes les plus puissantes de l'univers lorsque la Terre se révèle l'enjeu d'une guerre galactique entre deux races extraterrestres.")
            ->setPublicationDate(new \DateTime('2020/06/17'))
            ->setVideoImage("scifi/captainmarvel.jpg")
            ->setVideoLink("films/scifi/captainmarvel.mp4")
            ->setVideoDuration(new \DateTime('02:05:00'))
            ->setCategory($category6);
            $manager->persist($video19); 
        
        $video20 = new Videos();
        $video20->setVideoTitle("Spider-Man: Far From Home")
            ->setVideoDescription("L'araignée sympa du quartier décide de rejoindre ses meilleurs amis Ned, MJ, et le reste de la bande pour des vacances en Europe. Cependant, le projet de Peter de laisser son costume de super-héros derrière lui pendant quelques semaines est rapidement compromis quand il accepte à contrecoeur d'aider Nick Fury à découvrir le mystère de plusieurs attaques de créatures, qui ravagent le continent !")
            ->setPublicationDate(new \DateTime('2020/06/17'))
            ->setVideoImage("scifi/spidermanfar.jpg")
            ->setVideoLink("films/scifi/spidermanfar.mp4")
            ->setVideoDuration(new \DateTime('02:20:00'))
            ->setCategory($category6);
            $manager->persist($video20); 

        $video21 = new Videos();
        $video21->setVideoTitle("Black Panther")
            ->setVideoDescription("Après les événements qui se sont déroulés dans Captain America : Civil War, T'Challa revient chez lui prendre sa place sur le trône du Wakanda, une nation africaine technologiquement très avancée mais lorsqu'un vieil ennemi resurgit, le courage de T'Challa est mis à rude épreuve, aussi bien en tant que souverain qu'en tant que Black Panther. Il se retrouve entraîné dans un conflit qui menace non seulement le destin du Wakanda mais celui du monde entier.")
            ->setPublicationDate(new \DateTime('2020/06/17'))
            ->setVideoImage("scifi/blackpanther.jpg")
            ->setVideoLink("films/scifi/blackpanther.mp4")
            ->setVideoDuration(new \DateTime('02:15:00'))
            ->setCategory($category6);
            $manager->persist($video21); 

        $video22 = new Videos();
        $video22->setVideoTitle("Terminator Genisys")
            ->setVideoDescription("Le leader de la résistance John Connor envoie le sergent Kyle Reese dans le passé pour protéger sa mère, Sarah Connor et préserver l'avenir de l'humanité. Des événements inattendus provoquent une fracture temporelle et Sarah et Kyle se retrouvent dans une nouvelle version du passé. Ils y découvrent un allié inattendu : le Guardian. Ensemble, ils doivent faire face à un nouvel ennemi. La menace a changé de visage.")
            ->setPublicationDate(new \DateTime('2020/06/17'))
            ->setVideoImage("scifi/terminatorgenese.jpg")
            ->setVideoLink("films/scifi/termingenesis.mp4")
            ->setVideoDuration(new \DateTime('02:06:00'))
            ->setCategory($category6);
            $manager->persist($video22); 
                                        
        
        $video23 = new Videos();
        $video23->setVideoTitle("PSYCHO-PASS Sinners of the System: Case.3 - On the other side of love and hate")
            ->setVideoDescription("Après l’incident survenu en 2116 dans l’Union de l’Asie du Sud-Est (SEAUn), Shinya Kogami reprend son voyage vagabond. Dans un petit pays d’Asie du Sud, Kogami sauve un bus de réfugiés attaqués par les forces armées de guérilla. Parmi les réfugiés se trouve une jeune femme du nom de Tenzin, qui supplie Kogami de lui apprendre à se venger de l’ennemi. Que voient la jeune fille qui veut se venger et l’homme qui a exigé la vengeance lorsqu’ils contemplent le bord d’un monde d’où il n’y a pas d’échappatoire ?")
            ->setPublicationDate(new \DateTime('2020/06/26'))
            ->setVideoImage("animation/psychopass3.jpg")
            ->setVideoLink("films/animation/psychopass3.mp4")
            ->setVideoDuration(new \DateTime('01:08:00'))
            ->setCategory($category10);
            $manager->persist($video23); 


        $video24 = new Videos();
        $video24->setVideoTitle("Dragon Ball Z : La Résurrection de ‘F’")
            ->setVideoDescription("Une Terre où régnait la paix. Cependant, des survivants de l’armée de Freezer, Sorbet et Tagoma arrivent sur la planète. Leur but est de ressusciter Freezer avec les Dragon Balls. Leur terrible souhait est accordé, et le « F » qui avait organisé sa vengeance sur les Saiyans est revenu à la vie…")
            ->setPublicationDate(new \DateTime('2020/06/26'))
            ->setVideoImage("animation/dbzresurrectionfreezer.jpg")
            ->setVideoLink("films/animation/resurrectionfreez.mp4")
            ->setVideoDuration(new \DateTime('01:34:00'))
            ->setCategory($category10);
            $manager->persist($video24); 
        
        
        $video25 = new Videos();
        $video25->setVideoTitle("Dragon Ball Z - Battle of Gods en streaming")
            ->setVideoDescription("Quelques années après la disparition de Boo, Birusu, le dieu de la destruction, se réveille après 39 ans d’hibernation. Quelques années auparavant, le Poisson Oracle lui avait prédit qu’un puissant guerrier se dresserait contre lui dans exactement 39 ans. Ayant eu echo de la mort de Freezer, Birusu décide alors de partir à la recherche de son bourreau, Son Goku, dans l’espoir de tomber sur le fameux guerrier de la prophétie du Poisson Oracle.")
            ->setPublicationDate(new \DateTime('2020/06/26'))
            ->setVideoImage("animation/dbzbattleofgods.jpg")
            ->setVideoLink("films/animation/dbzofgods.mp4")
            ->setVideoDuration(new \DateTime('01:45:00'))
            ->setCategory($category10);
            $manager->persist($video25);        
        
        $video26 = new Videos();
        $video26->setVideoTitle("Dragon Ball Super : Broly en streaming")
            ->setVideoDescription("Quelque temps après le Tournoi du Pouvoir, la Terre est paisible. Son Goku et ses amis ont repris leur vie. Cependant, avec son expérience du Tournoi, Goku passe son temps à s’entraîner pour continuer à s’améliorer car ce dernier est conscient qu’il reste encore beaucoup d’individus plus forts à découvrir au sein des autres univers. Lorsqu’un jour, le vaisseau de Freezer refait surface sur la Terre. Celui-ci est accompagné d’un Saiyan, nommé Broly, avec son père, Paragus. La surprise de Goku et Vegeta est immense puisque les Saiyans sont censés avoir été complètement anéantis lors de la destruction de la planète Vegeta. Ils n’ont donc pas d’autre choix que de s’affronter, mais ce nouvel ennemi s’adapte très vite aux adversaires qu’il affronte…")
            ->setPublicationDate(new \DateTime('2020/06/26'))
            ->setVideoImage("animation/broly.jpg")
            ->setVideoLink("films/animation/broly.mp4")
            ->setVideoDuration(new \DateTime('01:40:00'))
            ->setCategory($category10);
            $manager->persist($video26);        
        
        $manager->flush();
    
    }

}