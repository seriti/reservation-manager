<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\ReserveTransfer;

class ReserveTransferController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'reserve_transfer'; 
        $table = new ReserveTransfer($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        //$template['title'] = MODULE_LOGO.'Transfers';
        
        return $this->container->view->render($response,'admin_popup.php',$template);
    }
}