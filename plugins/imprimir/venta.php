<?php
        session_start();
        extract($_REQUEST);
        include "../clases/connection.php"; 
        $connection = new connection(); 
         
        $sql = "select current_timestamp";
        $data = $connection->query($sql);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $fila = $results[0]; 
        
        $_SESSION['fecha'] = $fila[0];
        
        require('fpdf18/fpdf.php'); 
        class PDF extends FPDF
        {
            // Cabecera de página
            function Header()
            {
                $this->Image('logo.jpg' , 10 ,10, 0 , 0,'JPG');
                $this->SetY(30);               
            }
            
            // Pie de página
            function Footer()
            {
                $this->SetY(-0);
                //Arial italic 8
                $this->SetFont('TIMES','I',8);
                //Page number
                $this->Cell(0,10,'Fecha de generacion '.$_SESSION['fecha'],0,0,'R');
            }
        }
              
        $pdf=new PDF();

        $pdf->AddPage();
        $x = 80;
        
        
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('TIMES','',12);              
        
        $pdf->SetDrawColor(0,0,0);

        $pdf->SetX(175);
        $pdf->SetFillColor(216,216,216);
        $pdf->Cell(30,8,"REMISION",1,0,'C',true);   
        $pdf->Ln();

        if( $numero < 9 )
          $remision = "000$numero";
        else if( $numero >= 10 && $numero <= 99 )
          $remision = "00$numero";
        else if( $numero >= 100 && $numero <= 909 )
          $remision = "0$numero";
        else 
          $remision = $numero;


        $pdf->SetX(175);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(30,8,"$remision",1,0,'C',true);    

        $pdf->Ln();
        $pdf->SetX(152); 
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(60,6,"Movil: (+57) 304 339 46 18",0,0,'C',true); 
        $pdf->Ln();
        $pdf->Ln();



        $pdf->SetFillColor(216,216,216); 
        $pdf->SetFont('TIMES','B',14);              

        $pdf->Cell(20,14,"CANT",1,0,'C',true); 
        $pdf->Cell(110,14,utf8_decode("DESCRIPCIÓN"),1,0,'C',true); 
        $pdf->Cell(35,14,"V. UNITARIO",1,0,'C',true); 
        $pdf->Cell(30,14,"V. TOTAL",1,0,'C',true); 
        $pdf->Ln();


        $query = "SELECT 
                       venta.cantidad ,
                       venta.total precio ,
                       producto.nombre ,
                       (venta.cantidad * venta.total) as total
                  FROM venta 
                  INNER JOIN producto ON producto.id = producto_id
                   WHERE numero = $numero";
        $data = $connection->query($query);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
          
        $pdf->SetFont('TIMES','',12);    
        $pdf->SetFillColor(255,255,255);

        $total = 0; 

        if( count( $results) > 0 ) 
        { 
             
                   
             foreach( $results as $fila )
             {  
                   
                 $total += $fila['total'];                        
                  
                 $fila['precio'] = number_format($fila['precio'],0,"",".");
                 $fila['total'] = number_format($fila['total'],0,"",".");

                 $pdf->Cell(20,10,$fila['cantidad'],1,0,'L',true); 
                 $pdf->Cell(110,10,utf8_decode($fila['nombre']),1,0,'L',true); 
                 $pdf->Cell(35,10,"$ ". $fila['precio'],1,0,'R',true); 
                 $pdf->Cell(30,10,"$ ". $fila['total'],1,0,'R',true); 
                 $pdf->Ln();
                 
             }
        }
        $total = number_format($total,0,"B",".");
        

        $pdf->Cell(20,10,"",1,0,'L',true); 
        $pdf->Cell(110,10,"",1,0,'L',true); 
        $pdf->SetFillColor(216,216,216); 

        $pdf->Cell(35,10,"TOTAL",1,0,'C',true); 
        $pdf->SetFillColor(255,255,255); 

        $pdf->Cell(30,10,"$ ". $total,1,0,'R',true); 
        $pdf->Ln();
        $pdf->Ln();


        $pdf->Cell(80,30,"VENDEDOR",'B',0,'L',true); 
        $pdf->SetX(125);
        $pdf->Cell(80,30,"CLIENTE",'B',0,'L',true); 
         
    
        $pdf->Output();       
?>