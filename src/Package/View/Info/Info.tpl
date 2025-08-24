{{translation.import()}}
{{$request = request()}}
Package: {{$request.package}}

{{if(!is.empty($request.module))}}Module: {{$request.module|>string.uppercase.first}}

{{/if}}Commands:
{{if(!is.empty($request.submodule))}}Submodule: {{$request.submodule|>string.uppercase.first}}

{{/if}}[01] {{binary()}} {{$request.package}}

[02] {{binary()}} {{$request.package}} setup
[03] {{binary()}} {{$request.package}} apache2
[04] {{binary()}} {{$request.package}} apache2 backup
[05] {{binary()}} {{$request.package}} apache2 reload
[06] {{binary()}} {{$request.package}} apache2 restart
[07] {{binary()}} {{$request.package}} apache2 restore
[08] {{binary()}} {{$request.package}} apache2 setup
[09] {{binary()}} {{$request.package}} apache2 site
[10] {{binary()}} {{$request.package}} apache2 site create
[11] {{binary()}} {{$request.package}} apache2 site delete
[12] {{binary()}} {{$request.package}} apache2 site disable
[13] {{binary()}} {{$request.package}} apache2 site enable
[14] {{binary()}} {{$request.package}} apache2 site has
[15] {{binary()}} {{$request.package}} apache2 start
[16] {{binary()}} {{$request.package}} apache2 stop
[17] {{binary()}} {{$request.package}} cron backup
[18] {{binary()}} {{$request.package}} cron init
[19] {{binary()}} {{$request.package}} cron restart
[20] {{binary()}} {{$request.package}} cron restore
[21] {{binary()}} {{$request.package}} cron start
[22] {{binary()}} {{$request.package}} cron stop
[23] {{binary()}} {{$request.package}} openssl
[24] {{binary()}} {{$request.package}} openssl init
[25] {{binary()}} {{$request.package}} php
[26] {{binary()}} {{$request.package}} php backup
[27] {{binary()}} {{$request.package}} php restart
[28] {{binary()}} {{$request.package}} php restore
[29] {{binary()}} {{$request.package}} php start
[30] {{binary()}} {{$request.package}} php stop
[31] {{binary()}} {{$request.package}} music record

Description:
[01] {{__('info')}}

[02] {{__('setup')}}

[03] {{__('apache2')}} 

[04] {{__('apache2.backup')}}

[05] {{__('apache2.reload')}}

[06] {{__('apache2.restart')}}

[07] {{__('apache2.restore')}}

[08] {{__('apache2.setup')}}

[09] {{__('apache2.site')}}

[10] {{__('apache2.site.create')}}

[11] {{__('apache2.site.delete')}} 

[12] {{__('apache2.site.disable')}} 

[13] {{__('apache2.site.enable')}} 

[14] {{__('apache2.site.has')}} 

[15] {{__('apache2.start')}} 

[16] {{__('apache2.stop')}}

[17] {{__('cron.backup')}} 

[18] {{__('cron.init')}} 

[19] {{__('cron.restart')}} 

[20] {{__('cron.restore')}} 

[21] {{__('cron.start')}} 

[22] {{__('cron.stop')}} 

[23] {{__('openssl')}} 

[24] {{__('openssl.init')}} 

[25] {{__('php')}} 

[26] {{__('php.backup')}} 

[27] {{__('php.restart')}} 

[28] {{__('php.restore')}} 

[29] {{__('php.start')}} 

[30] {{__('php.stop')}} 

[31] {{__('music.record')}} 

