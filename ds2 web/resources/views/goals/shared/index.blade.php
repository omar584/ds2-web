@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="h2 mb-0">Objectifs partagés</h1>
        <p class="text-muted">Découvrez et rejoignez les objectifs d'autres utilisateurs</p>
    </div>
</div>

@if($sharedGoals->isEmpty())
    <div class="text-center py-5">
        <div class="display-1 text-muted mb-4">
            <i class="bi bi-people"></i>
        </div>
        <h2 class="h4 text-muted mb-3">Aucun objectif partagé pour le moment</h2>
        <p class="text-muted">Soyez le premier à partager un objectif avec la communauté !</p>
        <a href="{{ route('goals.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Créer un objectif
        </a>
    </div>
@else
    <div class="row g-4">
        @foreach($sharedGoals as $goal)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">{{ $goal->title }}</h5>
                            <span class="badge bg-{{ $goal->visibility === 'public' ? 'success' : 'info' }}">
                                {{ ucfirst($goal->visibility) }}
                            </span>
                        </div>
                        
                        <p class="card-text text-muted small mb-3">{{ Str::limit($goal->description, 100) }}</p>
                        
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $goal->progress }}%"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                            <span>
                                <i class="bi bi-person"></i> {{ $goal->user->name }}
                            </span>
                            <span>
                                <i class="bi bi-people"></i> 
                                {{ $goal->children()->count() }} participants
                            </span>
                        </div>

                        @if($goal->location_name)
                            <div class="small text-muted mb-3">
                                <i class="bi bi-geo-alt"></i> {{ $goal->location_name }}
                            </div>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="bi bi-eye"></i> Voir détails
                            </a>
                            @if($goal->user_id !== Auth::id() && !$goal->children()->where('user_id', Auth::id())->exists())
                                <form action="{{ route('goals.shared.join', $goal) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-person-plus"></i> Rejoindre
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $sharedGoals->links() }}
    </div>
@endif
@endsection 