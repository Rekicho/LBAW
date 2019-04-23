<article class="review">
    <a href="profile.html"><span class="username">{{$review->username}}</span></a>
    <i class="fas fa-flag" data-toggle="modal" data-target="#reportModal"></i>
    <div class="float-right product-rating">
        {{-- @for($i=0; $i<$review->rating; $i++)
        <i class="fas fa-star"></i>
        @endfor
        @for($i=0; $i< 5 - $review->rating; $i++)
        <i class="far fa-star"></i>
        @endfor --}}

        @showRating($review->rating)
    </div>
    <p>{{$review->comment}}</p>
    <span class="date">{{$review->date_time}}</span>
</article>