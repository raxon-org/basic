{{R3M}}
{{$request = request()}}
Package: {{$request.package}}

Module: {{$request.module|uppercase.first}}

{{if(!is.empty($request.submodule))}}
Submodule: {{$request.submodule|uppercase.first}}

{{/if}}

[01] {{binary()}} {{$request.package}} {{$request.module}}

[02] {{binary()}} {{$request.package}} {{$request.module}} init

[01] Openssl options
[02] Init openssl for development
