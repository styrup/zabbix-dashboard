<?php
class AlertItem {
        public $Host;
        public $Time;
        public $Desc;
        public $Sev;
        
    function __construct($host,$desc,$time,$sev) {
       $this->Host = $host;
       $this->Time = $time;
       $this->Desc = $desc;
       $this->Sev = $sev;
   }
   
   public function __toString()
   {
     return $this->Time ." ". $this->Host . ": ". $this->Desc;
   }
   public function printdata(){
       return date("j/n H:i",$this->Time) . " " . $this->Host . ": " . $this->Desc;
   }
   public function GetAlertTime(){
       return date("j/n H:i",$this->Time);
   }
}
?>