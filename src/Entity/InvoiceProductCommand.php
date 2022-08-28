<?php 

namespace App\Entity;

use Fpdf\Fpdf;

class InvoiceProductCommand extends FPDF {

    private $host;
    private $pdf;

    function __construct($host, $orientation='P', $unit='mm', $size='A4')
    {
        parent::__construct($orientation='P', $unit='mm', $size='A4');
        $this->host = $host;
    }

    function Header() 
    {
        $this->Image("./uploads/images/logowritewhiteorange.PNG",10,10,50);
        $this->SetY(10);
        $this->SetX(-32);
        $this->SetFont('Arial','B',18);
        $this->Cell(50,10,"TURBO",0,1);
        $this->SetFont('Arial','',14);
        $this->SetX(-80);
        $this->Cell(50,7,"242 rue Faubourg Saint Antoine,",0,1);
        $this->SetX(-36);
        $this->Cell(50,7,"75012 Paris",0,1);
        $this->SetX(-48);
    }

    function body($info_user, $products) 
    {
        // Destinataire
        $this->SetY(60);
        $this->SetX(10);
        $this->SetFont('Arial','B',12);
        $this->Cell(50,10,"A destination de: ",0,1);
        $this->SetFont('Arial','',12);
        $this->Cell(50,7,utf8_decode($info_user["customer"]),0,1);
        $this->Cell(50,7,utf8_decode($info_user["address"]),0,1);
        $this->Cell(50,7,utf8_decode($info_user["city"]),0,1);
        
        // No Facture
        $this->SetY(60);
        $this->SetX(-60);
        $this->Cell(50,7,"Commande : ".$info_user["invoice_no"]);
        
        // Date Facture
        $this->SetY(68);
        $this->SetX(-60);
        $this->Cell(50,7,"Date : ".$info_user["invoice_date"]);
        
        // Description / Prix / Quantite / Total
        $this->SetY(105);
        $this->SetX(10);
        $this->SetFont('Arial','B',12);
        $this->Cell(80,9,"PRODUIT",1,0);
        $this->Cell(40,9,"PRIX",1,0,"C");
        $this->Cell(30,9,"QUANTITE",1,0,"C");
        $this->Cell(40,9,"TOTAL",1,1,"C");
        $this->SetFont('Arial','',12);
        
        //Affiche les produits
        foreach($products as $product) {
            $line = 1;
            if ($this->GetStringWidth($product["name"]) < 80) {
                $this->Cell(80,9,$product["name"],"LR",0);
            } else {
                $textLength = strlen($product["name"]);
                $errMargin=10;
                $startChar=0;
                $maxChar=0;
                $textArray=array();
                $tmpString="";

                while($startChar < $textLength) {
                    while($this->GetStringWidth($tmpString) < (80-$errMargin) && ($startChar+$maxChar) < $textLength) {
                        $maxChar++;
                        $tmpString=substr($product["name"], $startChar, $maxChar);
                    }
                    $startChar=$startChar+$maxChar;
                    array_push($textArray, $tmpString);
                    $maxChar=0;
                    $tmpString='';
                }
                $line=count($textArray);

                $xPos=$this->GetX();
                $yPos=$this->GetY();

                $this->MultiCell(80,9,utf8_decode($product["name"]),"LR",0);
                $this->SetXY($xPos+80, $yPos);
            }


            $this->Cell(40,$line*9,$product["price"],"R",0,"R");
            $this->Cell(30,$line*9,$product["quantity"],"R",0,"C");
            $this->Cell(40,$line*9, (string) (intval($product["quantity"]) * doubleval($product["price"])) ,"R",1,"R");
        }

        // Tables Vides
        for($i=0 ; $i<12-count($products)-$line ; $i++) {
            $this->Cell(80,9,"","LR",0);
            $this->Cell(40,9,"","R",0,"R");
            $this->Cell(30,9,"","R",0,"C");
            $this->Cell(40,9,"","R",1,"R");
        }

        //Affiche le total de la commande
        $this->SetFont('Arial','B',12);
        $this->Cell(150,9,"TOTAL",1,0,"R");
        $this->Cell(40,9,$info_user["total_amt"],1,1,"R");
    }

    function Footer()
    {
        $this->SetY(-50);
        $this->Cell(0,10,"Signature",0,1,"R");
        $this->SetFont('Arial','',10);
    }
}