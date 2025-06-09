<?php 
    $this->mypdf_class->tcpdf();
	global $company_pdf;
	global $master_pdf;
	global $trans_pdf;
	global $yy;

	$company_pdf = $company_data;
	$master_pdf  = $master_data;
	$trans_pdf   = $trans_data;

	class MYPDF extends TCPDF {
		public function Header(){
			global $company_pdf;
			global $master_pdf;
            $date_time 		    = date('d-m-Y h:i:s a');
            $title 	            = $master_pdf[0]['titles'][1].' '.$master_pdf[0]['titles'][2];
			$print_type 	    = 'TAX INVOICE';

			$entry_no 			= $master_pdf[0]['entry_no'];
            $entry_date			= date('d-m-Y', strtotime($master_pdf[0]['entry_date']));
			$bill_no 			= $master_pdf[0]['bill_no'];
			$order_no 			= $master_pdf[0]['order_no'];
			$notes 				= $master_pdf[0]['notes'];
			$supplier_name		= $master_pdf[0]['supplier_name'];
			$supplier_address	= $master_pdf[0]['supplier_address'];
			$transport_name		= $master_pdf[0]['transport_name'];

			$this->SetFont('Times', '', 8);
			$header='<table border="0" cellpadding="3">
                            <tr>
                                <td width="100%" style="font-size:16px; text-align:center; font-weight:bold;">'.$company_pdf[0]['company_name'].'</td>
                            </tr>
                            <tr>
                                <td width="100%" style="font-size:12px; text-align:center;">'.$company_pdf[0]['address'].'</td>
                            </tr>
                            <tr>
                                <td width="100%" style="font-size:12px; text-align:center;">'.$company_pdf[0]['city_name'].' - '.$company_pdf[0]['pincode'].' '.$company_pdf[0]['state_name'].', CODE : '.$company_pdf[0]['state_code'].'</td>
                            </tr>
                            <tr>
                                <td width="100%" style="font-size:12px; text-align:center;">GSTIN/UIN: '.$company_pdf[0]['gstin'].', EMAIL : '.$company_pdf[0]['email'].'</td>
                            </tr>
                            <tr>
                                <td width="100%" style="font-size:14px; text-align:center; font-weight:bold;">TAX INVOICE</td>
                            </tr>
                            <tr>
                                <td width="25%" style="font-size:12px; text-align:left;">INVOICE NO. : <b>'.$master_pdf[0]['entry_no'].'</b></td>
                                <td width="50%" style="font-size:12px; text-align:center;">PARTY : <b>'.$master_pdf[0]['customer_name'].'</b></td>
                                <td width="25%" style="font-size:12px; text-align:right;">DATED : <b>'.$master_pdf[0]['entry_date'].'</b></td>
                            </tr>
                        </table>				
                        <table cellpadding="5" border="1" style="font-weight:bold;">
                            <tr>
                                <th width="3%" 	style="text-align:center;">#</th>
                                <th width="13%" style="text-align:center;">DESCRIPTION OF GOODS</th>
                                <th width="6%"  style="text-align:center;">HSN/SAC</th>
                                <th width="6%"  style="text-align:right;">QTY</th>
                                <th width="6%"  style="text-align:right;">RATE</th>
                                <th width="6%"  style="text-align:right;">AMT</th>
                                <th width="10%" style="text-align:right;">DISC</th>
                                <th width="10%" style="text-align:right;">TAXABLE AMT</th>
                                <th width="10%" style="text-align:right;">SGST</th>
                                <th width="10%" style="text-align:right;">CGST</th>
                                <th width="10%" style="text-align:right;">IGST</th>
                                <th width="10%" style="text-align:right;">AMOUNT</th>
                            </tr>
                        </table>';
			$this->writeHTMLCell(285, 200, 5, 5, $header, 0, 0, 0, true, 'L', true);
			// $this->SetTopMargin(3);	
			$yy = $this->GetY();
			$yy = $yy + 37.5;
			$this->line(5,$yy,5,140);
			$this->line(13.5,$yy,13.5,140);
			$this->line(50.5,$yy,50.5,140);
			$this->line(67.7,$yy,67.7,140);
			$this->line(84.7,$yy,84.7,140);
			$this->line(102,$yy,102,140);
			$this->line(119,$yy,119,140);
			$this->line(147.5,$yy,147.5,140);
			$this->line(176,$yy,176,140);
			$this->line(204.5,$yy,204.5,140);
			$this->line(233,$yy,233,140);
			$this->line(261.5,$yy,261.5,140);
			$this->line(290,$yy,290,140);
			$this->SetTopMargin($yy + 10);
		}

		public function Footer(){
        	global $master_pdf;
        	$this->SetFont('helvetica', '', 8);
    		$footer='<table border="1" cellpadding="5">
                        <tr >
                            <td width="3%"  style="text-align:center;"></td>
                            <td width="13%" style="text-align:center;"></td>
                            <td width="6%"  style="text-align:center;">TOTAL</td>
                            <td width="6%"  style="text-align:right;">'.$master_pdf[0]['total_mtr'].'</td>
                            <td width="6%"  style="text-align:right;"></td>
                            <td width="6%"  style="text-align:right;">'.$master_pdf[0]['sub_amt'].'</td>
                            <td width="10%" style="text-align:right;">'.$master_pdf[0]['disc_amt'].'</td>
                            <td width="10%" style="text-align:right;">'.$master_pdf[0]['taxable_amt'].'</td>
                            <td width="10%" style="text-align:right;">'.$master_pdf[0]['sgst_amt'].'</td>
                            <td width="10%" style="text-align:right;">'.$master_pdf[0]['cgst_amt'].'</td>
                            <td width="10%" style="text-align:right;">'.$master_pdf[0]['igst_amt'].'</td>
                            <td width="10%" style="text-align:right;">'.$master_pdf[0]['net_amt'].'</td>
                        </tr>
                        <tr>
                            <td width="20%" >
                                <table border="0" cellpadding="5">
                                    <tr>
                                        <td width="100%" style="border-bottom: 1px solid #000;">PAYMENT MODE</td>
                                    </tr>
                                    <tr>
                                        <td width="100%">'.$master_pdf[0]['payment_mode'].'</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="80%" >
                                <table border="0" cellpadding="3">
                                    <tr>
                                        <td width="70%" ></td>
                                        <td width="10%" style="border-bottom: 1px solid #000; border-right: 1px solid #000; text-align: right;">DISC ('.$master_pdf[0]['bill_disc_per'].' %)</td>
                                        <td width="20%" style="border-bottom: 1px solid #000; text-align: right;">'.$master_pdf[0]['bill_disc_amt'].'</td>
                                    </tr>
                                    <tr>
                                        <td width="70%" ></td>
                                        <td width="10%" style="border-bottom: 1px solid #000; border-right: 1px solid #000; text-align:right;">ROUND OFF</td>
                                        <td width="20%" style="border-bottom: 1px solid #000; text-align:right;">'.$master_pdf[0]['round_off'].'</td>
                                    </tr>
                                    <tr>
                                        <td width="70%" ></td>
                                        <td width="10%" style="border-bottom: 1px solid #000; border-right: 1px solid #000; text-align:right;">BILL AMT</td>
                                        <td width="20%" style="border-bottom: 1px solid #000; text-align:right;">'.$master_pdf[0]['total_amt'].'</td>
                                    </tr>
                                    <tr>
                                        <td width="70%" ></td>
                                        <td width="10%" style="border-bottom: 1px solid #000; border-right: 1px solid #000; text-align:right;">ADVANCE AMT</td>
                                        <td width="20%" style="border-bottom: 1px solid #000; text-align:right;">'.$master_pdf[0]['advance_amt'].'</td>
                                    </tr>
                                    <tr>
                                        <td width="70%" colspan="10">Amount Chargeable (in words) : '.number_to_word($master_pdf[0]['balance_amt']).'</td>
                                        <td width="10%" style="border-right: 1px solid #000; text-align:right;">BALANCE AMT</td>
                                        <td width="20%" style="text-align:right;">'.$master_pdf[0]['balance_amt'].'</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="20%" >
                                <table border="0" cellpadding="5">';
                                    $footer .= '<tr>
                                                    <td width="50%" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">TAXABLE</td>
                                                    <td width="50%" style="border-bottom: 1px solid #000;" >'.$master_pdf[0]['taxable_amt'].'</td>
                                                </tr>';
                                    if($master_pdf[0]['cgst_amt'] > 0):
                                        $footer .= '<tr>
                                                        <td width="50%" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">CGST</td>
                                                        <td width="50%" style="border-bottom: 1px solid #000;">'.$master_pdf[0]['cgst_amt'].'</td>
                                                    </tr>';
                                    endif;
                                    if($master_pdf[0]['sgst_amt'] > 0):
                                        $footer .= '<tr>
                                                        <td width="50%" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">SGST</td>
                                                        <td width="50%" style="border-bottom: 1px solid #000;">'.$master_pdf[0]['sgst_amt'].'</td>
                                                    </tr>';
                                    endif;
                                    if($master_pdf[0]['igst_amt'] > 0):
                                        $footer .= '<tr>
                                                        <td width="50%" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">IGST</td>
                                                        <td width="50%" style="border-bottom: 1px solid #000;">'.$master_pdf[0]['igst_amt'].'</td>
                                                    </tr>';
                                    endif;
                $footer .= '</table>
                            </td>
                            <td width="80%" >
                                <table border="0" cellpadding="0">
                                    <tr>
                                        <td width="50%" height="60px">'.$master_pdf[0]['notes'].'</td>
                                        <td width="50%" height="60px" style="border-left: 1px solid #000; "align="center">
                                            for ZOOP NX, <br/><br/><br/><br/>Authorised Signatory
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>';
		  	$this->writeHTMLCell(0, 0, 5, 140, $footer, 0, 0, 0, true, 'L', true);
        	// Page number
        	$this->Cell(0, 120, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    	}
	}

	// create new PDF document
	$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Imran Khan');
	$pdf->SetTitle('TAX INVOICE');
	$pdf->SetSubject('TAX INVOICE');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(5, 0, 7);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(74);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 25);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) 
	{
	    require_once(dirname(__FILE__).'/lang/eng.php');
	    $pdf->setLanguageArray($l);
	}


	$pdf->SetFont('Times', '', 8);
	$pdf->AddPage('L');
	$tbl = '<table border="1" cellpadding="5">';
                foreach ($trans_pdf as $key => $value){
                    $tbl.=' <tr >
                                <td width="3%"  style="text-align:center;">'.($key+1).'</td>
                                <td width="13%" style="text-align:center;">'.$value['trans_type'].'</td>
                                <td width="6%"  style="text-align:center;">'.$value['hsn_name'].'</td>
                                <td width="6%"  style="text-align:right;">'.$value['mtr'].'</td>
                                <td width="6%"  style="text-align:right;">'.$value['rate'].'</td>
                                <td width="6%"  style="text-align:right;">'.$value['amt'].'</td>
                                <td width="10%" style="text-align:right;">('.$value['disc_per'].' %) '.$value['disc_amt'].'</td>
                                <td width="10%" style="text-align:right;">'.$value['taxable_amt'].'</td>
                                <td width="10%" style="text-align:right;">('.$value['sgst_per'].' %) '.$value['sgst_amt'].'</td>
                                <td width="10%" style="text-align:right;">('.$value['cgst_per'].' %) '.$value['cgst_amt'].'</td>
                                <td width="10%" style="text-align:right;">('.$value['igst_per'].' %) '.$value['igst_amt'].'</td>
                                <td width="10%" style="text-align:right;">'.$value['total_amt'].'</td>
                            </tr>';
                }
	        $tbl.=' </table>';
	$pdf->writeHTML($tbl, true, false, false, false, '');
	// $pdf->writeHTMLCell(188, 150, 6, 134, $tbl, 0, 0, 0, true, 'L', true);
	$pdf->IncludeJS("print();");
	// ---------------------------------------------------------

	//Close and output PDF document
	ob_end_clean();
	$pdf->Output('TAX INVOICE', 'I');
	//============================================================+
	// END OF FILE
	//============================================================+
