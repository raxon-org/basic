{{R3M}}
{{$options = options()}}
{{$site.has = Package.Raxon.Org.Basic:Main:apache2.site.has($options)}}
{{if(!is.empty($site.has))}}
true
{{else}}
false
{{/if}}