<?php
class fecha
{
   private $dia;
   private $mes;
   private $anio;
   public  $inicial;
   
   function getDia($mes)
   {
      $select = "<select name='dd'>";
      for($i = 1 ; $i <= 30 ; $i++ )
      {
         $select.="<option value='$i'>$i</option>";
      }
      $select .= "</select>";
     
      return $select;
   } 
   function getMes()
   {
       $select = "<select name='mm'>";
     
      for($i = 1 ; $i <= 12 ; $i++ )
      {
         $select.="<option value='$i'>".$this->conversion($i)."</option>";
      }
      $select.= "</select>";
     
      return
      $select;
   } 
   function getAnio()
   {
      $select = "<select name='aa'>";
      for($i = $this->inicial ; $i < $this->inicial + 10  ; $i++ )
      {
         $select.="<option value='$i'>".$i."</option>";
      }
      $select.="</select>";
      return  $select;
   }
   function conversion($mes)
   {
      switch($mes)
      {
         case 1:
         return "Enero";
         break;
         
         case 2:
         return "Febrero";
         break;
         
         case 3:
         return "Marzo";
         break;
         
         case 4:
         return "Abril";
         break;
         
         case 5:
         return "Mayo";
         break;
         
         case 6:
         return "Junio";
         break;
         
         case 7:
         return "Julio";
         break;
         
         case 8:
         return "Agosto";
         break;
         
         case 9:
         return "Septiembre";
         break;
         
         case 10:
         return "Octubre";
         break;
         
         case 11:
         return "Noviembre";
         break;
         
         case 12:
         return "Diciembre";
         break;
           
      }
      
   } 
}

?>