@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="h2 mb-0">Mes Objectifs</h1>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('goals.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouvel Objectif
        </a>
    </div>
</div>

@if($goals->isEmpty())
    <div class="text-center py-5">
        <div class="display-1 text-muted mb-4">
            <i class="bi bi-clipboard"></i>
        </div>
        <h2 class="h4 text-muted mb-3">Aucun objectif pour le moment</h2>
        <p class="text-muted">Commencez par créer votre premier objectif pour tracer votre chemin vers le succès !</p>
        <a href="{{ route('goals.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Créer mon premier objectif
        </a>
    </div>
@else
    <div class="row g-4">
        @foreach($goals as $goal)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">{{ $goal->title }}</h5>
                            <span class="badge bg-{{ $goal->visibility === 'private' ? 'secondary' : ($goal->visibility === 'friends' ? 'info' : 'success') }}">
                                {{ ucfirst($goal->visibility) }}
                            </span>
                        </div>
                        
                        <p class="card-text text-muted small mb-3">{{ Str::limit($goal->description, 100) }}</p>
                        
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $goal->progress }}%"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                            <span>
                                <i class="bi bi-calendar"></i>
                                {{ $goal->deadline ? $goal->deadline->format('d/m/Y') : 'Pas de deadline' }}
                            </span>
                            <span>{{ number_format($goal->progress) }}% complété</span>
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
                            @if($goal->user_id === Auth::id())
                                <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet objectif ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection 