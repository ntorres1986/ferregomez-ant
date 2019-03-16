<?php
   include "../../clases/connection.php";
   require_once '../../plugins/PHPExcel/PHPExcel.php';
   
   $connection = new connection(); 
   $objPHPExcel = new PHPExcel();


   // Se asignan las propiedades del libro
  $objPHPExcel->getProperties()->setCreator("FerreGomezJp") // Nombre del autor
    ->setLastModifiedBy("Nelson Torres") //Ultimo usuario que lo modificó
    ->setTitle("Inventario") // Titulo
    ->setSubject("Reporte Excel de inventario") //Asunto
    ->setDescription("Inventario de productos") //Descripción
    ->setKeywords("Inventario") //Etiquetas
    ->setCategory("Inventario"); //Categorias

    $tituloReporte = "Inventario";
    $titulosColumnas = array('PRODUCTO', 'STOCK', 'CANTIDAD', 'COSTO' , 'PRECIO' , 'TOTAL COSTO' , 'TOTAL PRECIO');

    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1',$tituloReporte) // Titulo del reporte
    ->setCellValue('A2',  $titulosColumnas[0])  //Titulo de las columnas
    ->setCellValue('B2',  $titulosColumnas[1])
    ->setCellValue('C2',  $titulosColumnas[2])
    ->setCellValue('D2',  $titulosColumnas[3])
    ->setCellValue('E2',  $titulosColumnas[4])
    ->setCellValue('F2',  $titulosColumnas[5])
    ->setCellValue('G2',  $titulosColumnas[6]);


     $query = "SELECT 
	               producto.nombre ,
	               producto_punto.cantidad ,
	               producto_punto.precio ,
	               producto_punto.costo costo ,
	               producto.stock
	           FROM  producto_punto 
	           INNER JOIN producto ON producto.id = producto_id 
	           WHERE producto_punto.estado = 1 
	           ORDER BY producto.nombre ASC ";
	  
	 $data = $connection->query($query);  
	 $results = $data->fetchAll(PDO::FETCH_BOTH );

     $i = 3;
     foreach ($results as $key => $fila) 
	 {
	 	 $totalCosto = $fila['cantidad'] * $fila['costo'];
	 	 $totalPrecio = $fila['cantidad'] * $fila['precio'];
	     $objPHPExcel->setActiveSheetIndex(0)
	         ->setCellValue('A'.$i, utf8_encode($fila['nombre']))
	         ->setCellValue('B'.$i, $fila['stock'])
	         ->setCellValue('C'.$i, $fila['cantidad'])
	         ->setCellValue('D'.$i, $fila['costo'])
	         ->setCellValue('E'.$i, $fila['precio'])
	         ->setCellValue('F'.$i, $totalCosto)
	         ->setCellValue('G'.$i, $totalPrecio);
	     $i++;
	 }



	$estiloTituloReporte = array
	(
			'font' => array
			(
			        'name'      => 'Verdana',
			        'bold'      => true,
			        'italic'    => false,
			        'strike'    => false,
			        'size' =>16,
			        'color'     => array(
			            'rgb' => 'FFFFFF'
			        )
            ),
		    'fill' => array
		    (
		      'type'  => PHPExcel_Style_Fill::FILL_SOLID,
		      'color' => array(
		            'argb' => '909090')
		   ),
		   'borders' => array
		    (
		        'allborders' => array
		        (
		            'style' => PHPExcel_Style_Border::BORDER_NONE
		        )
		    ),
           'alignment' => array
           (
        		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        		'rotation' => 0,
        		'wrap' => TRUE
    		)
    );
 
    $estiloTituloColumnas = array
    (
	    'font' => array
	    (
	        'name'  => 'Arial',
	        'color' => array(
	            'rgb' => 'FFFFFF'
	        )
	    ),
    	'fill' => array
    	(
        	'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
  			'rotation'   => 90,
        	'startcolor' => array
        	(
            	'rgb' => '666666'
        	),
        	'endcolor' => array
        	(
            	'argb' => '666666'
        	)
    	)
	);
 
	$estiloInformacion = new PHPExcel_Style();
	$estiloInformacion->applyFromArray( array
	(
		'font' => array
		(
    		'name'  => 'Arial',
    		'color' => array
    		(
        		'rgb' => '000000'
    		)	
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array
			(
        	'argb' => 'FFd9b7f4')
			),
		'borders' => array
		(
    		'left' => array
    		(
        		'style' => PHPExcel_Style_Border::BORDER_THIN ,
  				'color' => array
  				(
          			'rgb' => '3a2a47'
        		)
    		)
		)
    ));
    
    $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($estiloTituloReporte);

	$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($estiloTituloColumnas);
	/*

	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:D".($i-1));

	for($i = 'A'; $i <= 'G'; $i++)
	{
       $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(TRUE);
    }

    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(TRUE);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(TRUE);
    */
    // Calculate the column widths
	$objPHPExcel->setActiveSheetIndex(0);	 

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(60);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

	// Se asigna el nombre a la hoja
	//$objPHPExcel->getActiveSheet()->setTitle('Alumnos');
	 
	// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
	// Inmovilizar paneles
	//$objPHPExcel->getActiveSheet(0)->freezePane('A4');
	$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,3);

	// Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Inventario.xlsx"');
	header('Cache-Control: max-age=0');
	 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	//$objWriter->save('php://output'); 
	$objWriter->save('Inventario.xlsx');

	echo "Inventario.xlsx";
?>