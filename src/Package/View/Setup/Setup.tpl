{{R3M}}
{{$register = Package.Raxon.Basic:Init:register()}}
{{if(!is.empty($register))}}
{{Package.Raxon.Basic:Import:role.system()}}
{{Package.Raxon.Basic:Main:apache2.setup()}}
{{Package.Raxon.Basic:Main:openssl.init(flags(), options())}}
{{Package.Raxon.Basic:Main:cron.init()}}
{{Package.Raxon.Basic:Main:apache2.restore()}}
{{Package.Raxon.Basic:Main:apache2.backup()}}
{{Package.Raxon.Basic:Main:apache2.stop()}}
{{Package.Raxon.Basic:Main:apache2.start()}}
{{/if}}