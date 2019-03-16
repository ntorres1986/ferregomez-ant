<?php 
        date_default_timezone_set('America/Bogota');
        session_start();
        extract($_REQUEST);
        include "../../clases/connection.php";
        require"../../plugins/fpdf181/fpdf.php";
        include "../../clases/func.php";  
   
        $func = new func();
        $connection = new connection();   
    
   

        $_SESSION['fecha'] = $date = date('Y-m-d H:i:s'); 

        if( empty($_POST['id']) )
        {
            $request = file_get_contents('php://input');   
            $request = json_decode($request);
            $id = $request->id;
        } 
        
              
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
        
        $pdf->SetDrawColor(0,0,0);

        $pdf->SetXY(4,.1);
        $pdf->SetFillColor(216,216,216);
        //$pdf->Cell(1.2,.4,"COTIZACION",1,0,'C',true);   
        $pdf->Ln();
        $pdf->SetXY(4,.5);
        $pdf->SetFillColor(255,255,255);
        //$pdf->Cell(1.2,.4,"",1,0,'C',true);    
       

        $query = "SELECT 
                       cotizacion.id,  
                       producto_cotizacion.cantidad ,
                       producto_cotizacion.precio ,
                       producto.nombre ,
                       YEAR( cotizacion.fecha ) year ,
                       MONTH( cotizacion.fecha ) month ,
                       DAY( cotizacion.fecha ) day ,
                       (producto_cotizacion.cantidad * producto_cotizacion.precio) as total ,
                       concat(nombres,' ',apellidos) cliente ,
                       cliente.documento 
                  FROM cotizacion 
                  INNER JOIN producto_cotizacion ON cotizacion_id = cotizacion.id
                  INNER JOIN cliente ON cotizacion.cliente_id = cliente.id 
                  INNER JOIN producto ON producto.id = producto_cotizacion.producto_id
                  WHERE cotizacion.id = $id";


        $data = $connection->query($query);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $numero = $results[0]['id'];
        $cliente = $results[0]['cliente'];
        $documento = $results[0]['documento'];

        
        if( $numero < 9 )
          $remision = "000$numero";
        else if( $numero >= 10 && $numero <= 99 )
          $remision = "00$numero";
        else if( $numero >= 100 && $numero <= 999 )
          $remision = "0$numero";
        else 
          $remision = $numero;

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
        $pdf->Cell(2,.2,utf8_decode("TIPO"),0,0,'L',true); 
        $pdf->SetXY(4.8 ,1.6); 
        $pdf->Cell(.8,.2,"COTIZACION","B",0,'L',true);  
 
        

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
         
        $total = 0; 
        $registros = 0;

        if( count( $results) > 0 ) 
        {      
             foreach( $results as $fila )
             {     
                 $registros++;
                 $pdf->SetX(1.4); 

                 $total += $fila['total'];                        
                  
                 $fila['precio'] = number_format($fila['precio'],0,"",".");
                 $fila['total'] = number_format($fila['total'],0,"",".");

                 $pdf->Cell(.4,.2,$fila['cantidad'],1,0,'L',true); 
                 $pdf->Cell(2.8,.2, strtoupper  ( $fila['nombre']) ,1,0,'L',true); 
                 $pdf->Cell(.7,.2,"$ ". $fila['precio'],1,0,'R',true); 
                 $pdf->Cell(.7,.2,"$ ". $fila['total'],1,0,'R',true); 
                 $pdf->Ln();
                 if( $registros == 27 )
                   $pdf->AddPage(); 
                 
             }
        } 
 

        $total = number_format($total,0,"B","."); 
 
        $pdf->SetX(1.4);         
        $pdf->Cell(.4,.2,"",1,0,'L',true); 
        $pdf->Cell(2.8,.2,"",1,0,'L',true);  
        $pdf->SetFillColor(223,223,223); 
        $pdf->SetTextColor(0,0,0); 
        $pdf->Cell(.7,.2,"TOTAL",1,0,'L',true); 
        $pdf->SetFillColor(255,255,255); 
        $pdf->SetTextColor(0,0,0); 
        $pdf->Cell(.7,.2,"$ ". $total,1,0,'R',true); 
        $pdf->Ln();
         

        $pdf->SetTextColor(0,0,0);  
        $vendedor = "";
        
        if( !empty($usuario_id) )
        {
            $query = "SELECT concat(nombre,' ',apellido) usuario FROM usuario WHERE id = $usuario_id";
            $data = $connection->query($query);
            $result = $data->fetch(PDO::FETCH_BOTH ); 
            $vendedor = "Vendedor : ".$result['usuario'];

            $pdf->SetX(1.4);   
            $pdf->SetFont('Segoe','',3);     
            $pdf->Cell(4.6,.2,$vendedor,0,0,'L',true);   
        }
        
 
        $prefijo = substr(md5(uniqid(rand())),0,6);

        if(file_exists("Cotizacion$prefijo.pdf") )
         unlink("Cotizacion.pdf");
          

        $pdf->Output("Cotizacion$prefijo.pdf","F");
        echo json_encode( array( "pdf_uri" => "Cotizacion$prefijo.pdf"  ) );   
 
         
?>