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
            throw new Exception('Name not set');
        }
        $dir = new Dir();
        $list = $dir->read('/mnt/Vps3/');
        dd($list);
    }

}