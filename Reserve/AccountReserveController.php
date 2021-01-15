<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;

use Seriti\Tools\Template;

use App\Reserve\AccountReserve;
use App\Reserve\Helpers;

class AccountReserveController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $db = $this->container->mysql;
        $user = $this->container->user;

        //NB: TABLE_PREFIX constant not applicable as not called within admin module
        $table_prefix = MODULE_RESERVE['table_prefix'];
               
        $table_name = $table_prefix.'reserve'; 
        $table = new AccountReserve($this->container->mysql,$this->container,$table_name);

        $param = [];
        $param['user_id'] = $user->getId();
        $param['table_prefix'] = $table_prefix;
        $table->setup($param);
        $html = $table->processTable();
            
        $template['title'] = 'Your Reservations';
        $template['html'] = $html;
        //$template['javascript'] = $dashboard->getJavascript();
        
        return $this->container->view->render($response,'public.php',$template);
    }
}