<?php
namespace App\Reserve;

use Seriti\Tools\SetupModule;

use Seriti\Tools\BASE_UPLOAD;
use Seriti\Tools\UPLOAD_DOCS;

class DefaultSetup extends SetupModule
{
    public function setup() {
        //upload_dir is NOT publically accessible
        $upload_dir = BASE_UPLOAD.UPLOAD_DOCS;
        $this->setUpload($upload_dir,'PRIVATE');

        $param = [];
        $param['info'] = 'Specify email footer text / contact details';
        $param['rows'] = 5;
        $param['value'] = '';
        $this->addDefault('TEXTAREA','RESERVE_EMAIL_FOOTER','Email footer',$param);

        $param = [];
        $param['info'] = 'Specify reservation package footer text / contact details';
        $param['rows'] = 5;
        $param['value'] = '';
        $this->addDefault('TEXTAREA','RESERVE_PACKAGE_FOOTER','Catalogue footer',$param);

        $param = [];
        $param['info'] = 'Specify default itinerary text for new reservations.';
        $param['rows'] = 5;
        $param['value'] = '';
        $this->addDefault('TEXTAREA','RESERVE_ITINERARY','Itinerary notes',$param);

        $param = [];
        $param['info'] = 'Specify default administration text for new reservations.';
        $param['rows'] = 5;
        $param['value'] = '';
        $this->addDefault('TEXTAREA','RESERVE_ADMIN','Admin notes',$param);

        $param = [];
        $param['info'] = 'Specify default emergency text for new reservations.';
        $param['rows'] = 5;
        $param['value'] = '';
        $this->addDefault('TEXTAREA','RESERVE_EMERGENCY','Emergency notes',$param);

        $param = [];
        $param['info'] = 'Specify default people preferences text for new reservations.';
        $param['rows'] = 5;
        $param['value'] = '';
        $this->addDefault('TEXTAREA','RESERVE_PREFERENCE','Preference notes',$param);

        $param = [];
        $param['info'] = 'Specify reservation invoice footer text / bank account details / any info you require to be added.';
        $param['rows'] = 10;
        $param['value'] = '';
        $this->addDefault('TEXTAREA','RESERVE_INVOICE_FOOTER','Invoice PDF footer',$param);

        $param = [];
        $param['info'] = 'Select the image you would like to use as a signature on invoices and other documents(max 50KB)';
        $param['max_size'] = 50000;
        $param['value'] = 'images/sample_sig.jpeg';
        $this->addDefault('IMAGE','RESERVE_SIGN','Invoice signature',$param);

        $param = [];
        $param['info'] = 'Specify the name and title you wish to have below signature image.';
        $param['value'] = 'Chief Executive Officer';
        $this->addDefault('TEXT','RESERVE_SIGN_TXT','Invoice signature subtext',$param);
    }    
}
