<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\{Follower, Status, User};

class TimelineController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    public function personal()
    {
      // TODO: Use redis for timelines
      $following = Follower::whereProfileId(Auth::user()->profile->id)->pluck('following_id');
      $following->push(Auth::user()->profile->id);
      $timeline = Status::whereHas('media')
                  ->whereNull('in_reply_to_id')
                  ->whereIn('profile_id', $following)
                  ->orderBy('id','desc')
                  ->withCount(['comments', 'likes'])
                  ->simplePaginate(10);
      return view('timeline.personal', compact('timeline'));
    }

    public function local()
    {
      // TODO: Use redis for timelines
      $timeline = Status::whereHas('media')
                  ->whereNull('in_reply_to_id')
                  ->orderBy('id','desc')
                  ->withCount(['comments', 'likes'])
                  ->simplePaginate(10);
      return view('timeline.public', compact('timeline'));
    }

}
