<?php

include 'connectdb.php';

$sql = "SELECT d.nome as disciplina, u.nome as professor, c.nome as curso, p.cargahorsem, p.cargahortot, 
p.ementa, p.objetivos, p.conteudo, p.metodologia, p.avaliacao, p.recursos, p.bibliografiab, p.bibliografiac 
FROM disciplina d INNER JOIN planoensino p ON p.disid = d.id INNER JOIN usuario u ON d.usuid = u.id 
INNER JOIN curso c ON c.id = d.curid WHERE d.id = '$disid' ORDER BY 1";

$query =  mysql_query ($sql) or die(mysql_error());
$linha = mysql_fetch_array($query);

$disciplina = $linha["disciplina"];
$professor = $linha["professor"];
$curso = $linha["curso"];
$cargahorsem = $linha["cargahorsem"];
$cargahortot = $linha["cargahortot"];
$ementa = $linha["ementa"];
$objetivos = $linha["objetivos"];
$conteudo = $linha["conteudo"];
$metodologia = $linha["metodologia"];
$avaliacao = $linha["avaliacao"];
$recursos = $linha["recursos"];
$bibliografiab = $linha["bibliografiab"];
$bibliografiac = $linha["bibliografiac"];

mysql_close($dblink);

require 'fpdf17/fpdf.php';

//Instanciation of inherited class
$pdf=new FPDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin(20);
$pdf->SetRightMargin(20);
//*** CURSO
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10,'CURSO',0,0);
$pdf->SetX(45);
$pdf->SetFont('Times','',10);
$pdf->Cell(0,10,"$curso",0,0);
$pdf->SetFillColor(0,0,0);
//$pdf->Rect(20, 40, 20, 6 ,'D');
//$pdf->Rect(40, 40, 150, 6 ,'D');
//**** PROFESSOR
$pdf->Ln(10);
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10,'PROFESSOR',0,0);
$pdf->Rect(20, $pdf->GetY()+1.7, 170, 6 ,'D');
$pdf->Ln(8);
$pdf->SetFont('Times','',10);
$pdf->Cell(0.1);
$pdf->MultiCell(170,5,"$professor");
//**** DISCIPLINA
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10,'DISCIPLINA',0,0);
$pdf->Rect(20, $pdf->GetY()+1.7, 170, 6 ,'D');
$pdf->Ln(6);
$pdf->SetFont('Times','',10);
$pdf->Cell(0.1);
$pdf->Cell(0,10,"$disciplina",0,0);
//**** CARGA
$pdf->Ln(8);
$pdf->Cell(1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10, utf8_decode('CARGA HORÁRIA'),0,0);
$pdf->SetX(55);
$pdf->Cell(0,10, utf8_decode('CARGA HORÁRIA'),0,0);
$pdf->SetX(91);
$pdf->Cell(0,10,'TERMO LETIVO',0,0);
$pdf->SetX(133);
$pdf->Cell(0,10, utf8_decode('PERÍODO'),0,0);
$pdf->SetX(158);
$pdf->Cell(0,10, utf8_decode('ANO/EXERCÍCIO'),0,0);

$pdf->Rect(20,  $pdf->GetY()+1.7, 34, 10 ,'D');
$pdf->Rect(54,  $pdf->GetY()+1.7, 34, 10 ,'D');
$pdf->Rect(88,  $pdf->GetY()+1.7, 34, 10 ,'D');
$pdf->Rect(122, $pdf->GetY()+1.7, 34, 10 ,'D');
$pdf->Rect(156, $pdf->GetY()+1.7, 34, 10 ,'D');

$pdf->Ln(4);
$pdf->Cell(10);
$pdf->Cell(0,10,'SEMESTRAL',0,0);
$pdf->SetX(62);
$pdf->Cell(0,10,'SEMANAL',0,0);
$pdf->SetFont('Times','',10);

$pdf->Ln(6);
$pdf->SetX(20);
$pdf->Cell(34,10,"$cargahortot",0,0,'C');
$pdf->SetX(54);
$pdf->Cell(34,10,"$cargahorsem",0,0,'C');
$pdf->SetX(88);
$pdf->Cell(34,10,'',0,0,'C');
$pdf->SetX(122);
$pdf->Cell(34,10,'',0,0, 'C');
$pdf->SetX(156);
$pdf->Cell(34,10, date("Y"),0,0, 'C');

$pdf->Rect(20,  $pdf->GetY()+1.7, 34, 6 ,'D');
$pdf->Rect(54,  $pdf->GetY()+1.7, 34, 6 ,'D');
$pdf->Rect(88,  $pdf->GetY()+1.7, 34, 6 ,'D');
$pdf->Rect(122, $pdf->GetY()+1.7, 34, 6 ,'D');
$pdf->Rect(156, $pdf->GetY()+1.7, 34, 6 ,'D');

//********** EMENTA
$pdf->Ln(10);
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10,'EMENTA',0,0);
$pdf->Rect(20, $pdf->GetY()+1.7, 170, 6 ,'D');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(170,5,"$ementa");

//********** Objetivos
$linhaAtual = $pdf->GetY() + 3;
$pdf->Ln(1);
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10,'OBJETIVOS',0,0);
$pdf->Rect(20, $pdf->GetY()+1.7, 170, 6 ,'D');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(170,5,"$objetivos");

//********** CONTEUDO PROGRAMATICO
$linhaAtual = $pdf->GetY() + 3;
$pdf->Ln(1);
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10, utf8_decode('CONTEÚDO PROGRAMÁTICO'),0,0);
$pdf->Rect(20, $pdf->GetY()+1.7, 170, 6 ,'D');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(170,5,"$conteudo");
//********** METODOLOGIA DE ENSINO
$pdf->Ln(8);
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10,'METODOLOGIA DE ENSINO',0,0);
$pdf->Rect(20, $pdf->GetY()+1.7, 170, 6 ,'D');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(170,5,"$metodologia");
//********** AVALIACAO
$pdf->Ln(8);
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10,utf8_decode('AVALIAÇÃO'),0,0);
$pdf->Rect(20, $pdf->GetY()+1.7, 170, 6 ,'D');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(170,5,"$avaliacao");
//********** RECURSOS TEMATICOS
$pdf->Ln(8);
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10, utf8_decode('RECURSOS TEMÁTICOS'),0,0);
$pdf->Rect(20, $pdf->GetY()+1.7, 170, 6 ,'D');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(170,5,"$recursos");
//********** BIBLIOGRAFIA BASICA
$pdf->Ln(8);
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10, utf8_decode('BIBLIOGRAFIA BÁSICA'),0,0);
$pdf->Rect(20, $pdf->GetY()+1.55, 170, 6 ,'D');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(170,5,"$bibliografiab");
//********** BIBLIOGRAFIA COMPLEMENTAR
$pdf->Ln(8);
$pdf->Cell(0.1);
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10,'BIBLIOGRAFIA COMPLEMENTAR',0,0);
$pdf->Rect(20, $pdf->GetY()+1.55, 170, 6 ,'D');

$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(170,5,"$bibliografiac");

$pdf->Output("arquivos/$disid.pdf");
echo "<script>window.open('arquivos/$disid.pdf');</script>"; 

?>
