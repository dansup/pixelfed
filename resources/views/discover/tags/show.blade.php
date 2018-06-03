@extends('layouts.app')

@section('content')

<div class="container">
  
  <div class="profile-header row my-5">
    <div class="col-12 col-md-3">
      <div class="profile-avatar">
        <img class="img-thumbnail" src="https://placehold.it/300x300" style="border-radius:100%;" width="172px">
      </div>
    </div>
    <div class="col-12 col-md-9 d-flex align-items-center">
      <div class="profile-details">
        <div class="username-bar pb-2  d-flex align-items-center">
          <span class="h1">{{$tag->name}}</span>
        </div>
        <p class="font-weight-bold">
          {{$count}} posts
        </p>
      </div>
    </div>
  </div>

  <div class="profile-timeline mt-5 row">
    @foreach($posts as $status)
      <div class="col-12 col-md-4 mb-4">
        <a class="card info-overlay" href="{{$status->url()}}">
          <div class="square">
            <div class="square-content" style="background-image: url('{{$status->thumb()}}')"></div>
            <div class="info-overlay-text">
              <h5 class="text-white m-auto font-weight-bold">
                <span class="pr-4">
                  <span class="icon-heart pr-1"></span> {{$status->likes_count}}
                </span>
                <span>
                  <span class="icon-speech pr-1"></span> {{$status->comments_count}}
                </span>
              </h5>
            </div>
          </div>
        </a>
      </div>
    @endforeach
  </div>

</div>

@endsection
