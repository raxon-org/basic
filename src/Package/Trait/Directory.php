<?php
namespace Package\Raxon\Basic\Trait;

use Exception;
use Raxon\Exception\DirectoryCreateException;
use Raxon\Exception\ObjectException;
use Raxon\Module\Data;
use Raxon\Module\Dir;
use Raxon\Module\Core;
use Raxon\Module\File;
use Raxon\Parse\Module\Parse;

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
        $options->source = 'Internal_' . str_replace('-', '_', Core::uuid());
        $parse = new Parse($object, $data, $flags, $options);
        $parse->limit(['date']);
        $directory = $parse->compile($options->directory);
        Dir::create($directory, Dir::CHMOD);
        File::permission($object, [
            'dir' => $directory,
        ]);
    }

}