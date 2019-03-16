<?php
        extract($_REQUEST);      
        include "../../clases/connection.php";
        require"../../plugins/fpdf181/fpdf.php";
        include "../../clases/func.php";  
        $func = new func();
        $connection = new connection();  

         
           
        $sql = "SELECT current_timestamp , year(current_date) year , month(current_date) month , day(current_date) day";
        $data = $connection->query($sql);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $fila = $results[0];

          
        $_SESSION['fecha'] = $fila[0];  
           
              
        $pdf=new FPDF("L","cm",array(10,10)  );

        $pdf->AddPage(); 
        $pdf->Image('ferregomez_encabezado.png' , 1.5 ,0.4, 1 ,.6,'PNG');
        $pdf->SetY(0);
        $pdf->AddFont('Segoe','','segoeui.php'); 
        $pdf->SetFont('Segoe','',2);   
        $pdf->Cell(1.8,.4,'Fecha de generacion '.$_SESSION['fecha'],0,0,'R');

        $pdf->SetLineWidth(0);
         

        $pdf->AddFont('Segoeb','','segoeuib.php');
        $pdf->AddFont('Segoe','','segoeui.php');
        
        
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Segoeb','',3);            
        
        $pdf->SetDrawColor(255,255,255);

        $pdf->SetXY(4,.1);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(1.2,.4,"",1,0,'C',true);   
        $pdf->Ln();
        $pdf->SetXY(4,.5);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(1.2,.4,"",1,0,'C',true);   

        $pdf->SetDrawColor(0,0,0);

       

        $bolsas = 2;
        $vr_bolsas = 20;

        $cliente = "";
        $documento = "";
        $tipo_venta_id = 1;
        $usuario_id = "";
        $vendedor = $vendedor;

        if( !empty($nombre) )
            $cliente = $nombre;


        if( $tipo_venta_id == 1)
        {
            $tipopago = "CONTADO";
        }
        else
        {
            $tipopago = utf8_decode("CRÉDITO");
        } 


        $year = $results[0]['year'];
        $month = $func->GetNameMonth( $results[0]['month'] );
        $day = $results[0]['day'];   

        $pdf->Ln(.7);  
        $pdf->SetFont('Segoe','',2);       

        
        
        $pdf->SetX(1.4);
        $pdf->Cell(1,.2,"Fecha :",0,0,'L',true);  
        $pdf->SetXY(1.7 ,1.2);
        $pdf->Cell(1.2,.2,"$day de $month de $year ","B",1,'L',true); 
        
        $pdf->SetX(1.4);
        $pdf->Cell(1,.2,utf8_decode("Señor(es) "),0,0,'L',true); 

        $pdf->SetXY(1.7 ,1.4);
        $pdf->Cell(1.2,.2,utf8_decode($cliente),"B",0,'L',false); 

        $pdf->SetXY(3.8 ,1.4);
        $pdf->Cell(1,.2,utf8_decode("Nit. o C.C "),0,0,'L',true); 
        $pdf->SetXY(4.8 ,1.4);
        $pdf->Cell(.8,.2,utf8_decode($documento),"B",0,'L',true); 

        $pdf->Ln(.2); 

        $pdf->SetX(1.4);
        $pdf->Cell(1,.2,utf8_decode("Dirección"),0,0,'L',true); 
        $pdf->SetXY(1.7 ,1.6); 
        $pdf->Cell(1.2,.2,"","B",0,'L',true); 

        $pdf->SetXY(3.8 ,1.6); 
        $pdf->Cell(2,.2,utf8_decode("Pago"),0,0,'L',true); 
        $pdf->SetXY(4.8 ,1.6); 
        $pdf->Cell(.8,.2,$tipopago,"B",0,'L',true);  
 
        

        $pdf->Ln();
        $pdf->Ln(); 

        $pdf->SetX(1.4);

        $pdf->SetFillColor(223,223,223);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Segoeb','',2);                    

        $pdf->Cell(.4,.2,"CANT",1,0,'C',true); 
       
        $pdf->Cell(2.8,.2,utf8_decode("DESCRIPCIÓN"),1,0,'C',true); 
         
        $pdf->Cell(.7,.2,"V. UNITARIO",1,0,'C',true); 
        $pdf->Cell(.7,.2,"V. TOTAL",1,0,'C',true); 
        $pdf->Ln(); 

        
       
          
        $pdf->SetFont('Segoe','',3);  
        $pdf->SetFillColor(255,255,255);
        $pdf->SetTextColor(0,0,0); 
         
        $t = 0; 
        $registros = 0;

        if( !empty($nombres) and !empty($precio) && !empty($cantidad) )
        {    
             
            foreach ($nombres as $key => $value)
             {    
                 $registros++;
                 $pdf->SetX(1.4); 

                 $n = $nombres[$key];
                 $p = $precio[$key];
                 $c = $cantidad[$key];
                 $t += $c * $p;    

                 $n = str_replace(array('[', ']'), "", $n); 

                 $s_t = number_format( ($c*$p),0,"","."); 
                  
                 $p = number_format($p,0,"",".");

                 $pdf->Cell(.4,.2,$c,1,0,'L',true); 
                 $pdf->Cell(2.8,.2, strtoupper($n) ,1,0,'L',true); 
                 $pdf->Cell(.7,.2,"$ ". $p,1,0,'R',true); 
                 $pdf->Cell(.7,.2,"$ ". $s_t,1,0,'R',true); 
                 $pdf->Ln();

                 if( $registros == 27 )
                   $pdf->AddPage(); 
                 
             }
        } 

       
        $pdf->SetX(1.4);         
        $pdf->Cell(.4,.2,"",1,0,'L',true); 
        $pdf->Cell(2.8,.2,"",1,0,'L',true);  
        $pdf->SetFillColor(223,223,223); 
        $pdf->SetTextColor(0,0,0); 
        $pdf->Cell(.7,.2,"TOTAL",1,0,'L',true); 
        $pdf->SetFillColor(255,255,255); 
        $pdf->SetTextColor(0,0,0); 
        $pdf->Cell(.7,.2,"$ ". $t,1,0,'R',true); 
        $pdf->Ln();
        $pdf->Ln();
         

        $pdf->SetTextColor(0,0,0);  
        
        $pdf->SetX(1.3);   
        $pdf->SetFont('Segoe','',3);     
        $pdf->Cell(4.6,.2,"Vendedor : " .$vendedor,0,0,'L',true);   
        
        
 
        $prefijo = substr(md5(uniqid(rand())),0,6);

        if(file_exists("Recibos$prefijo.pdf") )
         unlink("Recibos$prefijo.pdf");
          

        $pdf->Output("Recibos$prefijo.pdf","F");
         echo "Recibos$prefijo.pdf" ; 
   
?>