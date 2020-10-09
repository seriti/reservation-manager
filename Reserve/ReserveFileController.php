<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\ReserveFile;

class ReserveFileController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table = TABLE_PREFIX.'file'; 
        $upload = new ReserveFile($this->container->mysql,$this->container,$table);

        $upload->setup();
        $html = $upload->processUpload();
        
        $template['html'] = $html;
        //$template['title'] = MODULE_LOGO.'Reservation documents';
        
        return $this->container->view->render($response,'admin_popup.php',$template);
    }
}