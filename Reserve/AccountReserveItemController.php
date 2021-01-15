<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\AccountReserveItem;

class AccountReserveItemController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'reserve_item'; 
        $table = new AccountReserveItem($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = 'Reservation Accommodation Units';
        
        return $this->container->view->render($response,'admin_popup.php',$template);
    }
}