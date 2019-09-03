<?php


namespace App\Service;




use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class UtilService
{
    /**
     * Retourne le nom de la route de la page précédente ou null si la route est inexistante
     *
     * @param Request $request
     * @param Router $router (accessible via $this->get('router') dans le controller)
     * @return string
     */
    function getPreviousRoute(Request $request, Router $router){

        $referer = $request->headers->get('referer');

        if($referer !== null){
            $host = $request->headers->get('Host');
            $domainReferer = parse_url($referer);
            $domainURL = $domainReferer['host'].":".$domainReferer['port'];
            if($host === $domainURL || $host === $domainReferer['host']){
                $route = $router->match($domainReferer['path']);
                return $route['_route'];
            }
            else{
                return null;
            }
        }
        else{
            return null;
        }
    }
}