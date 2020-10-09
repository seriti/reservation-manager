<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\PaymentOption;

class PaymentOptionController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'payment_option'; 
        $table = new PaymentOption($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.': All Payment options';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}