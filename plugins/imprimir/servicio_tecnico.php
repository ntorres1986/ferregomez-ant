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
                $this->Image('logo.jpg' , 155 ,10, 50 , 60,'JPG');
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

        $query = "SELECT  date(fecha) fecha , time(fecha) hora , nombre , cedula , telefono , imei , precio , observaciones 
                  FROM servicio_tecnico 
                  WHERE id = $id";
        $data = $connection->query($query);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $fila = $results[0];
        
        
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','B',12);              
         
        $pdf->SetY(18);  
        $pdf->Cell(140,8,"RESPUESTOS Y ACCESORIOS SERVICIO TECNICO ESPECIALIZADO",0,0,'C',false);
        $pdf->Ln();
        $pdf->SetFont('Arial','B',11);              
        

        $pdf->Cell(190,8,utf8_decode("C.C Celucredito Maracaibo No. 2 - Calle 53 No. 49 - 88 - Local B7-C7 - Cel. 304 39 46 18 - 487 17 54 Medellín"),0,0,'C',false);
        $pdf->Ln();
        $pdf->Ln();
        
        $pdf->SetFont('Arial','B',11); 
        $pdf->SetFillColor(216,216,216);
        $pdf->Cell(45,10,"FECHA DE INGRESO",1,0,'L',true); 

        $pdf->SetFont('Arial','',11);    
        $pdf->SetFillColor(249,249,249); 
        $pdf->Cell(50,10, $fila['fecha'] ,1,0,'C',true); 



        $pdf->SetFont('Arial','B',11); 
        $pdf->SetFillColor(216,216,216);          
        $pdf->Cell(45,10,"HORA DE INGRESO",1,0,'L',true); 


        $pdf->SetFont('Arial','',11);    
        $pdf->SetFillColor(249,249,249); 
        $pdf->Cell(50,10,$fila['hora'],1,0,'C',true);

        $pdf->Ln();




        $pdf->SetFont('Arial','B',11); 
        $pdf->SetFillColor(216,216,216);
        $pdf->Cell(45,10,"NOMBRE",1,0,'L',true); 

        $pdf->SetFont('Arial','',11);    
        $pdf->SetFillColor(249,249,249); 
        $pdf->Cell(50,10, $fila['nombre'] ,1,0,'C',true); 



        $pdf->SetFont('Arial','B',11); 
        $pdf->SetFillColor(216,216,216);  
        $pdf->Cell(45,10,"CEDULA",1,0,'L',true); 
     

        $pdf->SetFont('Arial','',11);    
        $pdf->SetFillColor(249,249,249); 
        $pdf->Cell(50,10,$fila['cedula'],1,0,'C',true);

        $pdf->Ln();
        


        $pdf->SetFont('Arial','B',11); 
        $pdf->SetFillColor(216,216,216);  
        $pdf->Cell(45,10,"TELEFONO",1,0,'L',true); 
     

        $pdf->SetFont('Arial','',11);    
        $pdf->SetFillColor(249,249,249); 
        $pdf->Cell(50,10,$fila['telefono'],1,0,'C',true);


        $pdf->SetFont('Arial','B',11); 
        $pdf->SetFillColor(216,216,216);  
        $pdf->Cell(45,10,"IMEI",1,0,'L',true); 
     

        $pdf->SetFont('Arial','',11);    
        $pdf->SetFillColor(249,249,249); 
        $pdf->Cell(50,10,$fila['imei'],1,0,'C',true);

        $pdf->Ln();
        $pdf->Ln();

        $pdf->SetFont('Arial','B',11);    
        $pdf->SetFillColor(255,255,255); 
        $pdf->SetDrawColor(220,220,220); 

        $pdf->Cell(60,10,"INFORMACION DEL EQUIPO ",0,0,'C',true);
        $pdf->Ln();
        $pdf->SetFont('Arial','I',11);    

        $pdf->MultiCell(190,4,utf8_decode($fila['observaciones']),0,'J',true);

        $pdf->Ln();


        $pdf->SetFont('Arial','',7); 
        $pdf->Ln();

         
        $pdf->Cell(60,10, utf8_decode("Sobre la tenencia y transferencia del equipo terminal  móvil :"),0,0,'L',true);
        $pdf->Ln();
         
        $txt = "1) El tiene un tiempo de 15 días calendario para reclamar su equipo terminal  móvi, después de ello incurre en gastos administrativo y de bodega a entregar en donación del equipo terminal o móvil descrito en esta orden a Punto Touch.";
        $pdf->MultiCell(190,3,utf8_decode("$txt"),0,'J',true);
        $pdf->Ln();


        $txt = "2) De ninguna manera se responde por equipos que hayan sido reportados como perdidos o robados, se presume que todo el equipo es de buena procedencia y probar antes la autoridades correspondientes si este equipo es requerido por las autoridades se entregará y no se responderá por su valor.";
        $pdf->MultiCell(190,3,utf8_decode("$txt"),0,'J',true);
        $pdf->Ln();

        $txt = "3) En caso de deterioro en la parte externa del equipo terminal móvil exonero al representante legal de Punto Touch o al establecimiento.";
        $pdf->MultiCell(190,3,utf8_decode("$txt"),0,'J',true);
        $pdf->Ln();

        $txt = "4) En caso de daño total o parcial y/o perdida del equipo terminal móvil, el establecimiento y su representante legal tendrán 60 días hábiles para responder se hará por un equipo de segunda igual o similar en sus características físicas y de software.";
        $pdf->MultiCell(190,3,utf8_decode("$txt"),0,'J',true);
        $pdf->Ln();

        $txt = "5) No se reponderá por ninguna información personal como fotos,documentos,archivos etc.; en la memoria interna o externa del equipo terminal móvil.";
        $pdf->MultiCell(190,3,utf8_decode("$txt"),0,'J',true);
        $pdf->Ln();

        $txt = "6) Declara que sus datos son correctos y veraces, y autoriza que te pueden hacer las notificaciones necesarias.";
        $pdf->MultiCell(190,3,utf8_decode("$txt"),0,'J',true);
        $pdf->Ln();


        $txt = "7) Si el equipo esta mojado, des armado o golepado entra al taller con riesgo se apague o empeore en el procedimiento de revisión o reparación.";
        $pdf->MultiCell(190,3,utf8_decode("$txt"),0,'J',true);
        $pdf->Ln();

        $txt = "8) Autorizo al técnico a manipular el equipo terminal móvil con el fin de dar solución a la falla por la cual se deja en reparación.";
        $pdf->MultiCell(190,3,utf8_decode("$txt"),0,'J',true);
        $pdf->Ln();

        $txt = "9) Todo display con el cristal quebrado Punto Touch no se hace responsable ya que este procedimiento es de riesgo.";
        $pdf->MultiCell(190,3,utf8_decode("$txt"),0,'J',true);
        $pdf->Ln();
 

        
        $pdf->SetDrawColor(0,0,0); 
        
        $pdf->Cell(80,30,"FIRMA",'B',0,'L',true); 
        
    
        $pdf->Output();       
?>