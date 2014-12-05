<?php
use TubeLink\TubeLink;

/**
 * Wrapper for TubeLink ( https://github.com/GromNaN/TubeLink )
 * @author Vasiliy Pedak
 */
class CTubeLink extends CApplicationComponent
{
    /**
     * @var TubeLink\TubeLink 
     */
    private $_parser;

    public function init()
    {
        $parser = new TubeLink();
        $parser->registerService(new \TubeLink\Service\Youtube());
        $parser->registerService(new \TubeLink\Service\Vimeo());
        
        $this->_parser = $parser;
    }
    
    /**
     * 
     * @param string $url Video Url
     * @return Tybe
     */
    public function parse($url)
    {
        return $this->_parser->parse($url);
    }
}

