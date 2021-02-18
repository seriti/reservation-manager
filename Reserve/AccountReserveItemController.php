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
        //NB: TABLE_PREFIX constant not applicable as not called within admin module
        $table_prefix = MODULE_RESERVE['table_prefix'];

        $table_name =  $table_prefix.'reserve_item'; 
        $table = new AccountReserveItem($this->container->mysql,$this->container,$table_name);

        $param = [];
        $param['table_prefix'] = $table_prefix;
        $table->setup($param);
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = 'Reservation Accommodation Units';
        
        return $this->container->view->render($response,'admin_popup.php',$template);
    }
}