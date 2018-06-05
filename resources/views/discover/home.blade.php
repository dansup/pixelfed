@extends('layouts.app')

@section('content')
<div class="container mt-5 pt-3">
  <section>
    <p class="lead text-muted font-weight-bold">Discover People</p>
    <div class="row">
      @foreach($people as $profile)
      <div class="col-md-4">
        <div class="card">
          <div class="card-body p-4 text-center">
            <div class="avatar pb-3">
              <a href="{{$profile->url()}}">
                <img src="{{$profile->avatarUrl()}}" class="img-thumbnail rounded-circle" width="64px">
              </a>
            </div>
            <p class="lead font-weight-bold mb-0"><a href="{{$profile->url()}}" class="text-dark">{{$profile->username}}</a></p>
            <p class="text-muted">{{$profile->name}}</p>
            <form class="follow-form" method="post" action="/i/follow" data-id="{{$profile->id}}" data-action="follow">
              @csrf
              <input type="hidden" name="item" value="{{$profile->id}}">
              <button class="btn btn-primary font-weight-bold px-4 py-0" type="submit">Follow</button>
            </form>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </section>
  <section class="pt-5 mt-5">
    <p class="lead text-muted font-weight-bold">Explore</p>
    <div class="profile-timeline row">
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
  </section>
</div>

@endsection

@push('meta')
<meta property="og:description" content="Discover People!">
@endpush
