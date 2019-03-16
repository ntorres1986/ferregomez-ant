function ValidarFormulario( $this , tipo )
{
    $(".ketchup-error-container").remove();
    error = 0;
    if( tipo == 'visible' )
    {
      $this.find('input:visible:enabled, textarea:visible:enabled, select:visible:enabled').each(function()
      {
            error +=  ValidarCampos( $(this) );
      });
    }
    else
    {
      $this.find('input:enabled, textarea:enabled, select:enabled').each(function()
      {
            error +=  ValidarCampos( $(this) );
      });
    }

    if( error > 0 )
       return false;
    else
       return true;
} 
function ValidarCampos( $this )
{
          error = 0;
          msg = ""; 



          if ( $this.hasClass('validar') )
          { 
            $name = $this.attr("name").replace("[","");
            $name = $name.replace("]",""); 

            $val = $this.val().replace("$" , "");
            $val = $val.replace("," , "");


            if( $this.hasClass('texto') )
            {
                  if( $val != "" )
                  {
                      if ( typeof $this.data('minlength') !== 'undefined' && typeof $this.data('maxlength') !== 'undefined'  )
                      {
                            if( $val.length < $this.data('minlength') || $val.length > $this.data('maxlength') )
                            {
                                if(  typeof $this.data('msg') === 'undefined' )
                                   msg += "<li>* La cantidad de caracteres debe estar entre " + $this.data('minlength') + " y " + $this.data('maxlength') + "</li>";
                                else
                                   msg += $this.data('msg');
                            }

                      }
                      else if ( typeof $this.data('minlength') !== 'undefined'  )
                      {
                            if( $val.length < $this.data('minlength'))
                            {
                                if(  typeof $this.data('msg') === 'undefined' )
                                   msg += "<li>* La cantidad de caracteres debe ser mayor a " + $this.data('minlength') + "</li>";
                                else
                                   msg += $this.data('msg');
                            }
                      }
                      else if ( typeof $this.data('maxlength') !== 'undefined' )
                      {
                            if( $val.length > $this.data('maxlength'))
                            {
                                if(  typeof $this.data('msg') === 'undefined' )
                                   msg += "<li>* La cantidad de caracteres debe ser menor a " + $this.data('maxlength') + "</li>";
                                else
                                   msg += $this.data('msg');
                            }
                      }
                  }
                  else if( $this.hasClass('requerido') )
                  {
                       msg += "<li>*  Este campo es requerido</li>";
                  }
            }
            else if( $this.hasClass('password') )
            { 
            }
            else if( $this.hasClass('numero') )
            {
                if( $val != "" )
                {
                    var expresion = /^[0-9]+$/;
                    if( !expresion.test( $val) )
                    {
                        msg += "<li>* El numero no es valido</li>";
                    }
                    if ( typeof $this.data('min') !== 'undefined'  && typeof $this.data('max') !== 'undefined' )
                    {
                          if( $val < $this.data('min') || $val > $this.data('max') )
                          {
                              if(  typeof $this.data('msg') === 'undefined' )
                                 msg += "<li>* El valor ingresado debe estar entre " + $this.data('min') + " y " + $this.data('max') + "</li>";
                              else
                                 msg += $this.data('msg');
                          }
                    }
                    else if ( typeof $this.data('min') !== 'undefined' )
                    {
                          if( $val < $this.data('min'))
                          {
                              if(  typeof $this.data('msg') === 'undefined' )
                                 msg += "<li>* El valor ingresado debe ser mayor a " + $this.data('min') + "</li>";
                              else
                                 msg += $this.data('msg');
                          }
                    }
                    else if ( typeof $this.data('max') !== 'undefined' )
                    {
                         if( $val > $this.data('max'))
                         {
                              if(  typeof $this.data('msg') === 'undefined' )
                                 msg += "<li>* El valor ingresado debe ser menor a " + $this.data('max') + "</li>";
                              else
                                 msg += $this.data('msg');
                         }
                    }
                }
                else if( $this.hasClass('requerido') )
                {
                     msg += "<li>*  Este campo es requerido</li>";
                } 
            }
            else if( $this.hasClass('decimal') )
            {
                if( $val != "" )
                {
                    var expresion = /^[+-]?\d+(\.\d+)?$/;
                    if( !expresion.test( $val) )
                    {
                        msg += "<li>* El numero no es valido</li>";
                    }
                    if ( typeof $this.data('min') !== 'undefined'  && typeof $this.data('max') !== 'undefined' )
                    {
                          if( $val < $this.data('min') || $val > $this.data('max') )
                          {
                              msg += "<li>* El valor ingresado debe estar entre " + $this.data('min') + " y " + $this.data('max') + "</li>";
                          }
                    }
                    else if ( typeof $this.data('min') !== 'undefined' )
                    {
                            if( $val < $this.data('min'))
                            {
                                msg += "<li>* El valor ingresado debe ser mayor a " + $this.data('min') + "</li>";
                            }
                    }
                    else if ( typeof $this.data('max') !== 'undefined' )
                    {
                        if( $val > $this.data('max'))
                            {
                                msg += "<li>* El valor ingresado debe ser menor a " + $this.data('max') + "</li>";
                            }
                    }
                }
                else if( $this.hasClass('requerido') )
                {
                     msg += "<li>*  Este campo es requerido</li>";
                }
            }
            else if( $this.hasClass('correo') )
            {
                  if( $val != "" )
                  {
                      var expresion = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
                      if( !expresion.test( $val) )
                      {
                          msg += "<li>* No es una correo valido</li>";
                      }
                  }
                  else if( $this.hasClass('requerido') )
                  {
                       msg += "<li>*  Este campo es requerido</li>";
                  }
            }
            else if( $this.hasClass('hora') )
            {
                  if( $val != "" )
                  {
                      var expresion =/^([0-9]{2})\:([0-9]{2})$/;
                      if( !expresion.test( $val) )
                      {
                          msg += "<li>* La hora no es valida</li>";
                      }
                  }
                  else if( $this.hasClass('requerido') )
                  {
                       msg += "<li>*  Este campo es requerido</li>";
                  }
            }
            else if( $this.hasClass('fecha') )
            {
                if( $val != "" )
                {
                    var expresion =/^\d{4}-\d{1,2}-\d{1,2}$/;
                    if( !expresion.test( $val) )
                    {
                        msg += "<li>* La fecha no es valida</li>";
                    }
                }
                else if( $this.hasClass('requerido') )
                {
                     msg += "<li>*  Este campo es requerido</li>";
                }
            }
            else if ( $this.hasClass('checkbox') )
            {

                   if( $('input[name="'+$this.attr("name")+'"]').is(':checked') )
                   {

                   }
                   else
                   {
                       msg += "<li>* Se debe seleccionar por lo menos una opcion</li>";
                   }

            }
            else if ( $this.hasClass('radio') )
            {
                 if( $('input[name="'+$this.attr("name")+'"]').is(':checked') )
                 {

                 }
                 else
                 {
                     msg += "<li>* Se debe seleccionar por lo menos una opcion</li>";
                 }
            }
            else if ( $this.hasClass('select') )
            {
                 if(  $val == "" || $val == null )
                 {
                     msg += "<li>* Se debe seleccionar una opcion</li>";
                 }
            } 

            $this.parent().siblings('.ketchup-error-container.' + $name ).remove(); 

            if( msg != "" )
            {
                 error = 1;

                 var position = $this.parent().position();
                 MostrarMensajeValidacion( msg , position.left , position.top , $this.parent() , $this );
                 return error;
            }

          }
          return error;
}
function MostrarMensajeValidacion( msg  , left , top , $padre , $this )
{
        $name = $this.attr("name").replace("[","");
        $name = $name.replace("]","");


        $div = $('<div class="ketchup-error-container '+$name+'" style="left: '+left+'px; display: block;"><ol>'+msg+'</ol><span></span></div>');
        $padre.append($div);
 
        altura = top - ($div.height() / 2);
        $div.css({ top : altura });
}