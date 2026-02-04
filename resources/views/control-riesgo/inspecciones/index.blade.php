<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspección en Curso - ContainerCheck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            padding-bottom: 50px;
        }

        .navbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid #eee;
            padding: 0.8rem 1.5rem;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
            padding: 0 10px;
        }

        .step-indicator::before {
            content: "";
            position: absolute;
            top: 15px;
            left: 10px;
            right: 10px;
            height: 2px;
            background: #ddd;
            z-index: 1;
        }

        .step {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            font-weight: bold;
            color: #999;
            cursor: pointer;
        }

        .step.active {
            border-color: #8a70d6;
            color: #8a70d6;
            background: #f1f0ff;
        }

        .step.completed {
            background: #8a70d6;
            border-color: #8a70d6;
            color: white;
        }

        .phase-card {
            display: none;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 20px;
        }

        .phase-card.active {
            display: block;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .disabled-field {
            pointer-events: none;
            opacity: 0.7;
            background-color: #f8f9fa !important;
        }

        .nav-link.active {
            color: #8a70d6 !important;
            font-weight: 700;
        }

        .locked-badge {
            font-size: 0.75rem;
            background: #e9ecef;
            color: #6c757d;
            padding: 4px 8px;
            border-radius: 4px;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold text-primary">ContainerCheck</span>
            <div class="d-flex align-items-center">
                <span class="badge bg-info text-dark me-2">Borrador</span>
                <a href="{{ asset('formats/formato_inspeccion.xlsx') }}" download
                    class="btn btn-sm btn-outline-success me-2">
                    <i class="bi bi-file-earmark-excel"></i> Formato Excel
                </a>
                <a href="{{ route('control-riesgo.index') }}" class="btn btn-sm btn-outline-secondary">Salir</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width: 700px;">
        <div class="mb-4 text-center">
            <h4 class="fw-bold m-0">{{ $activeVersion->version }}</h4>
            <p class="text-muted small">ID Inspección: #{{ $response->id }}</p>
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator">
            @foreach($activeVersion->phases as $index => $phase)
                @php
                    $isCompleted = $index <= $response->last_phase_completed;
                    $isActive = ($index == $response->last_phase_completed + 1) || ($response->last_phase_completed == -1 && $index == 0);
                @endphp
                <div class="step {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}"
                    onclick="goToStep({{ $index }})">
                    @if($isCompleted) <i class="bi bi-check"></i> @else {{ $index + 1 }} @endif
                </div>
            @endforeach
            <!-- Final Step: Signature -->
            <div class="step {{ $response->status == 'completed' ? 'completed' : '' }}" id="step-final">
                <i class="bi bi-pencil"></i>
            </div>
        </div>

        @foreach($activeVersion->phases as $index => $phase)
            <div id="phase-{{ $index }}"
                class="phase-card {{ ($index == 0 && $response->last_phase_completed == -1) || ($index == $response->last_phase_completed + 1) ? 'active' : '' }}">
                <div class="d-flex align-items-center mb-4">
                    <h5 class="fw-bold m-0 text-primary">{{ $phase->name }}</h5>
                    @if($index <= $response->last_phase_completed)
                        <span class="locked-badge"><i class="bi bi-lock-fill"></i> Fase Bloqueada</span>
                    @endif
                </div>

                <form class="phase-form" data-order="{{ $index }}" onsubmit="saveCurrentPhase(event, this)">
                    @csrf
                    <input type="hidden" name="phase_order" value="{{ $index }}">
                    <div class="row g-3">
                        @foreach($phase->fields as $field)
                            @php
                                $fieldVal = $response->fieldResponses->where('field_id', $field->id)->first()?->value;
                                $isLocked = $index <= $response->last_phase_completed;
                                $isCabecera = str_contains(strtoupper($phase->name), 'CABECERA');
                            @endphp
                            <div class="{{ $isCabecera ? 'col-md-6' : 'col-12' }}">
                                <label
                                    class="form-label fw-semibold small text-uppercase text-muted">{{ $field->label }}</label>

                                @if($field->type == 'text')
                                    <input type="text" name="fields[{{ $field->id }}]"
                                        class="form-control {{ $isLocked ? 'disabled-field' : '' }}" value="{{ $fieldVal }}"
                                        required>
                                @elseif($field->type == 'numeric')
                                    <input type="number" step="any" name="fields[{{ $field->id }}]"
                                        class="form-control {{ $isLocked ? 'disabled-field' : '' }}" value="{{ $fieldVal }}"
                                        required>
                                @elseif($field->type == 'date')
                                    @php 
                                        $finalDate = $fieldVal ?? date('Y-m-d');
                                    @endphp
                                    <input type="date" 
                                        class="form-control disabled-field" value="{{ $finalDate }}"
                                        readonly>
                                    <input type="hidden" name="fields[{{ $field->id }}]" value="{{ $finalDate }}">
                                @elseif($field->type == 'time')
                                    @php 
                                        $finalTime = $fieldVal ?? date('H:i');
                                    @endphp
                                    <input type="time" 
                                        class="form-control disabled-field" value="{{ $finalTime }}"
                                        readonly>
                                    <input type="hidden" name="fields[{{ $field->id }}]" value="{{ $finalTime }}">
                                @elseif($field->type == 'select')
                                    <select name="fields[{{ $field->id }}]"
                                        class="form-select {{ $isLocked ? 'disabled-field' : '' }}"
                                        data-rejection="{{ $field->rejection_value }}" required>
                                        <option value="" disabled {{ !$fieldVal ? 'selected' : '' }}>Seleccione...</option>
                                        @foreach(explode(',', $field->options) as $option)
                                            <option value="{{ trim($option) }}" {{ $fieldVal == trim($option) ? 'selected' : '' }}>
                                                {{ trim($option) }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif($field->type == 'photo')
                                    <div class="photo-input-group">
                                        @if(!$isLocked)
                                            <div class="d-flex gap-2 mb-2">
                                                <input type="file" name="fields[{{ $field->id }}]" id="input_{{ $field->id }}"
                                                    class="d-none" accept="image/*" onchange="previewImage(this, '{{ $field->id }}')">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="openCamera('{{ $field->id }}')"><i class="bi bi-camera"></i></button>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="openGallery('{{ $field->id }}')"><i class="bi bi-images"></i></button>
                                            </div>
                                        @endif
                                        <div id="preview_container_{{ $field->id }}" class="{{ !$fieldVal ? 'd-none' : '' }}">
                                            <img id="preview_img_{{ $field->id }}"
                                                src="{{ $fieldVal ? asset('storage/' . $fieldVal) : '' }}" class="img-thumbnail"
                                                style="max-height: 120px;">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        @if($index > 0)
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="prevStep({{ $index }})">Atrás</button>
                        @else
                            <span></span>
                        @endif

                        @if($isLocked)
                            <button type="button" class="btn btn-primary" onclick="nextStep({{ $index }})">Ver
                                Siguiente</button>
                        @else
                            <button type="submit" class="btn btn-primary">
                                <span class="btn-text">Guardar y Continuar</span>
                                <span class="btn-reject d-none text-white fw-bold">FINALIZAR POR RECHAZO</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        @endforeach

        <!-- Final Phase: Signature -->
        <div id="phase-final" class="phase-card">
            <h5 class="fw-bold text-primary mb-4">Finalizar Inspección</h5>

            @php
                $pendingSignatures = $response->inspectionSignatures->whereNull('signed_at');
            @endphp

            @if($pendingSignatures->count() > 0)
                <div class="alert alert-warning border-warning shadow-sm">
                    <h5 class="alert-heading fw-bold text-warning-emphasis">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Esperando Firmas del Personal
                    </h5>
                    <p class="mb-2">La inspección no puede finalizarse hasta que el siguiente personal haya ingresado al sistema y firmado:</p>
                    <ul class="list-group list-group-flush mb-3 rounded bg-white">
                        @foreach($pendingSignatures as $sig)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">{{ $sig->user->name }}</span>
                                    <br>
                                    <small class="text-muted">{{ $sig->role_in_inspection }} (CC: {{ $sig->user->cedula }})</small>
                                </div>
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="d-flex gap-2">
                        <a href="{{ request()->url() }}" class="btn btn-warning w-100 fw-bold">
                            <i class="bi bi-arrow-clockwise"></i> Actualizar Estado de Firmas
                        </a>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" onclick="prevStep('final')">Atras</button>
                    <button class="btn btn-secondary disabled" disabled>Esperando Firmas...</button>
                </div>

                <!-- Hidden elements to prevent JS errors if needed, though better to handle in JS -->
                <div class="d-none">
                    <canvas id="signature-pad"></canvas>
                    <form id="finalForm"></form>
                </div>
            @else
                <div class="alert alert-info small">
                    Al firmar esta inspección, todos los datos anteriores quedarán registrados permanentemente.
                </div>

                <form action="{{ route('control-riesgo.store', $response->id) }}" method="POST" id="finalForm">
                    @csrf
                    <input type="hidden" name="signature" id="signatureInput">

                    <div class="text-center">
                        <div class="signature-container bg-light rounded border mb-3" style="touch-action: none;">
                            <canvas id="signature-pad" style="width: 100%; height: 250px; background: white;"></canvas>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-danger" id="clearBtn"><i
                                class="bi bi-eraser"></i> Limpiar Firma</button>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep('final')">Atras</button>
                        <button type="submit" class="btn btn-success px-4 fw-bold">ENVIAR INSPECCIÓN</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let lastPhaseCompleted = {{ $response->last_phase_completed }};

        function openGallery(id) { const input = document.getElementById('input_' + id); input.removeAttribute('capture'); input.click(); }
        function openCamera(id) { const input = document.getElementById('input_' + id); input.setAttribute('capture', 'environment'); input.click(); }

        function previewImage(input, id) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('preview_container_' + id).classList.remove('d-none');
                    document.getElementById('preview_img_' + id).src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function lockPhaseUI(idx, form) {
            // Add 'Fase Bloqueada' badge if not present
            const header = document.querySelector(`#phase-${idx} .d-flex.align-items-center`);
            if (!header.querySelector('.locked-badge')) {
                const badge = document.createElement('span');
                badge.className = 'locked-badge';
                badge.innerHTML = '<i class="bi bi-lock-fill"></i> Fase Bloqueada';
                header.appendChild(badge);
            }

            // Disable all fields
            form.querySelectorAll('input, select, textarea').forEach(el => {
                el.classList.add('disabled-field');
            });

            // Hide photo buttons
            form.querySelectorAll('.photo-input-group .d-flex').forEach(el => {
                el.classList.add('d-none');
            });

            // Replace Submit button with 'Ver Siguiente' button
            const btnContainer = form.querySelector('.mt-4.d-flex.justify-content-between');
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                const nextBtn = document.createElement('button');
                nextBtn.type = 'button';
                nextBtn.className = 'btn btn-primary';
                nextBtn.innerText = 'Ver Siguiente';
                nextBtn.onclick = () => nextStep(idx);
                submitBtn.replaceWith(nextBtn);
            }
        }

        function checkRejection(form) {
            const btn = form.querySelector('button[type="submit"]');
            if (!btn) return;

            const allSelects = form.querySelectorAll('.form-select[data-rejection]');
            let hasAnyRejection = false;

            allSelects.forEach(select => {
                const rejVal = select.dataset.rejection;
                if (rejVal && select.value === rejVal) {
                    hasAnyRejection = true;
                }
            });

            const btnText = btn.querySelector('.btn-text');
            const btnReject = btn.querySelector('.btn-reject');

            if (hasAnyRejection) {
                btn.classList.add('btn-danger');
                btn.classList.remove('btn-primary');
                if (btnText) btnText.classList.add('d-none');
                if (btnReject) btnReject.classList.remove('d-none');
                form.dataset.hasRejection = "true";
            } else {
                btn.classList.add('btn-primary');
                btn.classList.remove('btn-danger');
                if (btnText) btnText.classList.remove('d-none');
                if (btnReject) btnReject.classList.add('d-none');
                delete form.dataset.hasRejection;
            }
        }

        document.addEventListener('change', function (e) {
            if (e.target.matches('.form-select[data-rejection]')) {
                checkRejection(e.target.closest('form'));
            }
        });

        async function saveCurrentPhase(e, form) {
            e.preventDefault();
            checkRejection(form); // Final safety check before processing

            if (form.dataset.hasRejection === "true") {
                if (!confirm("ADVERTENCIA: Se ha detectado un valor que NO CUMPLE con los requisitos mínimos. Esta inspección será RECHAZADA Y NOTIFICADA. ¿Desea finalizar el proceso ahora?")) {
                    return;
                }
                const formData = new FormData(form);
                formData.append('is_rejected', '1');

                try {
                    const res = await fetch("{{ route('control-riesgo.inspecciones.savePhase', $response->id) }}", {
                        method: 'POST', body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
                    });
                    if (res.ok) window.location.href = "{{ route('control-riesgo.reportes') }}";
                } catch (err) { alert("Error de red"); }
                return;
            }

            const btn = form.querySelector('button[type="submit"]');
            const order = form.dataset.order;

            btn.disabled = true;
            
            const btnText = btn.querySelector('.btn-text');
            const btnSpinner = btn.querySelector('.spinner-border');
            
            if (btnText) btnText.classList.add('d-none');
            if (btnSpinner) btnSpinner.classList.remove('d-none');

            const formData = new FormData(form);

            try {
                const res = await fetch("{{ route('control-riesgo.inspecciones.savePhase', $response->id) }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });

                if (res.ok) {
                    const data = await res.json();
                    
                    if (data.pending_signatures) {
                        alert("ATENCIÓN: " + data.message + "\n\nEl formulario no podrá avanzar hasta que el personal ingrese y firme.\nComunique al personal para que firme y luego haga clic en 'Verificar Firmas'.");
                        
                        // Change button to "Verify" mode but KEEP structure for loading state AND rejection check
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-warning');
                        btn.innerHTML = '<span class="btn-text fw-bold"><i class="bi bi-arrow-clockwise"></i> Verificar Firmas</span>' + 
                                        '<span class="btn-reject d-none text-white fw-bold">FINALIZAR POR RECHAZO</span>' +
                                        '<span class="spinner-border spinner-border-sm d-none" role="status"></span>';
                        
                        // Allow clicking again
                        btn.disabled = false;
                        return; // Stop here, don't advance
                    }

                    const currentOrder = parseInt(order);
                    if (currentOrder > lastPhaseCompleted) {
                        lastPhaseCompleted = currentOrder;
                    }

                    // Lock the UI of the form we just saved
                    lockPhaseUI(currentOrder, form);

                    nextStep(currentOrder);
                } else {
                    alert("Error al guardar. Verifique su conexión.");
                    resetBtnState(btn);
                }
            } catch (err) {
                console.error(err);
                alert("Error de red.");
                resetBtnState(btn);
            }
        }

        function resetBtnState(btn) {
            btn.disabled = false;
            const btnText = btn.querySelector('.btn-text');
            const btnSpinner = btn.querySelector('.spinner-border');
            if (btnText) btnText.classList.remove('d-none');
            if (btnSpinner) btnSpinner.classList.add('d-none');
        }

        function nextStep(currentIdx) {
            document.getElementById('phase-' + currentIdx).classList.remove('active');

            const nextIdx = currentIdx + 1;
            const nextPhase = document.getElementById('phase-' + nextIdx);

            if (nextPhase) {
                nextPhase.classList.add('active');
                updateSteps(nextIdx);
                const form = nextPhase.querySelector('form');
                if (form) checkRejection(form);
            } else {
                document.getElementById('phase-final').classList.add('active');
                updateSteps('final');
                resizeCanvas();
            }
        }

        function prevStep(currentIdx) {
            if (currentIdx === 'final') {
                document.getElementById('phase-final').classList.remove('active');
                const lastPhaseIdx = {{ $activeVersion->phases->count() - 1 }};
                document.getElementById('phase-' + lastPhaseIdx).classList.add('active');
                updateSteps(lastPhaseIdx);
            } else {
                document.getElementById('phase-' + currentIdx).classList.remove('active');
                const prevIdx = currentIdx - 1;
                const prevPhase = document.getElementById('phase-' + prevIdx);
                prevPhase.classList.add('active');
                updateSteps(prevIdx);
                const form = prevPhase.querySelector('form');
                if (form) checkRejection(form);
            }
        }

        function updateSteps(idx) {
            const steps = document.querySelectorAll('.step');
            steps.forEach((s, i) => {
                s.classList.remove('active');
                if (idx === 'final') {
                    if (i < steps.length - 1) s.classList.add('completed');
                    if (i === steps.length - 1) s.classList.add('active');
                } else {
                    if (i < idx) s.classList.add('completed');
                    if (i === idx) s.classList.add('active');
                }
            });
        }

        function goToStep(idx) {
            if (idx <= lastPhaseCompleted + 1) {
                // Allow jumping between already completed phases or the current one
                document.querySelectorAll('.phase-card').forEach(p => p.classList.remove('active'));
                const phaseCard = document.getElementById('phase-' + idx);
                phaseCard.classList.add('active');
                updateSteps(idx);

                // Re-check rejection on show
                const form = phaseCard.querySelector('form');
                if (form) checkRejection(form);
            }
        }


        // Initialize for current active phase on load
        document.addEventListener('DOMContentLoaded', () => {
            const activePhase = document.querySelector('.phase-card.active');
            if (activePhase) {
                const form = activePhase.querySelector('form');
                if (form) checkRejection(form);
            }
        });

        // Signature logic
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        document.getElementById('clearBtn').addEventListener('click', () => signaturePad.clear());

        document.getElementById('finalForm').addEventListener('submit', function (e) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert("Por favor firme antes de enviar.");
            } else {
                document.getElementById('signatureInput').value = signaturePad.toDataURL();
            }
        });
    </script>
</body>

</html>