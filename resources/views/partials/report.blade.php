<tr data-id="{{$report->id}}">
    <th scope="row"><a href="/product/{{$report->id_product}}">{{$report->name}}</a></th>
    <td>
        {{$report->username}}
    </td>
    <td>
        {{-- @if(!$report->has_deleted)
            Enabled
        @else
            Disabled
        @endif --}}
        {{$report->reason}}
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm updateReport" data-id={{$report->id}} data-toggle="modal" data-target="#disableReview">
            <i class="fas fa-minus-circle"></i>
            <span class="button-text">Disable</span>
        </button>
    </td>
</tr>