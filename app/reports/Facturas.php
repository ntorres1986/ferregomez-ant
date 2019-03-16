<?php
      
        session_start();
        extract($_REQUEST);
        include "../../clases/connection.php";
        require"../../plugins/fpdf181/fpdf.php";
        include "../../clases/func.php";  
   
        $func = new func();
        $connection = new connection();  

        if( empty($_POST['id']) )
        {
            $request = file_get_contents('php://input');   
            $request = json_decode($request);
            $id = $request->id;
        }

        $query = "SELECT factura FROM venta WHERE id = $id";
        $data = $connection->query($query);        
        $result = $data->fetch(PDO::FETCH_ASSOC);

        if( $result['factura'] != 'NULL' )
        {
            $query = "SELECT id FROM resolucion WHERE status = 1";
            $data = $connection->query($query);        
            $result = $data->fetchAll(PDO::FETCH_ASSOC); 
            if( count($result) > 0 ) 
            {
                $resolucion_id = $result[0]['id']; 
                $query = "SELECT numero, id  FROM resolucion_numeros WHERE resolucion_id = $resolucion_id AND estado = 1 ORDER BY(numero) ASC LIMIT 1;";
                $data = $connection->query($query);
                $result = $data->fetch(PDO::FETCH_ASSOC);

                if( $result['factura'] != NULL && $result['factura'] != "" ) 
                {
                  $factura = $result['numero']; 
                  $factura_numeros_id = $result['id']; 
                  $query = "UPDATE venta SET factura = '$factura' WHERE id = $id";
                  $connection->query($query); 

                  $query = "UPDATE resolucion_numeros SET estado = 2 WHERE id = $factura_numeros_id";
                  $connection->query($query);

                }
                else
                {
                  $factura = NULL;
                  $factura_numeros_id = 0;
                }  
                
            }
        }    
        else 
          $factura = $result['factura'];

  
        
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
        $pdf->Cell(1.2,.4,"FACTURA DE VENTA",1,0,'C',true);   
        $pdf->Ln();
        $pdf->SetXY(4,.5);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(1.2,.4,"$factura",1,0,'C',true);    
       

        $query = "SELECT 
                       venta.factura,
                       venta.bolsas,
                       venta.vr_bolsas,
                       producto_venta.cantidad ,
                       producto_venta.precio ,
                       producto.nombre ,
                       YEAR( venta.fecha ) year ,
                       MONTH( venta.fecha ) month ,
                       DAY( venta.fecha ) day ,
                       (producto_venta.cantidad * producto_venta.precio) as total ,
                       concat(nombres,' ',apellidos) cliente ,
                       cliente.documento ,
                       tipo_venta_id
                  FROM venta 
                  INNER JOIN producto_venta ON venta_id = venta.id
                  INNER JOIN producto ON producto.id = producto_venta.producto_id
                  LEFT JOIN cliente ON venta.cliente_id = cliente.id 
                  WHERE venta.id = $id";
        $data = $connection->query($query);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $numero = $results[0]['factura'];
        $bolsas = $results[0]['bolsas'];
        $vr_bolsas = $results[0]['vr_bolsas'];

        $cliente = $results[0]['cliente'];
        $documento = $results[0]['documento'];
        $tipo_venta_id = $results[0]['tipo_venta_id'];

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

        $query = "SELECT * FROM venta_anexo WHERE venta_id = $id ";
        $data = $connection->query($query);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        if( count($results) > 0 )
        {
              foreach( $results as $fila )
              {     
                 $pdf->SetX(1.4); 
                 
                 $fila['nombre'] = utf8_encode($fila['nombre']); 
                
                 $sub_total = $fila['precio'] * $fila['cantidad']; 
                 $total += $sub_total;        
                  
                 $fila['precio'] = number_format($fila['precio'],0,"","."); 
                 $sub_total = number_format($sub_total,0,"","."); 

                 $pdf->Cell(.4,.2,$fila['cantidad'],1,0,'L',true); 
                 $pdf->Cell(2.8,.2,utf8_decode( strtoupper  ( $fila['nombre']) ),1,0,'L',true); 
                 $pdf->Cell(.7,.2,"$ ". $fila['precio'],1,0,'R',true); 
                 $pdf->Cell(.7,.2,"$ ". $sub_total,1,0,'R',true); 
                 $pdf->Ln(); 
              }
        }
        $bolsas = 1;
 
        if( $bolsas > 0 )
        {
             $pdf->SetX(1.4); 

             $total_bolsas = number_format($bolsas * $vr_bolsas ,0,"",".");
             $vr_bolsas = number_format( $vr_bolsas ,0,"",".");
             $pdf->Cell(.4,.2,$bolsas,1,0,'L',true); 
             $pdf->Cell(2.8,.2,"IMPUESTO BOLSAS",1,0,'L',true); 
             $pdf->Cell(.7,.2,"$ ". $vr_bolsas ,1,0,'R',true); 
             $pdf->Cell(.7,.2,"$ ". $total_bolsas,1,0,'R',true); 
             $pdf->Ln();
        } 

        $iva = $total * 16 / 100; 
        $iva = number_format($iva ,0,"",".");

        $subtotal = $total - $iva;
        $subtotal = number_format($subtotal ,0,"","."); 

        $total = number_format($total,0,"B","."); 


 
        $pdf->SetX(1.4); 
        $pdf->SetFont('Segoeb','',3);    
        $pdf->Cell(.4,.2,"",1,0,'L',true); 
        $pdf->Cell(2.8,.2,"",1,0,'L',true); 
        $pdf->Cell(.7,.2,"SUBTOTAL",1,0,'L',true); 
        $pdf->Cell(.7,.2,"$subtotal",1,0,'R',true); 
        $pdf->Ln();

        $pdf->SetX(1.4); 
        $pdf->Cell(.4,.2,"",1,0,'L',true); 
        $pdf->Cell(2.8,.2,"",1,0,'L',true); 
        $pdf->Cell(.7,.2,"IVA",1,0,'L',true); 
        $pdf->Cell(.7,.2,$iva,1,0,'R',true); 
        $pdf->Ln();

       
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

        if(file_exists("Ventas$prefijo.pdf") )
         unlink("Ventas.pdf");
          

        $pdf->Output("Ventas$prefijo.pdf","F");
        echo json_encode( array( "pdf_uri" => "Ventas$prefijo.pdf"  ) );   
 
         
?>