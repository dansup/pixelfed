<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{
    Hashtag,
    Like,
    Media,
    Notification,
    Profile,
    StatusHashtag,
    Status,
};
use Auth,Cache;
use Carbon\Carbon;
use League\Fractal;
use App\Transformer\Api\{
    AccountTransformer,
    StatusTransformer,
};
use App\Jobs\StatusPipeline\NewStatusPipeline;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class InternalApiController extends Controller
{
    protected $fractal;

    public function __construct()
    {
        $this->middleware('auth');
        $this->fractal = new Fractal\Manager();
        $this->fractal->setSerializer(new ArraySerializer());
    }

    public function compose(Request $request)
    {
        $this->validate($request, [
            'caption' => 'nullable|string',
            'media.*'   => 'required',
            'media.*.id' => 'required|integer|min:1',
            'media.*.filter' => 'nullable|string|max:30',
            'media.*.license' => 'nullable|string|max:80',
            'visibility' => 'required|string|in:public,private|min:2|max:10'
        ]);

        $profile = Auth::user()->profile;
        $visibility = $request->input('visibility');
        $medias = $request->input('media');
        $attachments = [];
        $status = new Status;

        foreach($medias as $k => $media) {
            $m = Media::findOrFail($media['id']);
            if($m->profile_id !== $profile->id || $m->status_id) {
                abort(403, 'Invalid media id');
            }
            $m->filter_class = $media['filter'];
            $m->license = $media['license'];
            $m->caption = strip_tags($media['alt']);
            $m->order = isset($media['cursor']) && is_int($media['cursor']) ? (int) $media['cursor'] : $k;
            if($media['cw'] == true) {
                $m->is_nsfw = true;
                $status->is_nsfw = true;
            }
            $m->save();
            $attachments[] = $m;
        }

        $status->caption = strip_tags($request->caption);
        $status->visibility = 'draft';
        $status->scope = 'draft';
        $status->profile_id = $profile->id;
        $status->save();

        foreach($attachments as $media) {
            $media->status_id = $status->id;
            $media->save();
        }

        $status->visibility = $visibility;
        $status->scope = $visibility;
        $status->save();

        NewStatusPipeline::dispatch($status);

        return $status->url();
    }

    public function notifications(Request $request)
    {
        $this->validate($request, [
          'page' => 'nullable|min:1|max:3',
        ]);
        $profile = Auth::user()->profile;
        $timeago = Carbon::now()->subMonths(6);
        $notifications = Notification::with('actor')
        ->whereProfileId($profile->id)
        ->whereDate('created_at', '>', $timeago)
        ->orderBy('id', 'desc')
        ->simplePaginate(30);
        $notifications = $notifications->map(function($k, $v) {
            return [
                'id' => $k->id,
                'action' => $k->action,
                'message' => $k->message,
                'rendered' => $k->rendered,
                'actor' => [
                    'avatar' => $k->actor->avatarUrl(),
                    'username' => $k->actor->username,
                    'url' => $k->actor->url(),
                ],
                'url' => $k->item->url()
            ];
        });
        return response()->json($notifications, 200, [], JSON_PRETTY_PRINT);
    }

    public function discover(Request $request)
    {
        $profile = Auth::user()->profile;
        
        $following = Cache::get('feature:discover:following:'.$profile->id, []);
        $people = Profile::select('id', 'name', 'username')
            ->with('avatar')
            ->inRandomOrder()
            ->whereHas('statuses')
            ->whereNull('domain')
            ->whereNotIn('id', $following)
            ->whereIsPrivate(false)
            ->take(3)
            ->get();

        $posts = Status::select('id', 'caption', 'profile_id')
          ->whereHas('media')
          ->whereHas('profile', function($q) {
            $q->where('is_private', false);
          })
          ->whereIsNsfw(false)
          ->whereVisibility('public')
          ->where('profile_id', '<>', $profile->id)
          ->whereNotIn('profile_id', $following)
          ->withCount(['comments', 'likes'])
          ->orderBy('created_at', 'desc')
          ->take(21)
          ->get();

        $res = [
            'people' => $people->map(function($profile) {
                return [
                    'avatar' => $profile->avatarUrl(),
                    'name' => $profile->name,
                    'username' => $profile->username,
                    'url'   => $profile->url(),
                ];
            }),
            'posts' => $posts->map(function($post) {
                return [
                    'url' => $post->url(),
                    'thumb' => $post->thumb(),
                    'comments_count' => $post->comments_count,
                    'likes_count' => $post->likes_count,
                ];
            })
        ];
        return response()->json($res, 200, [], JSON_PRETTY_PRINT);
    }
}
