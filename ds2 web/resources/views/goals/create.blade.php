@extends('layouts.app')

@push('styles')
<style>
    #map {
        height: 300px;
        border-radius: 0.375rem;
    }
    .step-list {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h1 class="h3 mb-0">Créer un nouvel objectif</h1>
            </div>
            
            <div class="card-body">
                <form action="{{ route('goals.store') }}" method="POST" id="goalForm">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre de l'objectif</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                    id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                    id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Catégorie</label>
                                        <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                            <option value="">Choisir une catégorie</option>
                                            <option value="education">Éducation</option>
                                            <option value="career">Carrière</option>
                                            <option value="health">Santé</option>
                                            <option value="fitness">Sport</option>
                                            <option value="personal">Personnel</option>
                                            <option value="travel">Voyage</option>
                                            <option value="other">Autre</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="deadline" class="form-label">Date limite</label>
                                        <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                            id="deadline" name="deadline" value="{{ old('deadline') }}">
                                        @error('deadline')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="visibility" class="form-label">Visibilité</label>
                                <select class="form-select @error('visibility') is-invalid @enderror" 
                                    id="visibility" name="visibility" required>
                                    <option value="private">Privé</option>
                                    <option value="friends">Amis</option>
                                    <option value="public">Public</option>
                                </select>
                                @error('visibility')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Localisation (optionnel)</label>
                                <div id="map" class="mb-2"></div>
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                                <input type="text" class="form-control" id="location_name" name="location_name" 
                                    placeholder="Nom du lieu" readonly>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="mb-3">Étapes</h4>
                            <div class="step-list" id="stepsList">
                                <div class="step-item mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="steps[0][title]" 
                                                        placeholder="Titre de l'étape" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="date" class="form-control" name="steps[0][due_date]">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" 
                                                        onclick="removeStep(this)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addStep()">
                                <i class="bi bi-plus"></i> Ajouter une étape
                            </button>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Créer l'objectif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialisation de la carte
    const map = L.map('map').setView([48.8566, 2.3522], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;

    map.on('click', function(e) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker(e.latlng).addTo(map);
        
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
        
        // Reverse geocoding
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('location_name').value = data.display_name;
            });
    });

    // Gestion des étapes
    let stepCount = 1;

    function addStep() {
        const stepHtml = `
            <div class="step-item mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="steps[${stepCount}][title]" 
                                    placeholder="Titre de l'étape" required>
                            </div>
                            <div class="col-md-4">
                                <input type="date" class="form-control" name="steps[${stepCount}][due_date]">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100" 
                                    onclick="removeStep(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('stepsList').insertAdjacentHTML('beforeend', stepHtml);
        stepCount++;
    }

    function removeStep(button) {
        button.closest('.step-item').remove();
    }
</script>
@endpush 