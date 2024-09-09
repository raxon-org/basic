{{R3M}}
{{$request = request()}}
Package: {{$request.package}}

Module: {{$request.module|uppercase.first}}

{{if(!is.empty($request.submodule))}}
Submodule: {{$request.submodule|uppercase.first}}

{{/if}}

[01] {{binary()}} {{$request.package}} {{$request.module}}

[02] {{binary()}} {{$request.package}} {{$request.module}} backup
[03] {{binary()}} {{$request.package}} {{$request.module}} reload
[04] {{binary()}} {{$request.package}} {{$request.module}} restart
[05] {{binary()}} {{$request.package}} {{$request.module}} restore
[06] {{binary()}} {{$request.package}} {{$request.module}} setup
[07] {{binary()}} {{$request.package}} {{$request.module}} site
[08] {{binary()}} {{$request.package}} {{$request.module}} site create
[09] {{binary()}} {{$request.package}} {{$request.module}} site delete
[10] {{binary()}} {{$request.package}} {{$request.module}} site disable
[11] {{binary()}} {{$request.package}} {{$request.module}} site enable
[12] {{binary()}} {{$request.package}} {{$request.module}} site has
[13] {{binary()}} {{$request.package}} {{$request.module}} start
[14] {{binary()}} {{$request.package}} {{$request.module}} stop

[01] This info
[02] Backup Apache2 sites into Data/Apache2
[03] Reload Apache2 service
[04] Restart Apache2 service
[05] Restore Apache2 sites from Data/Apache2
[06] Setup Apache2 service to handle the raxon_org/framework and PHP
[07] Apache2 site info
[08] Create an apache2 site config
[09] Delete an apache2 site config
[10] Disable an apache2 site config
[11] Enable an apache2 site config
[12] Check if an apache2 site config exists
[13] Start Apache2 service
[14] Stop Apache2 service
