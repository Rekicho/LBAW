<div class="review">
    <span class="username">{{$review->username}}</span>
    <i class="report fas fa-flag" data-toggle="modal" data-target="#reportModal"  data-id="{{$review->id}}"></i>
    <div class="float-right product-rating">
        @showRating(floor($review->rating))
    </div>
    <p>{{$review->comment}}</p>
    <span class="date">{{$review->date_time}}</span>
</div>