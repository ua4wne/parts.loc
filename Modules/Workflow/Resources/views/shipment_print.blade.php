<!DOCTYPE html>
<html>
<head>
    <title>Invoice Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        th {text-align: left}
        td {text-align: right}
    </style>
</head>
<body>
<div style="width: 100%; max-width: 960px; margin: auto">
    <h3>{{ $head }}</h3>
    <h4>{{ $doc->created_at }}</h4>
    <table width="100%">
        <tr>
            <th>Основание</th>
            <td>Заказ клиента №{{ $doc->sale->doc_num }}</td>
        </tr>
        <tr>
            <th>Склад</th>
            <td>{{ $doc->warehouse->title }}</td>
        </tr>
        <tr>
            <th>Менеджер</th>
            <td>{{ $doc->author->name }}</td>
        </tr>
        <tr>
            <th>Ответственный</th>
            <td>{{ $doc->user->name }}</td>
        </tr>
        <tr>
            <th>Место сборки</th>
            <td>{{ $doc->dst->title }}</td>
        </tr>
        <tr>
            <th>Приоритет</th>
            <td>{{ $doc->rank }}</td>
        </tr>
        <tr>
            <th colspan="2">
                <h3>Задания на перемещение</h3>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%" cellpadding="0" cellspacing="0" border="1">
                    <thead>
                    <tr style="background-color: #eee">
                        <th style="text-align: left; padding: 5px 10px;">Из ячейки</th>
                        <th style="text-align: center; padding: 5px 10px;">В ячейку</th>
                        <th style="text-align: center; padding: 5px 10px;">Номенклатура</th>
                        <th style="text-align: right; padding: 5px 10px;">Кол-во</th>
                        <th style="text-align: center; padding: 5px 10px;">Ед.изм</th>
                        <th style="text-align: right; padding: 5px 10px;">Отметка о выполнении</th>
                    </tr>
                    </thead>
                    @if($rows)
                        <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td style="text-align: left; padding: 5px 10px;">{{ $row->src->title }}</td>
                                <td style="text-align: center; padding: 5px 10px;">{{ $row->dest_location }}</td>
                                <td style="text-align: center; padding: 5px 10px;">{{ $row->good->title }}</td>
                                <td style="text-align: right; padding: 5px 10px;">{{ $row->qty }}</td>
                                <td style="text-align: right; padding: 5px 10px;">{{ $row->unit->title }}</td>
                                <td style="text-align: right; padding: 5px 10px;"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endif
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
