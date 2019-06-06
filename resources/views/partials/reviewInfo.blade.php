<tr data-id="{{$review->id}}">
    <th scope="row"><a href="/product/{{$review->id_product}}">{{$review->name}}</a></th>
    <td>
        {{$review->username}}
    </td>
    <td>
        @showRating(floor($review->rating))
    </td>
    <td>
  </tr>