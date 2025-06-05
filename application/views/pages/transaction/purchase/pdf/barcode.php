<?php
// Include the main TCPDF library (search for installation path).
$this->mypdf_class->tcpdf();
global $barcode_data ; 
global $company_name;
global $description;
global $fabric_name;
global $design_name;
global $category_name;
global $color_name;
global $width_name;
global $cost_char;
global $mrp;
global $mtr;

global $shirt_mrp;
global $trouser_mrp;
global $twopc_suit_mrp;
global $threepc_suit_mrp;
global $jacket_mrp;

global $qrcode;
global $roll_no;
global $params;

$barcode_data = $data['barcode_data'];
$company_pdf  = $data['company_data'];

class MYPDF extends TCPDF 
{
    //Page header
    public function Header()  
    {
		
    }

    // Page footer
    public function Footer() 
    {	
    	$this->SetY(1);
		global $barcode_data ; 
		global $company_name;
		global $description;
		global $fabric_name;
		global $design_name;
		global $category_name;
		global $color_name;
		global $width_name;
		global $cost_char;
		global $mrp;
		global $mtr;

		global $shirt_mrp;
		global $trouser_mrp;
		global $twopc_suit_mrp;
		global $threepc_suit_mrp;
		global $jacket_mrp;

		global $qrcode;
		global $roll_no;
		global $params;
		$logo = base_url('public/assets/dist/images/vlogo.jpg');

		$this->Image($logo, 65, '', 4, 30, '', '', '', false, 300);

		$tbl = '
				<table border="0" cellpadding="4">		
					<tr>
						<td width="45%">
							<table>
								<tr>
									<td width="100%" style="font-size: 11px;" align="left">
										<b>'.$fabric_name.'</b>
									</td>
								</tr>
								<tr>
									<td width="100%" height="30px"><tcpdf method="write1DBarcode" params="'.$params.'" /></td>
								</tr>
								<tr>
									<td width="100%" style="font-size: 10px;" align="left">
										<b>'.$roll_no.'</b>
									</td>
								</tr>
								<tr>
									<td width="100%" style="font-size: 11px;" align="left">
										<b>'.$design_name.'</b>
									</td>
								</tr>
								<tr>
									<td width="100%" style="font-size: 11px;" align="left">
										<b>'.$category_name.'</b>
									</td>
								</tr>
								<tr>
									<td width="100%" style="font-size: 11px;" align="left">
										<b>CLR:- '.$color_name.'</b>
									</td>
								</tr>
							</table>
						</td>
						<td width="50%">
							<table>
								<tr>
									<td width="100%" style="font-size: 10px;" align="left">
										<b>MRP : <span style="font-family:dejavusans;">&#8377;</span>'.$mrp.'   /-</b>
									</td>
								</tr>
								<tr>
									<td width="100%" style="font-size: 10px;" align="left">
										<b>QTY : '.$mtr.' - MTR</b>
									</td>
								</tr>
							';

							if($shirt_mrp>0){
								$tbl .= '<tr>
									<td width="100%" style="font-size: 10px;" align="left">
										<b>SHIRT <span style="font-family:dejavusans;">&#8377;</span>'.round($shirt_mrp).' /-</b>
									</td>
								</tr>';
							}
							if($trouser_mrp>0){
								$tbl .= '<tr>
									<td width="100%" style="font-size: 10px;" align="left">
										<b>TROUSER <span style="font-family:dejavusans;">&#8377;</span>'.round($trouser_mrp).' /-</b>
									</td>
								</tr>';
							}
							if($twopc_suit_mrp>0){
								$tbl .= '<tr>
									<td width="100%" style="font-size: 10px;" align="left">
										<b>2PC SUIT <span style="font-family:dejavusans;">&#8377;</span>'.round($twopc_suit_mrp).' /-</b>
									</td>
								</tr>';
							}
							if($threepc_suit_mrp>0){
								$tbl .= '<tr>
									<td width="100%" style="font-size: 10px;" align="left">
										<b>3PC SUIT <span style="font-family:dejavusans;">&#8377;</span>'.round($threepc_suit_mrp).' /-</b>
									</td>
								</tr>';
							}
							if($jacket_mrp>0){
								$tbl .= '<tr>
									<td width="100%" style="font-size: 10px;" align="left">
										<b>JACKET <span style="font-family:dejavusans;">&#8377;</span>'.round($jacket_mrp).' /-</b>
									</td>
								</tr>';
							}

								$tbl .= '
							</table>
						</td>
					
					</tr>
				</table>
				<table border="0" cellpadding="2">
					<tr>
						<td style="text-align:center">Pkd. By- Royal Clothing<br/>
						</td>
					</tr>
				</table>';		
		
		$this->writeHTML($tbl, true, false, false, false, '');
    }
}

// $page_size = array('38','34');
$page_size = array('38','70');

// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT,$page_size, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Imran Khan');
$pdf->SetTitle('Barcode Pdf');
$pdf->SetSubject('Barcode Pdf');
// $pdf->SetFont('copperplateccheavy', '', 10);
// $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(1, 0, 1);
$pdf->SetHeaderMargin(4);
$pdf->SetFooterMargin(12);

// $pdf->SetMargins(PDF_MARGIN_LEFT- 0, PDF_MARGIN_TOP-29, PDF_MARGIN_RIGHT-16);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 0);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}



$company_name = strtoupper($company_pdf[0]['company_name']);

foreach ($barcode_data as $key => $value) {
	$pdf->AddPage();
	$qrcode 				= $value['qrcode'];
	$roll_no 				= $value['roll_no'] == 0 ? '' : $value['roll_no'];
	$mrp 					= round($value['mrp']);
	$fabric_name 			= $value['fabric_name'];
	$design_name 			= $value['design_name'];
	$category_name 			= $value['category_name']; 

	$color_name 			= $value['color_name'];
	$width_name 			= $value['width_name'];
	$mrp 					= $value['mrp'];
	$mtr					= $value['mtr'];

	$shirt_mrp 				= $value['shirt_mrp'];
	$trouser_mrp 			= $value['trouser_mrp'];
	$twopc_suit_mrp 			= $value['twopc_suit_mrp'];
	$threepc_suit_mrp 			= $value['threepc_suit_mrp'];
	$jacket_mrp 			= $value['jacket_mrp'];

	$cost_char				= $value['cost_char'];
	$description 			= $value['description'];
	
	$options['bgcolor'] 	= array(255,255,255);
	$options['border'] 		= false;
	$options['fgcolor'] 	= array(0,0,0);
	$options['font'] 		= 'helvetica';
	$options['fontsize'] 	= 6;
	$options['padding'] 	= 1;
	$options['position'] 	= 'C';
	$options['stretchtext'] = 2;
	$options['text'] 		= false;

	$params = $pdf->serializeTCPDFtagParameters(array($qrcode, 'I25', '', '', 33, 9, 0.8, $options, 'S'));
}

// ---------------------------------------------------------


// note


// first declare all variable global becouse they can inherited by any class function

// then set page size 24,42 if landscape mode first para is height and second width

// if u increase page height then set value of setfootermargin 
// ---------------------------------------------------------
// $pdf->IncludeJS("print();");
//Close and output PDF document
$pdf->Output('Barcode.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+