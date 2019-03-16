var globals = null;
var dataTable = null; 

$.formObject = function($o) 
{
      var o = {},
      real_value = function($field) 
      {
          var val = $field.val() || "";
          return val;
      };

      if (typeof o != "object") 
      {
          $o = $(o);
      }

      $(":input[name]", $o).each(function(i, field) 
      {
         
           
              var $field = $(field),
                  name = $field.attr("name"),
                  value = real_value($field);

              if (o[name] ) 
              {
                  if (!$.isArray(o[name])) 
                  {
                      o[name] = [o[name]];
                  }
                  o[name].push(value);
              }
              else 
              {
                  o[name] = value;
              }
           
      });

      return o;
} 
 
function agregrarModal()
{
    $("body").append("<div class='ui modal'>"+
          "<i class='close icon'></i>"+
          "<div class='header header-modal'>"+ 
          "</div>"+
          "<div class='content scrolling' id='content-modal'></div> "+
      "</div>");

}
function calcularCantidadEditar()
{
    $("#operacion").val();
    switch( Number($("#operacion").val()) )
    {
       case 1:
         $("#cantidadEnviar").val( Number($("#cantidadEnviar").data("cantidad")) + Number($("#cantidadEscribir").val())  );
       break;

       case 2:
         $("#cantidadEnviar").val( Number($("#cantidadEnviar").data("cantidad")) - Number($("#cantidadEscribir").val())  );
       break;

    }
}
function managePagination( action , obj )
{  
   switch( obj.opcion )
   {
      case "Trabajadores":
          obj.opcion = "paginar_trabajadores";
          PaginarTrabajadores( action , obj )
      break;

      case "consultar_marcas": 
          obj.opcion = "paginar_marcas";
          PaginarMarcas( action , obj )
      break;

      case "paginar_devolucion_compra":
          PaginarDevolucionCompra( action , obj )
      break;

      case "paginar_devolucion_venta":
          PaginarDevolucionVenta( action , obj )
      break;

      case "PaginarProductosCastigados":
          PaginarProductosCastigados( "PaginarProductosCastigados" , "get.php" , obj )
      break;

      case "paginar_reporte_entrada_salida":
          PaginarReporteEntadaSalida( action , obj );
      break;

      case "Facturas":
          PaginarFacturas( "Facturas" , "get.php" , obj )
      break;

      case "Proveedores":
          obj.opcion = "paginar_proveedores";
          PaginarProveedores(  action , obj )
      break;

      case "Clientes":
          obj.opcion = "paginar_clientes";
          PaginarClientes(  action , obj )
      break; 

      case "PorTerminar":
          PorTerminar( action , obj )
      break; 

      case "Productos":
          obj.opcion = "paginar_productos";
          PaginarProducto( action , obj )
      break;

      case "Inventario":  
          obj.opcion = "paginar_inventario";
          PaginarInventario( action , obj );
      break;

      case "paginar_reporte_ventas":
          PaginarReporteVentas( action , obj  );
      break;

      case "paginar_reporte_compras":
          PaginarReporteCompras( action , obj  );
      break;

      case "ReporteCuentasPorPagar": 
          PaginarReporteCuentasPorPagar( "PaginarReporteCuentasPorPagar" , "get.php"  , obj )
      break;

      case "PaginarReporteCuentasPorCobrar": 
          PaginarReporteCuentasPorCobrar( "PaginarReporteCuentasPorCobrar" , "get.php"  , obj )
      break;

      case "paginar_cxp": 
          PaginarPorPagar( action , obj )
      break;

      case "paginar_cxc": 
          PaginarPorCobrar( action , obj )
      break;

      case "paginar_reporte_gastos": 
          PaginarReporteGastos( action , obj)
      break;

      case "paginar_reporte_egresos": 
          PaginarReporteEgresos( action, obj);
      break;

      case "paginar_reporte_ventas": 
          PaginarReporteVentas(  action , obj)
      break;

      case "PaginarReporteEntregaAseo": 
          PaginarReporteEntregaAseo( "PaginarReporteEntregaAseo" , "get.php" , obj)
      break;

      case "PaginarReporteCortesias": 
          PaginarReporteCortesias( "PaginarReporteCortesias" , "get.php" , obj)
      break;

      case "PaginarKardex":
          PaginarKardex( "PaginarKardex" , "get.php" , obj );
      break;

     
   }
}
function PaginarTrabajadores( action , obj )
{ 
    dataTable = $('.table-trabajadores').DataTable
    ({
        "language": 
        {
           "url": "../assets/Spanish.json"
        },  
        "autoWidth": false , 
        initComplete:function( settings, json)
        { 
                                  
        },
        "processing": false ,
        "searching": false ,
        "pageLength": 10,
        "serverSide": true,
        "ordering": false, 
        "destroy": true,
        "columns": 
        [
             {"data": "id"},
             {"data": "id"},
             {"data": "codigo"},
             {"data": "nombre"},
             {"data": "apellido"},
             {"data": "cedula"},
             {"data": "telefono"}
        ],  
        columnDefs: 
        [
          { 
              targets:[0],
              render: function ( data, type, row, meta ) 
              {
                 return  "<i class='link abrir-modal icon write'  style='font-size:24px;' data-url='get.php' data-title='EDITAR TRABAJADOR' data-data='opcion=EditarTrabajador&idtrabajador="+data+"' ></i>";

              }
            },
            { 
              targets:[1],
              render: function ( data, type, row, meta ) 
              {
                 if( row.status == true )
                  {
                     return "<i style='font-size:24px;' class='icon trash link' data-url='set.php' data-response='content' data-data='opcion=InactivarTrabajador&id="+data+"' ></i>";
                  }
                  else
                  {
                     return "<i style='font-size:24px;' class='check circle icon link' data-url='set.php'  data-response='content' data-data='opcion=ActivarTrabajador&id="+data+"' ></i>";                     
                  } 
              }
            }       
        ], 
        "ajax":
        {
            "type": "POST", 
            "url": action,                    
            "data": obj,
            beforeSend: function (request) 
            { 
            },
            error: function( error )
            {  
               $("#content").append( error.responseText );
            }
        }
    });
}
function PaginarDevolucionCompra( action , obj )
{
    dataTable = $('.table-devolucion-compra').DataTable
    ({
        "language": 
        {
           "url": "../assets/Spanish.json"
        },  
        "autoWidth": false , 
        initComplete:function( settings, json)
        { 
                                  
        },
        "processing": false ,
        "searching": false ,
        "pageLength": 10,
        "serverSide": true,
        "ordering": false, 
        "destroy": true,
        "columns": 
        [
             {"data": "fecha"},
             {"data": "tipo"},
             {"data": "usuario"},
             {"data": "proveedor"},
             {"data": "productos"},
             {"data": "id"}
        ],  
        columnDefs: 
        [
          { 
              targets:[5],
              render: function ( data, type, row, meta ) 
              {
                 return "<i style='font-size:24px;' class='icon search link abrir-modal' data-title='DETALLE DE LA COMPRA' data-response='content' data-url='get.php' data-data='opcion=ObtenerDetalleDevolverCompra&compra_id="+data+"'  ></i>";
              }
          }      
        ],  
        "ajax":
        {
            "type": "POST", 
            "url": action,                    
            "data": obj,
            beforeSend: function (request) 
            { 
            },
            error: function( error )
            {  
               $("#content").append( error.responseText );
            }
        }
    });
}
function PaginarDevolucionVenta( action , obj )
{
    dataTable = $('.table-devolucion-venta').DataTable
    ({
        "language": 
        {
           "url": "../assets/Spanish.json"
        },  
        "autoWidth": false , 
        initComplete:function( settings, json)
        { 
                                  
        },
        "processing": false ,
        "searching": false ,
        "pageLength": 10,
        "serverSide": true,
        "ordering": false, 
        "destroy": true,
        "columns": 
        [
             {"data": "fecha"},
             {"data": "productos"},
             {"data": "usuario"},
             {"data": "cliente"},
             {"data": "count"},
             {"data": "id"}
        ],  
        columnDefs: 
        [
          { 
              targets:[5],
              render: function ( data, type, row, meta ) 
              {
                 return "<i style='font-size:24px;' class='icon search link abrir-modal' data-title='DETALLE DE LA VENTA' data-response='content' data-url='get.php' data-data='opcion=ObtenerDetalleDevolverVenta&venta_id="+data+"'  ></i>";
              }
          }      
        ],  
        "ajax":
        {
            "type": "POST", 
            "url": action,                    
            "data": obj,
            beforeSend: function (request) 
            { 
            },
            error: function( error )
            {  
               $("#content").append( error.responseText );
            }
        }
    });
}
function PaginarReporteEntadaSalida( action , obj )
{
    dataTable = $('.table-reporte-entrada-salida').DataTable
    ({
        "language": 
        {
           "url": "../assets/Spanish.json"
        },  
        "autoWidth": false , 
        initComplete:function( settings, json)
        { 
                                  
        },
        "processing": false ,
        "searching": false ,
        "pageLength": 10,
        "serverSide": true,
        "ordering": false, 
        "destroy": true,
        "columns": 
        [
             {"data": "fecha"},
             {"data": "nombre"},
             {"data": "entrada"},
             {"data": "salida"},
             {"data": "concepto"},
             {"data": "usuario"}
        ],  
        /*
        columnDefs: 
        [
            { 
              targets:[0],
              render: function ( data, type, row, meta ) 
              {
                 return  "<div style='font-size:24px;' class='link abrir-modal ion-edit' data-url='get.php' data-title='EDITAR TRABAJADOR' data-data='opcion=EditarMesa&id="+data+"' ></div>";
              }
            },
            { 
              targets:[1],
              render: function ( data, type, row, meta ) 
              {
                 if( row.status == true )
                  {
                     return "<div style='font-size:24px;' class='ion-checkmark link' data-url='set.php' data-response='content' data-data='opcion=InactivarMesa&id="+data+"' ></div>";
                  }
                  else
                  {
                     return "<div style='font-size:24px;' class='ion-close-round link' data-url='set.php'  data-response='content' data-data='opcion=ActivarMesa&id="+data+"' ></div>";                     
                  } 
              }
            } ,
            { 
              targets:[2],
              render: function ( data, type, row, meta ) 
              {
                 return  "MESA " + data ;
              }
            }     
        ], */
        "ajax":
        {
            "type": "POST", 
            "url": action,                    
            "data": obj,
            beforeSend: function (request) 
            { 
            },
            error: function( error )
            {  
               $("#content").append( error.responseText );
            }
        }
    });
}
function PaginarFacturas( action , obj )
{
    obj.opcion = "PaginarFacturas";
    dataTable = $('.table-facturas').DataTable
    ({
        "language": 
        {
           "url": "../assets/Spanish.json"
        },  
        "autoWidth": false , 
        initComplete:function( settings, json)
        { 
                                  
        },
        "processing": false ,
        "searching": false ,
        "pageLength": 10,
        "serverSide": true,
        "ordering": false, 
        "destroy": true,
        "columns": 
        [
             {"data": "id"},
             {"data": "inicio"},
             {"data": "fin"},
             {"data": "prefijo"},
             {"data": "fecha"}
        ],  
        columnDefs: 
        [
          { 
              targets:[0],
              render: function ( data, type, row, meta ) 
              {
                 return  "<div class='link abrir-modal edit_product' data-url='get.php' data-title='EDITAR NUMERACION' data-data='opcion=EditarFactura&id="+data+"' ></div>";
              }
            } 
        ], 
        "ajax":
        {
            "type": "POST", 
            "url": url,                    
            "data": obj,
            beforeSend: function (request) 
            { 
            },
            error: function( error )
            {  
               $("#content").append( error.responseText );
            }
        }
    });
}
function PaginarProveedores( action , obj )
{ 
    dataTable = $('.table-proveedores').DataTable
    ({
        "language": 
        {
           "url": "../assets/Spanish.json"
        },  
        "autoWidth": false , 
        initComplete:function( settings, json)
        { 
                                  
        },
        "processing": false ,
        "searching": false ,
        "pageLength": 10,
        "serverSide": true,
        "ordering": false, 
        "destroy": true,
        "columns": 
        [
             {"data": "id"},
             {"data": "id"},
             {"data": "nombre"},
             {"data": "documento"},
             {"data": "telefono"},
             {"data": "direccion"}
        ],  
        columnDefs: 
        [ 
          { 
              targets:[0],
              render: function ( data, type, row, meta ) 
              {
                 return  "<i class='link abrir-modal icon write'  style='font-size:24px;' data-url='get.php' data-title='EDITAR PROVEEDOR' data-data='opcion=EditarProveedor&idproveedor="+data+"' ></i>";
              }
            },
            { 
              targets:[1],
              render: function ( data, type, row, meta ) 
              {
                 if( row.status == true )
                  {

                     return "<i class='trash icon link '  style='font-size:24px;' data-url='set.php' data-response='content' data-data='opcion=InactivarProveedor&id="+data+"' ></i>";
                  }
                  else
                  {
                       return "<i class='check circle icon link'  style='font-size:24px;' data-url='set.php'  data-response='content' data-data='opcion=ActivarProveedor&id="+data+"' ></i>";                     
                  } 
              }
            }       
        ], 
        "ajax":
        {
            "type": "POST", 
            "url": action,                    
            "data": obj,
            beforeSend: function (request) 
            { 
            },
            error: function( error )
            {  
               $("#content").append( error.responseText );
            }
        }
    });
}
function PorTerminar( action , obj )
{
    obj.opcion = "paginar_por_terminar";
    if( $("#todos").val().length > 0 )
      obj.todos = "si";

    console.log("action " ,  action );
    console.log("obj " ,  obj );
    console.log("action " ,  action );
    
    dataTable = $('.table-por-terminar').DataTable
    ({
        "language": 
        {
           "url": "../assets/Spanish.json"
        },  
        "autoWidth": false , 
        initComplete:function( settings, json)
        {
            //OcultarLoading();
            //$(".tooltipstered").tooltipster({}); 

           // $("div.toolbar").html('<div class="dataTables_length" id="DataTables_Table_0_length"><label>Buscar:<select name="tipo" style="width:200px; margin-left:4px;" class="form-control-sm form-control" ><option>Materia prima</option><option>Terminado</option></select></label></div>');
                                  
        },
        "processing": false ,
        "searching": false ,
        "pageLength": 10,
        "serverSide": true,
        "ordering": false, 
        "destroy": true,
        "columns": 
        [
             {"data": "nombre"},
             {"data": "cantidad"},
             {"data": "stock"},
             {"data": "precio"},
             {"data": "costo"}
        ], 
        /*
        columnDefs: 
        [
          { 
              targets:[0],
              render: function ( data, type, row, meta ) 
              {
                 return  "<div class='link abrir-modal  edit_product' data-url='get.php' data-title='EDITAR PRODUCTO' data-data='opcion=EditarProducto&id="+data+"' ></div>";
              }
            },
            { 
              targets:[1],
              render: function ( data, type, row, meta ) 
              {
                 return  "<div class='link delete_product' data-url='set.php' data-title='BORRAR PRODUCTO' data-response='content'  data-data='opcion=BorrarProducto&id="+data+"' ></div>";
              }
            },
            { 
              targets:[5],
              render: function ( data, type, row, meta ) 
              {
                 return  row.cantidad * row.costo;
              }
            },
            { 
              targets:[6],
              render: function ( data, type, row, meta ) 
              {
                 return  row.cantidad * row.precio;
              }
            },
            {
              targets:[3],
              render:function( data , type , row , meta )
              {
                  abreviatura = "";
                  clase = "";
                  if( row.abreviatura )
                  {
                    abreviatura = "("+row.abreviatura+")";
                    clase = "subrayado link abrir-modal";
                  }
                  return "<div class='"+clase+"' data-url='get.php' data-title='Costos' data-data='opcion=VerCostosMedidas&idproducto="+row.id+"' data-response='content'>"+data+" "+ abreviatura + " </div>";

              }
            }       
        ],
        */ 
        "ajax":
        {
            "type": "POST", 
            "url": action,                    
            "data": obj,
            beforeSend: function (request) 
            {
                  //MostrarLoading();
            },
            error: function( error )
            {  
               $("#content").append( error.responseText );
            }
        }
    });
} 
function Consecutivos( action , obj )
{
    obj.opcion = "PaginarConsecutivos";
    dataTable = $('.table-consecutivos').DataTable
    ({
        "language": 
        {
           "url": "../assets/Spanish.json"
        },  
        "autoWidth": false , 
        initComplete:function( settings, json)
        {
            //OcultarLoading();
            //$(".tooltipstered").tooltipster({}); 

           // $("div.toolbar").html('<div class="dataTables_length" id="DataTables_Table_0_length"><label>Buscar:<select name="tipo" style="width:200px; margin-left:4px;" class="form-control-sm form-control" ><option>Materia prima</option><option>Terminado</option></select></label></div>');
                                  
        },
        "processing": false ,
        "searching": false ,
        "pageLength": 10,
        "serverSide": true,
        "ordering": false, 
        "destroy": true,
        "columns": 
        [
             {"data": "id"},
             {"data": "id"},
             {"data": "trabajador"},
             {"data": "inicio"},
             {"data": "fin"},
             {"data": "va"}
        ],  
        columnDefs: 
        [
          { 
              targets:[0],
              render: function ( data, type, row, meta ) 
              {
                 return  "<div class='link abrir-modal  edit_product' data-url='get.php' data-title='EDITAR CONSECUTIVO' data-data='opcion=EditarConsecutivo&id="+data+"' ></div>";
              }
            },
            { 
              targets:[1],
              render: function ( data, type, row, meta ) 
              {
                 return  "<div class='link confirm delete_product' data-url='set.php' data-title='BORRAR CONSECUTIVO' data-response='modal'  data-data='opcion=EliminarConsecutivo&id="+data+"' ></div>";
              }
            } 
        ], 
        "ajax":
        {
            "type": "POST", 
            "url": url,                    
            "data": obj,
            beforeSend: function (request) 
            {
                  //MostrarLoading();
            },
            error: function( error )
            {  
               $("#content").append( error.responseText );
            }
        }
    });
} 
function PaginarProducto( action , obj )
{ 
    dataTable = $('.table-productos').DataTable
    ({
        "language": 
        {
           "url": "../assets/Spanish.json"
        },  
        "autoWidth": false , 
        initComplete:function( settings, json)
        {
            //OcultarLoading();
            //$(".tooltipstered").tooltipster({}); 

           // $("div.toolbar").html('<div class="dataTables_length" id="DataTables_Table_0_length"><label>Buscar:<select name="tipo" style="width:200px; margin-left:4px;" class="form-control-sm form-control" ><option>Materia prima</option><option>Terminado</option></select></label></div>');
                                  
        },
        "processing": false ,
        "searching": false ,
        "pageLength": 10,
        "serverSide": true,
        "ordering": false, 
        "destroy": true,
        "columns": 
        [
             {"data": "id"},
             {"data": "id"},
             {"data": "nombre"},
             {"data": "cantidad"},
             {"data": "precio"},
             {"data": "stock"}
        ], 
        columnDefs: 
        [
            { 
              targets:[0],
              render: function ( data, type, row, meta ) 
              {
                 return  "<div class='link abrir-modal icon edit' style='font-size:24px;' data-url='get.php' data-title='EDITAR PRODUCTO' data-data='opcion=EditarProducto&id="+data+"' ></div>";
              }
            },
            { 
              targets:[1],
              render: function ( data, type, row, meta ) 
              {
                 return  "<i class='link icon trash' style='font-size:24px;' data-url='set.php' data-title='BORRAR PRODUCTO' data-response='content'  data-data='opcion=BorrarProducto&id="+data+"' ></i>";
              }
            },    
        ], 
        "ajax":
        {
            "type": "POST", 
            "url": action,                    
            "data": obj,
            beforeSend: function (request) 
            {
                  //MostrarLoading();
            },
            error: function( error )
            {  
               $("#content").append( error.responseText );
            }
        }
    });
} 
function PaginarInventario(  action , obj )
{   
    dataTable = $('.table-inventario').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
      },
      "processing": false ,
      "pageLength": 10,
      "ordering": false, 
      "destroy": true,
      "serverSide": true,
      "columns": 
      [
           {"data": "id" },
           {"data": "id" },
           {"data": "nombre"},
           {"data": "cantidad"},
           {"data": "stock"},
           {"data": "costo"},
           {"data": "precio"},
           {"data": "total_costo"},
           {"data": "total_precio"}
      ],                  
      columnDefs: 
      [
          { 
            targets:[0],
            render: function ( data, type, row, meta ) 
            {
               return  "<div class='link abrir-modal icon edit' style='font-size:24px;' data-url='get.php' data-title='EDITAR PRODUCTO' data-data='opcion=EditarProducto&id="+data+"' ></div>";
            }
          },
          { 
            targets:[1],
            render: function ( data, type, row, meta ) 
            {
               return  "<i class='link icon trash' style='font-size:24px;' data-url='set.php' data-title='BORRAR PRODUCTO' data-response='content'  data-data='opcion=BorrarProducto&id="+data+"' ></i>";
            }
          },
          { 
            targets:[5],
            render: function ( data, type, row, meta ) 
            {
               return  "$ " + data;
            }
          },
           { 
            targets:[6],
            render: function ( data, type, row, meta ) 
            {
               return  "$ " + data;
            }
          },
          { 
            targets:[7],
            render: function ( data, type, row, meta ) 
            {
               return  "$ " + data;
            }
          },
          { 
            targets:[8],
            render: function ( data, type, row, meta ) 
            {
               return  "$ " + data;
            }
          } 
      ], 
      "ajax":
      {
        "type": "post", 
        "url": action,                    
        "data": obj,
        beforeSend: function (request) 
        {
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        },
        dataSrc: function (json) 
        {
              console.log( json )
              /*
              var return_data = new Array();
              for(var i=0;i< json.length; i++)
              {
                  return_data.push
                  ({
                    'nombre': json[i].title,
                    'url'  : '<img src="' + json[i].url + '">',
                    'date' : json[i].date
                  })
              } 
              */
              globals = 
              {
                 "total_costo" : json.total_costo , 
                 "total_precio" : json.total_precio 
              }  

              return json.data;
        }
      },
      footerCallback: function (row, data, start, end, display) 
      {      
        var api = this.api();
        $(api.column(7).footer()).html('$' + numeral(globals.total_costo).format('0,0')  );
        $(api.column(8).footer()).html('$' + numeral(globals.total_precio).format('0,0')  );
        $(api.column(2).footer()).html("<div id='content_print' data-title='VENTAS' data-url='reports/Inventario.php'>Exportar a pdf</div>");
        $(api.column(3).footer()).html("<div id='content_excel' data-url='reports/Inventario.php'>Exportar a excel</div>");
      }
    });
} 
function PaginarReporteVentas( action , obj )
{  
    dataTable = $('.table-reporte-ventas').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {

          OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "fecha"},
           {"data": "trabajador"},
           {"data": "cliente"},
           {"data": "recibo"},
           {"data": "tipo"},
           {"data": "total"} ,
           {"data": "id"} ,
           {"data": "id"} ,
           {"data": "id"} ,
           {"data": "id"} 
      ],  
      columnDefs: 
      [
          { 
            targets:[5],
            render: function ( data, type, row, meta ) 
            {
               return numeral("$ " + data  ).format('0,0');
            }
          },
          { 
            targets:[6],
            render: function ( data, type, row, meta ) 
            {
               return  '<i  style="font-size:24px;" id="content_print_termica" data-url="imprimir_termica.php" data-data="opcion=imprimir_venta&id='+data+'"></i>';
            }
          },
          { 
            targets:[7],
            render: function ( data, type, row, meta ) 
            { 
               return '<i  style="font-size:24px;" id="content_print" data-title="RECIBO" data-url="reports/Recibos.php" data-data="id='+data+'"></i>';
            }
          },
          { 
            targets:[8],
            render: function ( data, type, row, meta ) 
            {
               return '<i  style="font-size:24px;" id="content_print" data-title="FACTURA" data-url="reports/Facturas.php" data-data="id='+data+'"></i>';
            }
          },
          { 
            targets:[9],
            render: function ( data, type, row, meta ) 
            { 
               return  "<i class='link abrir-modal icon search' style='font-size:24px;' data-url='get.php' data-title='DETALLE VENTA' data-data='opcion=DetalleVenta&id="+data+"' ></i>";

            }
          } 
      ], 
      "ajax":
      {
        "type": "post", 
        "url": action,                    
        "data":obj,
        beforeSend: function (request) 
        {
              MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error);
        },
        dataSrc: function (json) 
        { 
        	OcultarLoading();
          globals = 
          {
             "total" : json.total
          } 
          return json.data;

        } 
      }
      ,
      footerCallback: function (row, data, start, end, display) 
      {
      
        var api = this.api();
        $(api.column(5).footer()).html('<b>$' + numeral( globals.total  ).format('0,0') + '</b>' ); 
      } 
    });
} 
function PaginarReporteCortesias( action , obj )
{ 
    obj.opcion = opcion;  
    dataTable = $('.table-reporte-cortesias').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          //OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "fecha"},
           {"data": "trabajador"},
           {"data": "cliente"},
           {"data": "direccion"},
           {"data": "recibo"},
           {"data": "tipo"},
           {"data": "nombre"},
           {"data": "costo"},
           {"data": "cantidad"},
           {"data": "total"} 
      ], 
      /*
      columnDefs: 
      [
          { 
            targets:[5],
            render: function ( data, type, row, meta ) 
            {
               return  row.cantidad * row.costo;
            }
          },
          { 
            targets:[6],
            render: function ( data, type, row, meta ) 
            {
               return  row.cantidad * row.precio;
            }
          },
          {
            targets:[3],
            render:function( data , type , row , meta )
            {
                abreviatura = "";
                if( row.abreviatura )
                  abreviatura = "("+row.abreviatura+")";
                return "<div class='subrayado link abrir-modal' data-url='get.php' data-title='Costos' data-data='opcion=VerCostosMedidas&idproducto="+row.id+"' data-response='content'>"+data+" "+ abreviatura + " </div>";

            }
          }       
      ], */
      "ajax":
      {
        "type": "post", 
        "url": url,                    
        "data":obj,
        beforeSend: function (request) 
        {
              //MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error);
        },
        dataSrc: function (json) 
        { 
          globals = 
          {
             "total" : json.total
          } 
          return json.data;
        }
      },
      footerCallback: function (row, data, start, end, display) 
      {
      
        var api = this.api(),
        intVal = function (i) 
        {
              return typeof i === 'string' ?
                   i.replace(/[, Rs]|(\.\d{2})/g,"")* 1 :
                   typeof i === 'number' ?
                   i : 0;
        },
        total7 = api.column(7).data().reduce(function (a, b) 
        {
               console.log("globals",globals);
                return intVal(a) + intVal(b);
        }, 0);
  
        $(api.column(7).footer()).html('$' + globals.total ); 
      }
    });
} 
function PaginarReporteCompras( action , obj )
{ 

    dataTable = $('.table-reporte-compras').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          //OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "searching": false ,
      "processing": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
      "destroy": true,
      "columns": 
      [
           {"data": "id"},
           {"data": "fecha"},
           {"data": "trabajador"},
           {"data": "factura"},
           {"data": "tipo"},
           {"data": "proveedor"},
           {"data": "total"},
           {"data": "productos"},
           {"data": "id"} 
      ], 
      
      columnDefs: 
      [
          { 
            targets:[8],
            render: function ( data, type, row, meta ) 
            {
               return  "<i class='link abrir-modal icon search' style='font-size:24px;' data-url='get.php' data-title='DETALLE DE LA COMPRA' data-data='opcion=DetalleCompra&id="+data+"' ></i>";
                
            }
          } , { 
            targets:[0],
            render: function ( data, type, row, meta ) 
            {
               return  "<i class='editar_compra icon write'  style='font-size:24px;' data-id='"+data+"' ></i>";

            }
          }       
      ],
      "ajax":
      {
        "type": "post", 
        "url": action,                    
        "data":obj,
        beforeSend: function (request) 
        {
              //MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error);
        },
        dataSrc: function (json) 
        {          
          globals = 
          {
             "total" : json.total 
          } 
          return json.data;
        }
      },
      footerCallback: function (row, data, start, end, display) 
      {      
        var api = this.api();  
        $(api.column(6).footer()).html('$' + globals.total);
      }
    });
}
function PaginarReporteCuentasPorPagar( action , obj )
{  
    obj.opcion = opcion;
    dataTable = $('.table-reporte-cuentas-por-pagar').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          //OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "tipo_compra_id"},
           {"data": "fecha"},
           {"data": "factura"},
           {"data": "trabajador"},
           {"data": "proveedor"},
           {"data": "total"} 
      ],  
      columnDefs: 
      [
          { 
            targets:[0],
            render: function ( data, type, row, meta ) 
            {
                if( data == 3 )
                  return '<div class="pagado"></div>';
                else
                  return '<div class="sin_pagar"></div>';
            }
          }           
      ], 
      "ajax":
      {
        "type": "post", 
        "url": url,                    
        "data":obj,
        beforeSend: function (request) 
        {
              //MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        }
      }
    });
}
function PaginarReporteCuentasPorCobrar( action , obj )
{  
    obj.opcion = opcion;
    dataTable = $('.table-reporte-cuentas-por-cobrar').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          //OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "id"},
           {"data": "fecha"},
           {"data": "recibo"},
           {"data": "trabajador"},
           {"data": "cliente"},
           {"data": "total"} 
      ],  
      columnDefs: 
      [
          { 
            targets:[0],
            render: function ( data, type, row, meta ) 
            {
                if( data == 3 )
                  return '<div class="pagado"></div>';
                else
                  return '<div class="sin_pagar"></div>';
            }
          }           
      ], 
      "ajax":
      {
        "type": "post", 
        "url": url,                    
        "data":obj,
        beforeSend: function (request) 
        {
              //MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        }
      }
    });
}
function PorPagarPendiente( action , obj )
{ 
    dataTable = $('.table-reporte-por-pagar').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          //OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "id"},
           {"data": "id"},
           {"data": "fecha"},
           {"data": "proveedor"},
           {"data": "total"} 
      ],  
      columnDefs: 
      [
          { 
            targets:[0],
            render: function ( data, type, row, meta ) 
            {
                 return "<div class='search detalle' data-titulo='DETALLE DE LA CUENTA POR COBRAR' data-page='get.php' data-data='opcion=DetallePorPagar&id="+data+"' aria-hidden='true'></div>";
            }
          } ,
          { 
            targets:[1],
            render: function ( data, type, row, meta ) 
            {
                 return "<div class='abonar link' data-response='content' data-url='get.php' data-data='opcion=AbonarPorPagar&id="+data+"'  ></div>";
            }
          }           
      ], 
      "ajax":
      {
        "type": "post", 
        "url": url,                    
        "data": obj,
        beforeSend: function (request) 
        {
            MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        },
        dataSrc: function (json) 
        {
          globals = 
          {
             "total" : json.total 
          } 
          return json.data;
        }
      },
      footerCallback: function (row, data, start, end, display) 
      { 
        var api = this.api(); 
        $(api.column(4).footer()).html('$' + globals.total  ); 
      }
    });
}
function PaginarPorPagar( action , obj )
{ 
    dataTable = $('.table-cxp').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          OcultarLoading();    
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [           
         {"data": "fecha"},
         {"data": "proveedor"},
         {"data": "cantidad"},
         {"data": "total"},
         {"data": "abono"},
         {"data": "debe"},
         {"data": "id"},
         {"data": "id"}
      ],  
      columnDefs: 
      [
          { 
            targets:[3],
            render: function ( data, type, row, meta ) 
            {
                return "$ "+ numeral( data ).format('0,0');
            }
          } ,
          { 
            targets:[4],
            render: function ( data, type, row, meta ) 
            {
                return "$ "+ numeral( data ).format('0,0');
            }
          } ,
          { 
            targets:[5],
            render: function ( data, type, row, meta ) 
            {
                return "$ "+ numeral( data ).format('0,0');
            }
          } ,
          { 
            targets:[6],
            render: function ( data, type, row, meta ) 
            {
                 return "<i style='font-size:24px;' data-blur='false' class='icon search link abrir-modal' data-title='DETALLE DE LA CUENTA' data-response='content' data-url='get.php' data-data='opcion=DetallePorPagar&id="+data+"'  ></i>";

            }
          } ,
          { 
            targets:[7],
            render: function ( data, type, row, meta ) 
            {
                 return "<i style='font-size:24px;' class='icon dollar link abrir-modal' data-title='REGISTRAR ABONOS' data-response='content' data-url='get.php' data-data='opcion=AbonarPorPagar&id="+data+"'  ></i>";
            }
          }           
      ], 
      "ajax":
      {
        "type": "post", 
        "url": action,                    
        "data": obj,
        beforeSend: function (request) 
        {
              MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        },
        dataSrc: function (json) 
        {
        	/*
          globals = 
          {
             "total" : json.total 
          } 
          */
          OcultarLoading();
          return json.data;
        }
      },
      footerCallback: function (row, data, start, end, display) 
      { 
        var api = this.api(); 
        //$(api.column(4).footer()).html('$' + globals.total  ); 
      }
    });
}
function PaginarPorCobrar( action , obj )
{ 
    dataTable = $('.table-cxc').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          OcultarLoading();     
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "fecha"},
           {"data": "cliente"},
           {"data": "cantidad"},
           {"data": "total"},
           {"data": "abono"},
           {"data": "debe"},
           {"data": "id"},
           {"data": "id"} 
      ], 
      columnDefs: 
      [
          { 
            targets:[3],
            render: function ( data, type, row, meta ) 
            {
                return "$ "+ numeral( data ).format('0,0'); 
            }
          } 
          ,
          { 
            targets:[7],
            render: function ( data, type, row, meta ) 
            {
                 return "<i style='font-size:24px;' class='icon dollar link abrir-modal' data-title='REGISTRAR ABONOS' data-response='content' data-url='get.php' data-data='opcion=AbonarPorCobrar&id="+data+"'  ></i>";

            }
          },
          { 
            targets:[6],
            render: function ( data, type, row, meta ) 
            {
                return "<i style='font-size:24px;' class='icon search link abrir-modal' data-title='DETALLE DE LA CUENTA' data-response='content' data-url='get.php' data-data='opcion=DetallePorCobrar&id="+data+"'  ></i>";
            }
          }   
                  
      ], 
      "ajax":
      {
        "type": "post", 
        "url": action,                    
        "data": obj,
        beforeSend: function (request) 
        {
            MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        },
        dataSrc: function (json) 
        {
          /* 
          globals = 
          {
             "total" : json.total 
          } 
          */
          OcultarLoading();    
          return json.data;
        }
      },
      footerCallback: function (row, data, start, end, display) 
      { 
        var api = this.api(); 
        //$(api.column(4).footer()).html('$' + globals.total  ); 
      }
    });
}
function PaginarReporteGastos( action , obj )
{ 
    
    dataTable = $('.table-reporte-gastos').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          //OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "fecha"},
           {"data": "concepto"},
           {"data": "trabajador"},
           {"data": "valor"} 
      ], 
      columnDefs: 
      [
          { 
            targets:[3],
            render: function ( data, type, row, meta ) 
            {
               return  "$ " + numeral(data).format("0,0");
            }
          } 
      ],
      "ajax":
      {
        "type": "post", 
        "url": action,                    
        "data": obj,
        beforeSend: function (request) 
        {
              //MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        },
        "dataSrc": function (json) 
        {
          console.log( json ) 
          globals = 
          {
             "total" : json.total
          } 
          return json.data;
        }
      },
      "footerCallback": function (row, data, start, end, display) 
      {
      
        var api = this.api(),
        intVal = function (i) 
        {
              return typeof i === 'string' ?
                   i.replace(/[, Rs]|(\.\d{2})/g,"")* 1 :
                   typeof i === 'number' ?
                   i : 0;
        },
        total7 = api.column(3).data().reduce(function (a, b) 
        {
               console.log("globals",globals);
                return intVal(a) + intVal(b);
        }, 0);
  
        $(api.column(3).footer()).html('$' + globals.total ); 
     }
    });
}
function PaginarMarcas( action , obj )
{ 

    dataTable = $('.table-marcas').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          //OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "id"},
           {"data": "imagen"}
      ],  
      columnDefs: 
      [
          { 
            targets:[0],
            render: function ( data, type, row, meta ) 
            { 
               return  "<i class='link icon trash' style='font-size:24px;' data-url='set.php' data-title='BORRAR MARCA' data-response='content'  data-data='opcion=EliminarMarca&id="+data+"' ></i>";                
            }
          }, 
          { 
            targets:[1],
            render: function ( data, type, row, meta ) 
            {
               let ruta =  "http://ferregomezjp.com/marcas/"+data;
               return  "<img src='"+ruta+"' style='width:48px;height:48px;' />";
            }
          } 
      ], 
      "ajax":
      {
        "type": "post", 
        "url": action,                    
        "data": obj,
        beforeSend: function (request) 
        {
              //MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        }
      } 
    });
}
function PaginarKardex( action , obj )
{ 
    obj.opcion = opcion;
    
    dataTable = $('.table-kardex').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          //OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "fecha"},
           {"data": "concepto"},
           {"data": "nombre"},
           {"data": "entrada"},
           {"data": "salida"} ,
           {"data": "total"} 
      ], 
      "ajax":
      {
        "type": "post", 
        "url": url,                    
        "data": obj,
        beforeSend: function (request) 
        {
              //MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        },
        dataSrc: function (json) 
        {

          globals = [];
          globals.total = 0;  
          var return_data = new Array();
          for(var i=0;i< json.data.length; i++)
          {
              if( Number(json.data[i].tipo) == 1 )
                globals.total += Number( json.data[i].entrada );
              else
                globals.total -= Number( json.data[i].salida );

              return_data.push
              ({
                'id': json.data[i].id,
                'entrada': json.data[i].entrada,
                'salida': json.data[i].salida,
                'concepto': json.data[i].concepto,
                'fecha': json.data[i].fecha,
                'tipo': json.data[i].tipo,
                'nombre' : json.data[i].nombre ,
                'total' : globals.total
              })
          }
          return return_data;
        }
      }
    });
}
function PaginarReporteEgresos( action , obj )
{ 
  
    dataTable = $('.table-reporte-egresos').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          //OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "fecha"},
           {"data": "concepto"},
           {"data": "trabajador"},
           {"data": "valor"} 
      ], 
      columnDefs: 
      [
          { 
            targets:[3],
            render: function ( data, type, row, meta ) 
            {
               return  "$ " + numeral(data).format("0,0");
            }
          } 
      ],
      "ajax":
      {
        "type": "post", 
        "url": action,                    
        "data": obj,
        beforeSend: function (request) 
        {
              //MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        }
      }
    });
}
function PaginarClientes( action , obj )
{  
    dataTable = $('.table-clientes').DataTable
    ({
      "language": 
      {
         "url": "../assets/Spanish.json"
      }, 

      "autoWidth": false , 
      initComplete:function( settings, json)
      {
          //OcultarLoading();
          //$(".tooltipstered").tooltipster({});  
                                
      },
      "processing": false ,
      "searching": false ,
      "pageLength": 10,
      "serverSide": true,
      "ordering": false, 
       "destroy": true,
      "columns": 
      [
           {"data": "id"},
           {"data": "id"},
           {"data": "nombres"},
           {"data": "apellidos"},
           {"data": "documento"},
           {"data": "telefono"},
           {"data": "direccion"} ,
           {"data": "correo"} 
      ],  
      columnDefs: 
      [  
          { 
            targets:[0],
            render: function ( data, type, row, meta ) 
            {
                 return  "<i class='link abrir-modal icon write'  style='font-size:24px;' data-url='get.php' data-title='EDITAR CLIENTE' data-data='opcion=EditarCliente&idcliente="+data+"' ></i>";

            }
          },
          { 
              targets:[1],
              render: function ( data, type, row, meta ) 
              {
                  if( row.status == true )
                  { 
                     return "<i class='trash icon link '  style='font-size:24px;' data-url='set.php' data-response='content' data-data='opcion=InactivarCliente&id="+data+"' ></i>";
                  }
                  else
                  {
                     return "<i class='check circle icon link'  style='font-size:24px;' data-url='set.php'  data-response='content' data-data='opcion=ActivarCliente&id="+data+"' ></i>";                     
                  } 
              }
            }           
      ], 
      "ajax":
      {
        "type": "post", 
        "url": action,                    
        "data": obj,
        beforeSend: function (request) 
        {
              //MostrarLoading();
        },
        error: function( error )
        {   
           console.log('error',error.responseText);
        }
      }
    });
}
function ActualizarFila( accion , values , row_selected )
{ 

    switch (accion)
     {
        case "por_pagar":
            row_selected.find("td:eq(4)").html( numeral(values.abono).format('0,0') );
            row_selected.find("td:eq(5)").html( numeral(values.debe).format('0,0') );
        break;

        case "por_cobrar":
            row_selected.find("td:eq(4)").html( numeral(values.abono).format('0,0') );
            row_selected.find("td:eq(5)").html( numeral(values.debe).format('0,0') );
        break;

        case "devolver_compra":
            row_selected.find("td:eq(4)").html( numeral(values.count).format('0,0') );
        break;

        case "devolver_venta":
            row_selected.find("td:eq(3)").html( numeral(values.count).format('0,0') );
        break;

        case "editar_proveedor":
            row_selected.find("td:eq(2)").html( values.nombre );
            row_selected.find("td:eq(3)").html( values.documento );
            row_selected.find("td:eq(4)").html( values.telefono );
            row_selected.find("td:eq(5)").html( values.direccion );
        break;

        case "editar_cliente":
            row_selected.find("td:eq(2)").html( values.nombres );
            row_selected.find("td:eq(3)").html( values.apellidos );
            row_selected.find("td:eq(4)").html( values.documento );
            row_selected.find("td:eq(5)").html( values.telefono );
            row_selected.find("td:eq(6)").html( values.direccion );
            row_selected.find("td:eq(7)").html( values.correo );
        break;

        case "editar_trabajador":
            row_selected.find("td:eq(2)").html( values.codigo );
            row_selected.find("td:eq(3)").html( values.nombre );
            row_selected.find("td:eq(4)").html( values.apellido );
            row_selected.find("td:eq(5)").html( values.cedula );
            row_selected.find("td:eq(6)").html( values.telefono );
        break;

        case "editar_producto":
            row_selected.find("td:eq(2)").html( values.nombre );
            row_selected.find("td:eq(3)").html( values.cantidad );
            row_selected.find("td:eq(4)").html( values.stock );
            row_selected.find("td:eq(5)").html( values.costo );
            row_selected.find("td:eq(6)").html( values.precio );
            row_selected.find("td:eq(7)").html( values.total_costo );
            row_selected.find("td:eq(8)").html( values.total_precio );
        break;
    }
}
function MostrarLoading()
{
	$(".supercapa , .cargando").show();
}
function OcultarLoading()
{
	$(".supercapa , .cargando").hide();
}
function ColumnChar( data )
{

   var chart = AmCharts.makeChart("chartdiv", {
      "theme": "light",
      "type": "serial",
      "startDuration": 2,
        "dataProvider": data,
        "valueAxes": [{
            "position": "left",
            "title": "Visitors"
        }],
        "graphs": [{
            "balloonText": "[[category]]: <b>[[value]]</b>",
            "fillColorsField": "color",
            "fillAlphas": 1,
            "lineAlpha": 0.1,
            "type": "column",
            "valueField": "total"
        }],
        "depth3D": 20,
      "angle": 30,
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "trabajador",
        "categoryAxis": {
            "gridPosition": "start",
            "labelRotation": 90
        },
        "export": {
          "enabled": true
         }

    });
}