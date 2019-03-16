var socket; 
var row_selected= null;
$(document).ready(function()
{ 
    
    /*
    socket = io.connect('http://localhost:3000');

    socket.on("MostrarMensaje" , function(data){ 
       $.notify( data.msg ,"success");

    });
    */
    


	var listProductos = new Array();   
   
    GetMenu(); 


    ViewNotifications();


    var PAGINATION_URL;
    var PAGINATION_DATA;
    var PAGINATION_PAGE;
    var PAGINATION_RESULT;
    var PDF_VARS = new Array();

    var LAST_MENU = null;
    var LAST_SUBMENU = null;

  
 
    function azar() 
    {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < 5; i++)
          text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    } 
    $("*").on("click","#change",function(evt)
    {
         e.stopPropagation();
         e.stopImmediatePropagation();
         e.preventDefault();
         if( $(".notification_box").is(":visible") )
           $(".notification_box").hide();
         if( $('.listPoint').is(":visible") )
              $('.listPoint').hide();
         else
         {
             $.ajax
             ({
                  type: 'POST',
                  url: 'get.php',
                  data: {'opcion' : 'GetPoints'},
                  dataType:'json',
                  beforeSend: function() 
                  { 
                      $(".cargando").show();
                  },
                  success: function(data)
                  {
                      $(".cargando").hide();
                      if( data != null )
                      {
                          console.log( data );
                          var sucursales = "";
                          var checked =  '';
                          for (var i = 0; i < data.results.length; i++)
                          {
                            checked = "";
                            if( data.idpoint == data.results[i].id )
                               checked = "checked='true'";

                             sucursales += "<div class='tr'>"+
                                              "<div class='td_title'><div class='title_sucursal'>"+data.results[i].punto+"</div></div>"+
                                              "<div class='td_item'>"+
                                                 "<label class='ios7-switch'>"+
                                                      "<input type='radio' "+checked+" name='same' data-point='"+data.results[i].punto+"' value='"+data.results[i].id+"' class='item_sucursal' >"+
                                                      "<span></span>"+
                                                 "</label>"+
                                              "</div>"+
                                            "</div>";
                          }
                          $(".listBody").html( sucursales );
                          $(".listPoint").show();
                          $(".listBody").mCustomScrollbar(); 
                      }
                      else
                      {
                      }
                  }
             });
         }
       
         evt.preventDefault();
         evt.stopPropagation();
         evt.stopImmediatePropagation();         
    });
    $( "*" ).on( "click", ".item_sucursal", function( event ) 
    {   
          var id = $(this).val();
          var point = $(this).data("point");  


          $.ajax
          ({                
                type: 'POST',
                url: 'set.php',
                data: {'opcion' : 'ChangePoint' , 'id' : id , 'point' : point },
                dataType : 'json',
                success: function(data) 
                {                     
                    $("#name").html(data.usermane + " - " + data.point );
                    setTimeout(function()
                    { 
                        $(".listPoint").hide();
                        $(".listBody").html(""); 
                        $("#content").html("");
                        GetMenu();
                    }, 1000);                                              
                }
          }); 
           
          event.stopPropagation();
          event.stopImmediatePropagation();
    }); 
    $("*").delegate("#exit","click",function(evt)
    {
       evt.stopPropagation();
       evt.stopImmediatePropagation();
       evt.preventDefault();
       $.ajax
       ({                
            type: 'POST',
            url: 'get.php',
            data: 'opcion=cerrar',
            success: function(data) 
            {                  
               $('body').addClass('logout');
               setTimeout(logout,600);                                              
            }
       }); 
       
       evt.preventDefault();
       evt.stopPropagation();
       evt.stopImmediatePropagation();         
    });

    $("*").delegate(".firmar","click",function(evt)
    {
       evt.stopPropagation();
       evt.stopImmediatePropagation();
       evt.preventDefault();
 
        
       $("#firmado").val(1);
       $("#respuesta-firma").removeAttr("src");
       $("#respuesta-firma").attr("src" , "firmas_temp/firma.png?"+azar());

       
       evt.preventDefault();
       evt.stopPropagation();
       evt.stopImmediatePropagation();         
    });


    $("*").on("click","#back",function(evt)
    {  
   
       $('.notification_box').hide();
       $('#absolute').hide();
       $("#result_absolute" ).html( "" );
       
       if( LAST_SUBMENU != null )
       {
           LAST_SUBMENU = null;
           GetContentMenu( LAST_MENU );   
       } 
       else if( LAST_MENU != null )
       {
           $("#back").animate({ "width" : "0px" , "opacity" : 0});
           LAST_MENU = null;
           GetMenu();
       } 
       evt.preventDefault();
       evt.stopPropagation();
       evt.stopImmediatePropagation();         
    });
    $("#content").on("click","#menu",function(e)
    { 
        e.stopPropagation();
        e.stopImmediatePropagation();
        e.preventDefault(); 
        $(".listPoint").hide();
        $('#content').removeClass('animated zoomIn');
        listProductos = new Array(); 

        $("#back").removeClass("BackSubMenu").addClass("BackMenu"); 

        
        LAST_MENU = $(this);
        GetContentMenu( $(this) );   
    });
    $("#content").on("click",".submenu",function(e)
    {  
        e.stopPropagation();
        e.stopImmediatePropagation();
        e.preventDefault();
        LAST_SUBMENU = $(this);
        $("#back").removeClass("BackMenu").addClass("BackSubMenu");   
        listProductos = new Array();  
        GetContentSubMenu( $(this) );  
    });
    $("#content").on("click",".verMas",function(e)
    {  
        e.stopPropagation();
        e.stopImmediatePropagation();
        e.preventDefault();
        if( $('.hide').is(":visible") )
        {
          $(this).html("Mostrar");
          $('.hide').slideUp('400','easeOutQuad');
        }
        else
        {
          $(this).html("Mostrar");
          $('.hide').slideDown('400','easeInQuad'); 
        } 
    });
    $("*").on("keyup","#factura",function(e)
    { 
        e.stopPropagation();
        e.stopImmediatePropagation();
        e.preventDefault();

        if( $(this).val().trim().length > 0 ) 
           ValidarFactura( $(this)  );  
    });

    $( "*" ).on( "click", ".cantidades button", function( event ) 
    {   
          $(".cantidades button").removeClass('yellow');
          $(this).addClass('yellow');  
          
          let operacion = $(this).data("operacion");
          if( operacion == 3 )
          {
             $("#cantidadEnviar").val( $("#cantidadEnviar").data("cantidad"));
             $("#cantidadEscribir").val("");
          }
          else
          {
             $("#operacion").val( operacion );            
          }
          calcularCantidadEditar();


          event.preventDefault(); 
          event.stopPropagation();
          event.stopImmediatePropagation();
    }); 

    $( "*" ).on( "keyup", "#cantidadEscribir", function( event ) 
    {     
          calcularCantidadEditar();  

          event.preventDefault(); 
          event.stopPropagation();
          event.stopImmediatePropagation();
    }); 

    $("*").on("keyup","#nombreProducto",function(e)
    { 
        var codigo = "";
        var texto = $(this).val().trim();
        if( texto.length >= 3 )
        {
           var parts = texto.split(" ");
           if( parts.length == 1 )
           {
              codigo = parts[0][0];
              if( parts[0].length > 0 )
                codigo += parts[0][ parts[0].length - 1 ];
           }
           else           
           {
              for (var i = 0; i < parts.length; i++) 
              {
                 codigo += parts[i][0];
              }
           }
           $("#campoCodigo").val( codigo.toUpperCase() );
        }
        e.stopPropagation();
        e.stopImmediatePropagation();
        e.preventDefault(); 
    });

    $("#header").on("click","#home",function(e)
    {          
        e.stopPropagation();
        e.stopImmediatePropagation();
        e.preventDefault();
        $("#back").animate({ "width" : "0px" , "opacity" : 0});
        $(".listPoint").hide();
        $(".notification_box").hide();
        $('#absolute').hide();
        $("#result_absolute" ).html( "" );
        $('#content').removeClass('animated zoomIn');
        GetMenu();  
    });

    $("#search").on("focus","input:text",function(e)
    { 
        $("#search").animate({ "width" : "250px"});
    });

    $("#search").on("blur","input:text",function(e)
    { 
        $("#search").animate({ "width" : "200px"});
    });

    $("*").on("click",".detalle",function(e)
    {
          $this = $(this); 
          $.ajax
          ({
                type: 'POST',
                url:  $this.data("page"),
                data: $this.data("data"), 
                success: function(data) 
                { 
                    $(".modal-body" ).html( data ); 
                    $(".modal-title" ).html( "Detalle de la compra" ); 
                    $("#myLargeModalLabel" ).html( $this.data("titulo") ); 
                    $('#myModal').modal('show');

                },
                error: function(xhr)
                { 
                    
                }
          });
    });

    $("*").on("click",".abonar",function(e)
    {
          $this = $(this); 
          $.ajax
          ({
                type: 'POST',
                url:  $this.data("url"),
                data: $this.data("data"), 
                success: function(data) 
                { 
                    $(".modal-body" ).html( data ); 
                    $(".modal-title" ).html( "Abonar" ); 
                    $("#" + $this.data("response") ).html( $this.data("titulo") ); 
                    $('#myModal').modal('show');

                },
                error: function(xhr)
                { 
                    
                }
          });
    });

    $("#content").on("click",".sacar_dinero",function(e)
    {
          $this = $(this); 
          $.ajax
          ({
                type: 'POST',
                url:  'get.php',
                data: { 'opcion' : 'SacarDinero' }, 
                success: function(data) 
                { 
                    $(".modal-body" ).html( data ); 
                    $(".modal-title" ).html( "Sacar dinero de la caja" );  
                    $('#myModal').modal('show');

                },
                error: function(xhr)
                { 
                    
                }
          });
    });

    $("#content").on("click",".ingresar_dinero",function(e)
    {
          $this = $(this); 
          $.ajax
          ({
                type: 'POST',
                url:  'get.php',
                data: { 'opcion' : 'IngresarDinero' }, 
                success: function(data) 
                { 
                    $(".modal-body" ).html( data ); 
                    $(".modal-title" ).html( "Ingresar dinero a la caja" );  
                    $('#myModal').modal('show');

                },
                error: function(xhr)
                { 
                    
                }
          });
    });
    $("*").on("click",".ketchup-error-container",function(e)
    {
         $(this).remove();
         e.stopPropagation();
         e.preventDefault();
    });

    $("#content").on("keyup","#buscarProductoCompra",function(e)
    { 
         e.stopPropagation();
         e.stopImmediatePropagation();
         e.preventDefault();

         $(".search-producto").show();
         $this = $(this);  
         
         if( $this.val().length > 0 )
         {
               $.ajax
               ({
                      type: 'POST',
                      url:  'get.php',
                      data: { 'opcion' : 'BuscarProductoCompra' , 'producto' : $this.val() , 'listProductos' : listProductos , 'idcliente' : $("#idcliente").val() , 'tipocliente' : $("#tipocliente").val() }, 
                      success: function(data) 
                      {  
                          data = data.trim(); 
                          $(".search-producto").hide(); 
                          /*

                          $('#buscarProductoCompra').popup
                          ({
                            title   : 'Buscar producto' ,
                            on : 'clic' ,
                            position : 'right center' ,
                            html : data ,
                            closable : false
                          }).popup('show'); 
                          */
                          var top  = $this.offset().top - 10;
                          var left = $this.offset().left + $this.width() + 20;

                          $('#absolute').html(data);
                          $('#absolute').css({ top : top , left : left }).show(); 
                          $("#result_absolute").mCustomScrollbar();
                           
                      },
                      error: function(xhr)
                      { 
                          
                      }
               });
         } 
         else
         {
              $('#buscarProductoCompra').popup("hide");
              $('#absolute').hide();
              $("#result_absolute" ).html( "" );
         }
    });

    $("#content").on("keyup","#buscarProductoVenta",function(e)
    { 
         e.stopPropagation();
         e.stopImmediatePropagation();
         e.preventDefault();

         $(".search-producto").show();
         $this = $(this);  
         if( $this.val().length > 0 )
         {
             $.ajax
             ({
                    type: 'POST',
                    url:  'get.php',
                    data: { 'opcion' : 'BuscarProductoVenta' , 'producto' : $this.val() , 'listProductos' : listProductos , 'idcliente' : $("#idcliente").val() , 'tipocliente' : $("#tipocliente").val() }, 
                    success: function(data) 
                    {  
                        data = data.trim(); 
                        $(".search-producto").hide(); 
                        /*
                        $('#buscarProductoVenta').popup
                        ({
                          title   : 'Buscar producto' ,
                          on : 'clic' ,
                          position : 'right center' ,
                          html : data ,
                          closable : false
                        }).popup('show'); 
                        */
                        var top  = $this.offset().top - 10;
                        var left = $this.offset().left + $this.width() + 20;

                        $('#absolute').html(data);
                        $('#absolute').css({ top : top , left : left }).show(); 
                        $("#result_absolute").mCustomScrollbar();
                         
                    },
                    error: function(xhr)
                    { 
                        
                    }
             });
         } 
         else
         {
              $('#buscarProductoVenta').popup("hide");
              $('#absolute').hide();
              $("#result_absolute" ).html( "" );
         }
    }); 

    $("#content").on("keyup","#buscarProveedorCompra",function(e)
    { 
         e.stopPropagation();
         e.stopImmediatePropagation();
         e.preventDefault();

         $(".search-proveedor").show();
 
         $this = $(this);   
            
         $.ajax
         ({
                type: 'POST',
                url:  'get.php',
                data: { 'opcion' : 'BuscarProveedor' , 'escrito' : $this.val() }, 
                success: function(data) 
                {  
                    data = data.trim();  
                    $(".search-proveedor").hide();
                    /*

                    $('#buscarProveedorCompra').popup
                    ({
                      title   : 'Buscar proveedor' ,
                      on : 'clic' ,
                      position : 'right center' ,
                      html : data ,
                      closable : false
                    }).popup('show'); 
                    */
                    var top  = $this.offset().top - 10;
                    var left = $this.offset().left + $this.width() + 20;

                    $('#absolute').html(data);
                    $('#absolute').css({ top : top , left : left }).show(); 
                    $("#result_absolute").mCustomScrollbar();

                  
                },
                error: function(xhr)
                { 
                    $('#absolute').hide();
                    $("#result_absolute" ).html( "" );  
                }
         });                  
    });

   
    $("*").on("click",".seleccionar_proveedor",function(e)
    { 
         idproveedor = $(this).data("idproveedor");
         proveedor = $(this).data("proveedor");

         $("#buscarProveedorCompra").val(proveedor);
         $("#hiddenProveedor").val(idproveedor);
         $("#absolute").html("").hide();
          

         e.stopPropagation();
         e.stopImmediatePropagation();
         e.preventDefault();
    });

    $("#content").on("keyup","#buscarProductoCantidades",function(e)
    { 
         e.stopPropagation();
         e.stopImmediatePropagation();
         e.preventDefault();

         $(".cargando").show();
         $this = $(this);  

         console.log( $this.val() );
           
         $.ajax
         ({
                type: 'POST',
                url:  'get.php',
                data: { 'opcion' : "buscarProductoCantidad" , 'producto' : $this.val() }, 
                success: function(data) 
                {  
                    data = data.trim(); 
                    $(".cargando").hide();

                    if( data.length > 0 )
                    {
                        var top  = $this.offset().top - 10;
                        var left = $this.offset().left + $this.width() + 20;

                        $('#absolute').html(data);
                        $('#absolute').css({ top : top , left : left }).show(); 
                        $("#result_absolute").mCustomScrollbar(); 

                    }
                    else
                    {
                        $('#absolute').hide();
                        $("#result_absolute" ).html( "" );
                    }
                },
                error: function(xhr)
                { 
                    
                }
         }); 
    });


    $("#content").on("keyup","#buscarProductoInventario",function(e)
    { 
         $(".cargando").show();
         $this = $(this);    
         console.log( $this.val() );
         if( $this.val().length > 0 )
         { 
             $.ajax
             ({
                    type: 'POST',
                    url:  'get.php',
                    data: { 'opcion' : 'InventarioLike' , 'producto' : $this.val() , 'page' : 1  }, 
                    dataType: 'json',
                    success: function(data) 
                    {  
                         $("#content_pagination").html( data.form );
                         $(".cargando").hide();
                         console.log( data );
                         PAGINATION_DATA = data.var;
                         PAGINATION_RESULT = "content_pagination";

                    },
                    error: function(xhr)
                    { 
                        $(".cargando").hide();
                    }
             });
         }
         else
          $(".cargando").hide();  
    });

    $("#content").on("keyup","#buscarProductoCotizacion",function(e)
    {
         $this = $(this);   
         if( $this.val().length > 0 )
         {
               $.ajax
               ({
                      type: 'POST',
                      url:  'get.php',
                      data: { 'opcion' : 'buscarProductoCotizacion' , 'producto' : $this.val() , 'listProductos' : listProductos  }, 
                      success: function(data) 
                      {  
                          data = data.trim(); 


                      	  if( data.length > 0 )
                      	  {
                      	  	  var top  = $this.offset().top - 10;
                              var left = $this.offset().left + $this.width() + 20;

                              $('#absolute').html(data);
                              $('#absolute').css({ top : top , left : left }).show(); 
                              $("#result_absolute").mCustomScrollbar(); 

                      	  }
                          else
                          {
                              $('#absolute').hide();
                              $("#result_absolute" ).html( "" );
                          }
                      },
                      error: function(xhr)
                      { 
                          
                      }
               });
          } 
          else
          {
              $('#absolute').hide();
              $("#result_absolute" ).html( "" );
          }
    });  

    $("#content").on("keyup","#buscarClienteVenta",function(e)
    {
         e.stopPropagation();
         e.stopImmediatePropagation();
         e.preventDefault();
         $("#idcliente").val("");
         $("#tipocliente").val(""); 
         $(".search-cliente").show();

         $("#buscarClienteVenta").removeClass("con_cliente"); 

         $this = $(this);   
         if( $this.val().length > 0 )
         {
               $.ajax
               ({
                      type: 'POST',
                      url:  'get.php',
                      data: { 'opcion' : 'buscarClienteVenta' , 'escrito' : $this.val()  }, 
                      success: function(data) 
                      {  
                          $(".search-cliente").hide();
                      
                          var top  = $this.offset().top - 10;
                          var left = $this.offset().left + $this.width() + 20;
                          /*
                          $('#absolute').html(data.data);
                          $('#absolute').css({ top : top , left : left }).show(); 
                          $("#result_absolute").mCustomScrollbar();
                          */


                          //$(".contenido").html( data );
                          $('#buscarClienteVenta').popup({
                            title   : 'Popup Title' ,
                            on : 'clic' ,
                            position : 'right center' ,
                            html : data ,
                            closable : false
                          }).popup('show'); 

                          /*

                           $('.flowing.popup').popup({
                              transition: "Fade"
                            }).popup('toggle');
                          
                           */

                           //$('.flowing.popup').popup('show');

                          
                      },
                      error: function(xhr)
                      { 
                          
                      }
               });
         }  
    }); 

    $("#content").on("keyup","#buscarClienteCotizacion",function(e)
    {
         $("#idclienteCotizacion").val(""); 
         $("#buscarClienteCotizacion").removeClass("con_cliente").addClass("sin_cliente");

         $this = $(this);   
         if( $this.val().length > 0 )
         {
               $.ajax
               ({
                      type: 'POST',
                      url:  'get.php',
                      dataType : 'json',
                      data: { 'opcion' : 'buscarClienteCotizacion' , 'escrito' : $this.val()  }, 
                      success: function(data) 
                      {  

                          if( data.status > 0 )
                          {
                              var top  = $this.offset().top - 10;
                              var left = $this.offset().left + $this.width() + 20;

                              $('#absolute').html(data.data);
                              $('#absolute').css({ top : top , left : left }).show(); 
                              $("#result_absolute").mCustomScrollbar(); 

                          }
                          else
                          { 
                              $('#absolute').hide();
                              $("#result_absolute" ).html( "" );
                          }
                      },
                      error: function(xhr)
                      { 
                          
                      }
               });
          } 
          else
          {
              $('#absolute').hide();
              $("#result_absolute" ).html( "" );
          }
    });   

    $("#content").on("keyup","#buscarProveedorDevolucion",function(e)
    {
         $("#idproveedor").val(""); 
         $("#buscarProveedorDevolucion").removeClass("con_cliente").addClass("sin_cliente");

         $this = $(this);   
         if( $this.val().length > 0 )
         {
               $.ajax
               ({
                      type: 'POST',
                      url:  'get.php',
                      dataType : 'json',
                      data: { 'opcion' : 'buscarProveedorDevolucion' , 'escrito' : $this.val()  }, 
                      success: function(data) 
                      {  

                          if( data.status > 0 )
                          {
                              var top  = $this.offset().top - 10;
                              var left = $this.offset().left + $this.width() + 20;

                              $('#absolute').html(data.data);
                              $('#absolute').css({ top : top , left : left }).show(); 
                              $("#result_absolute").mCustomScrollbar(); 

                          }
                          else
                          { 
                              $('#absolute').hide();
                              $("#result_absolute" ).html( "" );
                          }
                      },
                      error: function(xhr)
                      { 
                          
                      }
               });
          } 
          else
          {
              $('#absolute').hide();
              $("#result_absolute" ).html( "" );
          }
    }); 

    $("*").on("click","#bell",function(event)
    {
         event.stopPropagation();
         event.stopImmediatePropagation();
         event.preventDefault();

         if( $(".listPoint").is(":visible") )
           $(".listPoint").hide();

         $this = $(this);   
         if( $this.hasClass("bell_animation") )
         {
               if( $(".notification_box").is(":visible") )
                   $(".notification_box").hide(); 
               else
               {
                 var top  = $this.offset().top + 80;
                 var left = ($this.offset().left - $('.notification_box').width() ) + 55;
                 $(".notification_box").css({ top : top , left : left }).show(); 
                
                 $(".supercapa").show(); 
                 $.ajax
                 ({
                        type: 'POST',
                        url:  'get.php',
                        data: { 'opcion' : 'GetAllNotification' }, 
                        beforeSend: function() 
                        { 
                            $(".cargando").show();
                        },
                        success: function(data) 
                        {  
                            $(".cargando").hide();
                            $(".notification_body").html( data );
                            TweenMax.staggerFrom(".notification_info", 2, { y : -50 , opacity:0, delay:0.1, ease:Back.easeOut, force3D:true}, 0.1);
                        },
                        error: function(xhr)
                        { 
                            
                        }
                 }); 
               }
          } 
          else
          {

              $('.notification_box').hide();
          }
    });  

    $( "*").on( "click", ".notification_close", function( event ) 
    {
        $(".notification_box").hide();
        $(".supercapa").hide();
        
         event.stopPropagation();
         event.stopImmediatePropagation();
         event.preventDefault();
    });

    $( "*").on( "click", ".btn-add-cliente-venta", function( event ) 
    {    
        $("#idcliente").val( $(this).data("id") );
        $("#tipocliente").val( $(this).data("tipocliente") );

        $('#buscarClienteVenta').popup('hide'); 


        $("#buscarClienteVenta").val( $(this).data("cliente") ); 
        $("#buscarClienteVenta").removeClass("sin_cliente").addClass("con_cliente");
    }); 

    $( "#absolute").on( "click", ".btn-add-proveedor-devolucion", function( event ) 
    {    
        $("#idproveedor").val( $(this).data("id") ); 

        $('#absolute').hide();
        $("#result_absolute" ).html( "" ); 

        $("#buscarProveedorDevolucion").val( $(this).data("proveedor") ); 
        $("#buscarProveedorDevolucion").removeClass("sin_cliente").addClass("con_cliente");
    }); 

    $( "#absolute").on( "click", ".btn-add-producto-cotizacion", function( event ) 
    {   
        $('#absolute').hide();
        $("#result_absolute" ).html( "" );

        $this = $(this);
        idproducto = $this.data("idproducto");
        nombre = $this.data("nombre");
        cantidad = $this.data("cantidad");
        precio = $this.data("precio");
        precio2 = $this.data("precio2");
        precio3 = $this.data("precio3");
        costoliquidado = $this.data("costoliquidado");
        costonormal = $this.data("costonormal");

        precio = precio.replace(",", ""); 


         
 
        $('#tabla tbody').append('<tr class="row_dinamic">'+ 
                                   '<input type="hidden" name="idproducto[]" value='+idproducto+' />'+ 
                                   '<td>'+nombre+'</td>'+
                                   '<td><input type="text" name="precio[]" value='+precio+' class="form-control cantidad validar numero requerido" data-min="1" /></td>'+
                                   '<td>'+cantidad+'</td>'+
                                   '<td><input type="text" name="cantidad[]" class="form-control cantidad validar numero requerido" data-min="1" /></td>'+
                                   '<td><div class="delete-photo eliminarProducto" data-idproducto='+idproducto+'></div></td>'+
                                 '</tr>'); 
         $this.parents("tr").remove();
         listProductos.push( idproducto );
         $(".btn-save").removeAttr("disabled"); 
         $(".der").mCustomScrollbar(); 
    }); 

    $( "#absolute").on( "click", ".btn-add-producto-cantidad", function( event ) 
    {   
        //$('#absolute').hide();
       //$("#result_absolute" ).html( "" );

        $this = $(this);
        idproducto = $this.data("idproducto");
        nombre = $this.data("nombre"); 
         
 
        $('#recibe tbody').append('<tr>'+ 
                                   '<input type="hidden" name="idproducto[]" value='+idproducto+' />'+ 
                                   '<td style="text-align:left;">'+nombre+'</td>'+
                                   '<td><i class="icon trash eliminarProducto" style="font-size:24px;" data-idproducto='+idproducto+'></i></td>'+
                                 '</tr>'); 
         $this.parents("tr").remove();   
    });  
    
    $( "*").on( "click", ".plus", function( event ) 
    {   
         event.stopPropagation();
         event.stopImmediatePropagation();
         event.preventDefault(); 
 
         $categoria = $('#categoria:first').parent().html();

         console.log("categoria" ,  $categoria ); 

         $categoria = '<select id="categoria" name="categoria[]" class="form-control validar requerido select chosen">' + 
                         '<option value="">Seleccione</option>'+
                         '<option value="1" data-folder="electricos">electricos</option>'+
                         '<option value="2" data-folder="ferreteria">ferreteria</option>'+
                         '<option value="3" data-folder="herramienta">herramienta</option>'+
                         '<option value="4" data-folder="linea griferias-pvc">linea griferías-pvc</option>'+
                         '<option value="5" data-folder="seguridad">seguridad</option>'+
                         '<option value="6" data-folder="varios">varios</option>'+
                      '</select>';

 
         $("<div class='three fields' id='referencias'>"+
                                  "<div class='field'>"+
                                       "<label>Imagen</label>"+
                                       "<input type='file' class='form-control validar requerido texto'id='nombreProducto'  name='imagen[]' >"+
                                  "</div>"+
                                  "<div class='field'>"+
                                       "<label>Nombre del producto</label>"+
                                       "<input type='text' name='nombres[]' /> "+
                                  "</div>"+
                                  "<div class='field'>"+
                                      "<label>Categoria</label>"+
                                       $categoria+
                                  "</div>"+
                                "</div>").insertBefore("#referencias");  
          $("select.chosen").chosen();

    }); 

    $( "*").on( "click", ".plus-marcas", function( event ) 
    {   
         event.stopPropagation();
         event.stopImmediatePropagation();
         event.preventDefault(); 

         $("<div class='two fields' id='referencias'> " +

                                  "<div class='field'>"+
                                      "<label>Imagen</label>"+
                                      "<input type='file' class='form-control validar requerido texto' name='imagen[]' >"+
                                  "</div>"+

                                  "<div class='field'>"+
                                     
                                  "</div>"+
                        "</div>").insertBefore("#referencias");
    }); 

    $( "*").on( "click", ".btn-add-producto-venta", function( event ) 
    {   
        $('#absolute').hide();
        $("#result_absolute" ).html( "" );

        $("#buscarProductoVenta").popup("hide");

        $this = $(this);
        idproducto = $this.data("idproducto");
        nombre = $this.data("nombre");
        cantidad = $this.data("cantidad");
        precio = $this.data("precio");
        costoliquidado = $this.data("costoliquidado");
        costonormal = $this.data("costonormal");
        descuento = $this.data("descuento");

       
 
        $('#tabla tbody').append('<tr class="row_dinamic">'+ 
                                   '<input type="hidden" name="idproducto[]" value='+idproducto+' />'+
                                   '<input type="hidden" name="costonormal[]" value='+costonormal+' />'+
                                   '<input type="hidden" name="costoliquidado[]" value='+costoliquidado+' />'+
                                   '<td>'+nombre+'</td>'+
                                   '<td>$ '+precio+'</td>'+
                                   '<td>$ '+descuento+'</td>'+
                                   '<td>'+cantidad+'</td>'+
                                   '<td><input type="text" style="width:100px;" name="cantidad[]" class="form-control cantidad validar numero requerido" data-min="1" data-max="'+cantidad+'" /></td>'+
                                   '<td><input type="text" style="width:100px;" name="precio[]" class="form-control precio validar decimal requerido data-min="1" value="'+precio+'"/></td>'+
                                   '<td><i class="trash outline icon eliminarProducto" data-idproducto='+idproducto+' style="font-size:32px;"></i></td>'+
                                 '</tr>'); 
         $this.parents("tr").remove();
         listProductos.push( idproducto );
         $(".btn-save").removeAttr("disabled");
         //$("form").validationEngine({ scroll: false });
         $(".der").mCustomScrollbar(); 
    }); 

    $( "*" ).on( "click", ".btn-add-producto-compra", function( event ) 
    {   
        $('#absolute').hide();
        $("#result_absolute" ).html( "" );

        $("#buscarProductoCompra").popup("hide");

        $doble = $("#doble").val();        

        $this = $(this);
        idproducto = $this.data("idproducto");
        nombre = $this.data("nombre"); 
        costonormal = $this.data("costonormal");
        precio = $this.data("precio");
        preciominimo = $this.data("preciominimo");     


        $('#tabla tbody').append('<tr class="row_dinamic row_dinamic_compra">'+ 
                                   '<td class="indice"></td>'+
                                   '<input type="hidden" name="idproducto[]" value='+idproducto+' />'+
                                   '<td>'+nombre+'</td>'+
                                   '<td><input type="text" style="width:100px;" name="cantidad[]" class="form-control cantidad validar requerido numero" data-min="1"  /></td>'+
                                   '<td>'+
                                       '<input type="text" style="width:100px;" name="costo[]"  value='+costonormal+' class="form-control costo required validar requerido decimal" data-min="1" />'+ 
                                   '</td>'+ 
                                   '<td>'+
                                       '<input type="text" style="width:100px;" name="precio[]" value='+precio+' class="form-control precio validar requerido decimal"  data-min="1" />'+
                                   '</td>'+
                                   '<td>'+
                                       '<input type="text" style="width:100px;" name="preciominimo[]" value='+preciominimo+' class="form-control  validar requerido decimal"  data-min="1" />'+
                                   '</td>'+
                                   '<td>'+
                                       '<div class="delete-photo eliminarProducto" data-idproducto='+idproducto+'></div>'+
                                   '</td>'+
                                 '</tr>'); 
         $this.parents("tr").remove();
         listProductos.push( idproducto );
         $(".btn-save").removeAttr("disabled"); 
         $("#result_absolute").mCustomScrollbar('update'); 

         var ind = 1;
         $(".row_dinamic_compra").each(function()
         {
              $(this).find(".indice").html(ind);
              ind++;
         });
    }); 

    $( "*" ).on( "click", ".eliminarProducto", function( event ) 
    {
        idproducto = $(this).data("idproducto"); 
        $(this).parents('tr').remove();

        if ( $('#tabla >tbody >tr').length == 0 )
        {
            $(".btn-save").attr("disabled" , 'disabled' ); 
        }

        for( var i = 0 ; i < listProductos.length ; i++ )
        {
            if (listProductos[i] == idproducto )
            {
               listProductos.splice(i, 1);
            }
        }  

        var ind = 1;
         $(".row_dinamic_compra").each(function()
         {
              $(this).find(".indice").html(ind);
              ind++;
         });
        CalculateTotalCost();
        CalculateTotalPrice();
    });


    

    $( "*" ).on( "focus", ".cantidad,.costo,.precio,#BuscarProducto", function( event ) 
    {
       $(this).select(); 
    });
    $( "*" ).on( "click", "#close_absolute", function( event ) 
    {    
         $('#buscarProductoVenta').popup("hide");
         $('#buscarProductoCompra').popup("hide");
         $('#buscarClienteVenta').popup("hide");
         $('#buscarProveedorCompra').popup("hide");

         

         $("#absolute").hide();
         $("#result_absolute").html("");

    }); 

    $( "*" ).on( "click", ".btn-enviar", function( event ) 
    {

          $(this).parents("form").submit();



          event.stopPropagation();
          event.stopImmediatePropagation();
          event.preventDefault();
    });
 
    $( "*" ).on( "submit", "form", function( event ) 
    {  
          event.stopPropagation();
          event.stopImmediatePropagation();
          event.preventDefault();  


          if( ValidarFormulario( $(this) , 'all' ) == true )
          { 
             
              if( $(this).data("type") == 'json' )
              {
                  var formData = new FormData( $(this) [0] );
                  $this = $(this);  

                  $.ajax
                  ({
                      type: 'POST',
                      url: $this.attr('action'),
                      processData: false,
                      contentType: false,
                      data: formData,
                      dataType : 'json',
                      beforeSend: function() 
                      { 
                          $(".supercapa , .cargando").show();
                      },
                      success: function(data)
                      { 


                          if( data.var )
                              PDF_VARS = data.var;

                          if( $(".date").is(":visible") && $(".k-datepicker").is(":hidden") )
                          {
                                $(".date").kendoDatePicker({
                                  format: "yyyy-MM-dd" 
                                })
                          } 

                          if( typeof( data.status ) !== "undefined" )
                          { 
                            $(".modal").remove(); 
                             if( data.status == false )
                             {
                                 $("body").append("<div class='ui modal'>"+
                                        "<i class='close icon'></i>"+
                                        "<div class='header header-modal'>"+ 
                                        "</div>"+
                                        "<div class='content scrolling' id='content-modal'></div> "+
                                     "</div>");

                                $(".ui.modal").find('.content').html( data.msg );

                                $(".header-modal").html( "..." );
 
                                $('.ui.modal').modal
                                ({
                                    blurring: true  , 
                                    allowMultiple : false ,
                                    closable : false  
                                }).modal('show');
                             }
                          } 
                          else if( typeof data.row !== 'undefined' && typeof data.values !== 'undefined' )
                          { 

                               ActualizarFila( data.row , data.values , row_selected );
                               $("#" + $this.data('response') ).html(data.msg); 
                          }
                          else if( $this.data("get") === "char" )
                          {
                              ColumnChar( data );
           
                              
                          }                         
                          else 
                            $("#" + $this.data('response') ).html(data.msg);


                          $('.ui.checkbox').checkbox();
                           
                          $(".supercapa , .cargando").hide();
                      }
                  }); 
              } 
              else if( $(this).data("paginate") == false )
              {
                  var formData = new FormData( $(this) [0] );
                  $this = $(this); 
                 
                  $.ajax
                  ({
                      type: 'POST',
                      url: $this.attr('action'),
                      processData: false,
                      contentType: false,
                      data: formData,
                      beforeSend: function() 
                      { 
                          $(".supercapa , .cargando").show();
                      },
                      success: function(data)
                      { 

                            $("#" + $this.data('response') ).html(data);
                            $(".supercapa , .cargando").hide();

                            if( $(".date").is(":visible")  )
                            {
                                  $(".date").kendoDatePicker({
                                    format: "yyyy-MM-dd" 
                                  })
                            } 
                            $('.ui.checkbox').checkbox(); 
                      }
                  }); 
              } 
              else
              {   
                  $this = $(this);  
                  var obj = $.formObject($this); 
                  obj.opcion = $this.data("opcion");  

                  console.log("opcion" , obj );
                
                  managePagination( $this.attr("action") , obj );
              }
          }
    });

    $( "*" ).on( "keyup", ".costo", function( event ) 
    {  
          event.stopPropagation();
          event.stopImmediatePropagation();
          event.preventDefault(); 


          $this = $(this);  
          value = $this.val().replace(",", "."); 
          value = value.replace("$", ""); 
          value = value.trim(); 
           
          if( Number(value) > 0)
          {
              $this.val(  value  );
          }
          else
          {
              $this.val("");
          }
          CalculateTotalCost();
    });

    $("*").on("keyup","#BuscarProducto",function(event)
    {
        event.stopPropagation();
        event.stopImmediatePropagation();
        event.preventDefault();

        $this = $(this);

        $.ajax
        ({
                type: 'POST',
                url:  'get.php',
                data: { 'opcion' : "Inventario" } ,
                beforeSend: function() 
                { 
                    //$(".supercapa , .cargando").show();
                },
                success: function(data) 
                {  
                    $("#content").html( data ); 
                    //$(".supercapa , .cargando").hide();
                
                    obj = 
                    {
                        'opcion' : "Inventario" ,
                        'producto' : $this.val()
                    };
                    managePagination(  "get.php" , obj ); 
                }
        });  
  
    });
 
    $( "*" ).on( "keyup", ".precio", function( event ) 
    {  
          event.stopPropagation();
          event.stopImmediatePropagation();
          event.preventDefault();

          $this = $(this);  
          value = $this.val().replace(",", "."); 
          //value = value.replace("$", "");  
           
          if( Number( value ) > 0)
          { 
              $this.val( value  );
          }
          else
          { 
              $this.val("");
          }
          CalculateTotalPrice();
    });

    $( "*" ).on( "keyup", ".cantidad", function( event ) 
    {  

          event.stopPropagation();
          event.stopImmediatePropagation();
          event.preventDefault(); 
          $this = $(this);

          $this.val( $this.val().trim() );
 
          if( $this.val() >= 0 )
          { 
            if( $this.val() >= 0 )
            {
               CalculateTotalCost();
               CalculateTotalPrice();
            }
          }
          else
          {
              $this.val(0);
              CalculateTotalCost();
              CalculateTotalPrice();
          } 
    });
   
    $( "*" ).on( "change", "#categoria", function( event ) 
    { 
        $(this).parents(".form-body").find("#folder_categoria").val( $(this).find(':selected').data('folder') );
        event.stopPropagation();
        event.stopImmediatePropagation();
        event.preventDefault(); 
    });

    $( "*" ).on( "change", "#tCompra", function( event ) 
    {
        event.stopPropagation();
        event.stopImmediatePropagation();
        event.preventDefault(); 


        switch( Number( $(this).val()) ) 
        {
            case 1:
               $(".fllegada").attr('disabled' , true );
               $(".fpago").attr('disabled' , true );
            break;

            case 2: 
               $(".fllegada").removeAttr('disabled');
               $(".fpago").removeAttr('disabled');
            break;
            default:
               $(".fllegada").attr('disabled' , true );
               $(".fpago").attr('disabled' , true );
            break;
        } 
    }); 
    var hideshow = false;
   
    $( "*" ).on( "click", "#hide-show", function( event ) 
    {
        event.stopPropagation();
        event.stopImmediatePropagation();
        event.preventDefault();

        $.ajax
        ({
            type: 'POST',
            url: 'set.php', 
            data: { 'opcion':'change_oculta' },  
            beforeSend: function() 
            { 
            },
            success: function(data)
            {
               GetMenu();
            }
        }); 
    }); 

    $( "*" ).on( "click", ".venta-oculta", function( event ) 
    {
        event.stopPropagation();
        event.stopImmediatePropagation(); 
        id = $(this).data("id");

        $.ajax
        ({
            type: 'POST',
            url: 'set.php', 
            data: { 'opcion':'ocultar_venta' , 'id' : id },  
            beforeSend: function() 
            { 
                
            },
            success: function(data)
            {
            }
        }); 
    }); 
    $( "*" ).on( "change", "#idpuntoInventario", function( event ) 
    {  
          $this = $(this); 
          if( $this.val() > 0 )
          {
          
              PAGINATION_RESULT = 'results_pagination';
              PAGINATION_URL = "set.php";
              PAGINATION_PAGE = 1; 
              PAGINATION_DATA = 
              { 
                "opcion" : "Inventario" ,
                'page' : PAGINATION_PAGE
              }; 
              Paginate();
          }
          else
            $("#" + PAGINATION_RESULT).html("");

          event.stopPropagation();
          event.stopImmediatePropagation();
          event.preventDefault(); 
    }); 
    $("*").on("click",".venta-check",function(e)
    {
        $contador = Number( $(".contador").html().trim() );
        $valor = Number($(this).val());

        let id = $(this).data("id");

        console.log("contador " , $contador );
        console.log("valor " , $valor );

        let status;

        if( $(this).is(":checked") )
        {
            $(".contador").html( "" + Number($contador + $valor) ); 
            status = 1; 
        } 
        else
        { 
            $(".contador").html( "" + Number($contador - $valor) );  
            status = 0; 

        }


        $.ajax
        ({
            type: 'POST',
            url: 'set.php',
            data: {'opcion' : 'resourceSalesOnly' , 'status' : status , 'id' : id }, 
            beforeSend: function() 
            { 
            },
            success: function(data)
            {
                console.log( data );
            }
        }); 


        event.stopPropagation();
        event.stopImmediatePropagation();
    }); 
    $("*").on("click",".chAccess",function(e)
    {

      var index = $(this).data('index'); 
      $(".chAccess"+index).prop("checked", $(this).is(":checked") );

      var idusuario = $("#idtrabajador_permiso").val();

      var status = false;

      if( $(this).is(":checked") )
      {
          status = true;
      }

      $.ajax
      ({
          type: 'POST',
          url: 'set.php', 
          data: $(this).parents(".content_access").find("form").serialize() + '&status=' + status + '&usuario_id=' + idusuario + '&menu_id=' + $(this).val(), 
          dataType: 'json',
          beforeSend: function() 
          { 
             $(".supercapa , .cargando").show();
          },
          success: function(data)
          {
          }
      }); 


      e.stopPropagation();
      e.stopImmediatePropagation();
    });
    $("*").on("click",".chAccessChildren",function(e)
    {
 
      var idusuario = $("#idtrabajador_permiso").val();
      var status = false;

      if( $(this).is(":checked") )
      {
          status = true;
      }

      $.ajax
      ({
          type: 'POST',
          url: 'set.php', 
          data: { 'opcion' : 'UpdateSubMenuAccess' , 'submenu_id' : $(this).val() , 'usuario_id' : idusuario , 'status' : status },
          dataType: 'json', 
          success: function(data)
          {
              console.log( data );
          }
      }); 


      e.stopPropagation();
      e.stopImmediatePropagation();
    });
    $( "*" ).on( "change", "#idtrabajador_permiso", function( event ) 
    {  
          $this = $(this); 
          if( $this.val() > 0 )
          {
              $.ajax
              ({
                  type: 'POST',
                  url: 'get.php', 
                  data: { 'opcion' : 'GetMenuAcces' , 'usuario_id' : $this.val() },
                  beforeSend: function() 
                  { 
                      $(".supercapa , .cargando").show();
                  },
                  success: function(data)
                  {
                      $(".result_access").html(data);  
                      $(".supercapa , .cargando").hide(); 
                  }
              });
          }
          else
          {
               $(".result_access").html("");
          }
          event.stopPropagation();
          event.stopImmediatePropagation();
          event.preventDefault(); 
    }); 
   
    $("*").on("click",".page-item","click",function(evt)
    { 

         if ($(this).attr('data-page')) 
         { 
            PAGINATION_DATA.page = Number( $(this).attr('data-page') );
            console.log( PAGINATION_DATA );
            Paginate();

         } 
         evt.preventDefault();
         evt.stopPropagation();
         evt.stopImmediatePropagation();
    });
    $('*').on('click','#go_btn',function(e)
    {
          var page = parseInt($('.goto').val());
          var no_of_pages = parseInt($('.totalPagination').attr('a'));
          if(page != 0 && page <= no_of_pages)
          {
              PAGINATION_DATA.page = Number( page );
              Paginate();
          }
          else
          {
            alert('Ingrese una pagina entre 1 y '+no_of_pages);
            $('.goto').val("").focus();
          }

          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation(); 
    }); 
    $( "*" ).on( "click", ".link", function( event ) 
    {   
         event.stopPropagation();
         event.stopImmediatePropagation();
         event.preventDefault();

         $(".supercapa").hide(); 
         $(".notification_box").fadeOut(500);
         row_selected = $(this).parents("tr");

         TweenMax.staggerTo(".notification_info", 2, { y : -50 , opacity:0, delay:0.1, ease:Back.easeOut, force3D:true}, 0.1);
         
         
         var borrar = false;
         $this = $(this);   
         
         if( $(this).hasClass("confirm") )
         {
             var borrar = confirm("Esta seguro de ejecutar esta operación!"); 
             if( borrar == true)
             {     
                 $.ajax
                 ({
                        type: 'post',
                        url: $this.data('url'),
                        data: $this.data('data'), 
                        success: function(data) 
                        { 

                            $("#" + $this.data("response") ).html( data ); 
                            $this.parents("tr").remove();

                            if( $(".date").is(":visible")  )
                            {
                                $('.date').datepicker
                                ({
                                    format: 'yyyy-mm-dd' ,
                                    autoclose: true
                                });
                            }
                        },
                        error: function(xhr){}
                  });
             }
         }
         else if( $(this).hasClass("abrir-modal") )
         { 
             var title = $(this).data("title"); 
             
             $.ajax
             ({
                    type: 'post',
                    url: $this.data('url'),
                    data: $this.data('data'),
                    success: function(data) 
                    { 
                        $(".modal").remove();

                        agregrarModal();
                        
                        $(".ui.modal").find('.content').html( data ); 

                        $(".header-modal").html( $this.data("title") );

                        blurring = true;
                        if( $this.data('blur') == false )
                          blurring = false;
                        $('.ui.modal').modal
                        ({
                            blurring: blurring  , 
                            allowMultiple : false ,
                            closable : false  
                        }).modal('show');
 
                    },
                    error: function(xhr){}
              });              
         } 
         else
         {
             console.log( $this.data('url') );
             console.log( $this.data('data') );
             console.log( $this.data('response') );
             $.ajax
             ({
                  type: 'post',
                  url: $this.data('url'),
                  data: $this.data('data'), 
                  success: function(data) 
                  { 

                      $("#" + $this.data("response") ).html( data );  
                      if(  $this.data('paginate') == true )
                      {  
                          obj = 
                          {
                              'opcion' : $this.data("opcion") 
                          };
                          managePagination(  "get.php" , obj );

                          $(".date").kendoDatePicker({
                                  format: "yyyy-MM-dd" 
                                })
                      }
                      else
                      {
                        $("td[colspan=4]").find("p").hide();
                        if( $(".date").is(":visible")  )
                        {
                            $('.date').datepicker
                            ({
                                format: 'yyyy-mm-dd' ,
                                autoclose: true
                            });
                        }
                      }
                  },
                  error: function(xhr){}
             });
         } 
    });
    $("*").on("click","#header_visor_close",function(e)
    {
         $("#visor").hide();
         $(".supercapa").hide();

         e.preventDefault();
         e.stopPropagation();
         e.stopImmediatePropagation();
    });

    $( "*" ).on( "click", ".eliminarProductoCompra", function( e ) 
    {

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        idproducto = $(this).data("idproducto");
        cantidad = $(this).data("cantidad");
        idcompra = $(this).data("idcompra");

        $this = $(this);

        $.ajax
        ({
            type: 'POST',
            url: 'set.php', 
            data: { 'opcion':'eliminarProductoCompra' , 'idcompra' : idcompra , 'cantidad' : cantidad , 'idproducto' :  idproducto }, 
            dataType:'json', 
            beforeSend: function() 
            { 
            },
            success: function(data)
            {
                if( data.status == false )
                {
                    $(".modal").remove(); 
                    
                    $("body").append("<div class='ui modal'>"+
                        "<i class='close icon'></i>"+
                        "<div class='header header-modal'>"+ 
                        "</div>"+
                        "<div class='content scrolling' id='content-modal'></div> "+
                        "</div>");

                    let msg = `<div class='alert alert-danger'>
                                    <div class='msg_danger'></div>
                                    <div cass=''>No se puede eliminar el producto de la compra<br>Cantidad en inventario <b class='colorRed'>${data.cantidad} </b></div>
                                </div>`;

                    $(".ui.modal").find('.content').html( msg );

                    $(".header-modal").html( "..." );
 
                    $('.ui.modal').modal
                    ({
                        blurring: true  , 
                        allowMultiple : false ,
                        closable : false  
                    }).modal('show');
                }
                else
                {
                    $this.parents('tr').remove();

                    if ( $('#tabla >tbody >tr').length == 0 )
                    {
                        $(".btn-save").attr("disabled" , 'disabled' ); 
                    }
        
                    for( var i = 0 ; i < listProductos.length ; i++ )
                    {
                        if (listProductos[i] == idproducto )
                        {
                        listProductos.splice(i, 1);
                        }
                    }
        
                    var ind = 1;
                    $(".row_dinamic_compra").each(function()
                    {
                        $(this).find(".indice").html(ind);
                        ind++;
                    });
                    CalculateTotalCost();
                    CalculateTotalPrice();
                }
            }
        }); 
        
    });

    $("*").on("click",".editar_compra",function(e)
    {
         if( confirm("Esta seguro de editar esta compra?") )
         {
            let id = $(this).data("id")
            $.ajax
            ({
                type: 'post',
                url:  "get.php",
                data: {"opcion" : "editarCompra" , "id" : id } ,
                success: function(data)
                {
                  $("#content").html(data)    
                  $(".date").kendoDatePicker({
                    format: "yyyy-MM-dd" 
                  })
                  $("select.chosen").chosen();
                  $('select.dropdown').dropdown();
                  CalculateTotalCost();
                  CalculateTotalPrice();                            
                },
                complete: function(objeto, exito){},error: function(msg)
                {
                    console.log(msg.status+"  "+msg.statusText);
                }
            });
         }

         e.preventDefault();
         e.stopPropagation();
         e.stopImmediatePropagation();
    });
    $("*").on("click","#content_print",function(e)
    {        
        $this = $(this); 
        
        $.ajax
        ({
            type: 'post',
            url:  $this.data("url") ,
            data: $this.data("data") ,
            dataType:'json',
            success: function(fileName)
            {
                 
                $(".supercapa").fadeIn(); 
                $("#header_visor_title").html( $this.data("title") );
                $("#body_visor").html("<iframe src='reports/"+fileName.pdf_uri+"' id='iframe' style='width:100%; height:100%; padding:0 ; margin:0p;' frameborder='0'></iframe>");
 
                $("#visor").fadeIn(0); 
                $(".supercapa").css("z-index" , 9999); 
                $("#visor").css('z-index', 999999999);

                setTimeout(function()
                { 
 
                    $.ajax
                    ({
                        type: 'POST',
                        url: 'set.php', 
                        data: { 'opcion' : 'DeletePDF' , 'pdf' : fileName.pdf_uri },
                        dataType: 'json', 
                        success: function(data)
                        {
                            console.log( data );
                        }
                    });  
                }, 1000);                                                              
            },
            complete: function(objeto, exito){},error: function(msg)
                  {
                      console.log(msg.status+"  "+msg.statusText);
                  }
        });
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
    }); 

    $("*").on("click","#content_print_termica",function(e)
    {        
        $this = $(this); 
        
        $.ajax
        ({
            type: 'post',
            url:  $this.data("url") ,
            data: $this.data("data") , 
            success: function(fileName)
            {
                 
                console.log('Impreso...');                                                         
            },
            complete: function(objeto, exito){},error: function(msg)
            {
                console.log(msg.status+"  "+msg.statusText);
            }
        });
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
    }); 


    $("*").on("click","#content_excel",function(e)
    {        
        $this = $(this); 

        $(".supercapa , .cargando").show();
        $.ajax
        ({
            type: 'post',
            url:  "reports/excel.php" ,
            data: {},
            success: function(data)
            { 
               window.open("reports/"+data);      
               $(".supercapa , .cargando").hide();                                                
            } 
        });
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
    }); 

    $("*").on("click","tr.ExpandirVenta",function(e)
    {
        event.stopPropagation(); 
        $this = $(this); 
        var $target = $(event.target); 

        if( $target.closest("tr").next().find("p").is(':hidden') )
        {

             $.ajax
            ({
                type: 'POST',
                url: 'get.php', 
                data: { 'opcion' : 'ObtenerDetalleDevolverVenta' ,'venta_id' : $this.data('ventaid') },
                beforeSend: function() 
                { 
                    $(".cargando").show();
                },
                success: function(data)
                {
                    $(".cargando").hide();  

                    $target.closest("tr").next().find("p").html( data );


                    if ( $target.closest("td").attr("colspan") > 1 ) 
                    {
                        $target.slideUp();
                    } 
                    else 
                    {
                        $target.closest("tr").next().find("p").slideToggle();
                    }             
                }
            });  
        }
        else
        {
           $target.closest("tr").next().find("p").slideToggle();
        } 
    });

    $("*").on("click","tr.ExpandirProductoCotizacion",function(e)
    { 
        event.stopPropagation(); 
        $this = $(this); 
        var $target = $(event.target); 

        if( $target.closest("tr").next().find("p").is(':hidden') )
        {

             $.ajax
            ({
                type: 'POST',
                url: 'get.php', 
                data: { 'opcion' : 'ObtenerProductosCotizacion' ,'cotizacion_id' : $this.data('cotizacionid') },
                beforeSend: function() 
                { 
                    $(".cargando").show();
                },
                success: function(data)
                {
                    $(".cargando").hide();  

                    $target.closest("tr").next().find("p").html( data );
                    if( $(".date").is(":visible")  )
                    {
                        $('.date').datepicker
                        ({
                            format: 'yyyy-mm-dd' ,
                            autoclose: true
                        });
                    }


                    if ( $target.closest("td").attr("colspan") > 1 ) 
                    {
                        $target.slideUp();
                    } 
                    else 
                    {
                        $target.closest("tr").next().find("p").slideToggle();
                    }             
                }
            });  
        }
        else
        {
           $target.closest("tr").next().find("p").slideToggle();
        } 
    });
 
  
    function CalculateTotalPrice()
    {
        var suma = 0;
        $(".row_dinamic").each(function()
        {  
            cantidad =  $(this).find(".cantidad").val()  ;
            precio =     $(this).find(".precio").val() ; 
            suma +=   cantidad * precio ; 
          
        }); 
        $(".header_price").html( numeral(suma).format('0,0') );
    } 
    function CalculateTotalCost()
    {
        var suma = 0;
        $(".row_dinamic").each(function()
        { 
            if(typeof( $(this).find(".costo").val() ) != "undefined" )
            {
              costo =  $(this).find(".costo").val();
              if( Number(costo) > 0 )
              {
                cantidad =  $(this).find(".cantidad").val()  ;
                suma +=   cantidad * costo ; 
              }
            }
        });  
        $(".header_cost").html(  numeral(suma).format('0,0') );
    } 
    function formatNumber(num,prefix)
    {
       prefix = prefix || '';
       num += '';
       var splitStr = num.split('.');
       var splitLeft = splitStr[0];
       var splitRight = splitStr.length > 1 ? '.' + splitStr[1] : '';
       var regx = /(\d+)(\d{3})/;
       while (regx.test(splitLeft)) 
       {
          splitLeft = splitLeft.replace(regx, '$1' + ',' + '$2');
       }
       return prefix + splitLeft + splitRight;
    }
    function unformatNumber(num) 
    {
        return num.replace(/([^0-9\.\-])/g,'')*1;
    }
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
    function GetMenu()
    { 
        $.ajax
        ({
                type: 'POST',
                url:  'get.php',
                data: { 'opcion' : 'GetMenu' } ,
                dataType: 'json', 
                success: function(data) 
                {   
                    if( data.length)
                    {
                      menu = "<div id='contentMenu'>";
                      for (var i = 0; i < data.length ; i++) 
                      {
                        menu +='<div id="menu" data-id='+data[i].id+' data-url='+data[i].url+' data-paginate='+data[i].paginate+' data-children='+data[i].children+' >'+
                                  '<div id="menu_icon">'+
                                    '<img src="../assets/imagenes/icon-menu/' + data[i].icon + '">'+
                                  '</div>'+
                                  '<div id="menu_text">'+data[i].texto+'</div>'+
                              '</div>';
                      }
                      
                      menu += "</div>";
                      $('#content').html( menu );
                      $('#content').addClass('animated zoomIn');
                    }                    
                },
                error: function(xhr)
                { 
                    
                }
        });     
    }
    function GetSubMenu( menu_id )
    {
        $.ajax
        ({
              type: 'POST',
              url:  'get.php',
              data: { 'opcion' : 'GetSubMenu' , 'menu_id' : menu_id } ,
              dataType : 'json',
              beforeSend: function() 
              { 
                  
              },
              success: function(data) 
              {    
                  if( data.length )
                  {
                      var submenu = "<div id='content_submenu'>";
                      for (var i = 0; i < data.length; i++) 
                      {
                          submenu += "<div class='submenu' data-id="+data[i].id+" data-url="+data[i].url+" data-paginate="+data[i].paginate+" data-children="+data[i].children+">"+
                                        "<div class='submenu_icon'>"+
                                            '<img src="../assets/imagenes/icon-submenu/' + data[i].icon + '">'+
                                        "</div>" +
                                        "<div class='submenu_text'>"+data[i].texto+"</div>" +
                                      "</div>";

                      }
                      submenu += "</div>";
                      $("#content").removeAttr("class");
                      $("#content").html(submenu);

                      TweenMax.staggerFrom(".submenu", 1, { x : -50 , opacity:0, delay:0.1, ease:Back.easeOut, force3D:true}, 0.1);
                  }
              },
              error: function(xhr)
              { 
                  
              }
        });  
    }
    function GetContentMenu( menu )
    {  
        
        $("#back").animate({ "width" : "130px" , "opacity" : 1 });
        if( menu.data("children") == true )
        {
            GetSubMenu( menu.data("id") ); 
        }
        else
        {
          $.ajax
          ({
                  type: 'POST',
                  url:  'get.php',
                  data: { 'opcion' : menu.data("url") } ,
                  beforeSend: function() 
                  { 
                      $(".supercapa , .cargando").show();
                  },
                  success: function(data) 
                  {  
                      $("#content").html( data ); 
                      $(".supercapa , .cargando").hide(); 

                      if( menu.data('paginate') == true )
                      {
                          obj = 
                          {
                              'opcion' : menu.data('url')
                          };
                          managePagination(  "get.php" , obj );
                      }
                      else
                      {
                          $('#content').addClass('animated zoomIn');

                          $('select.dropdown').dropdown();

                          $("select.chosen").chosen();

                          if( $(".start").is(":visible") &&  $(".end").is(":visible")  )
                          {
                              dateRange(); 
                          }


                          if( $(".date").is(":visible")  )
                          {
                                $(".date").kendoDatePicker({
                                  format: "yyyy-MM-dd" 
                                })
                          } 
                      }
                  },
                  error: function(xhr)
                  { 
                      
                  }
          });  
        }
    }
    function GetContentSubMenu( menu )
    {    
        $.ajax
        ({
              type: 'POST',
              url:  'get.php',
              data: { 'opcion' : menu.data("url") } ,
              beforeSend: function() 
              { 
                  $(".supercapa , .cargando").show();
              },
              success: function(data) 
              {  
                  $("#content").html( data ); 
                  $('#content').addClass('animated zoomIn');
                  $(".supercapa , .cargando").hide(); 

                  if( menu.data('paginate') == true )
                  {
                      obj = 
                      {
                          'opcion' : menu.data('url')
                      };
                      managePagination(  "get.php" , obj );
                  }
                  else
                  {
                      if( $(".date").is(":visible")  )
                      {
                          if( $(".date").is(":visible")  )
                          {
                                $(".date").kendoDatePicker
                                ({
                                  format: "yyyy-MM-dd" 
                                })
                          } 
                      }
                      if( $(".start").is(":visible") &&  $(".end").is(":visible")  )
                      {
                          dateRange(); 
                      }
                      $('select.dropdown').dropdown();

                      $("select.chosen").chosen();
                  }
              },
              error: function(xhr)
              { 
                  
              }
        });  
    } 
    function dateRange()
    {
        function startChange() 
        {
          var startDate = start.value(),
          endDate = end.value();

          if (startDate) 
          {
              startDate = new Date(startDate);
              startDate.setDate(startDate.getDate());
              end.min(startDate);
          } 
          else if (endDate) 
          {
              start.max(new Date(endDate));
          } 
          else 
          {
              endDate = new Date();
              start.max(endDate);
              end.min(endDate);
          }
        }

        function endChange() 
        {
              var endDate = end.value(),
              startDate = start.value();

              if (endDate) 
              {
                  endDate = new Date(endDate);
                  endDate.setDate(endDate.getDate());
                  start.max(endDate);
              }
              else if (startDate) 
              {
                  end.min(new Date(startDate));
              } 
              else 
              {
                  endDate = new Date();
                  start.max(endDate);
                  end.min(endDate);
              }
        }

        var start = $(".start").kendoDatePicker
        ({
              change: startChange ,
              format: "yyyy-MM-dd" 
        }).data("kendoDatePicker");

        var end = $(".end").kendoDatePicker
        ({
              change: endChange ,
              format: "yyyy-MM-dd" 
        }).data("kendoDatePicker");

          start.max(end.value());
          end.min(start.value());
    }
    function ValidarFactura( field )
    {
        $.ajax
        ({
              type: 'POST',
              url:  'get.php',
              data: { 'opcion' : 'ValidarFactura' , 'factura' : field.val() } ,
              dataType:'json',
              beforeSend: function() 
              { 
                  var top  = $("#factura").offset().top + 3;
                  var left = $("#factura").offset().left + $("#factura").width() - $(".loading_input").width(); 
                  $(".loading_input").css({ 'top' : top + "px", 'left' : left + "px" }).show(); 

              },
              success: function(data) 
              {  
                  $(".loading_input").hide(); 
                  if( Number(data.cant) > 0 )
                  {
                     var position = field.parent().position();
                     MostrarMensajeValidacion( "El numero de factura ya existe" , position.left , position.top , field.parent() , field );
                     $(".btn-save").attr("disabled" , 'disabled' ); 

                  }
                  else
                  {
                     $(".btn-save").removeAttr("disabled"); 

                  }
              },
              error: function(xhr)
              { 
                  
              }
        });  
    }
    function logout()
    {
       window.location = "../";
    } 

    setInterval(function()
    { 
       ViewNotifications(); 
    }, 100000 ); 

    function ViewNotifications()
    { 
        $.ajax
        ({
              type: 'POST',
              url:  'get.php',
              data: { 'opcion' : 'ViewNotifications' } , 
              success: function(data) 
              {    
                  if( Number(data.length) > 0 )
                  { 
                     $("#bell" ).addClass('bell_animation');
                  }
                  else
                  { 
                     $("#bell" ).removeClass('bell_animation');
                  }

              },
              error: function(xhr)
              { 
                  
              }
        });
    } 
});