<?php
namespace Package\Raxon\Basic\Trait;

use Raxon\App;

use Exception;
use Raxon\Exception\ObjectException;
use Raxon\Module\Cli;
use Raxon\Module\Core;
use Raxon\Module\Data;
use Raxon\Module\Dir;
use Raxon\Module\File;
use Raxon\Node\Module\Node;
use Raxon\Parse\Module\Parse;

trait Install {

    /**
     * @throws Exception
     */
    public function install_list(object $options, object $application): void
    {
        $object = $this->object();
        $read = $options->read ?? [];
        $patch = $options->patch ?? null;
        foreach($read as $nr => $file){
            if($file->type === File::TYPE){
                $file->extension = File::extension($file->target);
                if($file->extension === 'rax'){
                    $explode = explode('.rax', $file->target, 2);
                    if(array_key_exists(1, $explode)){
                        $file->target = $explode[0];
                        $file->original_extension = File::extension($file->target);
                        if(!File::exist($file->target) || $patch !== null){
                            $clone_options = new Data();
                            if(!property_exists($application->frontend,'subdomain') || empty($application->frontend->subdomain)){
                                $clone_options->set('frontend.host', $application->frontend->domain . '.' . $application->frontend->extension);
                            } else {
                                $clone_options->set('frontend.host', $application->frontend->subdomain . '.' . $application->frontend->domain . '.' . $application->frontend->extension);
                            }
                            if(!property_exists($application->backend,'subdomain')  || empty($application->backend->subdomain)){
                                $clone_options->set('backend.host', $application->backend->domain . '.' . $application->backend->extension);
                            } else {
                                $clone_options->set('backend.host', $application->backend->subdomain . '.' . $application->backend->domain . '.' . $application->backend->extension);
                            }
                            $data = new Data($object->data());
                            $clone = clone $object;
                            $clone->data(App::OPTIONS, $clone_options->data());
                            switch($file->original_extension){
                                case 'json':
                                    echo Cli::info('Processing file:') . $file->target . PHP_EOL;
                                    $content = $clone->parse_read($file->url);
                                    if($patch !== null) {
                                        File::delete($file->target);
                                    }
                                    $dir_target = Dir::name($file->target);
                                    if(!File::exist($dir_target)){
                                        Dir::create($dir_target, Dir::CHMOD);
                                        File::permission($object, [
                                            'target' => $dir_target,
                                        ]);
                                    }
                                    File::write($file->target, Core::object($content->data(), Core::JSON));
                                    File::permission($object, [
                                        'target' => $file->target,
                                    ]);
                                    //imports should be in a json file (class => url/contains)
                                    if(str_contains($file->target, 'System.Route')){
                                        $command = 'app raxon/node object import -class=System.Route -url="' . $file->target . '" -patch';
                                        Core::execute($object, $command, $output, $notification);
                                        if($output){
                                            echo $output;
                                        }
                                        if($notification){
                                            echo $notification;
                                        }
                                    }
                                    break;
                                default:
                                    echo Cli::info('Processing file:') . $file->target . PHP_EOL;
                                    $clone_options->set('source', $file->url);
                                    $flags = App::flags($clone);
                                    $parse = new Parse($clone, $data, $flags, $clone_options->data());
                                    $read = File::read($file->url);
                                    $content = $parse->compile($read, $data);
                                    if($patch !== null) {
                                        File::delete($file->target);
                                    }
                                    $dir_target = Dir::name($file->target);
                                    if(!File::exist($dir_target)){
                                        Dir::create($dir_target, Dir::CHMOD);
                                        File::permission($object, [
                                            'target' => $dir_target,
                                        ]);
                                    }
                                    File::write($file->target, $content);
                                    File::permission($object, [
                                        'target' => $file->target,
                                    ]);
                                    break;
                            }
                        }
                    }
                } else {
                    if($patch !== null) {
                        File::delete($file->target);
                    }
                    echo Cli::info('Processing file:') . $file->target . PHP_EOL;
                    $dir_target = Dir::name($file->target);
                    if(!File::exist($dir_target)){
                        Dir::create($dir_target, Dir::CHMOD);
                        File::permission($object, [
                            'target' => $dir_target,
                        ]);
                    }
                    File::copy($file->url, $file->target);
                    File::permission($object, [
                        'target' => $file->target,
                    ]);
                }
            } else {
                d($file);
                /*
                if(!)
                d($file);
                */
            }
        }
    }

    /**
     * @throws Exception
     */
    public function install_api(object $options, object $application): void
    {

        if(!property_exists($application, 'package')){
            throw new Exception('Application -> package not set');
        }
        $object = $this->object();
        $dir_read = $object->config('project.dir.vendor') .
            $application->package .
            $object->config('ds') .
            'src' .
            $object->config('ds') .
            $object->config('dictionary.api') .
            $object->config('ds')
        ;
        if(!property_exists($application, 'backend')){
            throw new Exception('Application -> backend not set');
        }
        if(!property_exists($application->backend, 'name')){
            throw new Exception('Application -> backend.name not set');
        }
        $dir_target = $object->config('project.dir.domain') .
            $application->backend->name .
            $object->config('ds')
        ;
        if(!File::exist($dir_target)){
            Dir::create($dir_target, Dir::CHMOD);
            File::permission($object, [
                'target' => $dir_target,
            ]);
        }
        $dir = new Dir();
        $read = $dir->read($dir_read, true);
        $count = 0;
        if($read !== false){
            foreach($read as $nr => $file){
                if($file->type === File::TYPE){
                    $explode = explode($dir_read, $file->url, 2);
                    if(array_key_exists(1, $explode)){
                        $file->target = $dir_target . $explode[1];
                    }
                    $count++;
                } else {
                    d($dir_read);
                    d($file->url);
                    $explode = explode($dir_read, $file->url, 2);
                    dd($explode);
                    if(array_key_exists(1, $explode)){
                        $file->target = $dir_target . $explode[1];
                    }
                }
            }
            $options->read = $read;
            echo 'Installing API: ' . $count . ' files' . PHP_EOL;
            $this->install_list($options, $application);
        } else {
            echo 'No Installations files for API: ' . $count . ' files' . PHP_EOL;
        }
    }

    /**
     * @throws Exception
     */
    public function install_application(object $options, object $application): void
    {
        if(!property_exists($application, 'package')){
            throw new Exception('Application -> package not set');
        }
        if(!property_exists($application, 'frontend')){
            throw new Exception('Application -> frontend not set');
        }
        if(!property_exists($application->frontend, 'name')){
            throw new Exception('Application -> frontend.name not set');
        }
        $object = $this->object();
        $dir_read = $object->config('project.dir.vendor') .
            $application->package .
            $object->config('ds') .
            'src' .
            $object->config('ds') .
            $object->config('dictionary.application') .
            $object->config('ds')
        ;
        $dir_target = $object->config('project.dir.domain') .
            $application->frontend->name .
            $object->config('ds') .
            $object->config('dictionary.application') .
            $object->config('ds') .
            self::NAME .
            $object->config('ds')
        ;
        if(!File::exist($dir_target)){
            Dir::create($dir_target, Dir::CHMOD);
            File::permission($object, [
                'target' => $dir_target,
            ]);
        }
        $dir = new Dir();
        $read = $dir->read($dir_read, true);
        $count = 0;
        foreach($read as $nr => $file){
            if($file->type === File::TYPE){
                $explode = explode($dir_read, $file->url, 2);
                if(array_key_exists(1, $explode)){
                    $file->target = $dir_target . $explode[1];
                }
                $count++;
            }
        }
        $options->read = $read;
        echo 'Installing Frontend: ' . $count . ' files' . PHP_EOL;
        $this->install_list($options, $application);
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function install_frontend_get(object $options): ?object
    {
        $object = $this->object();
        $has_frontend = false;
        $frontend_options = [];
        if(property_exists($options, 'frontend')){
            if(property_exists($options->frontend, 'host')){
                $has_frontend = true;
                $frontend_options = [
                    'where' => [
                        [
                            'value' => $options->frontend->host,
                            'attribute' => 'name',
                            'operator' => 'partial',
                        ]
                    ]
                ];
            }
        }
        if($has_frontend === false){
            throw new Exception('Frontend.host option is required and must be defined in Node/System.Host.json aborting...');
        }
        $class = 'System.Host';
        $node = new Node($object);
        $response = $node->record(
            $class,
            $node->role_system(),
            $frontend_options
        );
        return $response['node'] ?? null;
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function install_backend_get(object $options): ?object
    {
        $object = $this->object();
        $has_backend = false;
        $backend_options = [];
        if(property_exists($options, 'backend')){
            if(property_exists($options->backend, 'host')){
                $has_backend = true;
                $backend_options = [
                    'where' => [
                        [
                            'value' => $options->backend->host,
                            'attribute' => 'name',
                            'operator' => 'partial',
                        ]
                    ]
                ];
            }
        }
        if($has_backend === false){
            throw new Exception('Backend.host option is required and must be defined in Node/System.Host.json aborting...');
        }
        $class = 'System.Host';
        $node = new Node($object);
        $response =  $node->record(
            $class,
            $node->role_system(),
            $backend_options
        );
        return $response['node'] ?? null;
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function install_system_application(object $flags, object $options): array
    {
        $object = $this->object();
        /*
        if(!property_exists($options, 'url')){
            throw new Exception('Option -url not set');
        }
        if (!property_exists($options->url, 'node_extension')) {
            throw new Exception('Option -url.node_extension not set');
        }
        if (!property_exists($options->url, 'node_content_type')) {
            throw new Exception('Option -url.node_content_type not set');
        }
        if(!property_exists($options->url, 'extension')){
            throw new Exception('Option -url.extension not set');
        }
        if(!property_exists($options->url, 'content_type')){
            throw new Exception('Option -url.content_type not set');
        }
//        echo Cli::info('Installed:') . ' ' . $count . ' System.Server.Extension' . PHP_EOL;
        $read = $object->data_read($options->url->node_content_type);
        if (!$read) {
            throw new Exception('Node: System.Server.ContentType.json not found aborting...');
        }
        /*
        $active = [];
        $node_system_server_content_type = $read->data('System.Server.ContentType');
        foreach ($node_system_server_content_type as $content_type) {
            $active[] = $content_type->content_type ?? null;
        }
        $data_content_type = $object->data_read($options->url->content_type);
        if(!$data_content_type){
            throw new Exception('Node (Import): System.Server.ContentType.json not found aborting...');
        }
        $count = 0;
        foreach ($data_content_type->data('System.Server.ContentType') as $content_type) {
            if(!in_array($content_type->content_type, $active, true)){
                $record = (object)[
                    'content_type' => $content_type->content_type,
                    'extension' => $content_type->extension,
                ];
                $node = new Node($object);
                $role_system = $node->role_system();
                $response = $node->create('System.Server.ContentType', $role_system, $record);
                $count++;
            }
        }
        echo Cli::info('Installed:') . ' ' . $count . ' System.Server.ContentType' . PHP_EOL;
        */
        $url_system_application =
            $object->config('controller.dir.data') .
            'System.Application' .
            $object->config('extension.json')
        ;
        $data_system_application = $object->parse_read($url_system_application);
        if($data_system_application === null){
            throw new Exception('Node (Import): "'. $url_system_application .'" not found aborting...');
        }
        $list = $data_system_application->data('System.Application');
        $node = new Node($object);
        $class = 'System.Application';
        $role = $node->role_system();
        foreach($list as $application){
            $exist = $node->record($class, $role, [
                'where' => [
                    [
                        'value' => $application->name,
                        'attribute' => 'name',
                        'operator' => '===',
                    ]
                ]
            ]);
            if ($exist === null) {
                $response = $node->create($class, $role, $application);
                echo $application->name . ' created...' . PHP_EOL;
            } else {
                if (
                    property_exists($options, 'patch') &&
                    $options->patch === true
                ) {
                    $application->uuid = $exist['node']->uuid;
                    $response = $node->patch($class, $role, $application);
                    echo $application->name . ' patched...' . PHP_EOL;
                }
            }
        }
        return $list;
    }
}