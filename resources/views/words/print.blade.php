<!doctype html>
<html>
<head>
</head>
<body>
    <table>
        <tbody>
            @foreach($words as $word)
            <tr>
                <td class="word" style="width:5%">{{ $word->word }}</td>
                <td class="phonetics" style="width:5%">{{ $word->phonetics }}</td>
                <td class="senses">{{ implode('; ', array_slice($word->senses,0,2)) }}</td>
                <td class="attrs" style="width:22%">{{ implode('; ', array_slice($word->attrs,0,2)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
