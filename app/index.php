<?php
   session_start();
   if( empty($_SESSION['idusuario'] ) )
   {
      header("Location: ../");
   }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">  
</head>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<script >   

  function  loadScript( path , type ) 
  {
     var time = new Date().getTime();  
     switch( type )
     {
         case "stylesheet": 
            var element = document.createElement('link');
            element.setAttribute('rel', 'stylesheet');
            element.setAttribute('type', 'text/css');
            element.setAttribute('href', path + "?" + time );
            document.getElementsByTagName('head')[0].appendChild(element); 
         break;
         case "javascript":
            var element = document.createElement('script');
            element.setAttribute('type', 'text/javascript');
            element.setAttribute('src', path + "?" + time );
            document.getElementsByTagName('head')[0].appendChild(element); 
         break;
     }
  }
 
</script>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />







    <meta name="author" content="nTorres" />
    <title>FerreGomezJP</title>   
    <link rel="stylesheet" href="../assets/estilo/animate.css" type="text/css"  /> 
    <link rel="stylesheet" href="../assets/estilo/admin.css?<?php echo microtime(true); ?>" type="text/css"  /> 
    <link rel="stylesheet" type="text/css" href="../plugins/semantic/semantic.min.css"> 
    
    <!--<link rel="stylesheet" href="../plugins/bootstrap/css/bootstrap.css" type="text/css"  /> -->
    <link rel="stylesheet" href="../assets/estilo/loading.css" type="text/css"  />
    <link rel="stylesheet" href="../assets/estilo/loading-big.css" type="text/css"  /> 
    <link rel="stylesheet" href="../assets/estilo/triangule.css" type="text/css"  /> 
    <link rel="stylesheet" href="../assets/estilo/ios-switch.css" type="text/css"  />    
    <link rel="stylesheet" href="../plugins/malihu-custom-scrollbar/jquery.mCustomScrollbar.css" />  
    <link rel="stylesheet" href="../plugins/chosen_v1.7.0/component-chosen.css">

    <link rel="stylesheet" href="../plugins/kendoui/styles/kendo.common.min.css" type="text/css"  /> 
    <link rel="stylesheet" href="../plugins/kendoui/styles/kendo.bootstrap-v4.min.css" type="text/css"  /> 

    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />

    <!--
    <link rel="stylesheet" href="../plugins/datepicker/css/bootstrap-datepicker.css" /> 
    
    -->
    <link rel="stylesheet" href="../assets/estilo/validador-form.css" /> 

    <link rel="stylesheet" href="../plugins/semantic/semantic.min.css" /> 

</head>
<body>   
    <div class="notification_box">
       <div class="notification_close"></div>
       <div class="notification_body">
           <div class="notification_info">
               <div class="notification_info_icon"></div>
               <div class="notification_info_text"></div>
           </div>
       </div>
    </div>

    <div id="visor">
        <div id="header_visor">
             <div id="header_visor_title"></div>
             <div id="header_visor_close"></div>
        </div>
        <div id="body_visor"></div>
    </div>
    <div id="absolute"></div>  
    <div class="supercapa"></div>
    
    <div class="cargando">  
          <div class="ui active dimmer">
            <div class="ui indeterminate text loader">Cargando por favor espere</div>
          </div>
          <p></p>
    </div>
  
    <div class="listPoint">
      <div class="listPointClose"></div> 
      <div class="listBody">AQUI VA EL CONTENIDO</div>
    </div> 
    
    <div id="header"> 
      <div id="logo"></div>      
      <div id="home"></div>
      <div id="back">Atras</div>

      <div id="name"><?php echo $_SESSION['name'] ." - ". $_SESSION['punto']; ?></div>
      <div id="tools">
        <div id="exit"></div> 
        <div id="bell"></div>
        <div id="search">
          <input type="text" name="search" id='BuscarProducto'>
        </div>
      </div>    
    </div> 
 
    <div id="content"></div>
     
    <script src="../assets/js/jquery-3.1.1.min.js" ></script> 
    <script src="../assets/js/socket.io.js"></script>
    <script src="../assets/js/numeral.min.js"></script>


    <script src="../plugins/kendoui/js/kendo.web.min.js" ></script> 
    <script src="../plugins/kendoui/js/cultures/kendo.culture.es-CO.min.js" ></script> 

    <script src="../plugins/chosen_v1.7.0/chosen.jquery.js" ></script>  
    <script src="../plugins/notify/notify.min.js" ></script>  
    <!--
    <script src="../plugins/bootstrap/js/tether.min.js" ></script> 
    <script src="../plugins/bootstrap/js/bootstrap.js" ></script> 
    -->
    
    <script src="../assets/js/functions.js?<?php echo microtime(true); ?>" ></script> 
    <script src="../assets/js/admin.js?<?php echo microtime(true); ?>" ></script> 
    <script src="../assets/js/validador-form.js" ></script> 
 
      
    
    <script src="../plugins/malihu-custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="../assets/js/TweenMax.min.js"></script>

    <script src="../plugins/semantic/semantic.js"></script>
    <script src="../plugins/datatables/js/jquery.dataTables.min.js" ></script> 
    
    <script src="../plugins/datatables/js/dataTables.semanticui.min.js" ></script>
    <link rel="stylesheet" href="../plugins/datatables/css/dataTables.semanticui.min.css" />


    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>  
  
      
</body>
</html>

 