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
        $customer_name = !empty($master_pdf[0]['customer_name'])?$master_pdf[0]['customer_name']:'';
        $customer_mobile = !empty($master_pdf[0]['customer_mobile'])?$master_pdf[0]['customer_mobile']:'';
        $customer_gst_no = !empty($master_pdf[0]['customer_gst_no'])?$master_pdf[0]['customer_gst_no']:'';
        $salesman_name = !empty($master_pdf[0]['salesman_name'])?$master_pdf[0]['salesman_name']:'';

        $this->SetFont('copperplateccheavy', '', 9,false);

        $tbl_header = '<table cellpadding="3" style="border-top: 1px solid #000; border-left: 1px solid #000;border-right: 1px solid #000;"> 
                        <tr>
                            <td width="25%"></td>
                            <td width="50%" style="text-align:center;"><b>ESTIMATE</b></td>
                             <td width="25%"></td>
                        </tr>
                    </table>
                    <table cellpadding="1" style="border-top: 1px solid #000; border-left: 1px solid #000;border-right: 1px solid #000;"> 
                        <tr>
                            <td width="20%"></td>
                            <td width="60%" style="text-align:center;font-size:35px"><img src="'.$logo.'" height="60px"></td>
                            <td width="20%"></td>
                        </tr>
                         <tr>
                            <td width="100%" align="center">'.$company_pdf[0]['address'].'</td>
                        </tr>
                    </table>
                    <table border="1" cellpadding="0" >
                        <tr>
                            <td width="70%"><table cellpadding="2">
                                    <tr>
                                        <td><b>&nbsp; &nbsp;ORDER NO : '.$master_pdf[0]['entry_no'].'</b></td>
                                    </tr>
                                     <tr>
                                        <td><b>&nbsp; &nbsp;PARTY NAME : '.$customer_name.'</b></td>
                                    </tr>
                                    <tr>
                                        <td><b>&nbsp; &nbsp;Mob. : '.$customer_mobile.'</b></td>
                                    </tr>
                                    <tr>
                                       <td style="height:40px"><b>&nbsp; &nbsp;ADDRESS. : '.$master_pdf[0]['customer_address'].'</b></td>
                                    </tr>
                                </table>
                            </td>
                            <td width="30%">
                                <table cellpadding="2">
                                    <tr>
                                        <td ><b>QT No &nbsp; &nbsp; : &nbsp;'.$master_pdf[0]['entry_no'].'</b></td>
                                    </tr>
                                    <tr>
                                        <td ><b>QT Date &nbsp;: '.$master_pdf[0]['entry_date'].'</b></td>
                                    </tr>
                                    <tr>
                                        <td><b>Salesman &nbsp; &nbsp;: '.$salesman_name.'</b></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table  border="1" cellpadding="3">
                        <tr>
                            <th style="width:3%;"><b>Sr.</b></th>
                            <th style="width:59%;"><b>Description Of Goods</b></th>
                            <th style="width:8%;text-align:center;"><b>Qty</b></th>
                            <th style="width:8%;text-align:center;"><b>Mtr</b></th>
                            <th style="width:10%;text-align:center;"><b>Rate</b></th>
                            <th style="width:12%;text-align:center;"><b>Amount</b></th>
                        </tr>
                    </table>';

        $this->writeHTML($tbl_header, true, false, false, false, '');
        $yy = $this->GetY(); 
        $yy = $yy - 6;
        
        $this->line(5,$yy,5,217);
        $this->line(11,$yy,11,217);
        $this->line(129,$yy,129,217);       
        $this->line(145,$yy,145,225);
        $this->line(161,$yy,161,217);
        $this->line(181,$yy,181,217);
        $this->line(205,$yy,205,217);

        $this->SetTopMargin($yy + 1);
    }

    // Page footer
    public function Footer() 
    {
        $this->SetFont('copperplateccheavy', 'B', 10,false);
        global $master_pdf;
        global $company_pdf ;

        $amt_words  = number_to_word($master_pdf[0]['balance_amt']);
        $gst_amt = $master_pdf[0]['sgst_amt'] + $master_pdf[0]['cgst_amt'] + $master_pdf[0]['igst_amt'];
      
        $tbl_footer ='';
        $tbl_footer .='<table width="100%"  border="0" style="border:1px solid #000;" cellpadding="4" >
            <tr style="font-size:12px;">
                <th style="width:70%;text-align:right;"><b>Total Qty : </b>'.$master_pdf[0]['total_qty'].' &nbsp; &nbsp; &nbsp; </th>
                <th style="width:18%;"><b>SUB TOTAL</b></th>
                <th style="width:12%;text-align:right"><b>'.$master_pdf[0]['sub_amt'].'</b></th>
            </tr>
        </table>
       <table border="1" cellpadding="2">
        <tr>
            <td width="70%" style="font-size:10px;">
                <table>
                    <tr>
                        <td height="35px;">TERMS & CONDITION : <br/>
                            TRIAL DATE : '.$master_pdf[0]['trial_date'].'<br/>
                            FINISH DATE : '.$master_pdf[0]['delivery_date'].'<br/>
                            MASTER NAME :  '.$master_pdf[0]['master_name'].'
                        </td>
                    </tr>
                </table>
            </td>
            <td width="30%">
                <table style="line-height: 1.8;">';
            if($master_pdf[0]['disc_amt'] >0){
                $tbl_footer .='<tr>
                    <td width="40%" style="font-size:10px;" align="left">DISC AMT </td>
                    <td width="60%"style="font-size:10px;" align="right">&nbsp;&nbsp;'.$master_pdf[0]['disc_amt'].'</td>
                </tr>';
            }else{
                $tbl_footer .='<tr>
                    <td width="40%" style="font-size:10px;" align="left"></td>
                    <td width="60%"style="font-size:10px;" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>';
            }
                
            
            $tbl_footer .='</table>
            </td>       
         </tr>
         <tr>
            <td width="70%"></td>
            <td width="30%">
                <table>
                    <tr>
                        <td width="50%" style="font-size:10px;" align="left">TOTAL NET AMT. </td>
                        <td width="50%" style="font-size:10px;" align="right">'.$master_pdf[0]['total_amt'].'</td>
                    </tr>
                </table>
            </td>
         </tr>
            
        </table>
        <table cellpadding="1" border="1" style="font-size:10px;">
            <tr>
                
                <td width="70%" style="font-size:10px;">
                    <table>
                        <tr>
                            <td>Amt. in Words : '.$amt_words.'</td>
                        </tr>
                    </table> 
                </td>           
                
                <td width="30%">
                    <table >
                        <tr>
                            <td width="50%" style="font-size:10px;" align="left">ADVANCE AMT. </td>
                            <td width="50%" style="font-size:10px;" align="right">'.$master_pdf[0]['advance_amt'].'</td>
                        </tr>
                        <tr>
                            <td width="50%" style="font-size:10px;border-bottom:1px solid #000;" align="left">BALANCE</td>
                            <td width="50%" style="font-size:10px;border-bottom:1px solid #000;" align="right">'.$master_pdf[0]['balance_amt'].' &nbsp;</td> 
                        </tr> 
                        <tr>
                            <td width="100%" style="text-align:center;"><br/><br/>
                                For '.$company_pdf[0]['company_name'].'<br/>
                                <br/><br/><br/>
                                Signature
                            </td>
                        </tr>
                    </table> 
                </td>
            </tr>
        </table>';

        $this->writeHTML($tbl_footer, true, false, false, false, '');
        // Set font
        $this->SetFont('copperplateccheavy', 'I', 9);
        // Page number
        $this->Cell(0, 0, 'Page '.$this->getPageNumGroupAlias().'/'.$this->getPageGroupAlias(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array('210','297'), true, 'UTF-8', false); 
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
$pdf->SetFooterMargin(80);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 78);

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

$body .= '<table cellpadding="2" >';
            foreach($trans_data as $key => $value) :

                $goods_name = ($value['trans_type']=='FABRIC')?'<b>'.$value['fabric_name'].'</b> - '.$value['design_name'].' <br>'.$value['color_name']:(($value['trans_type']=='STITCHING')?'<b>'.$value['apparel_name'].'</b>':(($value['trans_type']=='PACKAGE')?'<b>'.$value['apparel_name'].'</b> - '.$value['fabric_name'].' <br>'.$value['design_name'].' <br>'.$value['color_name']:(($value['trans_type']=='READYMADE')?'<b>'.$value['category_name'].'</b> - '.$value['design_name'].' <br>'.$value['color_name']:'')));

                $category_name = ($value['trans_type']=='FABRIC')?$value['fabric_name']:(($value['trans_type']=='STITCHING')?$value['apparel_name']:(($value['trans_type']=='PACKAGE')?$value['apparel_name']:(($value['trans_type']=='READYMADE')?$value['product_name']:'')));
                $mtr = ($value['total_mtr']>0) ? $value['total_mtr']: '';
                $body .= '<tr style="font-size:12px;">
                            <td style="width:3%;border-bottom-color:#ccc;text-align:center;">'.($key+1).'</td>
                            <td style="width:39%;border-bottom-color:#ccc;">'.$goods_name.'</td>
                            <td style="width:20%;border-bottom-color:#ccc;"><b>'.$category_name.'</b></td>  
                            <td style="width:8%;text-align:center;border-bottom-color:#ccc;">'.$value['qty'].'</td>
                            <td style="width:8%;text-align:center;border-bottom-color:#ccc;">'.$mtr.'</td>
                            <td style="width:10%;text-align:center;border-bottom-color:#ccc;">'.round($value['amt']).'</td>
                            <td style="width:12%;text-align:center;border-bottom-color:#ccc;">'.round($value['amt']).'</td>
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