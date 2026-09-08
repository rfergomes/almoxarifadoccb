@extends('layouts.app')

@section('title', 'Meu Perfil | Almoxarifado CCB')
@section('page_title', 'Meu Perfil')

@section('breadcrumb')
  <li class="breadcrumb-item active">Meu Perfil</li>
@endsection

@section('content')
<div class="row g-4">
  <!-- Card Lateral: Resumo do Usuário -->
  <div class="col-lg-4">
    <div class="card shadow-sm border-0 h-100 text-center">
      <div class="card-header bg-gradient bg-primary py-4 position-relative" style="border-radius: 0.5rem 0.5rem 0 0;">
        <div class="position-absolute top-100 start-50 translate-middle">
          <div class="position-relative d-inline-block">
            @if($user->hasAvatar())
              <img src="{{ $user->avatar_url }}" 
                   alt="{{ $user->name }}" 
                   id="profile_avatar_display"
                   class="rounded-circle border border-4 border-white shadow" 
                   style="width: 120px; height: 120px; object-fit: cover;">
            @else
              <div id="profile_avatar_initials"
                   class="rounded-circle border border-4 border-white bg-dark text-white d-flex align-items-center justify-content-center shadow" 
                   style="width: 120px; height: 120px; font-size: 2.8rem; font-weight: 700; letter-spacing: 1px;">
                {{ $user->initials() }}
              </div>
            @endif
          </div>
        </div>
      </div>
      <div class="card-body pt-5 mt-4">
        <h4 class="fw-bold text-navy mb-1" id="profile_name_display">{{ $user->name }}</h4>
        <p class="text-muted small mb-2"><i class="bi bi-envelope-fill me-1 text-secondary"></i>{{ $user->email }}</p>
        
        <div class="my-3">
          <span class="badge bg-{{ $user->primary_role_badge ?? 'primary' }} fs-6 px-3 py-2 rounded-pill shadow-sm">
            <i class="bi bi-shield-check me-1"></i>{{ $user->primary_role ?? 'Usuário' }}
          </span>
        </div>

        <hr class="my-3 text-muted opacity-25">

        <div class="text-start px-3 small">
          <div class="d-flex justify-content-between py-1 border-bottom">
            <span class="text-muted"><i class="bi bi-calendar3 me-1"></i>Membro desde:</span>
            <span class="fw-semibold text-dark">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
          </div>
          <div class="d-flex justify-content-between py-1 border-bottom">
            <span class="text-muted"><i class="bi bi-activity me-1"></i>Status da Conta:</span>
            <span class="badge bg-success">Ativo</span>
          </div>
          <div class="d-flex justify-content-between py-1">
            <span class="text-muted"><i class="bi bi-shield-lock me-1"></i>Perfil de Acesso:</span>
            <span class="fw-semibold text-dark">{{ $user->primary_role }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Coluna Principal: Formulários de Edição -->
  <div class="col-lg-8">
    <div class="d-flex flex-column gap-4">
      
      <!-- Card 1: Dados Pessoais e Foto de Perfil -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title mb-0 fw-bold text-dark">
            <i class="bi bi-person-bounding-box text-primary me-2"></i>Informações Pessoais & Foto
          </h5>
        </div>
        <div class="card-body p-4">
          <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="formUpdateProfile">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-md-12">
                <label class="form-label fw-semibold">Nome Completo *</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-person text-secondary"></i></span>
                  <input type="text" 
                         name="name" 
                         id="input_profile_name"
                         class="form-control @error('name') is-invalid @enderror" 
                         value="{{ old('name', $user->name) }}" 
                         required 
                         placeholder="Seu nome completo">
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-text text-muted small">Este é o nome exibido no cabeçalho superior e nas movimentações registradas por você.</div>
              </div>

              <div class="col-md-12">
                <label class="form-label fw-semibold">E-mail de Acesso (Login)</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-envelope text-secondary"></i></span>
                  <input type="email" class="form-control bg-light text-muted" value="{{ $user->email }}" readonly disabled>
                </div>
                <div class="form-text text-muted small"><i class="bi bi-info-circle me-1"></i>Para alterar o e-mail institucional da sua conta, contate um Administrador do sistema.</div>
              </div>

              <!-- Upload de Foto de Perfil -->
              <div class="col-md-12 mt-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-camera text-primary me-1"></i>Foto de Perfil
                </label>

                <!-- Prévia dinâmica da foto selecionada -->
                <div id="new_avatar_preview_container" class="mb-3 d-none align-items-center gap-3 p-3 bg-light border rounded">
                  <img id="new_avatar_preview" src="" alt="Nova Foto" class="rounded-circle shadow-sm border border-2 border-primary" style="width: 70px; height: 70px; object-fit: cover;">
                  <div>
                    <span class="d-block fw-semibold text-primary small"><i class="bi bi-check-circle me-1"></i>Nova foto selecionada!</span>
                    <small class="text-muted">Clique em "Salvar Alterações" abaixo para confirmar o upload.</small>
                  </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-3">
                  <div class="flex-grow-1">
                    <input type="file" 
                           name="avatar" 
                           id="input_profile_avatar" 
                           class="form-control @error('avatar') is-invalid @enderror" 
                           accept="image/png,image/jpeg,image/webp,image/gif">
                    @error('avatar')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small">Formatos permitidos: JPG, PNG, WEBP ou GIF (máximo de 5MB).</div>
                  </div>

                  @if($user->hasAvatar())
                  <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="remove_avatar" id="checkbox_remove_avatar" value="1">
                    <label class="form-check-label text-danger fw-semibold small" for="checkbox_remove_avatar">
                      <i class="bi bi-trash3 me-1"></i>Remover foto atual
                    </label>
                  </div>
                  @endif
                </div>
              </div>

              <div class="col-12 text-end pt-3 border-top mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                  <i class="bi bi-check2-circle me-1"></i> Salvar Alterações
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Card 2: Segurança e Troca de Senha -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title mb-0 fw-bold text-dark">
            <i class="bi bi-shield-lock text-warning me-2"></i>Segurança & Senha de Acesso
          </h5>
        </div>
        <div class="card-body p-4">
          <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-md-12">
                <label class="form-label fw-semibold">Senha Atual *</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-key text-secondary"></i></span>
                  <input type="password" 
                         name="current_password" 
                         class="form-control @error('current_password') is-invalid @enderror" 
                         required 
                         placeholder="Informe sua senha atual">
                  @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Nova Senha *</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-lock text-secondary"></i></span>
                  <input type="password" 
                         name="password" 
                         class="form-control @error('password') is-invalid @enderror" 
                         required 
                         placeholder="Mínimo de 8 caracteres">
                  @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Confirmar Nova Senha *</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-secondary"></i></span>
                  <input type="password" 
                         name="password_confirmation" 
                         class="form-control" 
                         required 
                         placeholder="Repita a nova senha">
                </div>
              </div>

              <div class="col-12 text-end pt-3 border-top mt-4">
                <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark fw-semibold">
                  <i class="bi bi-shield-check me-1"></i> Atualizar Senha
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('input_profile_avatar');
    const previewContainer = document.getElementById('new_avatar_preview_container');
    const previewImg = document.getElementById('new_avatar_preview');
    const removeCheckbox = document.getElementById('checkbox_remove_avatar');

    if (avatarInput) {
      avatarInput.addEventListener('change', function() {
        const file = this.files && this.files[0];
        if (file) {
          previewImg.src = URL.createObjectURL(file);
          previewContainer.classList.remove('d-none');
          previewContainer.classList.add('d-flex');
          if (removeCheckbox) {
            removeCheckbox.checked = false;
          }
        } else {
          previewImg.src = '';
          previewContainer.classList.add('d-none');
          previewContainer.classList.remove('d-flex');
        }
      });
    }

    if (removeCheckbox) {
      removeCheckbox.addEventListener('change', function() {
        if (this.checked) {
          if (avatarInput) avatarInput.value = '';
          if (previewContainer) {
            previewContainer.classList.add('d-none');
            previewContainer.classList.remove('d-flex');
          }
        }
      });
    }
  });
</script>
@endpush
