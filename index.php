<!DOCTYPE html>
<html lang="en">
    <head>
		<meta charset="UTF-8" /> 
        <script src="assets/js/jquery-3.1.1.min.js" type="text/javascript"></script> 
        <link rel="stylesheet" type="text/css" href="plugins/semantic/semantic.min.css"> 
        <script src="plugins/semantic/semantic.min.js"></script>

        <link rel="stylesheet" href="assets/estilo/validador-form.css" /> 
        <link rel="stylesheet" type="text/css" href="assets/estilo/index.css?<?php echo microtime(true); ?>" />
        <script type="text/javascript" src="assets/js/validador-form.js?<?php echo microtime(true); ?>"></script> 
        <link rel="stylesheet" href="assets/estilo/loading-big.css" type="text/css"  />  
        <link rel="stylesheet" href="assets/estilo/ios-switch.css" type="text/css"  /> 


        <link rel="stylesheet" href="plugins/malihu-custom-scrollbar/jquery.mCustomScrollbar.css" /> 
        <script src="plugins/malihu-custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>        
        <script type="text/javascript" src="assets/js/inicio.js?<?php echo microtime(true); ?>"></script>

        
    </head>
    <body> 
        <div class="supercapa"></div>
        <div class="cargando">
	        <div class="cssload-container-big">
	            <div class="cssload-speeding-wheel-big"></div>
	        </div>
	        Cargando...
	    </div>
    
        <div class="container"> 
            <div class="container-header">
                <div class="logo"></div>
            </div>
            <div class="container-body">
                <form  action="login.php" autocomplete="off">
                    <input type="text" name="usuario"  placeholder="Usuario">
                    <input type="password" name="clave"  placeholder="Clave">  
                    <input type="submit" name="submit" class='boton' value="INGRESAR">
                    <input type="hidden" name="opcion" value="login">     
                </form>
            </div>
            <div class="container-footer"></div> 
        </div>
        <div class="ui modal mini">
          <div class="header">Error</div>
          <div class="content">
            <p>Datos inconrrectos</p>
          </div>
          <div class="actions"> 
            <div class="ui cancel button">Aceptar</div>
          </div>
        </div>
    </body>
</html>

 
         
    