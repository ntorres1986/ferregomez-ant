<?php
        session_start();
        extract($_REQUEST);
        include "../clases/conexion.php";
        $conexion = new conexion("localhost","licores_celu","licores_celu","user1");
        $conexion->conectar();
        
        require('fpdf.php'); 

        class PDF extends FPDF
        {
            // Cabecera de página
            function Header()
            {
                $this->SetTextColor(0,0,0);
                $this->SetFont('TIMES','B',14);              
                $this->SetFillColor(216,216,216);
        

                $this->Image('logo.jpg' , 10 ,10, 30 , 12,'JPG');
                $this->SetY(30);
                $this->Cell(278,8,"INVENTARIO",1,0,'C',true);
                $this->Ln();

                $this->SetFillColor(239,239,239);


                $this->SetFont('TIMES','B',10); 

                $this->Cell(120,10,"NOMBRE",1,0,'C',true);
                $this->Cell(30,10,"PRECIO VENTA",1,0,'C',true);
                $this->Cell(15,10,"COSTO",1,0,'C',true);
                $this->Cell(18,10,"STOCK",1,0,'C',true);
                $this->Cell(15,10,"CANT",1,0,'C',true);
                $this->Cell(30,10,"VR INVENTARIO",1,0,'C',true);
                $this->Cell(30,10,"VR VENTA",1,0,'C',true);
                $this->Cell(20,10,"CCODIGO",1,0,'C',true);
                
             
            }
            
            // Pie de página
            function Footer()
            {
                $this->SetY(-15);
                //Arial italic 8
                $this->SetFont('TIMES','I',8);
                //Page number
                $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
            }
        }
        
              
       $pdf=new PDF("L");

       $pdf->AddPage();
        
        
         
                   
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('TIMES','',10);              
        $pdf->SetFillColor(255,255,255);
        
        
         
        $pdf->Ln();
        
        $sql = "select * from producto order by nombre asc";
        $consulta = $conexion->query($sql);
        if($conexion->numrows($consulta)>0)
        {
            $total_pre_venta = 0;
            $total_val_ivent = 0;
            $total_val_venta = 0;
            
            while($fila = $conexion->fetch($consulta))
            {
                        $vr_invent = $fila[8] * $fila[4];
                        $vr_venta = $fila[4] * $fila[2];
                        
                        $total_val_venta+=$vr_venta;
                        $total_val_ivent+=$vr_invent;                        
                        $total_pre_venta+=$fila[2];
                        
                        $vr_invent = number_format($vr_invent,0,"",".");
                        $vr_venta = number_format($vr_venta,0,"",".");
                        $fila[2] = number_format($fila[2],0,"",".");
                        $fila[8] = number_format($fila[8],0,"",".");
                        
                        $pdf->Cell(120,7,"$fila[1]",1,0,'L',true);
                        $pdf->Cell(30,7,"$fila[2]",1,0,'R',true);
                        $pdf->Cell(15,7,"$fila[8]",1,0,'R',true);
                        $pdf->Cell(18,7,"$fila[3]",1,0,'R',true);
                        $pdf->Cell(15,7,"$fila[4]",1,0,'R',true);
                        $pdf->Cell(30,7,"$vr_invent",1,0,'R',true);
                        $pdf->Cell(30,7,"$vr_venta",1,0,'R',true);
                        $pdf->Cell(20,7,"$fila[7]",1,0,'R',true);
        
                        $pdf->Ln();
               }       
         }  
         $utilidad = (int)$total_val_venta -(int)$total_val_ivent;
         $total_pre_venta = number_format($total_pre_venta,0,"",".");
         $total_val_ivent  = number_format($total_val_ivent,0,"",".");
         $total_val_venta  = number_format($total_val_venta,0,"",".");               
         $utilidad  = number_format($utilidad,0,"",".");
                
        $pdf->SetFillColor(216,216,216);
        $pdf->SetFont('TIMES','B',12);


            $pdf->Cell(120,10,"",1,0,'C',true);
            $pdf->Cell(30,10,"$ $total_pre_venta",1,0,'R',true);
            $pdf->Cell(15,10,"",1,0,'R',true);
            $pdf->Cell(18,10,"",1,0,'R',true);
            $pdf->Cell(15,10,"",1,0,'R',true);
            $pdf->Cell(30,10,"$ $total_val_ivent",1,0,'R',true);
            $pdf->Cell(30,10,"$ $total_val_venta",1,0,'R',true);
            $pdf->Cell(20,10,"",1,0,'R',true);
       
        
        $pdf->Output();       
?>