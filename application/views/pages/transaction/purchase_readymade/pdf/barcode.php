<?php
// Include the main TCPDF library (search for installation path).
$this->mypdf_class->tcpdf();
global $barcode_data ; 
global $company_name;
global $description;
global $category_name;
global $product_name;
global $design_name;
global $color_name;
global $size_name;
global $cost_char;
global $mrp;
global $qty;
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
		global $category_name;
		global $product_name;
		global $design_name;
		global $color_name;
		global $size_name;
		global $cost_char;
		global $mrp;
		global $qty;
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
										<b>'.$product_name.'</b>
									</td>
								</tr>
								<tr>
									<td width="100%" height="30px"><tcpdf method="write1DBarcode" params="'.$params.'" /></td>
								</tr>
								<tr>
									<td width="100%" style="font-size: 10px;" align="left">
										<b>'.$roll_no.'</b>
									</td>
								</tr>';
							if(!empty($design_name) && $design_name !='NA'){				
							$tbl .= '<tr>
									<td width="100%" style="font-size: 11px;" align="left">
										<b>'.$design_name.'</b>
									</td>
								</tr>';
							}
							if(!empty($color_name)){ 			
							$tbl .= '<tr>
									<td width="100%" style="font-size: 12px;" align="left">
										<b>Color: '.$color_name.'</b>
									</td>
								</tr>';
							}else{
								$tbl .= '<tr>
									<td width="100%" style="font-size: 12px;" align="left">
									</td>
								</tr>';
							}
							if(!empty($color_name)){
							$tbl .= '<tr>
									<td width="100%" style="font-size: 11px;" align="left">
										<b>Size: '.$size_name.'</b>
									</td>
								</tr>';
							}else{
								$tbl .= '<tr>
									<td width="100%" style="font-size: 12px;" align="left">
									</td>
								</tr>';
							}
					$tbl .='			
							</table>
						</td>
						<td width="55%">
							<table>';
						if($mrp>0){	
							$tbl .='<tr>
									<td width="100%" style="font-size: 12px;" align="left">
										<b>MRP : <span style="font-family:dejavusans;">&#8377;</span>'.round($mrp).' /-</b>
									</td>
								</tr>';
						}		
						$tbl .='<tr>
									<td width="100%" style="font-size: 12px;" align="left">
										<b>QTY : '.$qty.'</b>
									</td>
								</tr>
								
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
// $pdf->SetFont('helvetica', '', 10);
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
	$product_name 			= $value['product_name'];
	$design_name 			= $value['design_name']; 

	$category_name 			= $value['readymade_category_name'];
	$color_name 			= $value['color_name'];
	$size_name 				= $value['size_name'];
	$mrp 					= $value['mrp'];
	$qty					= $value['qty'];
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