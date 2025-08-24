{{translation.import()}}
{{$request = request()}}
Package: {{$request.package}}

{{if(!is.empty($request.module))}}Module: {{$request.module|>string.uppercase.first}}

{{/if}}

{{if(!is.empty($request.submodule))}}Submodule: {{$request.submodule|>string.uppercase.first}}

{{/if}}

[01] {{binary()}} {{$request.package}}

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
[27] {{binary()}} {{$request.package}} php restore
[28] {{binary()}} {{$request.package}} php restart
[29] {{binary()}} {{$request.package}} php start
[30] {{binary()}} {{$request.package}} php stop
[31] {{binary()}} {{$request.package}} music record

[01] {{__('info')}}
[02] {{__('setup')}}
[03] {{__('apache2')}} Apache2 options
[04] {{__('apache2.backup')}} Backup Apache2 sites into Data/Apache2
[05] {{__('apache2.reload')}} Reload Apache2 service
[06] {{__('apache2.restart')}}Restart Apache2 service
[07] {{__('apache2.restore')}} Restore Apache2 sites from Data/Apache2
[08] {{__('apache2.setup')}} Setup Apache2 service to handle the raxon_org/framework and PHP
[09] {{__('apache2.site')}} Apache2 site info
[10] {{__('apache2.site.create')}} Create an apache2 site config
[11] {{__('apache2.site.delete')}} Delete an apache2 site config
[12] {{__('apache2.site.disable')}} Disable an apache2 site config
[13] {{__('apache2.site.enable')}} Enable an apache2 site config
[14] {{__('apache2.site.has')}} Check if an apache2 site config exists
[15] {{__('apache2.start')}} Start Apache2 service
[16] {{__('apache2.stop')}} Stop Apache2 service
[17] {{__('cron.backup')}} Cron backup
[18] {{__('cron.init')}} Cron init
[19] {{__('cron.restart')}} Cron restart
[20] {{__('cron.restore')}} Cron restore
[21] {{__('cron.start')}} Cron start
[22] {{__('cron.stop')}} Cron stop
[23] {{__('openssl')}} Openssl options
[24] {{__('openssl.init')}} Initialize the openssl config for development
[25] {{__('php')}} PHP options
[26] {{__('php.backup')}} Backup PHP .ini file into Data/Php
[27] {{__('php.restore')}} Restore PHP .ini file from Data/Php
[28] {{__('php.restart')}} Restart php-fpm service
[29] {{__('php.start')}} Start php-fpm service
[30] {{__('php.stop')}} Stop php-fpm service
[31] {{__('music.record')}} Task creation of a yt-dlp download
