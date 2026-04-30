<table>
    <thead>
        <tr>
            <td colspan="2">{{ $header }}</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                @foreach ($row as $col)
                    <td>{{ html_entity_decode($col) }}</td> 
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>