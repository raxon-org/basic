<?php
namespace Package\Raxon\Basic\Trait;

use Exception;
use Raxon\Exception\DirectoryCreateException;
use Raxon\Exception\ObjectException;
use Raxon\Module\Data;
use Raxon\Module\Dir;
use Raxon\Module\Core;
use Raxon\Module\File as Module;
use Raxon\Parse\Module\Parse;

trait File {

    /**
     * @throws DirectoryCreateException
     * @throws Exception
     */
    public function search($flags, $options): void
    {
        $object = $this->object();
        $data = new Data($object->data());
        if(!property_exists($options, 'name')){
            throw new Exception('Option -name not set');
        }
        if(!property_exists($options, 'directory')){
            throw new Exception('Option -directory not set');
        }
        if(!property_exists($options, 'disable-symlink')){
            throw new Exception('Option -disable-symlink not set and could cause a recursion in the system which causes a kill after rubbishing the memory...');
        }
        $command = 'du ' . $options->directory;
        Core::execute($object, $command, $output, $notification);
        if($output){
            $list = explode(PHP_EOL, $output);
            breakpoint($list);
        }
        if($notification){
            echo $notification . PHP_EOL;
        }
    }

}