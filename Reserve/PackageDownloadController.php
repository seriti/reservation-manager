<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\PackageFile;

use Seriti\Tools\Secure;

class PackageDownloadController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table = MODULE_RESERVE['table_prefix'].'file'; 
        $upload = new PackageFile($this->container->mysql,$this->container,$table);

        $_GET['mode'] = 'download';

        $upload->setup();
        $html = $upload->processUpload();
        
        $template['html'] = $html;
        $template['title'] = 'Package document download';
        
        return $this->container->view->render($response,'public.php',$template);
    }
}