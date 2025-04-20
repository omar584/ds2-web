@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-0">Participants</h1>
            <p class="text-muted">Participants pour l'objectif : {{ $goal->title }}</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Participant</th>
                            <th>Date de participation</th>
                            <th>Progrès</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($participants as $participant)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($participant->user->name) }}&background=random" 
                                                 alt="{{ $participant->user->name }}"
                                                 class="rounded-circle">
                                        </div>
                                        <div>
                                            {{ $participant->user->name }}
                                            @if($participant->user_id === $goal->user_id)
                                                <span class="badge bg-primary ms-1">Créateur</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $participant->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="progress" style="height: 8px; width: 100px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $participant->progress }}%"
                                             aria-valuenow="{{ $participant->progress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $participant->progress }}%</small>
                                </td>
                                <td>
                                    @if($participant->completed_at)
                                        <span class="badge bg-success">Terminé</span>
                                    @else
                                        <span class="badge bg-info">En cours</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('goals.show', $goal) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour à l'objectif
        </a>
    </div>
</div>
@endsection 