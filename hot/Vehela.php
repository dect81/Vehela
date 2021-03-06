<?php

class Vehela
{

    public $_SETTINGS;
    private $Render;
    private $Router;
    private $DBQueue;
    private $ControllerName;
    private $start_time;
    public $error;
    public $QuickPass = true;
    const RootDir = __DIR__;

    public function Vehela()
    {
        $this->StartCalculate();
        $this->Init();
    }

    public function __destruct()
    {
        if(Registry::get('Router')->Controller != 'api')
            Registry::get('Controller')->TransferGenerationTime($this->EndCalculate());
    }

    private function Init()
    {
        $this->LaunchStartingSystem();
        $this->InitRouterSystem();
        $this->GoQuickPass();
        $this->InitRenderingSystem();
        $this->InitController();
    }

    private function LaunchStartingSystem(){
        require_once('systems/StartingSystem.php');
        $StartingSystem = new StartingSystem($this);
    }

    private function InitRouterSystem()
    {
        require_once('systems/Router.php');
        Registry::add('Router',new Router());
    }

    private function GoQuickPass(){
        Registry::add('QuickPass',$this->QuickPass);
        $this->InitController($this->_SETTINGS);
        Registry::del('Controller');
        Registry::del('QuickPass');
        $this->QuickPass = 0;
        Registry::add('QuickPass',$this->QuickPass);
    }

    private function InitRenderingSystem()
    {
        require_once('systems/Render.php');
        Registry::add('Render', new Render());
    }

    private function InitController()
    {
        require_once('/helpers/prototypes/HController.php');
        $HController = new HController($this);
    }

    private function StartCalculate()
    {
        $this->start_time = microtime(true);
    }

    private function EndCalculate()
    {
        $time = microtime(true) - $this->start_time;
        return $time;
   }

    public static function Model($ModelName)
    {

        if (!Tools::checkForIncluded($ModelName))
            Tools::includeFile(self::RootDir . "/../iframe/models/{$ModelName}.php");


        $Model = new $ModelName('oups');

        return $Model;
    }

    public static function RequestMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

}

?>
