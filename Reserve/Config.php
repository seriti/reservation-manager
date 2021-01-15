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
        $user = $this->container->user;

        //NB: Also defined in Website/ConfigPublic
        define('MODULE_RESERVE',$module);
                
        define('TABLE_PREFIX',$module['table_prefix']);
        if(!defined('CURRENCY_ID')) define('CURRENCY_ID','ZAR');
        if(!defined('CURRENCY_SYMBOL')) define('CURRENCY_SYMBOL','R');

        //defines access and resize parameters
        define('IMAGE_CONFIG',$module['images']);

        define('MODULE_ID','RESERVE');
        define('MODULE_LOGO','<span class="glyphicon glyphicon-home"></span> ');
        //define('MODULE_LOGO','<img src="'.BASE_URL.'images/lion40.png"> ');
        define('MODULE_PAGE',URL_CLEAN_LAST);
        
        $setup_pages = ['location','source','transfer_type','item','item_category','cash_type','service_operator',
                        'user_extend','agent','reserve_status','payment_option','payment_type','package_category'];

        $setup_link = '';
        if(in_array(MODULE_PAGE,$setup_pages)) {
            $page = 'setup_dashboard';
            $setup_link = '<a href="setup_dashboard"> -- back to setup options --</a><br/><br/>';
        } elseif(stripos(MODULE_PAGE,'_wizard') !== false) {
            $page = str_replace('_wizard','',MODULE_PAGE);
        } else {    
            $page = MODULE_PAGE;
        }
       
        //only show module sub menu for users with normal non-route based access
        if($user->getRouteAccess() === false) {
            $submenu_html = $menu->buildNav($module['route_list'],$page).$setup_link;
            $this->container->view->addAttribute('sub_menu',$submenu_html);
        }
       
        $response = $next($request, $response);
        
        return $response;
    }
}