<?php
$this->mypdf_class->tcpdf();
class MYPDF extends TCPDF {
    public function Header() {}
    public function Footer() {}
}
$pdf = new MYPDF('L', PDF_UNIT, ['250', '350'], true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Imran Khan');
$pdf->SetTitle('MEASUREMENT SLIP');
$pdf->SetSubject('MEASUREMENT SLIP');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(5, 7, 7);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(74);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}
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
    $body .='<table border="0" cellpadding="6" style="border-top: 7px solid #000; border-bottom: 7px solid #000; font-size: 32px;">
                <tr style="background-color:#eee;">
                    <td style="width:50%;">Date : '.$value['_date'].' </td>
                    <td style="width:50%;" align="right">Entry No : '.$value['entry_no'].'</td>
                </tr>
                </table><br/><br/>
                <table border="0" style="border-bottom: 7px solid #000; font-size: 40px;">
                    <tr>
                        <td width="50%">'.$value['customer_name'].'</td>
                        <td width="50%" style="text-align:center;">'.$value['apparel_cnt'].' '.$value['apparel_name'].'</td>
                    </tr>
                </table><br/><br/>
                <table border="0" cellpadding="5" style="font-size: 20px;">
                <tr>
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
                    <td width="30%">
                        <table  border="1" cellpadding="5">';
                            if(empty($value['style_image_data'])):
                                $body .= '<tr><td style="width: 100%; text-align: center; color: red;">NO STYLE ADDED !!!</td></tr>';
                            else:
                                foreach ($value['style_image_data'] as $k => $v):
                                    $body .= '<tr>
                                                <td style="width: 50%;" colspan="2">'.$v['ast_name'].'</td>
                                                <td style="width: 50%;">
                                                    <img src="'.(ENV == DEV ? assets(NOIMAGE) : $v['ast_image']).'" width="80px" height="80px"/>
                                                </td>
                                            </tr>';
                                endforeach;
                            endif;
                        $body .='</table>
                    </td>
                </tr>
            </table>';
endforeach;
$pdf->writeHTMLCell(340, 200, 5, 5, $body, 0, 0, 0, true, 'L', true);
$pdf->Output('ORDER-MEASUREMENT-SLIP.pdf', 'I');