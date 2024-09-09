{{R3M}}
{{$register = Package.Raxon.Org.Basic:Init:register()}}
{{if(!is.empty($register))}}
{{Package.Raxon.Org.Basic:Import:role.system()}}
{{Package.Raxon.Org.Basic:Main:apache2.setup()}}
{{Package.Raxon.Org.Basic:Main:openssl.init(flags(), options())}}
{{Package.Raxon.Org.Basic:Main:cron.init()}}
{{Package.Raxon.Org.Basic:Main:apache2.restore()}}
{{Package.Raxon.Org.Basic:Main:apache2.backup()}}
{{Package.Raxon.Org.Basic:Main:apache2.stop()}}
{{Package.Raxon.Org.Basic:Main:apache2.start()}}
{{/if}}