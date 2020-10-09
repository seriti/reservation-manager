<?php 
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use Seriti\Tools\BASE_URL;
use Seriti\Tools\SITE_NAME;
use Seriti\Tools\CURRENCY_ID;

class Config
{
    
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        
        $module = $this->container->config->get('module','reserve');
        $menu = $this->container->menu;

        //NB: Also defined in Website/ConfigPublic
        define('MODULE_RESERVE',$module);
        
        define('TABLE_PREFIX',$module['table_prefix']);
        define('MODULE_LOGO','<span class="glyphicon glyphicon-home"></span> ');
        //define('MODULE_LOGO','<img src="'.BASE_URL.'images/lion40.png"> ');
        define('MODULE_PAGE',URL_CLEAN_LAST);
        
        $submenu_html = $menu->buildNav($module['route_list'],MODULE_PAGE);
        $this->container->view->addAttribute('sub_menu',$submenu_html);
       
        $response = $next($request, $response);
        
        return $response;
    }
}