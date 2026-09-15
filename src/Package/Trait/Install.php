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
    public function install_list(object $options): void
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
                            if(!property_exists($options->frontend,'subdomain') || empty($options->frontend->subdomain)){
                                $clone_options->set('frontend.host', $options->frontend->domain . '.' . $options->frontend->extension);
                            } else {
                                $clone_options->set('frontend.host', $options->frontend->subdomain . '.' . $options->frontend->domain . '.' . $options->frontend->extension);
                            }
                            if(!property_exists($options->backend,'subdomain')  || empty($options->backend->subdomain)){
                                $clone_options->set('backend.host', $options->backend->domain . '.' . $options->backend->extension);
                            } else {
                                $clone_options->set('backend.host', $options->backend->subdomain . '.' . $options->backend->domain . '.' . $options->backend->extension);
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
            }
        }
    }

    /**
     * @throws Exception
     */
    public function install_api(object $options): void
    {
        if(!property_exists($options, 'package')){
            throw new Exception('Option -package not set');
        }
        $object = $this->object();
        $dir_read = $object->config('project.dir.vendor') .
            $options->package .
            $object->config('ds') .
            'src' .
            $object->config('ds') .
            $object->config('dictionary.api') .
            $object->config('ds')
        ;
        if(!property_exists($options, 'backend')){
            throw new Exception('Option -backend not set');
        }
        $dir_target = $object->config('project.dir.domain') .
            $options->backend->name .
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
                    unset($read[$nr]);
                }
            }
            $options->read = $read;
            echo 'Installing API: ' . $count . ' files' . PHP_EOL;
            $this->install_list($options);
        } else {
            echo 'No Installations files for API: ' . $count . ' files' . PHP_EOL;
        }
    }

    /**
     * @throws Exception
     */
    public function install_application(object $options): void
    {
        $object = $this->object();
        $dir_read = $object->config('project.dir.vendor') .
            $object->request('package') .
            $object->config('ds') .
            'src' .
            $object->config('ds') .
            $object->config('dictionary.application') .
            $object->config('ds')
        ;
        $dir_target = $object->config('project.dir.domain') .
            $options->frontend->name .
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
            } else {
                unset($read[$nr]);
            }
        }
        $options->read = $read;
        echo 'Installing Frontend: ' . $count . ' files' . PHP_EOL;
        $this->install_list($options);
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
    public function install_system_application(object $flags, object $options): void
    {
        $object = $this->object();
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
        $read = $object->data_read($options->url->node_extension);
        if (!$read) {
            throw new Exception('Node: System.Server.Extension.json not found aborting...');
        }
        $list_search = [];
        $active = [];
        $node_system_server_extension = $read->data('System.Server.Extension');
        foreach ($node_system_server_extension as $extension) {
            $active[] = $extension->name;
            $list_search[$extension->name] = $extension->uuid;
        }
        $data_extension = $object->data_read($options->url->extension);
        if(!$data_extension){
            throw new Exception('Node (Import): System.Server.Extension.json not found aborting...');
        }
        $extensions = [];
        $count = 0;
        foreach ($data_extension->data('System.Server.Extension') as $extension) {
            if (
                is_object($extension) &&
                property_exists($extension, 'name')) {
                if (!in_array($extension->extension, $extensions, true)) {
                    if (array_key_exists($extension->name, $list_search)) {
                        $extensions[] = $list_search[$extension->name];
                    }
                }
                if(!in_array($extension->name, $active, true)){
                    $record = (object)[
                        'name' => $extension->name,
                        'extension' => $extension->extension,
                    ];
                    $node = new Node($object);
                    $role_system = $node->role_system();
                    $response = $node->create('System.Server.Extension', $role_system, $record);
                    $count++;
                }
            }
        }
        echo Cli::info('Installed: ') . $count . ' System.Server.ContentType' . PHP_EOL;
        $read = $object->data_read($options->url->node_content_type);
        if (!$read) {
            throw new Exception('Node: System.Server.ContentType.json not found aborting...');
        }
        $active = [];
        $node_system_server_content_type = $read->data('System.Server.ContentType');
        foreach ($node_system_server_content_type as $content_type) {
            d($content_type);
            $active[] = $content_type->type;
        }
        $data_extension = $object->data_read($options->url->content_type);
        if(!$data_extension){
            throw new Exception('Node (Import): System.Server.ContentType.json not found aborting...');
        }
        $count = 0;
        foreach ($data_extension->data('System.Server.ContentType') as $content_type) {
            if(!in_array($content_type->type, $active, true)){
                $record = (object)[
                    'type' => $content_type->type,
                    'extension' => $content_type->extension,
                ];
                $node = new Node($object);
                $role_system = $node->role_system();
                $response = $node->create('System.Server.ContentType', $role_system, $record);
                $count++;
            }
        }
        echo Cli::info('Installed: ') . $count . ' System.Server.ContentType' . PHP_EOL;
        $class = 'Account.User';
        $node = new Node($object);
        $role_system = $node->role_system();
        $limit = 100;
        $count = $node->count($class, $role_system);
        $page_count = 1;
        if ($limit > 0) {
            $page_count = ceil($count / $limit);
        }
        if (!property_exists($options, 'sort')) {
            $options->sort = 'uuid';
        }
        if (!is_array($options->sort)) {
            $options->sort = [
                $options->sort => 'ASC'
            ];
        }
        $sort = $options->sort ?? ['uuid' => 'ASC'];
        $filter = $options->filter ?? [];
        if (empty($filter)) {
            $filter = [];
        } elseif (!is_array($filter)) {
            throw new Exception('Filter must be an array.');
        }
        $user_list = [];
        for ($page = 1; $page <= $page_count; $page++) {
            $response = $node->list($class, $role_system, [
                'sort' => $sort,
                'filter' => $filter,
                'limit' => $limit,
                'page' => $page
            ]);
            if (
                $response !== null &&
                is_array($response) &&
                array_key_exists('list', $response)
            ) {
                foreach ($response['list'] as $nr => $user) {
                    $user_list[] = $user->uuid ?? null;
                }
            }
        }
        $class = 'System.Application';
        $role = $node->role_system();
        $record = (object)[
            'name' => self::NAME,
            'user' => $user_list,
            'display' => (object)[
                'name' => self::DISPLAY_NAME,
            ],
            'directory' => (object)[
                'application' => 'Application/' . self::NAME . '/',
                'icon' => '/Application/' . self::NAME . '/Icon/Icon.png',
            ],
            'method' => null,
            'target' => null,
            'description' => self::DESCRIPTION,
            'extension' => $extensions,
        ];
        $environment = $object->config('framework.environment');
        if (property_exists($options->frontend->url, $environment)) {
            $record->url = $options->frontend->url->{$environment} . $record->directory->application;
            $record->icon_url = $options->frontend->url->{$environment} . $record->directory->icon;
        } else {
            throw new Exception('Frontend url not set for environment: ' . $environment);
        }
        $exist = $node->record($class, $role, [
            'where' => [
                [
                    'value' => self::NAME,
                    'attribute' => 'name',
                    'operator' => '===',
                ]
            ]
        ]);
        if ($exist === null) {
            $response = $node->create($class, $role, $record);
            echo $record->name . ' created...' . PHP_EOL;
        } else {
            if (
                property_exists($options, 'patch') &&
                $options->patch === true
            ) {
                $record->uuid = $exist['node']->uuid;
                $response = $node->patch($class, $role, $record);
                echo $record->name . ' patched...' . PHP_EOL;
            }
        }
    }
}