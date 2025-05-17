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

trait Music {

    /**
     * @throws DirectoryCreateException
     * @throws Exception
     */
    public function record($flags, $options): void
    {
        $object = $this->object();
        $data = new Data($object->data());
        if(!property_exists($options, 'directory')){
            $options->directory = $object->config('project.dir.mount') .
                'Vps3' .
                $object->config('ds') .
                'Mount' .
                $object->config('ds') .
                'Audio' .
                $object->config('ds') .
                'Music' .
                $object->config('ds') .
                '{{date(\'W-Y\')}}' .
                $object->config('ds')
            ;
        }

        $url = $options->url ?? null;
        if(!$url){
            throw new Exception('URL not set');
        }
        unset($options->url);
        $options->source = 'Internal_' . str_replace('-', '_', Core::uuid());
        $parse = new Parse($object, $data, $flags, $options);
        $parse->limit(['date']);
        $directory = $parse->compile($options->directory);
        Dir::change($directory, Dir::CHMOD);
        File::permission($object, [
            'dir' => $directory,
        ]);
        $o = $directory . '%(title)s [%(id)s].%(ext)s';
        $command = Core::binary($object) . ' raxon/task create -user.email=remco@universeorange.com -command[]=\'yt-dlp -x -o "' . $o . '" --restrict-filenames --audio-format mp3 --prefer-ffmpeg ' .  $url . '\' -connection=system';
        echo $command . PHP_EOL;
        exec($command, $output, $code);
        if($code !== 0) {
            throw new Exception('Command failed with code ' . $code . '.');
        }

    }

}