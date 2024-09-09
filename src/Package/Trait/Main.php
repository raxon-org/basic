<?php
namespace Package\Raxon\Org\Basic\Trait;

use Raxon\Org\Config;

use Raxon\Org\Exception\FileWriteException;
use Raxon\Org\Module\Dir;
use Raxon\Org\Module\Core;
use Raxon\Org\Module\Event;
use Raxon\Org\Module\File;
use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Sort;

use Exception;

use Raxon\Org\Exception\DirectoryCreateException;
use Raxon\Org\Exception\ObjectException;

trait Main {

    /**
     * @throws DirectoryCreateException
     * @throws Exception
     */
    public function apache2_setup(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        //php and apache2 should be installed by docker.
        //if there is a different sury package, there are multiple versions
        $dir = new Dir();
        $read = $dir->read('/etc/php/');
        $read = Sort::list($read)->with(['name' => 'desc']);
        $file_old = false;
        if(count($read) === 1){
            $file = current($read);
        } else {
            $file = current($read);
            $file_old = end($read);
        }
        $fpm = 'php' . $file->name . '-fpm';
        if($file_old){
            $php = 'php' . $file_old->name;
        } else {
            $php = 'php' . $file->name;
        }
        Dir::create('/run/php');
        $command = 'a2enmod proxy_fcgi setenvif';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'a2enconf ' . escapeshellarg($fpm);
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'a2dismod ' . escapeshellarg($php);
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'a2dismod mpm_prefork';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'a2enmod mpm_event';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'a2enmod http2';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'a2enmod rewrite';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'a2enmod ssl';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'a2enmod md';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = '. /etc/apache2/envvars';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
    }

    /**
     * @throws Exception
     */
    public function apache2_restore(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        $url = $object->config('project.dir.data') . 'Apache2' . $object->config('ds');
        $dir = new Dir();
        $read = $dir->read($url);
        if(!is_array($read)){
            return;
        }
        foreach($read as $file){
            if($file->type === File::TYPE){
                $source = $file->url;
                $destination = '/etc/apache2/sites-available/' . $file->name;
                if(File::exist($destination)) {
                    File::delete($destination);
                }
                File::copy($source, $destination);
                exec('chmod 640 ' . $destination);
                exec('chown root:root ' . $destination);
                $disabled = $object->config('server.site.disabled');
                if(
                    $disabled &&
                    is_array($disabled) &&
                    in_array(
                        $file->name,
                        $disabled,
                        true
                    )
                ){
                    $command = 'a2dissite ' . $file->name;
                    Core::execute($object, $command, $output, $notification);
                    if(!empty($output)){
                        echo $output . PHP_EOL;
                    }
                    if(!empty($notification)){
                        echo $notification . PHP_EOL;
                    }
                } else {
                    $command = 'a2ensite ' . $file->name;
                    Core::execute($object, $command, $output, $notification);
                    if(!empty($output)){
                        echo $output . PHP_EOL;
                    }
                    if(!empty($notification)){
                        echo $notification . PHP_EOL;
                    }
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function apache2_backup(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        $destination_dir = $object->config('project.dir.data') . 'Apache2' . $object->config('ds');
        $url = '/etc/apache2/sites-available/';
        $dir = new Dir();
        $read = $dir->read($url);
        Dir::create($destination_dir, Dir::CHMOD);
        foreach($read as $file){
            if($file->type === File::TYPE){
                $source = $file->url;
                $destination = $destination_dir . $file->name;
                if(File::exist($destination)) {
                    File::delete($destination);
                }
                File::copy($source, $destination);
                File::permission($object, [
                    'destination_dir' => $destination_dir,
                    'destination' => $destination
                ]);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function apache2_restart(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        $command = 'service apache2 restart';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
    }

    /**
     * @throws Exception
     */
    public function apache2_reload(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        $command = 'service apache2 reload';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
    }

    /**
     * @throws Exception
     */
    public function apache2_start(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        $command = 'service apache2 start';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
    }

    /**
     * @throws Exception
     */
    public function apache2_stop(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        $command = 'service apache2 stop';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function apache2_site_create($options=[]): void
    {
        $options = Core::object($options, Core::OBJECT_OBJECT);
        $object = $this->object();
        if ($object->config(Config::POSIX_ID) !== 0) {
            $exception = new Exception('Only root can configure apache2 site create...');
            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.create', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        if(
            property_exists($options, 'server') &&
            property_exists($options->server, 'admin')
        ){
            //nothing
        } else {
            $admin = $object->config('server.admin');
            if($admin){
                $options->server->admin = $admin;
            } else {
                $exception = new Exception('Please configure a server admin, or provide the option (server.admin)...');
                Event::trigger($object, 'raxon.org.basic.configure.apache2.site.create', [
                    'options' => $options,
                    'exception' => $exception
                ]);
                throw $exception;
            }
        }
        if(
            property_exists($options, 'server') &&
            property_exists($options->server, 'name')
        ){
            //nothing
        } else {
            $exception = new Exception('Please provide the option (server.name)...');
            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.create', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        if(
            property_exists($options, 'server') &&
            property_exists($options->server, 'root')
        ){
            //nothing
        } else {
            $options->server->root = $object->config('project.dir.public');
        }
        if(substr($options->server->root, -1, 1) === '/'){
            $options->server->root = substr($options->server->root, 0, -1);
        }
        $environments = [
            'production',
            'development'
        ];
        $is_missing = true;
        foreach($environments as $environment){
            if(!property_exists($options, $environment)){
                continue;
            }
            if($environment === Config::MODE_DEVELOPMENT){
                $is_missing = false;
                $explode = explode('.', $options->server->name);
                $count = count($explode);
                if($count === 2){
                    $options->server->name = $explode[0] . '.' . $object->config('localhost.extension');
                } else {
                    throw new Exception('server name should exist of domain and extension, for example: raxon.org');
                }
                if(
                    property_exists($options, 'server') &&
                    property_exists($options->server, 'alias') &&
                    is_array($options->server->alias)
                ){
                    $list = $options->server->alias;
                    foreach($list as $nr => $alias){
                        $explode = explode('.', $alias);
                        $count = count($explode);
                        if($count === 3){
                            $list[$nr] = $explode[0] . '.' . $options->server->name;
                        } else {
                            throw new Exception('server alias should exist of domain and extension, for example: raxon.org');
                        }
                    }
                    $options->server->alias = $list;
                }
            }
            if(
                property_exists($options, 'server') &&
                !property_exists($options->server, 'alias')
            ){
                $options->server->alias = [];
            }
            $parse = new Parse($object);
            $url = $object->config('controller.dir.data') . '001-site.' . $environment . '.conf';
            $read = File::read($url);
            $dir_available = '/etc/apache2/sites-available/';
            $dir = new Dir();
            $files = $dir->read($dir_available);
            if(
                $files &&
                is_array($files)
            ){
                foreach($files as $file){
                    if($file->type === File::TYPE){
                        if(
                            stristr($file->name, str_replace('.', '-', $options->server->name)) !== false &&
                            property_exists($options, 'force')
                        ){
                            if($options->force === true){
                                File::delete($file->url);
                            }
                        }
                        else if(stristr($file->name, str_replace('.', '-', $options->server->name)) !== false){
                            $exception = new Exception('Site ' . $options->server->name . ' already exists...');
                            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.create', [
                                'options' => $options,
                                'exception' => $exception
                            ]);
                            throw $exception;
                        }
                    }
                }
            }
            $is_missing = false;
            $object->set('options', $options);
            $read = $parse->compile($read, $object->data());
            $number = sprintf("%'.03d", File::count($dir_available));
            $url = $dir_available . $number . '-' . str_replace('.', '-', $options->server->name) . $object->config('extension.conf');
            File::write($url, $read);
            $command = 'chmod 640 ' . $url;
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
            $command = 'chown root:root ' . $url;
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
        }
        if($is_missing){
            throw new Exception('Please provide the option (development and/or production)...');
        }
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function apache2_site_has($options=[]): bool
    {
        $options = Core::object($options, Core::OBJECT_OBJECT);
        $object = $this->object();
        if ($object->config(Config::POSIX_ID) !== 0) {
            $exception = new Exception('Only root can configure host add...');
            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.enable', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        if (
            property_exists($options, 'server') &&
            property_exists($options->server, 'name')
        ) {
            //nothing
        } else {
            $exception = new Exception('Please provide the option (server.name)...');
            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.enable', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        $url = '/etc/apache2/sites-available/';
        $dir = new Dir();
        $read = $dir->read($url);
        $is_enabled = false;
        if ($read && is_array($read)) {
            foreach ($read as $file) {
                if ($file->type === File::TYPE) {
                    if (stristr($file->name, str_replace('.', '-', $options->server->name)) !== false) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function apache2_site_enable($options=[]): void
    {
        $options = Core::object($options, Core::OBJECT_OBJECT);
        $object = $this->object();
        if ($object->config(Config::POSIX_ID) !== 0) {
            $exception = new Exception('Only root can configure apache2_site_enable...');
            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.enable', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        if(
            property_exists($options, 'server') &&
            property_exists($options->server, 'name')
        ){
            //nothing
        } else {
            $exception = new Exception('Please provide the option (server.name)...');
            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.enable', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        $url = '/etc/apache2/sites-available/';
        $dir = new Dir();
        $read = $dir->read($url);
        $is_enabled = false;
        if($read && is_array($read)){
            foreach ($read as $file){
                if($file->type === File::TYPE){
                    if(stristr($file->name,str_replace('.', '-', $options->server->name)) !== false){
                        $command = 'a2ensite ' . $file->name;
                        Core::execute($object, $command, $output, $notification);
                        if(!empty($output)){
                            echo $output . PHP_EOL;
                        }
                        if(!empty($notification)){
                            echo $notification . PHP_EOL;
                        }
                        $is_enabled = true;
                        break;
                    }
                }
            }
        }
        if($is_enabled){
            echo 'Site ' . $options->server->name . ' enabled.' . PHP_EOL;
        } else {
            echo 'Site ' . $options->server->name . ' not found.' . PHP_EOL;
        }
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function apache2_site_disable($options=[]): void
    {
        $options = Core::object($options, Core::OBJECT_OBJECT);
        $object = $this->object();
        if ($object->config(Config::POSIX_ID) !== 0) {
            $exception = new Exception('Only root can configure apache2_site_disable...');
            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.disable', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        if(
            property_exists($options, 'server') &&
            property_exists($options->server, 'name')
        ){
            //nothing
        } else {
            $exception = new Exception('Please provide the option (server.name)...');
            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.enable', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        $url = '/etc/apache2/sites-enabled/';
        $dir = new Dir();
        $read = $dir->read($url);
        $is_disabled = false;
        if($read && is_array($read)){
            foreach ($read as $file){
                if($file->type === File::TYPE){
                    if(stristr($file->name,str_replace('.', '-', $options->server->name)) !== false){
                        $command = 'a2dissite ' . $file->name;
                        Core::execute($object, $command, $output, $notification);
                        if(!empty($output)){
                            echo $output . PHP_EOL;
                        }
                        if(!empty($notification)){
                            echo $notification . PHP_EOL;
                        }
                        $is_disabled = true;
                        break;
                    }
                }
            }
        }
        if($is_disabled){
            echo 'Site ' . $options->server->name . ' disabled.' . PHP_EOL;
        } else {
            echo 'Site ' . $options->server->name . ' not found.' . PHP_EOL;
        }
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function apache2_site_delete($options=[]): void
    {
        $options = Core::object($options, Core::OBJECT_OBJECT);
        $object = $this->object();
        if ($object->config(Config::POSIX_ID) !== 0) {
            $exception = new Exception('Only root can configure apache2_site_disable...');
            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.disable', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        if(
            property_exists($options, 'server') &&
            property_exists($options->server, 'name')
        ){
            //nothing
        } else {
            $exception = new Exception('Please provide the option (server.name)...');
            Event::trigger($object, 'raxon.org.basic.configure.apache2.site.enable', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        $url = '/etc/apache2/sites-available/';
        $dir = new Dir();
        $read = $dir->read($url);
        $is_delete = false;
        if($read && is_array($read)){
            foreach ($read as $file){
                if($file->type === File::TYPE){
                    if(stristr($file->name,str_replace('.', '-', $options->server->name)) !== false){
                        File::delete($file->url);
                        $is_delete = true;
                        break;
                    }
                }
            }
        }
        if($is_delete){
            echo 'Site ' . $options->server->name . ' deleted.' . PHP_EOL;
        } else {
            echo 'Site ' . $options->server->name . ' not found.' . PHP_EOL;
        }
    }

    /**
     * @throws Exception
     */
    public function php_restart(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        //php and apache2 should be installed by docker.
        //if there is a different sury package, there are multiple versions
        $dir = new Dir();
        $read = $dir->read('/etc/php/');
        $read = Sort::list($read)->with(['name' => 'desc']);
        $file = current($read);
        $fpm = 'php' . $file->name . '-fpm';
        $command = 'service ' . $fpm . ' restart';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
    }

    /**
     * @throws Exception
     */
    public function php_stop(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        //php and apache2 should be installed by docker.
        //if there is a different sury package, there are multiple versions
        $dir = new Dir();
        $read = $dir->read('/etc/php/');
        $read = Sort::list($read)->with(['name' => 'desc']);
        $file = current($read);
        $fpm = 'php' . $file->name . '-fpm';
        $command = 'service ' . $fpm . ' stop';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
    }

    /**
     * @throws Exception
     */
    public function php_start(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        //php and apache2 should be installed by docker.
        //if there is a different sury package, there are multiple versions
        $dir = new Dir();
        $read = $dir->read('/etc/php/');
        $read = Sort::list($read)->with(['name' => 'desc']);
        $file = current($read);
        $fpm = 'php' . $file->name . '-fpm';
        $command = 'service ' . $fpm . ' start';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
    }

    /**
     * @throws DirectoryCreateException
     * @throws Exception
     */
    public function php_backup(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        $dir = new Dir();
        $read = $dir->read('/etc/php/');
        $read = Sort::list($read)->with(['name' => 'desc']);
        $file = current($read);
        $php_version = $file->name;
        $dir_php = $object->config('project.dir.data') . 'Php/';
        $dir_version = $dir_php . $php_version . '/';
        $dir_fpm = $dir_version . 'Fpm/';
        $dir_cli = $dir_version . 'Cli/';
        $dir_fpm_pool_d = $dir_fpm . 'Pool.d/';
        Dir::create($dir_fpm, Dir::CHMOD);
        Dir::create($dir_fpm_pool_d, Dir::CHMOD);
        Dir::create($dir_cli, Dir::CHMOD);
        $command = 'cp /etc/php/' . $php_version . '/fpm/php.ini ' . $dir_fpm . 'php.ini';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'cp /etc/php/' . $php_version . '/fpm/php-fpm.conf ' . $dir_fpm . 'php-fpm.conf';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'cp /etc/php/' . $php_version . '/fpm/pool.d/www.conf ' . $dir_fpm_pool_d . 'www.conf';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        $command = 'cp /etc/php/' . $php_version . '/cli/php.ini ' . $dir_cli . 'php.ini';
        Core::execute($object, $command, $output, $notification);
        if(!empty($output)){
            echo $output . PHP_EOL;
        }
        if(!empty($notification)){
            echo $notification . PHP_EOL;
        }
        File::permission($object, [
            'dir_php' => $dir_php,
            'dir_version' => $dir_version,
            'dir_fpm' => $dir_fpm,
            'dir_fpm_pool_d' => $dir_fpm_pool_d,
            'dir_cli' => $dir_cli,
            'file_fpm_php_ini' => $dir_fpm . 'php.ini',
            'file_fpm_php_fpm_conf' => $dir_fpm . 'php-fpm.conf',
            'file_fpm_pool_d_www_conf' => $dir_fpm_pool_d . 'www.conf',
            'file_cli_php_ini' => $dir_cli . 'php.ini',
        ]);
    }

    /**
     * @throws Exception
     */
    public function php_restore(): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        $dir = new Dir();
        $read = $dir->read('/etc/php/');
        $read = Sort::list($read)->with(['name' => 'desc']);
        $file = current($read);
        $php_version = $file->name;
        if(File::exist($object->config('project.dir.data') . 'Php/' . $php_version . '/Fpm/php.ini')){
            $command = 'cp ' . $object->config('project.dir.data') . 'Php/' . $php_version . '/Fpm/php.ini /etc/php/' . $php_version . '/fpm/php.ini';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
            $command = 'chown root:root /etc/php/' . $php_version . '/fpm/php.ini';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
            $command = 'chmod 640 /etc/php/' . $php_version . '/fpm/php.ini';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
        }
        if(File::exist($object->config('project.dir.data') . 'Php/' . $php_version . '/Fpm/php-fpm.conf')){
            $command = 'cp ' . $object->config('project.dir.data') . 'Php/' . $php_version . '/Fpm/php-fpm.conf /etc/php/' . $php_version . '/fpm/php-fpm.conf';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
            $command = 'chown root:root /etc/php/' . $php_version . '/fpm/php-fpm.conf';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
            $command = 'chmod 640 /etc/php/' . $php_version . '/fpm/php-fpm.conf';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
        }
        if(File::exist($object->config('project.dir.data') . 'Php/' . $php_version . '/Fpm/Pool.d/www.conf')){
            $command = 'cp ' . $object->config('project.dir.data') . 'Php/' . $php_version . '/Fpm/Pool.d/www.conf /etc/php/' . $php_version . '/fpm/pool.d/www.conf';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
            $command = 'chown root:root /etc/php/' . $php_version . '/fpm/pool.d/www.conf';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
            $command = 'chmod 640 /etc/php/' . $php_version . '/fpm/pool.d/www.conf';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
        }
        if(File::exist($object->config('project.dir.data') . 'Php/' . $php_version . '/Cli/php.ini')){
            $command = 'cp ' . $object->config('project.dir.data') . 'Php/' . $php_version . '/Cli/php.ini /etc/php/' . $php_version . '/cli/php.ini';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
            $command = 'chown root:root /etc/php/' . $php_version . '/cli/php.ini';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
            $command = 'chmod 640 /etc/php/' . $php_version . '/cli/php.ini';
            Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo $output . PHP_EOL;
            }
            if(!empty($notification)){
                echo $notification . PHP_EOL;
            }
        }
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function openssl_init($flags, $options): void
    {
        $options = Core::object($options, Core::OBJECT_OBJECT);
        $object = $this->object();
        if ($object->config(Config::POSIX_ID) !== 0) {
            $exception = new Exception('Only root can configure openssl_init...');
            Event::trigger($object, 'raxon.org.basic.configure.openssl.init', [
                'options' => $options,
                'exception' => $exception
            ]);
            throw $exception;
        }
        if(!property_exists($options, 'country')){
            $options->country = 'NL';
        }
        if(!property_exists($options, 'state')){
            $options->state = 'Overijssel';
        }
        if(!property_exists($options, 'locality')){
            $options->locality = 'Borne';
        }
        if(!property_exists($options, 'organization')){
            $options->organization = 'universe orange';
        }
        if(!property_exists($options, 'unit')){
            $options->unit = 'Development';
        }
        if(!property_exists($options, 'name')){
            $options->name = 'raxon';
        }
        if(!property_exists($options, 'email')) {
            $options->email = 'development@universeorange.com';
        }
        if(!property_exists($options, 'keyout')){
            $options->keyout = 'key.key';
        }
        if(!property_exists($options, 'newkey')){
            $options->newkey = 'rsa:2048';
        }
        if(!property_exists($options, 'req')){
            $options->req = 'x509';
        }
        if(!property_exists($options, 'out')){
            $options->out = 'cert.pem';
        }
        if(!property_exists($options, 'days')){
            $options->days = 365;
        }
        $country = $options->country;
        $state = $options->state;
        $locality = $options->locality;
        $organization = $options->organization;
        $unit = $options->unit;
        $name = $options->name;
        $email = $options->email;
        $command = 'openssl req -' .
            $options->req .
            ' -newkey ' .
            $options->newkey .
            ' -keyout ' .
            $options->keyout.
            ' -out ' .
            $options->out .
            ' -days '.
            $options->days.
            ' -nodes -subj ' . "\"/C=$country/ST=$state/L=$locality/O=$organization/OU=$unit/CN=$name/emailAddress=$email\"";
        $dir = $object->config('project.dir.data') . 'Ssl' . $object->config('ds');
        Dir::create($dir, Dir::CHMOD);
        Dir::change($dir);
        exec($command, $output);
        echo implode(PHP_EOL, $output) . PHP_EOL;
        File::permission($object, [
            'dir' => $dir,
            'keyout' => $dir . $options->keyout,
            'out' => $dir . $options->out
        ]);
    }

    public function cron_backup($flags, $options): void
    {
        $object = $this->object();
        $url = '/etc/cron.d/raxon_org';
        $environment = $object->config('framework.environment');
        if(File::exist($url)){
            $target = $object->config('project.dir.data') .
                'Cron' .
                $object->config('ds') .
                'Cron' .
                '.' .
                $environment
            ;
            File::write($target, File::read($url));
        } else {
            //create cron file for each environment.
            $environments = [
                'development',
                'test',
                'staging',
                'replica',
                'production'
            ];

            $dir = $object->config('project.dir.data') .
                'Cron' .
                $object->config('ds')
            ;
            $source = $object->config('project.dir.package') .
                'Raxon' .
                $object->config('ds') .
                'Org' .
                $object->config('ds') .
                'Basic' .
                $object->config('ds') .
                'Data' .
                $object->config('ds') .
                'Cron'
            ;
            foreach($environments as $record){
                $url = $dir . 'Cron' . '.' . $record;
                if(!File::exist($url)){
                    Dir::create($dir, Dir::CHMOD);
                    File::write($url, File::read($source));
                    if($environment === Config::MODE_DEVELOPMENT){
                        File::permission($object, [
                            'url' => $url,
                            'dir' => $dir
                        ]);
                    }
                }
            }
        }
    }

    /**
     * @throws FileWriteException
     */
    public function cron_restore($flags=null, $options=null): void
    {
        $object = $this->object();
        $url = '/etc/cron.d/raxon_org';
        $environment = $object->config('framework.environment');
        $source = $object->config('project.dir.data') .
            'Cron' .
            $object->config('ds') .
            'Cron' .
            '.' .
            $environment
        ;
        if(File::exist($source)){
            File::write($url, File::read($source));
            $this->cron_init();

        }
    }

    /**
     * @throws ObjectException
     */
    public function cron_restart($flags, $options): void
    {
        $command = 'service cron restart';
        $object = $this->object();
        Core::execute($object, $command, $output, $notification);
        if($output){
            echo $output;
        }
        if($notification){
            echo $notification;
        }
    }

    /**
     * @throws ObjectException
     */
    public function cron_start($flags, $options): void
    {
        $command = 'service cron start';
        $object = $this->object();
        Core::execute($object, $command, $output, $notification);
        if($output){
            echo $output;
        }
        if($notification){
            echo $notification;
        }
    }

    /**
     * @throws ObjectException
     */
    public function cron_stop($flags, $options): void
    {
        $command = 'service cron stop';
        $object = $this->object();
        Core::execute($object, $command, $output, $notification);
        if($output){
            echo $output;
        }
        if($notification){
            echo $notification;
        }
    }

    /**
     * @throws ObjectException
     * @throws FileWriteException
     */
    public function cron_init(): void
    {
        $url = '/etc/crontab';
        $read = File::read($url);

        if($read){
            $read = explode(PHP_EOL, $read);
            $has_cron_d = false;
            foreach($read as $nr => $line){
                if(
                    strpos($line, 'run-parts') !== false &&
                    strpos($line, '/etc/cron.d') !== false &&
                    strpos($line, '/etc/cron.daily') === false
                ){
                    $has_cron_d = true;
                    break;
                }
            }
            $url_cron_d = '/etc/cron.d/raxon_org';
            if(!File::exist($url_cron_d)){
                $this->cron_restore();
            }
            if($has_cron_d === false) {
                $read[] = '*/1 *   * * *   root    cd / && run-parts --report /etc/cron.d';
                $read = implode(PHP_EOL, $read);
                File::write($url, $read);
                $command = 'service cron restart';
                $object = $this->object();
                Core::execute($object, $command, $output, $notification);
                if ($output) {
                    echo $output;
                }
                if ($notification) {
                    echo $notification;
                }
            }
        }
    }
}