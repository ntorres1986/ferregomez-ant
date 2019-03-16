 <?php
		extract($_REQUEST);
		session_start();
		include "../clases/conexion.php";
		$conexion = new conexion("localhost","licores_celu","licores_celu","user1");
		        
		$conexion->conectar();  
		  
		include "../clases/msg.php";
		$msg = new msg();

		$iduser  =  $_SESSION["usuario"] ;
		if(!empty($opcion))
		{
			switch($opcion)
			{

			    case "estadoCuenta":
			       require_once '../PHPExcel/PHPExcel.php';
			       date_default_timezone_set('America/Mexico_City');
			       $objPHPExcel = new PHPExcel();

				   if (PHP_SAPI == 'cli')
			          die('Este archivo solo se puede ver desde un navegador web');
			       $objPHPExcel->getProperties()->setCreator("Codedrinks") //Autor
							 ->setLastModifiedBy("Codedrinks") //Ultimo usuario que lo modificó
							 ->setTitle("Reporte Excel con PHP y MySQL")
							 ->setSubject("Reporte Excel con PHP y MySQL")
							 ->setDescription("Reporte de alumnos")
							 ->setKeywords("reporte alumnos carreras")
							 ->setCategory("Reporte excel");

				  $tituloReporte = "ESTADO DE CUENTA";
				  $titulosColumnas = array('FACTURA', 'FECHA', 'TIPO', 'OBSERVACION' , 'VR FACTURA' , 'ABONO' ,'SALDO');

				  // Se agregan los titulos del reporte
		          $objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A2',$titulosColumnas[0])
        		    ->setCellValue('B2',  $titulosColumnas[1])
		            ->setCellValue('C2',  $titulosColumnas[2])
        		    ->setCellValue('D2',  $titulosColumnas[3])
            		->setCellValue('E2',  $titulosColumnas[4])
            		->setCellValue('F2',  $titulosColumnas[5])
            		->setCellValue('G2',  $titulosColumnas[6]);

                 

            	  $sql = "select * from  venta where cliente_idcliente = $idcliente";
               	  $consulta = $conexion->query($sql);

               	  $i = 3;

               	  $estiloVentas = array
		          (		            
		            'fill' 	=> array
		            (
						'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation'   => 90,
		        		'startcolor' => array
		        		(
		            		'rgb' => 'd9edf7'
		        		),
		        		'endcolor'   => array
		        		(
		            		'rgb' => 'd9edf7'
		        		)
					)
		          );
		          $estiloDebito = array
		          (		            
		            'fill' 	=> array
		            (
						'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation'   => 90,
		        		'startcolor' => array
		        		(
		            		'rgb' => 'fcf8e3'
		        		),
		        		'endcolor'   => array
		        		(
		            		'rgb' => 'fcf8e3'
		        		)
					)
		          );
		          $estiloCredito = array
		          (		            
		            'fill' 	=> array
		            (
						'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation'   => 90,
		        		'startcolor' => array
		        		(
		            		'rgb' => 'dff0d8'
		        		),
		        		'endcolor'   => array
		        		(
		            		'rgb' => 'dff0d8'
		        		)
					)
		          );
		          $estiloDescuento = array
		          (		            
		            'fill' 	=> array
		            (
						'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation'   => 90,
		        		'startcolor' => array
		        		(
		            		'rgb' => 'f6a5de'
		        		),
		        		'endcolor'   => array
		        		(
		            		'rgb' => 'f6a5de'
		        		)
					)
		          );
		          $estiloAbono = array
		          (		            
		            'fill' 	=> array
		            (
						'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation'   => 90,
		        		'startcolor' => array
		        		(
		            		'rgb' => '989898'
		        		),
		        		'endcolor'   => array
		        		(
		            		'rgb' => '989898'
		        		)
					)
		          );
                  if($conexion->numrows($consulta)>0)
                  {
                  	   $sumaValor = 0;
		               $sumaDebito = 0;
		               $sumaCredito = 0; 
		              
		               while(  $fila = mysql_fetch_row($consulta))
		               {   
		                   if( empty($fa) )
		                      $fa = $fila[6];

		                   if( $fa != $fila[6] )
		                   {
		                       
		                       if( $fila[12] > 0 )
		                       { 
		                             $sql = "select descuento from venta where factura = '$fa'";
		                             $consultaDescuento = $conexion->query($sql);
		                             $filaDesccuento = mysql_fetch_row($consultaDescuento);
		                             if( $filaDesccuento[0] > 0 )
		                             {
		                                 
		                                 $sumaValor -= $filaDesccuento[0];
		                                 $auxSuma = $sumaValor;

		                                 
		                                  $objPHPExcel->setActiveSheetIndex(0)
					        		      ->setCellValue('A'.$i,  $fa)
							              ->setCellValue('B'.$i,  "")
					        		      ->setCellValue('C'.$i,  "")
					            		  ->setCellValue('D'.$i,  "")
					            		  ->setCellValue('E'.$i,  "")
					            		  ->setCellValue('F'.$i,  $filaDesccuento[0])
					            		  ->setCellValue('G'.$i,  $auxSuma);
					            		  
					            		  $objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloDescuento);
					            		  $i++;
					            		  	
		                             }        
		                       }
		                       
		                       $sql = "select * from  notadebito where factura = '$fa'";
		                       $resultDebito = $conexion->query($sql);
		                       if($conexion->numrows($resultDebito) > 0)
		                       {
		                           while(  $rowDebito = mysql_fetch_row($resultDebito))
		                           {
		                               $sumaValor -= $rowDebito[1];
		                               $auxSuma = $sumaValor ;

		                               
		                                $objPHPExcel->setActiveSheetIndex(0)
					        		      ->setCellValue('A'.$i,  $rowDebito[4])
							              ->setCellValue('B'.$i,  $rowDebito[3])
					        		      ->setCellValue('C'.$i,  "")
					            		  ->setCellValue('D'.$i,  $rowDebito[2])
					            		  ->setCellValue('E'.$i,  "")
					            		  ->setCellValue('F'.$i,  $rowDebito[1])
					            		  ->setCellValue('G'.$i,  $auxSuma);
					            		   

					            		 $objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloDebito);
					            		 $i++;
		                           }
		                       }
		                       $sql = "select * from  notacredito where factura = '$fa'";
		                       $resultCredito = $conexion->query($sql);
		                       if($conexion->numrows($resultCredito) > 0)
		                       {
		                           while(  $rowCredito = mysql_fetch_row($resultCredito))
		                           {
		                               $sumaValor += $rowCredito[1];
		                               $auxSuma = $sumaValor;
		                                
		                                $objPHPExcel->setActiveSheetIndex(0)
					        		      ->setCellValue('A'.$i,  $rowCredito[4])
							              ->setCellValue('B'.$i,  $rowCredito[3])
					        		      ->setCellValue('C'.$i,  "")
					            		  ->setCellValue('D'.$i,  $rowCredito[2])
					            		  ->setCellValue('E'.$i,  $rowCredito[1])
					            		  ->setCellValue('F'.$i,  "")
					            		  ->setCellValue('G'.$i,  $auxSuma);
					            		  
					            		$objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloCredito);
					            		$i++;
		                           }
		                       }  

		                       $sql = "select * from abono where factura = '$fa'";
		                       $resultAbono = $conexion->query($sql);
		                       if($conexion->numrows($resultAbono) > 0)
		                       {
		                           while(  $rowAbono = mysql_fetch_row($resultAbono))
		                           {
		                               $sumaValor -= $rowAbono[1];
		                               $auxSuma = $sumaValor ;
 
		                                $objPHPExcel->setActiveSheetIndex(0)
					        		      ->setCellValue('A'.$i,  $fa)
							              ->setCellValue('B'.$i,  $rowAbono[2])
					        		      ->setCellValue('C'.$i,  "")
					            		  ->setCellValue('D'.$i,  "")
					            		  ->setCellValue('E'.$i,  "")
					            		  ->setCellValue('F'.$i,  $rowAbono[1])
					            		  ->setCellValue('G'.$i,  $auxSuma);
					            		   
					            		  $objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloAbono);
					            		  $i++;
		                           }
		                       }
		                       


		                       $fa = $fila[6];
		                   } 

		                   $valor = $fila[5] * $fila[1];
		                   if( $fila[9] == "creditoPagado")
		                   $sumaValor += $valor;
		                   $auxSuma = $sumaValor;
		                   
		                  
	                       $objPHPExcel->setActiveSheetIndex(0)
				        		      ->setCellValue('A'.$i,  $fila[6])
						              ->setCellValue('B'.$i,  $fila[7])
				        		      ->setCellValue('C'.$i,  $fila[9])
				            		  ->setCellValue('D'.$i,  "")
				            		  ->setCellValue('E'.$i,  $valor)
				            		  ->setCellValue('F'.$i,  "")
				            		  ->setCellValue('G'.$i,  $auxSuma);
				           $objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloVentas);
				            		  $i++;
                            

                       }
                       /** ******************FIN WHILE********************************************************************************************************/

                  	   $sql = "select descuento from venta where factura = '$fa'";
		               $consulta = $conexion->query($sql);
		               $filaDesccuento = mysql_fetch_row($consulta);
		               if( $filaDesccuento[0] > 0 )
		               {
		                  
		                   $sumaValor -= $filaDesccuento[0];
		                   $auxSuma = $sumaValor ;

		                    
		                   $objPHPExcel->setActiveSheetIndex(0)
					        		      ->setCellValue('A'.$i,  $fa)
							              ->setCellValue('B'.$i,  "")
					        		      ->setCellValue('C'.$i,  "")
					            		  ->setCellValue('D'.$i,  "")
					            		  ->setCellValue('E'.$i,  "")
					            		  ->setCellValue('F'.$i,  $filaDesccuento[0])
					            		  ->setCellValue('G'.$i,  $auxSuma);
					            		  
					        $objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloDescuento);
					        $i++;
		               }

		               $sql = "select * from  notadebito where factura = '$fa'";
		               $resultDebito = $conexion->query($sql);
		               if($conexion->numrows($resultDebito) > 0)
		               {
		                   while(  $rowDebito = mysql_fetch_row($resultDebito))
		                   {
		                       $sumaValor -= $rowDebito[1];
		                       $auxSuma = number_format($sumaValor,0,"",".") ;

		                       $rowDebito[1]  = number_format($rowDebito[1],0,"",".") ;

		                       $tablaVentas.= "<tr class='warning'>
		                                         <td>$rowDebito[4]</td>
		                                         <td>$rowDebito[3]</td> 
		                                         <td></td>                         
		                                         <td>$rowDebito[2]</td> 
		                                         <td></td>
		                                         <td>$rowDebito[1]</td> 
		                                         <td>$auxSuma</td>                                                            
		                                       </tr>";
		                        $objPHPExcel->setActiveSheetIndex(0)
					        		      ->setCellValue('A'.$i,  $rowDebito[4])
							              ->setCellValue('B'.$i,  $rowDebito[3])
					        		      ->setCellValue('C'.$i,  "")
					            		  ->setCellValue('D'.$i,  $rowDebito[2])
					            		  ->setCellValue('E'.$i,  "")
					            		  ->setCellValue('F'.$i,  $rowDebito[1])
					            		  ->setCellValue('G'.$i,  $auxSuma);
					            		   
					            $objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloDebito);
					            $i++;
		                   }
		               }
		               $sql = "select * from  notacredito where factura = '$fa'";
		               $resultCredito = $conexion->query($sql);
		               if($conexion->numrows($resultCredito) > 0)
		               {
		                   while(  $rowCredito = mysql_fetch_row($resultCredito))
		                   {
		                       $sumaValor += $rowCredito[1];
		                       $auxSuma = $sumaValor;
		                      
		                       $objPHPExcel->setActiveSheetIndex(0)
					        		      ->setCellValue('A'.$i,  $rowCredito[4])
							              ->setCellValue('B'.$i,  $rowCredito[3])
					        		      ->setCellValue('C'.$i,  "")
					            		  ->setCellValue('D'.$i,  $rowCredito[2])
					            		  ->setCellValue('E'.$i,  $rowCredito[1])
					            		  ->setCellValue('F'.$i,  "")
					            		  ->setCellValue('G'.$i,  $auxSuma);
					            		  
					            $objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloCredito);
					            $i++;
		                   }
		               }  
		               $sql = "select * from abono where factura = '$fa'";
		               $resultAbono = $conexion->query($sql);
		               if($conexion->numrows($resultAbono) > 0)
		               {
		                   while(  $rowAbono = mysql_fetch_row($resultAbono))
		                   {
		                       $sumaValor -= $rowAbono[1];
		                       $auxSuma = $sumaValor ;

		                       
		                        $objPHPExcel->setActiveSheetIndex(0)
					        		      ->setCellValue('A'.$i,  $fa)
							              ->setCellValue('B'.$i,  $rowAbono[2])
					        		      ->setCellValue('C'.$i,  "")
					            		  ->setCellValue('D'.$i,  "")
					            		  ->setCellValue('E'.$i,  "")
					            		  ->setCellValue('F'.$i,  $rowAbono[1])
					            		  ->setCellValue('G'.$i,  $auxSuma);
					            		  
					            $objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloAbono);
					            $i++;
		                   }
		               }

		               $total = $sumaValor;
		               
		               //$total  = number_format($total,0,"",".") ;
		               
		                 $objPHPExcel->setActiveSheetIndex(0)
				        		      ->setCellValue('A'.$i,  "TOTAL")
						              ->setCellValue('B'.$i,  "")
				        		      ->setCellValue('C'.$i,  "")
				            		  ->setCellValue('D'.$i,  "")
				            		  ->setCellValue('E'.$i,  "")
				            		  ->setCellValue('F'.$i,  "")
				            		  ->setCellValue('G'.$i,  $total);
				          $objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloVentas);
				            		  $i++;
		               
		               $tablaVentas .= "</table>";
		                
                  }
                  $estiloTituloReporte = array(
		        	'font' => array(
			        	'name'      => 'Verdana',
		    	        'bold'      => true,
		        	    'italic'    => false,
		                'strike'    => false,
		               	'size' =>16,
			            	'color'     => array(
		    	            	'rgb' => 'FFFFFF'
		        	       	)
		            ),
			        'fill' => array(
						'type'	=> PHPExcel_Style_Fill::FILL_SOLID,
						'color'	=> array('argb' => 'FF220835')
					),
		            'borders' => array(
		               	'allborders' => array(
		                	'style' => PHPExcel_Style_Border::BORDER_NONE                    
		               	)
		            ), 
		            'alignment' =>  array(
		        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        			'rotation'   => 0,
		        			'wrap'          => TRUE
		    		)
		            );

				   $estiloTituloColumnas = array(
		            'font' => array(
		            	'size' =>8,
		                'name'      => 'Arial',
		                'bold'      => true,                          
		                'color'     => array(
		                    'rgb' => 'FFFFFF'
		                ),

		            ),
		            'fill' 	=> array(
						'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation'   => 90,
		        		'startcolor' => array(
		            		'rgb' => 'c47cf2'
		        		),
		        		'endcolor'   => array(
		            		'argb' => 'FF431a5d'
		        		)
					),
		            'borders' => array(
		            	'top'     => array(
		                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
		                    'color' => array(
		                        'rgb' => '2f8df6'
		                    )
		                ),
		                'bottom'     => array(
		                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
		                    'color' => array(
		                        'rgb' => '2f8df6'
		                    )
		                )
		            ),
					'alignment' =>  array(
		        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        			'wrap'          => TRUE
		    		));
                    $estiloInformacion = new PHPExcel_Style();
					$estiloInformacion->applyFromArray(
						array(
			           		'font' => array(
			               	'name'      => 'Arial',               
			               	'color'     => array(
			                   	'rgb' => '000000'
			               	)
			           	),
			           	'fill' 	=> array(
							'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
							'color'		=> array('argb' => 'FFd9b7f4')
						),
			           	'borders' => array(
			               	'left'     => array(
			                   	'style' => PHPExcel_Style_Border::BORDER_THIN ,
				                'color' => array(
			    	            	'rgb' => '3a2a47'
			                   	)
			               	)             
			           	)
			        ));


			        $estiloVentas = array
			        (		            
			            'fill' 	=> array
			            (
							'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
							'rotation'   => 90,
			        		'startcolor' => array
			        		(
			            		'rgb' => 'dff0d8'
			        		),
			        		'endcolor'   => array
			        		(
			            		'rgb' => 'dff0d8'
			        		)
						)
			        );

			        $sql = "select nombre from  cliente where idcliente = $idcliente";
               	    $consulta = $conexion->query($sql);
               	    $fila = mysql_fetch_row($consulta);
               	    $nombreCliente = $fila[0];


			        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');
			        //$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
			        $nombreCliente = utf8_encode($nombreCliente);

			        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "ESTADO DE CUENTA ".$nombreCliente);
							        

			         
			        //$objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($estiloTituloReporte);
					$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($estiloTituloColumnas);		
					//$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:D".($i-1));
							
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(10);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(15);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(10);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(25);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(12);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(10);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(10);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(10);
					
					
					// Se asigna el nombre a la hoja
					$objPHPExcel->getActiveSheet()->setTitle('Alumnos');

					// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
					$objPHPExcel->setActiveSheetIndex(0);
					// Inmovilizar paneles 
					//$objPHPExcel->getActiveSheet(0)->freezePane('A4');
					//$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

					// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="Reportedealumnos.xlsx"');
					header('Cache-Control: max-age=0');

					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('ESTADO DE CUENTA.xlsx');
					exit;

			    break;





			    case "pedidoExcel":
			       require_once '../PHPExcel/PHPExcel.php';
			       date_default_timezone_set('America/Mexico_City');
			       $objPHPExcel = new PHPExcel();

				   if (PHP_SAPI == 'cli')
			          die('Este archivo solo se puede ver desde un navegador web');
			       $objPHPExcel->getProperties()->setCreator("Codedrinks") //Autor
							 ->setLastModifiedBy("Codedrinks") //Ultimo usuario que lo modificó
							 ->setTitle("Reporte Excel con PHP y MySQL")
							 ->setSubject("Reporte Excel con PHP y MySQL")
							 ->setDescription("Reporte de alumnos")
							 ->setKeywords("reporte alumnos carreras")
							 ->setCategory("Reporte excel");

			      $objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1',"LISTA ".$codigo);


				  $tituloReporte = "PEDIDO";
				  $titulosColumnas = array('CANTIDAD', 'PRODUCTO', 'COSTO UNITARIO' , 'COSTO TOTAL');

				  // Se agregan los titulos del reporte
		          $objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A2',$titulosColumnas[0])
        		    ->setCellValue('B2',  $titulosColumnas[1])
		            ->setCellValue('C2',  $titulosColumnas[2])
        		    ->setCellValue('D2',  $titulosColumnas[3]);

                 
                  $sql = "select 
								codigo,nombre,pedido.cantidad,pedido.costo,cantidad_pedido,pedido.estado
								from producto,pedido
								where producto.idproducto = pedido.idproducto
								      and codigo = $codigo";

            	  $consulta = $conexion->query($sql);

            	  //$objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloDescuento);
							
                  if($conexion->numrows($consulta)>0)
                  { 
                  	   $i = 3;
                  	   $t = 0;
                  	   while(  $fila = mysql_fetch_row($consulta))
		               {             
		                    $ctotal = $fila[2] * $fila[3];  
		                    $t+= $ctotal; 
							$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A'.$i, $fila[2])
							->setCellValue('B'.$i, $fila[1])
							->setCellValue('C'.$i, $fila[3])
							->setCellValue('D'.$i, $ctotal);
							$i++;
		                }
		                $objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('D'.$i, $t);
                       /** ******************FIN WHILE********************************************************************************************************/

                  }
                  $estiloTituloReporte = array(
		        	'font' => array(
			        	'name'      => 'Verdana',
		    	        'bold'      => true,
		        	    'italic'    => false,
		                'strike'    => false,
		               	'size' =>16,
			            	'color'     => array(
		    	            	'rgb' => 'FFFFFF'
		        	       	)
		            ),
			        'fill' => array(
						'type'	=> PHPExcel_Style_Fill::FILL_SOLID,
						'color'	=> array('rgb' => '21a2ff')
					),
		            'borders' => array(
		               	'allborders' => array(
		                	'style' => PHPExcel_Style_Border::BORDER_NONE                    
		               	)
		            ), 
		            'alignment' =>  array(
		        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        			'rotation'   => 0,
		        			'wrap'          => TRUE
		    		)
		            );

				   $estiloTituloColumnas = array(
		            'font' => array(
		            	'size' =>8,
		                'name'      => 'Arial',
		                'bold'      => true,                          
		                'color'     => array(
		                    'rgb' => 'FFFFFF'
		                ),

		            ),
		            'fill' 	=> array(
						'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation'   => 90,
		        		'startcolor' => array(
		            		'rgb' => 'c47cf2'
		        		),
		        		'endcolor'   => array(
		            		'rgb' => '21a2ff'
		        		)
					),
		            'borders' => array(
		            	'top'     => array(
		                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
		                    'color' => array(
		                        'rgb' => '2f8df6'
		                    )
		                ),
		                'bottom'     => array(
		                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
		                    'color' => array(
		                        'rgb' => '2f8df6'
		                    )
		                )
		            ),
					'alignment' =>  array(
		        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        			'wrap'          => TRUE
		    		));
                    $estiloInformacion = new PHPExcel_Style();
					$estiloInformacion->applyFromArray(
						array(
			           		'font' => array(
			               	'name'      => 'Arial',               
			               	'color'     => array(
			                   	'rgb' => '000000'
			               	)
			           	),
			           	'fill' 	=> array(
							'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
							'color'		=> array('argb' => 'FFd9b7f4')
						),
			           	'borders' => array(
			               	'left'     => array(
			                   	'style' => PHPExcel_Style_Border::BORDER_THIN ,
				                'color' => array(
			    	            	'rgb' => '3a2a47'
			                   	)
			               	)             
			           	)
			        ));


			        $estiloVentas = array
			        (		            
			            'fill' 	=> array
			            (
							'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
							'rotation'   => 90,
			        		'startcolor' => array
			        		(
			            		'rgb' => '21a2ff'
			        		),
			        		'endcolor'   => array
			        		(
			            		'rgb' => '21a2ff'
			        		)
						)
			        );

			       

			        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:D1');
			        $nombreCliente = utf8_encode($nombreCliente);
			       
			        //$objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($estiloTituloReporte);
					$objPHPExcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray( $estiloVentas);		
					//$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:D".($i-1));
							
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(10);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(30);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(10);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(16);
					
					
					// Se asigna el nombre a la hoja
					$objPHPExcel->getActiveSheet()->setTitle('Alumnos');

					// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
					$objPHPExcel->setActiveSheetIndex(0);
					// Inmovilizar paneles 
					//$objPHPExcel->getActiveSheet(0)->freezePane('A4');
					//$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

					// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="Reportedealumnos.xlsx"');
					header('Cache-Control: max-age=0');

					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('PEDIDO.xlsx');
					exit;

			    break;




			    case "inventario":
			       require_once '../PHPExcel/PHPExcel.php';
			       date_default_timezone_set('America/Mexico_City');
			       $objPHPExcel = new PHPExcel();

				   if (PHP_SAPI == 'cli')
			          die('Este archivo solo se puede ver desde un navegador web');
			       $objPHPExcel->getProperties()->setCreator("Codedrinks") //Autor
							 ->setLastModifiedBy("Codedrinks") //Ultimo usuario que lo modificó
							 ->setTitle("Reporte Excel con PHP y MySQL")
							 ->setSubject("Reporte Excel con PHP y MySQL")
							 ->setDescription("Reporte de alumnos")
							 ->setKeywords("reporte alumnos carreras")
							 ->setCategory("Reporte excel");

			 

				  $tituloReporte = "INVENTARIO";
				  $titulosColumnas = array('NOMBRE', 'PRECIO VENTA', 'COSTO' , 'STOCK' , 'CANTIDAD', 'VR INVENTARIO', 'VR VENTA', 'CODIGO');

				  // Se agregan los titulos del reporte
		          $objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1',$titulosColumnas[0])
        		    ->setCellValue('B1',  $titulosColumnas[1])
		            ->setCellValue('C1',  $titulosColumnas[2])
        		    ->setCellValue('D1',  $titulosColumnas[3])
        		    ->setCellValue('E1',  $titulosColumnas[4])
        		    ->setCellValue('F1',  $titulosColumnas[5])
        		    ->setCellValue('G1',  $titulosColumnas[6])
        		    ->setCellValue('H1',  $titulosColumnas[7]);

                 $_proveedor = $_SESSION['proveedor'];
                 $_grupo = $_SESSION['grupo'];
            
                 $sql = "select producto.*,proveedor.nombre 
                    from producto,proveedor
                    where idproveedor = proveedor_id  
                          $_proveedor
                           $_grupo
                    group by (idproducto)
                    order by (producto.nombre) asc
                    ";
            	  $consulta = $conexion->query($sql);

            	  //$objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->applyFromArray($estiloDescuento);
							
                  if($conexion->numrows($consulta)>0)
                  { 
                  	   $i = 2;
                  	   $total_pre_venta = 0;
                       $total_val_ivent = 0;
                       $total_val_venta = 0;

                  	   while(  $fila = mysql_fetch_row($consulta))
		               {   
							$vr_invent = $fila[8] * $fila[4];
	                        $vr_venta = $fila[4] * $fila[2];
	                        
	                        $total_val_venta+=$vr_venta;
	                        $total_val_ivent+=$vr_invent;                        
	                        $total_pre_venta+=$fila[2];
	                        
	                           
	                         $tabla= "<tr>
	                                      <td>$fila[1]</td>
	                                      <td>$ $fila[2]</td>
	                                      <td>$fila[8]</td>
	                                      <td>$fila[3]</td>
	                                      <td>$fila[4]</td>
	                                      <td>$ $vr_invent</td>
	                                      <td>$ $vr_venta</td>                                
	                                      <td>$fila[7]</td>
	                                   </tr>";
	                         $objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A'.$i, $fila[1])
							->setCellValue('B'.$i, $fila[2])
							->setCellValue('C'.$i, $fila[8])
							->setCellValue('D'.$i, $fila[3])
							->setCellValue('E'.$i, $fila[4])
							->setCellValue('F'.$i, $vr_invent)
							->setCellValue('G'.$i, $vr_venta)
							->setCellValue('H'.$i, $fila[7]);
							$i++;
		                }
		                
                  }
                  $estiloTituloReporte = array(
		        	'font' => array(
			        	'name'      => 'Verdana',
		    	        'bold'      => true,
		        	    'italic'    => false,
		                'strike'    => false,
		               	'size' =>16,
			            	'color'     => array(
		    	            	'rgb' => 'FFFFFF'
		        	       	)
		            ),
			        'fill' => array(
						'type'	=> PHPExcel_Style_Fill::FILL_SOLID,
						'color'	=> array('rgb' => '21a2ff')
					),
		            'borders' => array(
		               	'allborders' => array(
		                	'style' => PHPExcel_Style_Border::BORDER_NONE                    
		               	)
		            ), 
		            'alignment' =>  array(
		        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        			'rotation'   => 0,
		        			'wrap'          => TRUE
		    		)
		            );

				   $estiloTituloColumnas = array(
		            'font' => array(
		            	'size' =>8,
		                'name'      => 'Arial',
		                'bold'      => true,                          
		                'color'     => array(
		                    'rgb' => 'FFFFFF'
		                ),

		            ),
		            'fill' 	=> array(
						'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation'   => 90,
		        		'startcolor' => array(
		            		'rgb' => 'c47cf2'
		        		),
		        		'endcolor'   => array(
		            		'rgb' => '21a2ff'
		        		)
					),
		            'borders' => array(
		            	'top'     => array(
		                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
		                    'color' => array(
		                        'rgb' => '2f8df6'
		                    )
		                ),
		                'bottom'     => array(
		                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
		                    'color' => array(
		                        'rgb' => '2f8df6'
		                    )
		                )
		            ),
					'alignment' =>  array(
		        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        			'wrap'          => TRUE
		    		));
                    $estiloInformacion = new PHPExcel_Style();
					$estiloInformacion->applyFromArray(
						array(
			           		'font' => array(
			               	'name'      => 'Arial',               
			               	'color'     => array(
			                   	'rgb' => '000000'
			               	)
			           	),
			           	'fill' 	=> array(
							'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
							'color'		=> array('argb' => 'FFd9b7f4')
						),
			           	'borders' => array(
			               	'left'     => array(
			                   	'style' => PHPExcel_Style_Border::BORDER_THIN ,
				                'color' => array(
			    	            	'rgb' => '3a2a47'
			                   	)
			               	)             
			           	)
			        ));


			        $estiloVentas = array
			        (		            
			            'fill' 	=> array
			            (
							'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
							'rotation'   => 90,
			        		'startcolor' => array
			        		(
			            		'rgb' => '21a2ff'
			        		),
			        		'endcolor'   => array
			        		(
			            		'rgb' => '21a2ff'
			        		)
						)
			        );
 
			        //$objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($estiloTituloReporte);
					$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray( $estiloVentas);		
					//$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:D".($i-1));
							
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("A")->setWidth(10);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("B")->setWidth(30);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("C")->setWidth(10);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(16);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("E")->setWidth(16);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("F")->setWidth(16);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("G")->setWidth(16);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("H")->setWidth(16);
			        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension("I")->setWidth(16);
					
					
					// Se asigna el nombre a la hoja
					$objPHPExcel->getActiveSheet()->setTitle('inventario');

					// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
					$objPHPExcel->setActiveSheetIndex(0);
					// Inmovilizar paneles 
					//$objPHPExcel->getActiveSheet(0)->freezePane('A4');
					//$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

					// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="Reportedealumnos.xlsx"');
					header('Cache-Control: max-age=0');

					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('INVENTARIO.xlsx');
					exit;

			    break;
			}
		}
?>