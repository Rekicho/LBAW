<tr data-id="{{$staff_member->id}}">
    <th scope="row">{{$staff_member->username}}</th>
    <td>
        @if($staff_member->is_enabled)
            Enabled
        @else
            Disabled
        @endif
    </td>
    <td>
    @if($staff_member->is_enabled)
        <button type="button" class="btn btn-danger btn-sm updateMember" data-id={{$staff_member->id}} data-toggle="modal" data-target="#confirmDisable">
            <i class="fas fa-minus-circle"></i>
            <span class="button-text">Disable</span>
          </button>
    @else
    <button type="button" class="btn btn-success btn-sm updateMember" data-id={{$staff_member->id}} data-toggle="modal" data-target="#confirmEnable">
            <i class="fas fa-plus-circle"></i>
            <span class="button-text">Enable</span>
        </button>
    @endif
    </td>
  </tr>