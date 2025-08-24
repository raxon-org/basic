{{translation.import()}}
{{$request = request()}}
{{$selected = (int) parameter($request.package, 1)}}
{{$list = parse.read(config('controller.dir.data') + 'Command.json', true, (object) ['array_fast' => true])}}
{{$sort = Sort::list($list.command)}}
{{$list.command = $sort->with(['command' => 'asc'])}}
Package: {{$request.package}}

{{if(!is.empty($request.module))}}Module: {{$request.module|>string.uppercase.first}}

{{/if}}
{{if(!is.empty($list.command))}}
{{$nr = 1}}Commands:
{{foreach($list.command as $item)}}
{{$key = $nr}}
{{if($key < 10)}}
{{$key = '0' + $key}}
{{/if}}[{{$key}}] {{$item.command}}

{{$nr++}}
{{/foreach}}
{{$nr = 1}}
Description:
{{foreach($list.command as $item)}}
{{$key = $nr}}
{{if($key < 10)}}
{{$key = '0' + $key}}
{{/if}}[{{$key}}] {{$item.description}}

{{$nr++}}
{{/foreach}}
{{/if}}