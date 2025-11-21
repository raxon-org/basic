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
        ob_start();
        exec($command, $output);
        $list_dir = [];
        if(!empty($output)){
            foreach($output as $line) {
                $line = trim($line);
                $explode = explode("\t", $line);
                $size = 0;
                $dir = '';
                foreach ($explode as $nr => $value) {
                    $value = trim($value);
                    if (empty($value) && $value !== 0) {
                        continue;
                    }
                    if ($nr === 0) {
                        $size = $value;
                    } else {
                        $dir = $value;
                        break;
                    }
                }
                $list_dir[] = (object)[
                    'dir' => $dir,
                    'size' => $size
                ];
            }
            echo 'Count: ' . count($list_dir) . PHP_EOL;
            $dir = new Dir();
            $list_file = [];
            foreach($list_dir as $file){
                $list = $dir->read($file->dir);
                if(!empty($list)){
                    foreach($list as $item){
                        $list_file[] = $item;
                    }
                }
            }
            foreach($list_File as $file){
                ddd($file);
            }
            breakpoint(count($list_file));
            breakpoint($list_file);
        }
    }

}