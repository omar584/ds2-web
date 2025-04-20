@extends('layouts.app')

@push('styles')
<style>
    #goalMap {
        height: 300px;
        border-radius: 0.375rem;
    }
    .step-list {
        max-height: 500px;
        overflow-y: auto;
    }
    .step-item {
        transition: all 0.3s ease;
    }
    .step-item.completed {
        opacity: 0.7;
    }
    .step-item.completed .step-title {
        text-decoration: line-through;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="h2 mb-0">{{ $goal->title }}</h1>
        <p class="text-muted mb-0">
            <i class="bi bi-folder"></i> {{ ucfirst($goal->category) }}
            <span class="mx-2">•</span>
            <i class="bi bi-eye"></i> {{ ucfirst($goal->visibility) }}
        </p>
    </div>
    <div class="col-md-4 text-md-end">
        @if($goal->user_id === Auth::id())
            <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editGoalModal">
                <i class="bi bi-pencil"></i> Modifier
            </button>
            <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet objectif ?')">
                    <i class="bi bi-trash"></i> Supprimer
                </button>
            </form>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Description</h5>
                <p class="card-text">{{ $goal->description ?: 'Aucune description' }}</p>
                
                <hr>
                
                <h5 class="card-title">Progression</h5>
                <div class="progress mb-3" style="height: 20px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $goal->progress }}%">
                        {{ number_format($goal->progress) }}%
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col">
                        <h6 class="text-muted mb-1">Date de création</h6>
                        <p class="mb-0">{{ $goal->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="col">
                        <h6 class="text-muted mb-1">Date limite</h6>
                        <p class="mb-0">{{ $goal->deadline ? $goal->deadline->format('d/m/Y') : 'Non définie' }}</p>
                    </div>
                    <div class="col">
                        <h6 class="text-muted mb-1">Étapes complétées</h6>
                        <p class="mb-0">{{ $goal->steps->where('is_completed', true)->count() }} / {{ $goal->steps->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">Étapes</h5>
                <div class="step-list">
                    @forelse($goal->steps as $step)
                        <div class="step-item mb-3 {{ $step->is_completed ? 'completed' : '' }}" id="step-{{ $step->id }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                {{ $step->is_completed ? 'checked' : '' }}
                                                onchange="toggleStep({{ $goal->id }}, {{ $step->id }}, this)"
                                                {{ $goal->user_id !== Auth::id() ? 'disabled' : '' }}>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="mb-1 step-title">{{ $step->title }}</h6>
                                            @if($step->due_date)
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar"></i> 
                                                    Date limite : {{ $step->due_date->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            @if($step->is_completed)
                                                <span class="badge bg-success">Complété</span>
                                            @elseif($step->due_date && $step->due_date->isPast())
                                                <span class="badge bg-danger">En retard</span>
                                            @else
                                                <span class="badge bg-primary">En cours</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">Aucune étape définie</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        @if($goal->latitude && $goal->longitude)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Localisation</h5>
                    <div id="goalMap" class="mb-3"></div>
                    <p class="card-text small text-muted mb-0">
                        <i class="bi bi-geo-alt"></i> {{ $goal->location_name }}
                    </p>
                </div>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Badges</h5>
                <div class="text-center py-4">
                    <p class="text-muted mb-0">Fonctionnalité à venir</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if($goal->user_id === Auth::id())
    <!-- Modal d'édition -->
    <div class="modal fade" id="editGoalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('goals.update', $goal) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier l'objectif</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                value="{{ $goal->title }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                rows="3">{{ $goal->description }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Catégorie</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="education" {{ $goal->category === 'education' ? 'selected' : '' }}>Éducation</option>
                                        <option value="career" {{ $goal->category === 'career' ? 'selected' : '' }}>Carrière</option>
                                        <option value="health" {{ $goal->category === 'health' ? 'selected' : '' }}>Santé</option>
                                        <option value="fitness" {{ $goal->category === 'fitness' ? 'selected' : '' }}>Sport</option>
                                        <option value="personal" {{ $goal->category === 'personal' ? 'selected' : '' }}>Personnel</option>
                                        <option value="travel" {{ $goal->category === 'travel' ? 'selected' : '' }}>Voyage</option>
                                        <option value="other" {{ $goal->category === 'other' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="deadline" class="form-label">Date limite</label>
                                    <input type="date" class="form-control" id="deadline" name="deadline" 
                                        value="{{ $goal->deadline ? $goal->deadline->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="visibility" class="form-label">Visibilité</label>
                            <select class="form-select" id="visibility" name="visibility" required>
                                <option value="private" {{ $goal->visibility === 'private' ? 'selected' : '' }}>Privé</option>
                                <option value="friends" {{ $goal->visibility === 'friends' ? 'selected' : '' }}>Amis</option>
                                <option value="public" {{ $goal->visibility === 'public' ? 'selected' : '' }}>Public</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    @if($goal->latitude && $goal->longitude)
        // Initialisation de la carte
        const map = L.map('goalMap').setView([{{ $goal->latitude }}, {{ $goal->longitude }}], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        L.marker([{{ $goal->latitude }}, {{ $goal->longitude }}])
            .addTo(map)
            .bindPopup("{{ $goal->location_name }}");
    @endif

    // Fonction pour basculer l'état d'une étape
    function toggleStep(goalId, stepId, checkbox) {
        fetch(`/goals/${goalId}/steps/${stepId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stepItem = document.getElementById(`step-${stepId}`);
                stepItem.classList.toggle('completed');
                
                // Mettre à jour la barre de progression
                const progressBar = document.querySelector('.progress-bar');
                progressBar.style.width = `${data.progress}%`;
                progressBar.textContent = `${Math.round(data.progress)}%`;
                
                // Mettre à jour le statut
                const badge = checkbox.closest('.card-body').querySelector('.badge');
                if (checkbox.checked) {
                    badge.className = 'badge bg-success';
                    badge.textContent = 'Complété';
                } else {
                    badge.className = 'badge bg-primary';
                    badge.textContent = 'En cours';
                }
            }
        });
    }
</script>
@endpush 