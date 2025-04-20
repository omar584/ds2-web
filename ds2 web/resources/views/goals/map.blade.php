@extends('layouts.app')

@push('styles')
<style>
    #goalsMap {
        height: 600px;
        border-radius: 0.375rem;
    }
    .goal-popup {
        max-width: 300px;
    }
    .goal-popup .progress {
        height: 6px;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="h2 mb-0">Carte des Objectifs</h1>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('goals.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouvel Objectif
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div id="goalsMap"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialisation de la carte
    const map = L.map('goalsMap').setView([46.2276, 2.2137], 6); // Centre sur la France
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Ajout des marqueurs pour chaque objectif
    const goals = @json($goals);
    const markers = L.markerClusterGroup();

    goals.forEach(goal => {
        const marker = L.marker([goal.latitude, goal.longitude]);
        
        const popupContent = `
            <div class="goal-popup">
                <h6 class="mb-2">${goal.title}</h6>
                <div class="progress mb-2">
                    <div class="progress-bar" role="progressbar" style="width: ${goal.progress}%"></div>
                </div>
                <p class="text-muted small mb-2">${goal.description || 'Aucune description'}</p>
                <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                    <span>${goal.location_name}</span>
                    <span>${goal.progress}% complété</span>
                </div>
                <a href="/goals/${goal.id}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-eye"></i> Voir détails
                </a>
            </div>
        `;
        
        marker.bindPopup(popupContent);
        markers.addLayer(marker);
    });

    map.addLayer(markers);

    // Ajuster la vue pour montrer tous les marqueurs
    if (goals.length > 0) {
        const bounds = markers.getBounds();
        map.fitBounds(bounds, { padding: [50, 50] });
    }
</script>
@endpush 