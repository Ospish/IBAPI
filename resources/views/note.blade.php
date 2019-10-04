<!DOCTYPE html>
@php
if ($type == 2) $typestr = 'stock';
else $typestr = 'store';
$rowstyle =  'border-bottom-color:#333333;border-bottom-style:solid;border-bottom-width:1px;';
$cellstyle = 'border-bottom-color:#333333;border-bottom-style:solid;border-bottom-width:1px; padding:5px 10px 5px 10px;text-align:left;';
@endphp
<html lang="ru">
   <body>
      <div id = 'msg'>
          <p>Вам поступила новая заявка!</p>
          <p>Информация:</p>
          <table>
              <tr><td>Товар:</td></tr>
                  @if ($type != 1)
                      <tr>
                          <th style="{{ $cellstyle }}">#</th>
                          <th style="{{ $cellstyle }}">Фото</th>
                          <th style="{{ $cellstyle }}">Наименование</th>
                          <th style="{{ $cellstyle }}">Стоимость</th>
                          <th style="{{ $cellstyle }}">Кол-во</th>
                          <th style="{{ $cellstyle }}">Сумма</th>
                      </tr>
                      @foreach ($productIds as $product)
                          <tr style="{{ $rowstyle }}">
                              <td style="{{ $cellstyle }} width: 10px">{{ $loop->iteration }}</td>
                              <td style="{{ $cellstyle }} width: 65px"><img style="width: 65px; height: 65px" src="https://api.inbloomshop.ru/public/api/file/oneblob/{{ $typestr }}/{{ $productIds[$loop->index] }}"></td>
                              <td style="{{ $cellstyle }}">{{ $productNames[$loop->index] }}</td>
                              <td style="{{ $cellstyle }} white-space:nowrap;">{{ $costs[$loop->index] }} RUB</td>
                              <td style="{{ $cellstyle }}">{{ $quantity[$loop->index] }}</td>
                              <td style="{{ $cellstyle }} white-space:nowrap;">{{ $costs[$loop->index] * $quantity[$loop->index] }} RUB</td>
                          </tr>
                      @endforeach
                      @if ($type == 2)
                        <tr><th></th><th></th><th></th><th></th><th style="padding:5px 10px 5px 10px;text-align:left;">Сумма: </th><th style="padding:5px 10px 5px 10px;text-align:left;">{{ $value }} RUB</th></tr>
                      @endif
                  @else
                      <tr>
                          <th style="{{ $cellstyle }}">#</th>
                          <th style="{{ $cellstyle }}">Наименование</th>
                          <th style="{{ $cellstyle }}">Цвета</th>
                      </tr>
                      @foreach ($productNames as $product)
                          <tr style="{{ $rowstyle }}">
                              <td style="{{ $cellstyle }} width: 10px">{{ $loop->iteration }}</td>
                              <td style="{{ $cellstyle }}">{{ $productNames[$loop->index] }}</td>
                              <td style="{{ $cellstyle }}">
                              @foreach ($costs[$loop->index] as $index => $colors)
                                  @if ($index > 0), @endif
                                      {{ trim($colors) }}
                              @endforeach
                              </td>
                          </tr>
                      @endforeach
                      <tr style="{{ $rowstyle }}">
                          <td style="{{ $cellstyle }} width: 10px">{{ count($productNames)+1 }}</td>
                          <td style="{{ $cellstyle }}">Коробка</td>
                          <td style="{{ $cellstyle }}">{{ $boxcolor }}</td>
                      </tr>
                @endif
          </table>
          <table>
              @if ($type == 0)
                  <tr><td>Имя клиента: </td><td>{{ $name }}</td></tr>
                  <tr><td>Выбранный город: </td><td>{{ $city }}</td></tr>
                  <tr><td>Адрес геолокации: </td><td>{{ $geo }}</td></tr>
                  <tr><td>Дата получения: </td><td>{{ $date }}</td></tr>
              @elseif ($type == 1)
                  <tr><td>Размер букета: </td><td>{{ $size }}</td></tr>
                  <tr><td>Имя клиента: </td><td>{{ $name }}</td></tr>
                  <tr><td>Выбранный город: </td><td>{{ $city }}</td></tr>
                  <tr><td>Адрес геолокации: </td><td>{{ $geo }}</td></tr>
                  <tr><td>Дата получения: </td><td>{{ $date }}</td></tr>
                  <tr><td>Поздравление: </td><td>{{ $congrats }}</td></tr>
              @elseif ($type == 2)
                  <tr><td>Имя партнера: </td><td>{{ $name }}</td></tr>
                  <tr><td>Город партнера: </td><td>{{ $city }}</td></tr>
                  <tr><td>Email: </td><td>{{ $email }}</td></tr>

              @endif
              <tr><td>Телефон: </td><td>{{ $phone }}</td></tr>

          </table>
      </div>
   </body>
</html>
