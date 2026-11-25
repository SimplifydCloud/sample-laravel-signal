<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIdeaRequest;
use App\Models\Idea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IdeaController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', Rule::in(['Experience', 'Integrations', 'Performance'])],
            'status' => ['nullable', Rule::in(['Exploring', 'Building', 'Shipped'])],
            'sort' => ['nullable', Rule::in(['popular', 'newest'])],
        ]);
        $ideas = Idea::query()
            ->when($filters['q'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status));
        if (($filters['sort'] ?? 'popular') === 'newest') {
            $ideas->latest('id');
        } else {
            $ideas->orderByDesc('votes')->orderByDesc('id');
        }
        $voter = $this->voter($request);

        return view('board', [
            'ideas' => $ideas->paginate(8)->withQueryString(),
            'filters' => $filters,
            'total' => Idea::count(),
            'totalVotes' => Idea::sum('votes'),
            'statusCounts' => Idea::query()->select('status')->selectRaw('count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'votedIds' => DB::table('idea_votes')->where('voter', $voter)->pluck('idea_id')->all(),
        ]);
    }

    public function store(StoreIdeaRequest $request): RedirectResponse
    {
        Idea::create($request->validated());

        return to_route('ideas.index', ['sort' => 'newest'])->with('message', 'Your idea is on the board. Thanks for shaping what comes next!');
    }

    public function vote(Request $request, Idea $idea): RedirectResponse
    {
        $voter = $this->voter($request);
        $added = DB::transaction(function () use ($idea, $voter): bool {
            $inserted = DB::table('idea_votes')->insertOrIgnore(['idea_id' => $idea->id, 'voter' => $voter]);
            if ($inserted) {
                $idea->increment('votes');
            }

            return (bool) $inserted;
        });

        return back()->with('message', $added ? 'Vote counted. Good ideas deserve a little momentum.' : 'You have already voted for this idea in this browser.');
    }

    private function voter(Request $request): string
    {
        if (! $request->session()->has('voter')) {
            $request->session()->put('voter', Str::uuid()->toString());
        }

        return hash('sha256', $request->session()->get('voter'));
    }
}
