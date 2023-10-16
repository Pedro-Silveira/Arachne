<?php
//call main fpdf file
require('fpdf.php');

class PDF_MC_Table extends FPDF {
	
function Header(){
	if($this->PageNo() == 1){
        global $editar;
		global $retorno;
		
		if($retorno != 0){
			$this->Image('images/logo-preto.png', 10, 10, 50);
			$this->SetFont('Arial', 'B', 16);
			$this->Cell(0, 10, utf8_decode('Relatório de Serviço'), 0, 0, 'R');
			$this->Ln(20);
			$this->SetFont('Arial', 'B', 14);
			$this->Cell(0, 10, utf8_decode('Data: '.date("d/m/Y", strtotime($editar["data"]))), 0, 1);
			$this->Cell(0, 10, utf8_decode('Sargento de Dia: '.$editar["sargento"]), 0, 1);
			$this->Cell(0, 10, utf8_decode('Motorista: '.$editar["motorista"]), 0, 0);
			$this->Ln(20);
		}else{
			$this->Image('images/logo-preto.png', 10, 10, 50);
			$this->SetFont('Arial', 'B', 16);
			$this->Cell(0, 10, utf8_decode('Relatório de Serviço'), 0, 0, 'R');
			$this->Ln(20);
			$this->SetFont('Arial', 'B', 14);
			$this->Cell(0, 10, utf8_decode('Não existe um serviço registrado nesta data!'), 0, 1);
		}
    }
}

function Footer(){
	if($this->isFinished){
		$this->SetY(-20);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(0, 10, '___________________________', 0, 1, 'R');
		$this->Cell(0, 0, utf8_decode('Página '.$this->PageNo().'/{nb}'), 0, 0, 'L');
		$this->Cell(0, 0, utf8_decode('Chefe da Seção de Inteligência'), 0, 0, 'R');
	}else{
		$this->SetY(-20);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(0, 10, '', 0, 1, 'R');
		$this->Cell(0, 0, utf8_decode('Página '.$this->PageNo().'/{nb}'), 0, 0, 'L');
	}
}

// variable to store widths and aligns of cells, and line height
var $widths;
var $aligns;
var $lineHeight;

//Set the array of column widths
function SetWidths($w){
    $this->widths=$w;
}

//Set the array of column alignments
function SetAligns($a){
    $this->aligns=$a;
}

//Set line height
function SetLineHeight($h){
    $this->lineHeight=$h;
}

//Calculate the height of the row
function Row($data)
{
    // number of line
    $nb=0;

    // loop each data to find out greatest line number in a row.
    for($i=0;$i<count($data);$i++){
        // NbLines will calculate how many lines needed to display text wrapped in specified width.
        // then max function will compare the result with current $nb. Returning the greatest one. And reassign the $nb.
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    }
    
    //multiply number of line with line height. This will be the height of current row
    $h=$this->lineHeight * $nb;

    //Issue a page break first if needed
    $this->CheckPageBreak($h);

    //Draw the cells of current row
    for($i=0;$i<count($data);$i++)
    {
        // width of the current col
        $w=$this->widths[$i];
        // alignment of the current col. if unset, make it left.
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //calculate the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}
}
?>