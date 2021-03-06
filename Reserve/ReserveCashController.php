<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\ReserveCash;

class ReserveCashController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'reserve_cash'; 
        $table = new ReserveCash($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = 'Reservation Cash allocations';
        
        return $this->container->view->render($response,'admin_popup.php',$template);
    }
}