<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class ArtistsController extends AbstractController
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Renders a list of artists
     * 
     * @Route("/artists", name="artists")
     */
    public function index(): Response
    {

        $artists = $this->getListOfArtists();                              

        return $this->render('artists/index.html.twig', [
            'controller_name' => 'ArtistsController',
            'artists' => $artists
        ]);
    }   
    
    /**
     * @Route("/show-albums", name="show_albums", methods={"GET","POST"})
     */
    public function showAlbums(Request $request) {
        
        $em = $this->getDoctrine()->getManager();              
            
        if ($request->isMethod('POST')) {

            $albumData = $request->request->get('artist');        
            $artist = $this->getArtist($albumData["name"]);     
            $createAlbumResponse = !empty($albumData) ? $this->createAlbum($artist[0]->name, $albumData["album"], $albumData["year"], $this->getUser()) : null;       
        
            if ($createAlbumResponse !== true) {          
                $this->addFlash('error', "Error creating your new album: " . $createAlbumResponse);
                return $this->redirectToRoute('artists');
            }        
        }               
        
        //if user is admin, get all albums
        if (in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $albums = $em->getRepository(Album::class)->findAll();
        } else {
            $albums = $em->getRepository(Album::class)->findByUser($this->getUser());            
        }     
             
        return $this->render('artists/albums.html.twig', [            
            'albums' => $albums
        ]);
    }
    
    /**
     * Create a new album
     * @param string $artist
     * @param string $albumName
     * @param string $year
     * @param User $user
     */
    public function createAlbum($artist, $albumName, $year, User $user) {
        
        $em = $this->getDoctrine()->getManager();

        try {

            $album = new Album();
            $album->setArtist($artist);
            $album->setAlbumName($albumName);
            $album->setYear($year);
            $album->setUser($user);
            $em->persist($album);
            $em->flush();

        } catch (\Exception $e) {                      
            return $e->getMessage();            
        }

        return true;
    } 
    
    /**
     * @Route("/edit/{album}", name="edit")
     */
    public function editFormAlbum($album) {

        $em = $this->getDoctrine()->getManager();
        $album = $em->find(Album::class, $album);   
        $artists = $this->getListOfArtists();
        
        return $this->render('artists/edit.html.twig', [            
            'album' => $album,
            'artists' => $artists
        ]);
    }

    /**
     * @Route("/update-album", name="update_album", methods={"POST"})
     */
    public function updateAlbum(Request $request) {
        
        $em = $this->getDoctrine()->getManager();      
        $albumData = $request->request->get('artist');        
        $artist = $this->getArtist($albumData["name"]);           
        
        $album = $em->find(Album::class, $albumData["id"]);   
        $album->setArtist($artist[0]->name);
        $album->setAlbumName($albumData["album"]);
        $album->setYear($albumData["year"]);               
        $em->flush();     

        //if user is admin, get all albums
        if (in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $albums = $em->getRepository(Album::class)->findAll();
        } else {
            $albums = $em->getRepository(Album::class)->findByUser($this->getUser());            
        }    
        
        return $this->render('artists/albums.html.twig', [            
            'albums' => $albums
        ]);
    }    

    /**
     * @Route("/delete-album", name="delete_album", methods={"DELETE"})
     */
    public function deleteAlbum(Request $request) {

        $em = $this->getDoctrine()->getManager();   

        $content = $request->getContent();
        $albumId = json_decode($content)->id;        
        $album = $em->find(Album::class, $albumId); 

        $albumFound = !empty($em->find(Album::class, $albumId)) ? true : false;       

        if (empty($albumFound)) {            
            return new JsonResponse([      
                'status' => 0,
                'message' => 'Album not found'
            ], JsonResponse::HTTP_BAD_REQUEST);        
        }

        $em->remove($album);
        $em->flush();

        $isAlbumDeleted = empty($em->find(Album::class, $albumId)) ? true : false;       

        if ($isAlbumDeleted === false) {
            return new JsonResponse([   
                'status' => 1,                     
                'message' => 'Album was not deleted'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([    
            'status' => 2,                    
            'message' => 'Album Deleted'
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @return Array - returns a list of artists
     */
    public function getListOfArtists() {

        $artistsResponse = $this->client->request('GET', 'https://moat.ai/api/task/', [
            'headers' => ['Basic' => 'ZGV2ZWxvcGVyOlpHVjJaV3h2Y0dWeQ==']
        ]);
        
        if ($artistsResponse->getStatusCode() !== 200) throw new Exception("Couldn't get list of artists");      
        
        return json_decode($artistsResponse->getContent());
    }   

    /**
     * @return Array - returns an artist
     */
    public function getArtist($id) {

        $artistsResponse = $this->client->request('GET', 'https://moat.ai/api/task/?artist_id=' . $id, [
            'headers' => ['Basic' => 'ZGV2ZWxvcGVyOlpHVjJaV3h2Y0dWeQ==']
        ]);
        
        if ($artistsResponse->getStatusCode() !== 200) throw new Exception("Couldn't get the artist");      
        
        return json_decode($artistsResponse->getContent());
    }
    
}
