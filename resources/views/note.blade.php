<!DOCTYPE html>
<html lang="ru">
    <style>
        td {
            margin: 0 10px;
        }
    </style>
   <body>
      <div id = 'msg'>
        Вам поступила новая заявка!<br>
         Информация:<br>
          <table>
              <tr><td>Товар:</td></tr>
              <tr><td>#</td><td>Фото</td><td>Наименование</td><td>Стоимость</td><td>Кол-во</td><td>Сумма</td></tr>
              @if $info_ids.length > 1
                  @foreach ($info_ids as $product)
                      <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td><img style="width: 100px; height: 100px" src="https://api.inbloomshop.ru/public/api/file/oneblob/stock/{{ $info_ids[$loop->index] }}"></td>
                          <td>{{ $info_products[$loop->index] }}</td>
                          <td>{{ $info_costs[$loop->index] }} RUB</td>
                          <td>{{ $info_quantity[$loop->index] }}</td>
                          <td>{{ $info_costs[$loop->index] * $info_quantity[$loop->index] }} RUB</td>
                      </tr>
                  @endforeach
              @else
                  <tr>
                      <td>0</td>
                      <td><img style="width: 100px; height: 100px" src="https://api.inbloomshop.ru/public/api/file/oneblob/stock/{{ $info_ids[0] }}"></td>
                      <td>{{ $info_products[0] }}</td>
                      <td>{{ $info_costs[0] }} RUB</td>
                      <td>{{ $info_quantity[0] }}</td>
                      <td>{{ $info_costs[0] * $info_quantity[0] }} RUB</td>
                  </tr>
              @endif
              <tr><td>Сумма: </td><td>{{ $value }} RUB</td></tr>
              <tr><td>Город: </td><td>{{ $city }}</td></tr>
              <tr><td>Email: </td><td>{{ $info_email }}</td></tr>
              <tr><td>Телефон: </td><td>{{ $phone }}</td></tr>
              @if ($type == 2)
                  <tr><td>Имя партнера: </td><td>{{ $name }}</td></tr>
              @else
                  <tr><td>Имя: </td><td>{{ $name }}</td></tr>
              @endif
          </table>
      </div>
   </body>
</html>
