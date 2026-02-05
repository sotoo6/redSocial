@extends('layout')

@section('content')

<h1>· Mensajes publicados ·</h1>

{{-- Filtro por asignatura --}}
<form method="GET" action="{{ url('/') }}" class="filtro-asignatura" style="margin-bottom: 20px;">
    <label for="subject-filter">Filtrar por asignatura:</label>

    <select name="subject" id="subject-filter">
        <option value="todas" {{ request('subject','todas') === 'todas' ? 'selected' : '' }}>Todas</option>

        @foreach ($subjects as $subj)
            <option value="{{ $subj }}" {{ request('subject','todas') === $subj ? 'selected' : '' }}>
                {{ $subj }}
            </option>
        @endforeach
    </select>

    <button type="submit" id="button-filter">Aplicar filtro</button>
</form>
    @if (session('status'))
        <p class="msg-estado">
            {{ session('status') }}
        </p>
    @endif
<div class="mensajes-publicados">
    @forelse ($messages as $m)
        <div class="mensaje">
            <h2>{{ $m['author'] }}</h2>
            <p class="asignatura">{{ $m['subject'] }}</p>
            <p>{{ $m['text'] }}</p>
            <em style="color:grey; font-size:12px;">
                {{ $m['createdAt'] ?? '' }}
            </em>

            {{-- Acciones (solo si el mensaje es del usuario logueado) --}}
            @if(session('user.name') === ($m['author'] ?? ''))
                <div class="acciones-mensaje" style="margin-top: 10px; display:flex; gap:10px;">

                    {{-- BORRAR --}}
                    <form method="POST"
                          action="{{ url('/messages/' . $m['id'] . '/delete') }}"
                          onsubmit="return confirm('¿Seguro que quieres borrar este mensaje?');">
                        @csrf
                        <button type="submit" class="btn-delete btn-accion">Borrar</button>
                    </form>

                    {{-- MODIFICAR (abre modal) --}}
                    <button type="button"
                        class="btn-editar btn-accion"
                        data-id="{{ $m['id'] }}"
                        data-subject="{{ $m['subject'] }}"
                        data-text="{{ $m['text'] }}">
                        Modificar
                    </button>
                </div>
            @endif
        </div>
    @empty
        <p style="text-align: center;">No hay mensajes publicados todavía.</p>
    @endforelse
</div>
<div id="editModalOverlay" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
        <div class="modal-header">
            <h3 id="editModalTitle">Editar mensaje</h3>
            <button type="button" id="closeEditModal" class="modal-close" aria-label="Cerrar">×</button>
        </div>

        <form method="POST" action="{{ url('/messages/update') }}" id="editForm">
            @csrf
            <input type="hidden" name="id" id="edit-id">

            <label for="edit-subject">Asignatura:</label>
            <select name="subject" id="edit-subject">
                @foreach ($subjects as $subj)
                    <option value="{{ $subj }}">{{ $subj }}</option>
                @endforeach
            </select>

            <label for="edit-text" style="display:block; margin-top:12px;">Mensaje (1-280 caracteres):</label>
            <textarea name="text" id="edit-text" maxlength="280" required></textarea>

            <div class="modal-actions">
                <button type="button" id="cancelEdit">Cancelar</button>
                <button type="submit">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- JS del modal --}}
<script>
(function () {
    const overlay = document.getElementById('editModalOverlay');
    const closeBtn = document.getElementById('closeEditModal');
    const cancelBtn = document.getElementById('cancelEdit');

    const inputId = document.getElementById('edit-id');
    const inputSubject = document.getElementById('edit-subject');
    const inputText = document.getElementById('edit-text');

    function openModal({id, subject, text}) {
        inputId.value = id;
        inputSubject.value = subject;
        inputText.value = text;

        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        inputText.focus();
        inputText.setSelectionRange(inputText.value.length, inputText.value.length);
    }

    function closeModal() {
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
    }

    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', () => {
            openModal({
                id: btn.dataset.id,
                subject: btn.dataset.subject,
                text: btn.dataset.text
            });
        });
    });

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // clic fuera (overlay)
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });

    // ESC para cerrar
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('open')) closeModal();
    });
})();
</script>

@endsection