<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;

class pdf
{
    private $domPdf;

    public function __construct()
    {
        $this->domPdf= new Dompdf();
        $pdfOptions = new Options();

        $pdfOptions->set('defaultFront','Garamond');

        $this->domPdf->setOptions($pdfOptions);
    }

    public function show ($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream("Planning.pdf",['Attachement'=>False]);
    }

}