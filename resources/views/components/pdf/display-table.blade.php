<table {{ $attributes->merge(['class' => 'border-lightgrey w-full']) }}>
    <thead>
        @isset($title)
            <tr>
                <td colspan="{{ count($rows) }}" class="bg-gainsboro text-center">{{ $title }}</td>
            </tr>
        @endisset
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr @class($row['class'] ?? [])>
                @foreach (($row['cells']) as $cell)
                    <td
                        @if (is_array($cell))
                            @style($cell['style'] ?? [])
                            @class($cell['class'] ?? [])
                            @isset($cell['colspan'])
                               colspan="{{ $cell['colspan'] }}" 
                            @endisset
                            @isset($col['rowspan'])
                                rowspan="{{ $cell['rowspan'] }}" 
                            @endisset
                        @endif>
                        {{ html_entity_decode(is_array($cell) ? $cell['text'] : $cell) }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>