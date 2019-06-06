<div class="review">
    <a href="profile.html"><span class="username">{{$review->username}}</span></a>
    <i class="fas fa-flag" data-toggle="modal" data-target="#reportModal"></i>
    <div class="float-right product-rating">

        @showRating(floor($review->rating))
    </div>
    <p>{{$review->comment}}</p>
    <span class="date">{{$review->date_time}}</span>
</div>