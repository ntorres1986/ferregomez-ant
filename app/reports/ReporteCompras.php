<?php
    session_start();
    include "../../clases/connection.php";
    require"../../plugins/fpdf181/fpdf.php";
    include "../../clases/func.php";

 
    $connection = new connection();  
    $func = new func();
    extract($_REQUEST);

    $query = "SELECT  *
              FROM configuracion  
              WHERE categoria = 'report-pdf'";  

    $data = $connection->query($query);  
    $colors_header = $data->fetchAll(PDO::FETCH_BOTH );

    
    $idpunto = $_SESSION['idpunto'];
    

    class PDF extends FPDF
    {
        public $title;
        public $logo ;
        public $date;     
        public $widths;    
        public $colors_header;      
    
        function SetVars( $title , $logo , $date , $widths , $colors_header )
        {
            $this->title = $title;
            $this->logo = $logo;
            $this->date = $date;
            $this->widths = $widths;
            $this->colors_header = $colors_header; 
        }
        function Header()
        { 
           //$this->Image("encabezado.png",-20,0,340,40);  

           $this->SetFillColor(255,255,255);

           $this->AddFont('Segoe','','segoeui.php');
           $this->SetFont('Segoe','',14);
           $this->Ln(0); 
           $this->Cell($this->pageWidth() - 5 ,10, $this->title ,0,0,'L',false); 
           $this->Ln(); 
           $this->SetDrawColor(209,209,209);    
           $this->Line(5, 10, $this->pageWidth() , 10 ) ;


           $this->SetFont('Segoe','',9);
           $this->SetDrawColor($this->colors_header[6]['valor'],$this->colors_header[7]['valor'],$this->colors_header[8]['valor']); 


           $this->SetFillColor($this->colors_header[0]['valor'],$this->colors_header[1]['valor'],$this->colors_header[2]['valor']); 
           $this->SetTextColor($this->colors_header[3]['valor'],$this->colors_header[4]['valor'],$this->colors_header[5]['valor']); 
           $this->Ln(); 

           $this->Cell($this->widths[0],10,"FECHA",1,0,'L',true);   
           $this->Cell($this->widths[1],10,"PRODUCTO",1,0,'L',true);
           $this->Cell($this->widths[2],10,"TRABAJADOR",1,0,'L',true);
           $this->Cell($this->widths[3],10,"FACTURA",1,0,'L',true);
           $this->Cell($this->widths[4],10,"TIPO",1,0,'L',true);
           $this->Cell($this->widths[5],10,"PROVEEDOR",1,0,'L',true);
           $this->Cell($this->widths[6],10,"CANT",1,0,'L',true);
           $this->Cell($this->widths[7],10,"PRECIO",1,0,'L',true);
           $this->Cell($this->widths[8],10,"TOTAL",1,0,'L',true);           
           $this->Ln(10); 
           $this->SetFillColor($this->colors_header[6]['valor'],$this->colors_header[7]['valor'],$this->colors_header[8]['valor']); 
            
           $this->Cell($this->pageWidth() -5,0.5,"",1,0,'L',true);           
           $this->Ln(); 

        }  
        function Footer()
        { 
        }
        function pageWidth()
        {
            $width = $this->w;
            $leftMargin = $this->lMargin;
            $rightMargin = $this->rMargin;
            return $width-$rightMargin-$leftMargin;
        }
    }

    $widths = array(30,60,50,20,20,50,15,20,22);
    
    $pdf=new PDF("L");
    $pdf->setMargins(5,0,0,0);
    $pdf->SetAutoPageBreak(true,5); 
    
    if( empty($fin) )
      $title = "REPORTE DE COMPRAS ($inicio)";
    else
      $title = "REPORTE DE COMPRAS DE ($inicio A $fin)";
    
    $pdf->SetVars($title,'','',$widths,$colors_header);
    
    $pdf->AliasNbPages();
    $pdf->AddPage();    
    $pdf->AddFont('Segoe','','segoeui.php');

    
    
    $condition = "";
    if( !empty($idproveedores) )
    {
       if( count($idproveedores) > 0 )
       {
          $list = trim(implode(",", $idproveedores));
          if( strlen($list) > 0 )
             $condition .= " AND compra.proveedor_id IN ( $list )"; 
       }
    }

    if( !empty($tipos) )
    { 
        $condition .=" AND compra.tipo_id IN ( $tipos )";  
    }
   
    if( !empty($fin) )
      $condition .= " AND DATE(compra.fecha) BETWEEN '$inicio' AND '$fin'";
    else
    {
      $condition .= " AND DATE(compra.fecha) = '$inicio'"; 
      $fin = "";
    } 
    $query = "SELECT 
                             compra.fecha , 
                             producto.nombre AS producto , 
                             concat( usuario.nombre , ' ' , usuario.apellido) AS trabajador ,
                             factura , 
                             tipo ,
                             tipo_compra_id ,
                             concat( proveedor.nombres , ' ' , proveedor.apellidos) AS proveedor ,
                             producto_compra.cantidad ,
                             producto_compra.precio ,
                             producto_compra.cantidad * producto_compra.precio AS total
                            FROM compra
                                 INNER JOIN producto_compra ON compra_id = producto_compra.id
                                 INNER JOIN tipo_compra ON tipo_compra_id = compra.tipo_compra_id      
                                 INNER JOIN usuario ON usuario_id = usuario.id 
                                 INNER JOIN producto ON producto_compra.producto_id = producto.id 
                                 LEFT JOIN proveedor ON proveedor_id = proveedor.id 
                            WHERE compra.punto_id = 1 $condition 
                            GROUP BY compra.id
                            ORDER BY compra.fecha DESC"; 
              /*

    $data = $connection->query($query);  
    $results = $data->fetchAll(PDO::FETCH_BOTH ); 
 
    $pdf->SetFillColor(255,255,255); 
    $pdf->SetTextColor($colors_header[9]['valor'],$colors_header[10]['valor'],$colors_header[11]['valor']);  
    $pdf->SetFont('Segoe','',8);
    $pdf->SetDrawColor($colors_header[6]['valor'],$colors_header[7]['valor'],$colors_header[8]['valor']); 


    if( count($results) > 0 )
    {
         $sum_total = 0;
         foreach ($results as $row ) 
         {
             $sum_total += $row['total'];

             $row['precio'] = $func->format( $row['precio'] );
             $row['total'] = $func->format( $row['total'] );

             $pdf->Cell($pdf->widths[0],6,$row['fecha'],1,0,'L',true);   
             $pdf->Cell($pdf->widths[1],6,$row['producto'],1,0,'L',true);
             $pdf->Cell($pdf->widths[2],6,$row['trabajador'],1,0,'L',true);
             $pdf->Cell($pdf->widths[3],6,$row['factura'],1,0,'L',true);
             $pdf->Cell($pdf->widths[4],6,$row['tipo'],1,0,'L',true);
             $pdf->Cell($pdf->widths[5],6,$row['proveedor'],1,0,'L',true);
             $pdf->Cell($pdf->widths[6],6,$row['cantidad'],1,0,'L',true);
             $pdf->Cell($pdf->widths[7],6,"$ ".$row['precio'],1,0,'R',true);           
             $pdf->Cell($pdf->widths[8],6,"$ ".$row['total'],1,0,'R',true);           
             $pdf->Ln(); 
         }   
         $sum = 0;
         for ( $i = 1 ; $i < count( $widths )  ; $i++  ) 
         {
              $sum += $widths[$i];
         } 

         $sum_total =$func->format($sum_total);

         $pdf->AddFont('Segoeb','','segoeuib.php');
         $pdf->SetFont('Segoeb','',12);
         $pdf->Cell( $pdf->widths[0],10,"TOTAL",1,0,'L',true);    
         $pdf->Cell( $sum ,10,"$ ".$sum_total ,1,0,'R',true);    
    }
    $pdf->Ln(); 
     
    $query = "select current_timestamp";
    $data = $connection->query($query);  
    $results = $data->fetch(PDO::FETCH_BOTH ); 
 

    $pdf->SetFont('Segoe','',6);
    $pdf->SetFillColor(0,0,0);  
    $pdf->Cell(30,10,utf8_decode("Fecha y hora generación del reporte : $results[current_timestamp]"),0,1,'L',false);

         
   
   if(file_exists("ReporteDeCompras.pdf") )
     unlink("ReporteDeCompras.pdf");
      

   $pdf->Output("ReporteDeCompras.pdf","F");
   echo "reports/ReporteDeCompras.pdf";
    
   function restarMes($fecha,$mes)
   {	
       list($year,$mon,$day) = explode('-',$fecha);
	   return date('Y-m-d',mktime(0,0,0,$mon-$mes,$day,$year));		
   }
   function format( $value )
   {
      return number_format($value,0,"",".");
   }
   */

?>