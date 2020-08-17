<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\League;
use App\Entity\Team;
use App\Entity\Player;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FootballController extends AbstractController
{
    public function __construct(HttpClientInterface $footballApi)
    {
        $this->client =  $footballApi;
    }
    
    /**
     * @Route("import-league/{leagueCode}", name="import-league", methods={"GET"})
     */
    public function importLeague($leagueCode): JsonResponse
    {
                
        $em = $this->getDoctrine()->getManager();
        if($em->getRepository(League::class)->findOneBy(['code' => $leagueCode])){
            return new JsonResponse(['message' => 'League already imported'], Response::HTTP_CONFLICT);
        }
        
        /* Get competition */
        try {
            $response = $this->getData('/competitions/'. $leagueCode);
        } catch (\Exception $exc) {
            return new JsonResponse(['message' => $exc->getMessage()], $exc->getCode()); 
        }

        $league = json_decode($response->getContent());
        
        /* Save competition */
        $new_league = $em->getRepository(League::class)->saveLeague($league->name, $league->code, $league->area->name);
        
        /* Get teams */
        try {
            $response = $this->getData('/competitions/'.$leagueCode.'/teams');
        } catch (\Exception $exc) {
            return new JsonResponse(['message' => $exc->getMessage()], $exc->getCode()); 
        }
        
        $teams = json_decode($response->getContent());
        
        foreach ($teams->teams as $team) {
            
            if($ex = $em->getRepository(Team::class)->findOneBy(['code' => $team->id])){
                $new_league->addTeam($ex);
                $em->persist($new_league);
                $em->flush();
            }
            else{
                /* Save team */                                 
                $new_team = $em->getRepository(Team::class)->saveTeam($team->name, $team->tla, $team->id, $team->shortName, $team->area->name, $team->email, $new_league);
                
                /* Get payers */
                try {
                    $response = $this->getData('/teams/'.$team->id);
                } catch (\Exception $exc) {
                    return new JsonResponse(['message' => $exc->getMessage()], $exc->getCode()); 
                }
                
                $players = json_decode($response->getContent());

                foreach ($players->squad as $player) {
                    /* Save player */
                    $em->getRepository(Player::class)
                            ->savePlayer($player->name, $player->position, $player->dateOfBirth, $player->countryOfBirth, $player->nationality, $new_team);
                }
            }
        }
        
        return new JsonResponse(['message' => 'Successfully imported'], Response::HTTP_CREATED);
    }
    
    /**
     * @Route("total-players/{leagueCode}", name="total-players", methods={"GET"})
     */
    public function totalPlayerLeague($leagueCode): JsonResponse
    {
        
        $em = $this->getDoctrine()->getManager();
        if(!$em->getRepository(League::class)->findOneBy(['code' => $leagueCode])){
            return new JsonResponse(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }
        
        $result = $em->getRepository(Player::class)->countAll($leagueCode);
        
        return new JsonResponse(['total' => $result], Response::HTTP_OK);
    }
    
    
    private function getData($url){

        $response = $this->client->request('GET', 'https://api.football-data.org/v2' . $url);
        
        if(200 !== $response->getStatusCode()){
            switch ($response->getStatusCode()){
                case 404:
                    $message = "Not Found";
                    break;
                case 504:
                    $message = "Server Error";
                    break;
                default:
                    $message = "Other error";    
            }
            throw new \Exception($message,$response->getStatusCode());   
        }
        return $response;
    }
}