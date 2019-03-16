$(document).ready(function()
{ 
    var SELECTED_ID_POINT = null;
    $( "*" ).on( "submit", "form", function( event ) 
    {  
          event.stopPropagation();
          event.stopImmediatePropagation();
          event.preventDefault();

          if( ValidarFormulario( $(this) , 'all' ) == true )
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
                  dataType:'json',
                  beforeSend: function() 
                  { 
                      $(".supercapa , .loading").show();
                  },
                  success: function(data)
                  {
                      $(".supercapa , .loading").hide();
                      if( data.status)
                      {
                          $('body').addClass('logout');
                          setTimeout(redireccionar('app'),30000);  
                      }
                      else
                      {
                         $(".content").find("p").html( data.msg );
                         $('.ui.modal').modal('show');
                           
                      }
                  }
              }); 
          }
    });
     
    $( "*" ).on( "click", ".title_sucursal", function( event ) 
    {  
          
          SELECTED_ID_POINT = $(this).data("value"); 

          $.ajax
          ({
              type: 'POST',
              url: 'modelo.php',
              data: { 'opcion' : 'create_session' , 'idpunto' : SELECTED_ID_POINT },
              dataType:'json',
              beforeSend: function() 
              { 
                  $(".supercapa , .loading").show();
              },
              success: function(data)
              { 
                 if( data.status == 'success' )
                 {
                     $('body').addClass('logout');
                     setTimeout(redireccionar('app'),30000);   
                 }
              }
          }); 
           
          event.stopPropagation();
          event.stopImmediatePropagation();
    }); 

    
    function redireccionar(data)
    {
       window.location = data;
    }
}); 