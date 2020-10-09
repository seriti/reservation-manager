<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;

use Seriti\Tools\Template;

use App\Reserve\Payment;
use App\Reserve\Helpers;

class PaymentController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'reserve_payment'; 
        $table = new Payment($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
            
        $template['title'] = '';
        $template['html'] = $html;
        //$template['javascript'] = $dashboard->getJavascript();
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}