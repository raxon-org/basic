<?php
namespace Package\Raxon\Basic\Trait;

use Raxon\Config;

use Raxon\Exception\FileMoveException;
use Raxon\Exception\FileWriteException;
use Raxon\Module\Data;
use Raxon\Module\Dir;
use Raxon\Module\Core;
use Raxon\Module\Event;
use Raxon\Module\File;
use Raxon\Parse\Module\Parse;
use Raxon\Module\Sort;

use Exception;

use Raxon\Exception\DirectoryCreateException;
use Raxon\Exception\ObjectException;

trait Directory {

    /**
     * @throws DirectoryCreateException
     * @throws Exception
     */
    public function create($flags, $options): void
    {
        $object = $this->object();
        if(!property_exists($options, 'directory')) {
            throw new ObjectException('Directory not set');
        }
        $data = new Data($object->data());
        $parse = new Parse($object, $data, $flags, $options);
        $parse->limit(['date']);
        $directory = $parse->compile($options->directory);
        ddd($directory);
        Dir::create($options->directory, Dir::CHMOD);
        File::permission($object, [
            $options->directory,
        ]);
    }

}