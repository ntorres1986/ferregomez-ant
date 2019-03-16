<?php
    session_start();

    extract($_REQUEST);    
    include "../../clases/connection.php";
    require"../../plugins/fpdf181/fpdf.php";
    include "../../clases/func.php";  
    include "../../clases/EnLetras.php";

    $numLetras = new EnLetras();

    $connection = new connection();

   
    class PDF extends FPDF
    { 
        public function setVars($imagen,$ciudad)
        {
          $this->imagen=$imagen; 
          $this->ciudad=$ciudad; 
        }
        function Header()
        {
           $this->Image('ferregomez_encabezado_sin_dir.png' ,140,19, 0,0,'PNG');
           $this->Ln();

           //$this->AddFont('Arial','','Arialui.php');

           $this->SetFillColor(0,0,0);     
           $this->SetFillColor(233,233,233); 
           $this->Ln();
           $this->SetFont('Arial','',8);
        } 
        function Footer()
        {
            $this->SetY(-20);
            //$this->SetFont('Arial','',8); 
            $this->Cell(0,10,'Pagina '.$this->PageNo().' de {nb}',0,0,'C');
        }
    }
    
    
    $sql = "";

      
    $pdf=new PDF();
    $pdf->SetMargins(20, 20 , 30); 
    $pdf->SetAutoPageBreak(true,10);  
    $pdf->AliasNbPages();

    $pdf->AddPage();
    $pdf->SetFont('Arial','',12); 
    $pdf->SetFillColor(0,0,0);     
    $pdf->SetFillColor(233,233,233);
     

    $query = "SELECT 
                abono_compra.fecha ,
                concat(nombres,' ',apellidos) proveedor, 
                direccion , 
                documento , 
                abono ,
                firmado
              FROM 
                abono_compra 
              INNER JOIN compra ON compra.id = compra_id 
              INNER JOIN proveedor ON  proveedor.id = proveedor_id
              WHERE abono_compra.id = $id";
    $data = $connection->query($query);  
    $result = $data->fetch(PDO::FETCH_BOTH );  

  
    $son = $numLetras->ValorEnLetras($result['abono'],"pesos");
    $valor = format( $result['abono']);

    $texto = "RECIBÍ de la Sra. Patricia,  la cantidad de $ $valor   , $son  , por concepto de la compra de mercancia en la fecha $result[fecha]";
 
    
    $correo = "" ; 

    $alto = 10;

   
    $pdf->Ln(); 
    $fecha = date("Y-M-d");
    $pdf->Cell(30,$alto,"CIUDAD  : MEDELLIN  $result[fecha]"  ,0,0,'L',false);    
   
    $pdf->Ln();  
   
    $pdf->Cell(30,$alto,"SEÑOR (A): " ,0,0,'L',false);               
    $pdf->Cell(30,$alto,"$result[proveedor]",0,0,'L',false);
    $pdf->Ln();
    
    $pdf->Cell(30,$alto,"DOCUMENTO:",0,0,'L',false);
    $pdf->Cell(30,$alto,"$result[documento]",0,0,'L',false);
    $pdf->Ln();
   
    $pdf->Cell(30,$alto,"DIRECCION:",0,0,'L',false);
    $pdf->Cell(30,$alto,"$result[direccion]",0,0,'L',false);               
    $pdf->Ln();
 
   
    $pdf->Ln(); 


    $pdf->MultiCell(175,5,$texto,0,'J'); 

    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();

    $pdf->Cell(30,$alto,"Atentamente",0,0,'L',false);
    $pdf->Ln();
    $pdf->Cell(30,$alto,"Ferregomez JP",0,0,'L',false);
    
  if( file_exists("../firmas/$result[firmado]"))
    $pdf->Image("../firmas/$result[firmado]",120,100,0,0,'PNG'); 

    //$pdf->Ln();
    //$pdf->Ln();
    //$pdf->Cell(30,$alto,"En caso de que usted ya haya cancelado favor hacer caso omiso a este comunicado.",0,0,'L',false);

    $pdf->Ln(98);

    $pdf->SetFont('Arial','',10); 
    $pdf->MultiCell(170,5,"Cra 55 No. 46-24 Tel: 2513071/2518048 Med. Local:190 Centro Cial Los Buenos Negocios",0,'J');
    $pdf->MultiCell(170,5,"Ferregomez JP",0,'C');
             
      // }
   // }

    $prefijo = substr(md5(uniqid(rand())),0,6);
    if(file_exists("Firma$prefijo.pdf") )
         unlink("Firma.pdf");
          

        $pdf->Output("Firma$prefijo.pdf","F");
        echo json_encode( array( "pdf_uri" => "Firma$prefijo.pdf"  ) );   
 
    
 
    function format( $value )
    {
        return number_format($value,0,"",".");
    }
?>