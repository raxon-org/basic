{{$flags = flags()}}
{{$options = options()}}
{{$site.has = Package.Raxon.Basic:Main:apache2.site.has($flags, $options)}}
{{if(!is.empty($site.has))}}
true
{{else}}
false
{{/if}}