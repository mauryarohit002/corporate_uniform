<?php
// Include the main TCPDF library (search for installation path).
$this->mypdf_class->tcpdf(); 
global $master_pdf; 
global $company_pdf;
global $yy;
$master_pdf      = $master_data; 
$company_pdf     = $company_data;
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF 
{
    //Page header
    public function Header() 
    {
        global $master_pdf ;
        global $company_pdf ;

        $logo =base_url('public/assets/dist/images/logo.jpg');


        $this->SetFont('copperplateccheavy', '', 9,false);

        $tbl_header = '<table cellpadding="0"> 
                        <tr>
                            <td width="20%" style="text-align:center"><img src="'.$logo.'" height="70px"></td>
                            <td width="20%"></td>
                            <td width="60%" style="font-size:13px"><b style="font-size:15px">'.$company_pdf[0]['company_name'].'</b><br/>'.$company_pdf[0]['address'].'<br/> Mobile : '.$company_pdf[0]['mobile'].'  / E-mail : '.$company_pdf[0]['email'].'<br/><b>GSTIN : </b>'.$company_pdf[0]['gstin'].'</td>
                            
                        </tr>
                    </table>
                    <br/><br/>
                    <table border="0" style="border-top: 1px solid #000; border-left: 1px solid #000;border-right: 1px solid #000;" cellpadding="3" >
                        <tr style="background-color:#222221; color:white">
                            <td style="width:70%"><b>BILL NO</b> &nbsp; &nbsp; &nbsp; &nbsp;: &nbsp;'.$master_pdf[0]['entry_no'].'</td>
                            <td style="width:30%; text-align:right"><b>BILL DATE</b> &nbsp;: '.$master_pdf[0]['entry_date'].'</td>
                        </tr>
                        <tr>
                            <td style="width:70%"><b>NAME</b>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;: '.$master_pdf[0]['customer_name'].'</td>
                            <td style="width:30% ; text-align:right"><b>TRIAL DATE</b> : '.$master_pdf[0]['trial_date'].'</td>
                        </tr>
                         <tr>
                            <td style="width:50%; text-align:left"><b>MOBILE NO</b> &nbsp;: '.$master_pdf[0]['customer_mobile'].'</td>
                            <td style="width:50%; text-align:right"><b>DELIVERY DATE</b> &nbsp;: '.$master_pdf[0]['delivery_date'].'</td>
                        </tr>
                        <tr>
                            <td style="width:100%" height="50px"><b>ADDRESS</b>&nbsp; &nbsp; &nbsp;: '.$master_pdf[0]['customer_address'].'</td>
                        </tr>
                        
                    </table>
                    <table  border="1" cellpadding="3">
                        <tr style="background-color:#222221; color:white">
                            <th style="width:38%;"><b>ITEMS</b></th>
                            <th style="width:14%;text-align:center;"><b>HSN</b></th>
                            <th style="width:10%;text-align:center;"><b>QTY</b></th>
                            <th style="width:10%;text-align:center;"><b>MTR</b></th>
                            <th style="width:14%;text-align:center;"><b>RATE</b></th>
                            <th style="width:14%;text-align:right;"><b>AMOUNT</b></th>
                        </tr>
                    </table>';

        $this->writeHTML($tbl_header, true, false, false, false, '');
        $yy = $this->GetY(); 
        $yy = $yy - 6;
        
        $this->line(5,$yy,5,147);
        // $this->line(39.5,$yy,39.5,147);
        $this->line(57.7,$yy,57.7,145);
        $this->line(77,$yy,77,145);       
        $this->line(90.8,$yy,90.8,145);
        $this->line(104.6,$yy,104.6,145);
        $this->line(124,$yy,124,145);
        $this->line(143,$yy,143,145);

        $this->SetTopMargin($yy + 1);
    }

    // Page footer
    public function Footer() 
    {
        $this->SetFont('copperplateccheavy', 'B', 10,false);
        global $master_pdf;

        $amt_words          = number_to_word($master_pdf[0]['balance_amt']);
        $gst_amt = $master_pdf[0]['sgst_amt'] + $master_pdf[0]['cgst_amt'] + $master_pdf[0]['igst_amt'];
      
      
        $tbl_footer ='';
        $tbl_footer .='<table width="100%"  border="1" cellpadding="4" >
            <tr style="font-size:12px;">
                <th style="width:52%;"><b>TOTAL</b></th>
                <th style="width:10%;text-align:center;"><b>'.$master_pdf[0]['total_qty'].'</b></th>
                <th style="width:10%;text-align:center;"><b>'.$master_pdf[0]['total_mtr'].'</b></th>
                <th style="width:14%;text-align:center;"><b></b></th>
                <th style="width:14%;text-align:right;"><b>'.$master_pdf[0]['sub_amt'].'</b></th>
            </tr>
        </table>
       <table border="1" cellpadding="3">
        <tr>
            <td width="55%" style="font-size:10px;">
                <table>
                    <tr>
                        <td height="35px;">Rupees : '.$amt_words.'</td>
                    </tr>
                   
                </table>
            </td>
            <td width="45%">
                <table style="line-height: 1.8;">
                    <tr>
                        <td width="50%" style="font-size:10px;border-bottom:1px solid #000;border-right:1px solid #000;" align="left">DISC AMT </td>
                        <td width="50%"style="font-size:10px;border-bottom:1px solid #000;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$master_pdf[0]['disc_amt'].'</td>
                    </tr>
                    <tr>
                        <td width="50%" style="font-size:10px;border-bottom:1px solid #000;border-right:1px solid #000;" align="left">TAXABLE AMT </td>
                        <td width="50%"style="font-size:10px;border-bottom:1px solid #000;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$master_pdf[0]['taxable_amt'].'</td>
                    </tr>';
         
          if($master_pdf[0]['igst_amt'] > 0):

             $tbl_footer .= '        
                    <tr>
                        <td width="50%" style="font-size:10px;border-bottom:1px solid #000;border-right:1px solid #000;" align="left">IGST AMT </td>
                        <td width="50%"style="font-size:10px;border-bottom:1px solid #000;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$master_pdf[0]['igst_amt'].'</td>
                    </tr>';
                
               
            else:        

                $tbl_footer .='  <tr>
                        <td width="50%" style="font-size:10px;border-bottom:1px solid #000;border-right:1px solid #000;" align="left">SGST AMT </td>
                        <td width="50%"style="font-size:10px;border-bottom:1px solid #000;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$master_pdf[0]['sgst_amt'].'</td>
                    </tr>
                    <tr>
                        <td width="50%" style="font-size:10px;border-bottom:1px solid #000;border-right:1px solid #000;" align="left">CGST AMT </td>
                        <td width="50%"style="font-size:10px;border-bottom:1px solid #000;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$master_pdf[0]['cgst_amt'].'</td>
                    </tr>';

             endif;  

            $tbl_footer .=' 
                    <tr>
                        <td width="50%" style="font-size:10px;border-right:1px solid #000;" align="left">TOTAL AMT </td>
                        <td width="50%" style="font-size:10px;" align="left">&nbsp;&nbsp;&nbsp;'.$master_pdf[0]['total_amt'].'</td>
                    </tr>
                   
                </table>
            </td>       
         </tr>
         <tr>
            <td width="55%">
                <table>
                    <tr>
                        <td width="50%" style="font-size:10px;border-right:1px solid #000;" align="left">ADVANCE AMT </td>
                        <td width="50%" style="font-size:10px;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$master_pdf[0]['advance_amt'].'</td>
                    </tr>
                </table>
            </td>
            <td width="45%">
                <table>
                    <tr>
                        <td width="50%" style="font-size:10px;border-right:1px solid #000;" align="left">BALANCE AMT </td>
                        <td width="50%" style="font-size:10px;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'.$master_pdf[0]['balance_amt'].'</td>     
                    </tr>
                </table>
            </td>
                  
         </tr>
            
        </table>
        <table cellpadding="1" style="font-size:10px;">
            <tr>
                <td style="border-left:1px solid #000;border-bottom:1px solid #000;width:65%;">
                    <table  cellpadding="1">
                        <tr>
                            <td style="font-weight:normal;font-size:9px;">
                            <b>E & OE:</b> <br/>1. NO CREDIT.<br/>
                                2. FOR BOOKING 60% ADVANCE & BALANCE ON DELIVERY.<br/>
                                3. GOODS SOLD WILL NOT BE TAKEN BACK OR EXCHANGED.<br/>
                                4. WE RECOMMEND PROFESSIONAL DRY CLEANING.<br/>
                               
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="border-right:1px solid #000;border-bottom:1px solid #000;width:35%;text-align:right;">
                    <table cellpadding="1">
                        <tr>
                            <td><br/><br/> <br/><br/></td>
                        </tr>
                        <tr>
                            <td>_______________________<br/> Authorized Signature &nbsp;&nbsp;&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>    
';

        $this->writeHTML($tbl_footer, true, false, false, false, '');
        // Set font
        $this->SetFont('copperplateccheavy', 'I', 9);
        // Page number
        $this->Cell(0, 0, 'Page '.$this->getPageNumGroupAlias().'/'.$this->getPageGroupAlias(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
       
    }


}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array('148','210'), true, 'UTF-8', false); 
$file_name = 'Order.pdf';

// $file_name = 'sales_invoice_pdf.pdf';
$file_path = 'INVOICE.pdf';
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Imran Khan');
$pdf->SetTitle('ORDER INVOICE Pdf');
$pdf->SetSubject('ORDER INVOICE Pdf');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(5, PDF_MARGIN_TOP, 5,true);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(70);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 68);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}


$pdf->SetFont('copperplateccheavy', '', 13,false);
$body = "";
$title = "Original for Buyer";
$pdf->startPageGroup();
$pdf->AddPage();

$body .= '<table cellpadding="3" >';
            foreach($trans_data as $key => $value) :

                $apparel_fabric = (!empty($value['fabric_name'])) ? $value['fabric_name'].' - '.$value['description'] : $value['apparel_name'].' - '.$value['description'];

                $body .= '<tr style="font-size:12px;">
                            <td style="width:38%;border-bottom-color:#ccc;">'.$apparel_fabric.'</td>
                            <td style="width:14%;text-align:center;border-bottom-color:#ccc;">'.$value['hsn_name'].'</td>
                            <td style="width:10%;text-align:center;border-bottom-color:#ccc;">'.$value['qty'].'</td>
                            <td style="width:10%;text-align:center;border-bottom-color:#ccc;">'.$value['total_mtr'].'</td>
                            <td style="width:14%;text-align:center;border-bottom-color:#ccc;">'.$value['rate'].'</td>
                            <td style="width:14%;text-align:right;border-bottom-color:#ccc;">'.$value['amt'].'</td>
                        </tr>';
            endforeach;
$body .= '</table>'; 


$pdf->writeHTML($body, true, false, false, false, '');

//Close and output PDF document
if(true)
{

    $pdf->Output($file_path, 'I');
}


//============================================================+
// END OF FILE
//============================================================+