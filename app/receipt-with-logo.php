<?php

require __DIR__ . '/escpos-php/autoload.php';
use Mike42\Escpos\Printer; 

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector; 
$connector = new NetworkPrintConnector("192.168.0.20", 9100);
$printer = new Printer($connector);
 
$items = array(
    new item("Example item #1", "4.00" , '' ,  '120' ),
    new item("Another thing", "3.50" , '' , '400'),
    new item("Something else", "1.00" ,  '' , '5'),
    new item("A final item", "4.45" , '' , '7000'),
);
$subtotal = new item('Subtotal', '12.95');
$tax = new item('A local tax', '1.30');
$total = new item('Total', '14.25', true); 
$date = "Monday 6th of April 2015 02:56:25 PM";
 
$printer -> setJustification(Printer::JUSTIFY_CENTER);

/* Name of shop */
$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
$printer -> text("FERREGOMEZ JP\n");
$printer -> selectPrintMode();
$printer -> text( "Venta de Electricos y Ferreteria en General.\n" );
$printer -> feed();


$printer -> text("-------------------------------------------\n");
$printer -> feed();

/* Title of receipt */
$printer -> setEmphasis(true);
$printer -> text("RECIBO DE CAJA\n");
$printer -> setEmphasis(false);

/* Items */
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> setEmphasis(true);


$rightCols = 6;
$leftCols = 36;
$finalCols = 6;

$producto = str_pad("Producto", $leftCols ,' ') ; 
$cantidad = str_pad("Cant", $finalCols, ' ', STR_PAD_LEFT);

$precio = str_pad( "Pre", $rightCols, ' ', STR_PAD_LEFT);


 $printer->text("$producto$cantidad$precio\n");

$printer -> setEmphasis(false);
foreach ($items as $item) {
    $printer->text($item);
}
$printer -> setEmphasis(true);
$printer -> text($subtotal);
$printer -> setEmphasis(false);
$printer -> feed();

/* Tax and total */
$printer -> text($tax);
$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
$printer -> text($total);
$printer -> selectPrintMode();

/* Footer */
$printer -> feed(2);
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> text("Gracias por comprar en FerregomezJP\n");
$printer -> text( "Para mayor informaci&oacute;n visite \nwww.ferregomezjp.com.co\n");
$printer -> feed(2);
$printer -> text($date . "\n");

/* Cut the receipt and open the cash drawer */
$printer -> cut();
$printer -> pulse();

$printer -> close();

/* A wrapper to do organise item names & prices into columns */
class item
{
    private $name;
    private $price;
    private $dollarSign;
    private $cant;

    public function __construct($name = '', $price = '', $dollarSign = false , $cant = '')
    {
        $this -> name = $name;
        $this -> price = $price;
        $this -> dollarSign = $dollarSign;
        $this -> cant = $cant;
    }
    
    public function __toString()
    {
        $rightCols = 6;
        $leftCols = 36;
        $finalCols = 6;
        /*
        if ($this->dollarSign) 
        {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        */

        //$first = str_pad( $this->cant, 2 , ".");
        $first = $this->cant;

        echo  $first."<br>"; 

        $name = str_pad($this->name, $leftCols ,' ') ;
         

        $sign = ($this->dollarSign ? '$ ' : '');
        $price = str_pad(  $this->price, $rightCols, ' ', STR_PAD_LEFT);
        $cant = str_pad(  $this->cant, $finalCols, ' ', STR_PAD_LEFT);

        return "$name$cant$price\n";
    }
}
