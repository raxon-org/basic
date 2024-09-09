{{R3M}}
{{$request = request()}}
Package: {{$request.package}}

Module: {{$request.module|uppercase.first}}

{{if(!is.empty($request.submodule))}}
Submodule: {{$request.submodule|uppercase.first}}

{{/if}}

[01] {{binary()}} {{$request.package}} {{$request.module}}

[02] {{binary()}} {{$request.package}} {{$request.module}} backup
[03] {{binary()}} {{$request.package}} {{$request.module}} restart
[04] {{binary()}} {{$request.package}} {{$request.module}} restore
[05] {{binary()}} {{$request.package}} {{$request.module}} start
[06] {{binary()}} {{$request.package}} {{$request.module}} stop

[01] PHP options
[02] Backup PHP .ini file into Data/Php
[03] Restart php-fpm service
[04] Restore PHP .ini file from Data/Php
[05] Start php-fpm service
[06] Stop php-fpm service