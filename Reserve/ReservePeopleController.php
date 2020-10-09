<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\ReservePeople;

class ReservePeopleController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'reserve_people'; 
        $table = new ReservePeople($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        //$template['title'] = MODULE_LOGO.'People';
        
        return $this->container->view->render($response,'admin_popup.php',$template);
    }
}