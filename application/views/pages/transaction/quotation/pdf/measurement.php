<?php
$this->mypdf_class->tcpdf();
class MYPDF extends TCPDF {
    public function Header() {}
    public function Footer() {}
}
$pdf = new MYPDF('P', PDF_UNIT, ['250', '350'], true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Imran Khan');
$pdf->SetTitle('MEASUREMENT SLIP');
$pdf->SetSubject('MEASUREMENT SLIP');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(5, 5, 5);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(74);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

$logo =base_url('public/assets/dist/images/logo.jpg');

$pdf->AddPage();
$pdf->SetFont('freesans', 'B', 9);
$body = '';
$mdivider = 9;
$sdivider = 5;
foreach ($trans_data as $key => $value):
    $colspan = ceil((count($value['style_data']) / $sdivider));
    if($key != 0) {
        $body .= '<br pagebreak="true"/>';
    }
    $body .='<table border="1" cellpadding="6" style="font-size: 12px;">
                <tr>
                    <td style="width:60%;text-align:center"><img src="'.$logo.'" height="70px"> <br/> <span style="text-decoration:underline;text-transform:uppercase;">Measurement Form</span></td>
                    <td style="width:20%;" align="right">Fabric Swatch</td>
                </tr>
                </table>
                <table border="0" style="font-size: 14px;border-left: 1px solid #000; border-bottom: 1px solid #000;border-right: 1px solid #000;" cellpadding="4">
                    <tr>
                        <td width="15%">ORDER NO </td>
                        <td width="40%"> : '.$value['entry_no'].'</td>
                        <td width="10%">DATE</td>
                        <td width="15%"> : '.$value['entry_date'].'</td>
                    </tr>
                    <tr>
                        <td width="25%">CUSTOMER NAME </td>
                        <td width="75%"> : '.$value['customer_name'].'</td>
                   </tr>
                    <tr>
                        <td width="15%">TRIAL DATE </td>
                        <td width="40%"> : '.$value['_date1'].'</td>
                        <td width="10%">DEL. DATE</td>
                        <td width="15%"> : '.$value['_date'].'</td>

                    </tr>
                </table>
                <table border="0" cellpadding="5" style="font-size: 12px;">
                <tr>
                <td width="50%">
                        <table  border="1" cellpadding="5">';
                            if(empty($value['style_image_data'])):
                                $body .= '<tr><td style="width: 100%; text-align: center; color: red;">NO STYLE ADDED !!!</td></tr>';
                            else:
                                foreach ($value['style_image_data'] as $k => $v):
                                    $body .= '<tr>
                                                <td style="width: 50%;" colspan="2">'.$v['ast_name'].'</td>
                                                <td style="width: 50%;">
                                                    <img src="'.((ENV == DEV) ? assets(NOIMAGE) : $v['ast_image']).'" width="80px" height="80px"/>
                                                </td>
                                            </tr>';
                                endforeach;
                            endif;
                        $body .='</table>
                    </td>
                    <td width="30%">';
                           if(empty($value['measurement_data'])):
                                $body .= '<table  border="1" cellpadding="5"><tr><td style="width: 100%; text-align: center; color: red;">NO MEASUREMENT ADDED !!!</td></tr></table>';
                            else:
                                $body .= '<table  border="1" cellpadding="5">';
                                foreach ($value['measurement_data'] as $k => $v):
                                    $body .= '<tr>
                                                <td style="width: 50%;" colspan="2">'.$v['measurement_name'].'</td>
                                                <td style="width: 50%;">
                                                    <table border="0" cellpadding="5">
                                                        <tr>
                                                            <td style="border-right: 1px solid #000;">'.$v['value1'].'</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>';
                                endforeach;
                                $body .='</table>';
                            endif;
            $body .='</td>
                    
                </tr>
            </table>';
endforeach;
$pdf->writeHTMLCell(300, 200, 5, 5, $body, 0, 0, 0, true, 'P', true);
$pdf->Output('ORDER-MEASUREMENT-SLIP.pdf', 'I');