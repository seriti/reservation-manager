<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;

use Seriti\Tools\Template;

use App\Reserve\ReserveMessage;
use App\Reserve\Helpers;

class OrderMessageController
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

        $table_name = TABLE_PREFIX.'reserve_message'; 
        $table = new ReserveMessage($this->container->mysql,$this->container,$table_name);

        $param = [];
        $table->setup($param);
        $html = $table->processTable();
            
        $template['title'] = '';
        $template['html'] = $html;
        //$template['javascript'] = $dashboard->getJavascript();
        
        return $this->container->view->render($response,'admin_popup.php',$template);
    }
}