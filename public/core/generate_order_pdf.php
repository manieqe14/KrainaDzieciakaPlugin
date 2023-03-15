<?php
require('fpdf/fpdf.php');
require('fpdf/tfpdf.php');
require_once ("variables.php");

class PDF extends tFPDF
{
    
    private $order;
    static $use_font = 'Poppins';
    
    function setOrder($title){
        $this->order = $title;
    }
    
    // Page header
    function Header()
    {
        /* $use_font = 'DejaVu';
        $this->AddFont('DejaVu','','DejaVuSansMono.ttf',true);
        $this->AddFont('DejaVu','B','DejaVuSansMono-Bold.ttf',true); */
        
        $this->AddFont('Poppins','','Poppins-Regular.ttf',true);
        $this->AddFont('Poppins','B','Poppins-Bold.ttf',true);
        
        $this->SetFont(self::$use_font,'',14);
        $image_url = wp_upload_dir()['baseurl'] . '/logo/logo_mini.png';
        
        // Logo
        $this->Image($image_url,10,6,30);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30,10, 'Zamówienie nr ' . $this->order->get_id(),0,2,'C');
        $this->SetFont($use_font,'',10);
        $this->Cell(30,10, variables::polish_months_in_date($this->order->get_date_created()->date("j F Y, G:i:s")), 0, 1, 'C');
        // Line break
        $this->Ln(20);
    }
    
    function generateContent()
    {   
        $this->SetDrawColor(220,220,220);
        //width of multicell
        $w = 130;
        $this->SetFont(self::$use_font,'B',12);
        // Header
        $this->Cell(130,7, 'Przedmiot', 1, 0, 'C');
        $this->Cell(30,7, 'Ilość', 1, 0, 'C');
        $this->Cell(30,7, 'Cena', 1, 0, 'C');
           
        $this->Ln();
        $order_items = $this->order->get_items();
        $this->SetFont(self::$use_font,'',9);
        // Data
        foreach($order_items as $item){
            $product = $item->get_product();
            $image = wp_get_attachment_image_url( $product->get_image_id(), 'small' );
            //
            $x=$this->GetX();
            $y=$this->GetY();
            if($image){
                $this->Image($image, 12, $this->GetY()+2, 12);
            }
            if($item->get_variation_id()){
                $attributes = $product->get_attributes();
                foreach($attributes as $key => $value){
                    $this->MultiCell(130, 5, $item->get_name() . "\n" . $key . ": " . $value . "\nSKU: " . $product->get_sku() , 1, 'C');
                }
            }
            else{
                $this->MultiCell(130, 5, $item->get_name() . "\nSKU: " . $product->get_sku() . "\n " , 1, 'C');
            }
            $this->SetXY($x+$w,$y);
            $this->Cell(30, 15, $item->get_quantity(), 1, 0, 'C');
            $this->Cell(30, 15, $item->get_total() . ' PLN', 1, 1, 'C');
            
        }
        
        
        $this->Ln(20);
        
        $shipping_address = $this->order->get_shipping_first_name() . " " . $this->order->get_shipping_last_name() . "\n" . $this->order->get_shipping_address_1() . "\n" . $this->order->get_shipping_address_2() . "\n" . $this->order->get_shipping_postcode() . " " . $this->order->get_shipping_city();
        
        $billing_address = $this->order->get_billing_first_name() . " " . $this->order->get_billing_last_name() . "\n" . $this->order->get_billing_address_1() . "\n" . $this->order->get_billing_address_2() . "\n" . $this->order->get_billing_postcode() . " " . $this->order->get_billing_city();
        
        $w=70;
        $this->SetFont(self::$use_font,'B',10);
        $this->Cell($w,7, 'Dane rozliczeniowe', 1, 0, 'C');
        $this->Cell($w,7, 'Dane do wysyłki', 1, 1, 'C');
        $this->SetFont(self::$use_font,'',8);
        $x=$this->GetX();
        $y=$this->GetY();
        $this->MultiCell($w, 5, $billing_address, 1);
        $this->SetXY($x+$w,$y);
        $inpost_symbol = '';
        if(in_array($this->order->get_shipping_method(), array('Paczkomat InPost'))){
            $inpost_symbol = 'Wybrany paczkomat: ' . $this->order->get_meta('Symbol punktu');
        }
        $this->MultiCell($w, 5, $shipping_address . "\n" . $inpost_symbol, 1,);
        
        
        $this->SetFont(self::$use_font,'',9);
        $this->Ln(10);
        
        $width = 40;
        $this->SetFont(self::$use_font,'B',9);
        $this->Cell($width,7, 'Płatność:', 1, 0, 'R');
        $this->SetFont(self::$use_font,'',9);
        $this->Cell(90,7, $this->order->get_payment_method_title(), 1, 1, 'C');
        
        $this->SetFont(self::$use_font,'B',9);
        $this->Cell($width,7, 'Wysyłka:', 1, 0, 'R');
        $this->SetFont(self::$use_font,'',9);
        $this->Cell(90,7, $this->order->get_shipping_method(), 1, 1, 'C');
        
        $this->SetFont(self::$use_font,'B',9);
        $this->Cell($width,7, 'Suma:', 1, 0, 'R');
        $this->SetFont(self::$use_font,'',9);
        $this->Cell(90,7, $this->order->get_subtotal() . 'zł', 1, 1, 'C');
        
        $this->SetFont(self::$use_font,'B',9);
        $this->Cell($width,7, 'Koszty wysyłki:', 1, 0, 'R');
        $this->SetFont(self::$use_font,'',9);
        $this->Cell(90,7, $this->order->get_shipping_total() . 'zł', 1, 1, 'C');
        
        if($this->order->get_total_discount() > 0){
            $this->SetFont(self::$use_font,'B',9);
            $this->Cell($width,7, 'Kupon:', 1, 0, 'R');
            $this->SetFont(self::$use_font,'',9);
            $this->Cell(90,7, $this->order->get_total_discount() . 'zł', 1, 1, 'C');
        }
        
        $this->SetFont(self::$use_font,'B',9);
        $this->Cell($width,7, 'Razem:', 1, 0, 'R');
        $this->SetFont(self::$use_font,'',9);
        $this->Cell(90,7, $this->order->get_total() . 'zł', 1, 1, 'C');
        
        
    }
    
}


?>