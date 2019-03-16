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

           $this->Cell($this->widths[0],10,"PRODUCTO",1,0,'L',true);   
           $this->Cell($this->widths[1],10,"CANT",1,0,'L',true);
           $this->Cell($this->widths[2],10,"STOCK",1,0,'L',true);
           $this->Cell($this->widths[3],10,"COSTO",1,0,'L',true);
           $this->Cell($this->widths[4],10,"PRECIO",1,0,'L',true);
           $this->Cell($this->widths[5],10,"TOTAL COSTO",1,0,'L',true);
           $this->Cell($this->widths[6],10,"TOTAL PRECIO",1,0,'L',true);      
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

    $widths = array(108,30,30,30,30,30,30);
    
    $pdf=new PDF("L");
    $pdf->setMargins(5,0,0,0);
    $pdf->SetAutoPageBreak(true,5); 

    $title = "INVENTARIO";
    
    $pdf->SetVars($title,'','',$widths,$colors_header);
    
    $pdf->AliasNbPages();
    $pdf->AddPage();    
    $pdf->AddFont('Segoe','','segoeui.php'); 
     
    $query = "SELECT 
                   producto.nombre ,
                   producto_punto.producto_id  ,
                   producto_punto.cantidad ,
                   producto_punto.precio ,
                   producto_punto.costo costo ,
                   producto.stock
               FROM  producto_punto 
               INNER JOIN producto ON producto.id = producto_id  
               WHERE punto_id = $idpunto AND producto_punto.estado = 1 
               ORDER BY producto.nombre ASC "; 

    $data = $connection->query($query);  
    $results = $data->fetchAll(PDO::FETCH_BOTH ); 
 
    $pdf->SetFillColor(255,255,255); 
    $pdf->SetTextColor($colors_header[9]['valor'],$colors_header[10]['valor'],$colors_header[11]['valor']);  
    $pdf->SetFont('Segoe','',8);
    $pdf->SetDrawColor($colors_header[6]['valor'],$colors_header[7]['valor'],$colors_header[8]['valor']); 


    if( count($results) > 0 )
    {
         $sum_costo = 0;
         $sum_precio = 0;
         $sum_costo_total = 0;
         $sum_precio_total = 0;
         $sum_cant = 0;
         foreach ($results as $row ) 
         {
              
             $sum_costo += $row['costo'];
             $sum_precio += $row['precio'];

             $total_precio = $row['precio'] * $row['cantidad'];
             $total_costo = $row['costo'] * $row['cantidad'];

             $sum_costo_total += $total_costo;
             $sum_precio_total += $total_precio;

             $costo = $func->format( $row['costo'] );
             $precio = $func->format( $row['precio'] );
             $costo = $func->format( $row['costo'] );

             $total_precio = $func->format( $total_precio);
             $total_costo = $func->format( $total_costo );

             $sum_cant+= $row['cantidad'];

             $pdf->Cell($pdf->widths[0],6,$row['nombre'],1,0,'L',true);   
             $pdf->Cell($pdf->widths[1],6,$row['cantidad'],1,0,'L',true);
             $pdf->Cell($pdf->widths[2],6,$row['stock'],1,0,'L',true);
             $pdf->Cell($pdf->widths[3],6,"$ ".$costo,1,0,'L',true);
             $pdf->Cell($pdf->widths[4],6,"$ ".$precio,1,0,'L',true);
             $pdf->Cell($pdf->widths[5],6,"$ ".$total_costo,1,0,'L',true);
             $pdf->Cell($pdf->widths[6],6,"$ ".$total_precio,1,0,'R',true);             
             $pdf->Ln(); 
         }   
         $sum = 0;
         for ( $i = 1 ; $i < count( $widths )  ; $i++  ) 
         {
              $sum += $widths[$i];
         } 
          

         $sum_costo =$func->format($sum_costo);
         $sum_precio =$func->format($sum_precio);
         $sum_costo_total =$func->format($sum_costo_total);
         $sum_precio_total =$func->format($sum_precio_total);

         $pdf->AddFont('Segoeb','','segoeuib.php');
         $pdf->SetFont('Segoeb','',12);
         

         $pdf->Cell($pdf->widths[0],6,'',1,0,'L',true);   
         $pdf->Cell($pdf->widths[1],6,$sum_cant,1,0,'L',true);
         $pdf->Cell($pdf->widths[2],6,'',1,0,'L',true);
         $pdf->Cell($pdf->widths[3],6,"$ ".$sum_costo,1,0,'L',true);
         $pdf->Cell($pdf->widths[4],6,"$ ".$sum_precio,1,0,'L',true);
         $pdf->Cell($pdf->widths[5],6,"$ ".$sum_costo_total,1,0,'L',true);
         $pdf->Cell($pdf->widths[6],6,"$ ".$sum_precio_total,1,0,'R',true);      

         
    }
    $pdf->Ln(); 
     
    $date = date('Y-m-d H:i:s');
 

    $pdf->SetFont('Segoe','',6);
    $pdf->SetFillColor(0,0,0);  
    $pdf->Cell(30,10,utf8_decode("Fecha y hora generación del reporte : $date"),0,1,'L',false);

         
   $prefijo = substr(md5(uniqid(rand())),0,6);
   if(file_exists("Inventario$prefijo.pdf") )
     unlink("Inventario$prefijo.pdf");
      

   $pdf->Output("Inventario$prefijo.pdf","F"); 
   echo json_encode( array( "pdf_uri" => "Inventario$prefijo.pdf"  ) );   
    
   function restarMes($fecha,$mes)
   {    
       list($year,$mon,$day) = explode('-',$fecha);
       return date('Y-m-d',mktime(0,0,0,$mon-$mes,$day,$year));     
   }
   function format( $value )
   {
      return number_format($value,0,"",".");
   }
?>