@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow mb-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Inspección #{{ $inspection->id }}</h2>
            <span class="text-sm px-2 py-1 rounded bg-blue-100 text-blue-800 font-semibold">{{ ucfirst(str_replace('_', ' ', $inspection->status)) }}</span>
        </div>
        
        <div class="flex gap-2">
            @if($canEdit)
            <button type="button" onclick="saveInspection('{{ route('inspector.inspections.index') }}')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Guardar y Salir
            </button>
            @else
            <a href="{{ route('inspector.inspections.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
            @endif
            
            @if($inspection->status === 'pending_signature')
                 <span class="text-yellow-600 font-bold self-center">Esperando firma participante</span>
            @elseif($inspection->status === 'completed')
                <a href="{{ route('pdf.inspection', $inspection->id) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            @else
                <!-- Save Button is inside form, but maybe a global one here? -->
            @endif
        </div>
    </div>

    @if($canEdit)
    <form action="{{ route('inspector.inspections.update', $inspection->id) }}" method="POST" id="inspectionForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Stepper Navigation -->
        <div class="mb-8">
            <div class="flex flex-wrap border-b border-gray-200">
                @foreach($phases as $index => $phase)
                <button type="button" 
                    id="step-tab-{{ $index }}"
                    class="step-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none {{ $index === 0 ? 'border-b-2 border-blue-500 text-blue-600' : '' }}"
                    onclick="switchStep({{ $index }})">
                    {{ $index + 1 }}. {{ $phase->name }}
                </button>
                @endforeach
            </div>
        </div>

        @foreach($phases as $index => $phase)
           <div id="step-content-{{ $index }}" class="step-content {{ $index === 0 ? '' : 'hidden' }}">
               <div class="mb-8 border border-gray-200 rounded-lg overflow-hidden">
                   <div class="bg-gray-100 px-4 py-2 border-b border-gray-200">
                       <h3 class="text-lg font-bold text-gray-700">{{ $phase->order }}. {{ $phase->name }}</h3>
                       <p class="text-sm text-gray-500">{{ $phase->description }}</p>
                   </div>
                   
                   <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                       @foreach($phase->fields as $field)
                           @php
                               $val = $inspection->values->where('field_id', $field->id)->first();
                               $currentValue = $val ? $val->value : '';
                           @endphp

                            <div class="mb-2 field-wrapper" 
                                data-field-id="{{ $field->id }}"
                                @if(isset($field->conditionals['linked_to']) && $field->conditionals['linked_to'])
                                    data-conditional-linked-to="{{ $field->conditionals['linked_to'] }}"
                                    data-conditional-value="{{ $field->conditionals['value'] }}"
                                    style="display: none;"
                                @endif
                            >
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                   {{ $field->label }} @if($field->required) <span class="text-red-500">*</span> @endif
                               </label>
                               
                               @if($field->type === 'text')
                                   <input type="text" name="values[{{ $field->id }}]" value="{{ $currentValue }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" {{ $field->required ? 'required' : '' }}>
                               
                               @elseif($field->type === 'number')
                                   <input type="number" name="values[{{ $field->id }}]" value="{{ $currentValue }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" {{ $field->required ? 'required' : '' }}>
                               
                               @elseif($field->type === 'date')
                                   <input type="date" name="values[{{ $field->id }}]" value="{{ $currentValue }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" {{ $field->required ? 'required' : '' }}>
                                   
                               @elseif($field->type === 'select')
                                   <select name="values[{{ $field->id }}]" class="shadow border rounded w-full py-2 px-3 text-gray-700" {{ $field->required ? 'required' : '' }}>
                                       <option value="">Seleccione...</option>
                                       @if($field->options)
                                           @foreach(json_decode($field->options) as $opt)
                                               <option value="{{ $opt }}" {{ $currentValue == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                           @endforeach
                                       @endif
                                   </select>
                                   
                               @elseif($field->type === 'photo')
                                   <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:bg-gray-50 transition" id="preview-container-{{ $field->id }}">
                                       @if($currentValue)
                                           <div class="mb-3">
                                               <img src="{{ asset('storage/' . $currentValue) }}" id="img-preview-{{ $field->id }}" class="mx-auto rounded-lg shadow-md max-h-48 object-cover">
                                               <input type="hidden" name="values[{{ $field->id }}]" value="{{ $currentValue }}">
                                               <p class="text-xs text-green-600 mt-2 font-bold"><i class="fas fa-check-circle"></i> Foto Guardada</p>
                                           </div>
                                           <button type="button" onclick="openPhotoModal({{ $field->id }}, '{{ addslashes($field->label) }}', '{{ addslashes($field->description ?? 'Tomar una foto clara.') }}')" 
                                                class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold hover:bg-blue-200 focus:outline-none">
                                               <i class="fas fa-camera mr-1"></i> Retomar Foto
                                           </button>
                                       @else
                                           <div id="no-photo-{{ $field->id }}">
                                               <i class="fas fa-camera text-gray-400 text-4xl mb-2"></i>
                                               @if($field->description)
                                                   <p class="text-sm text-gray-600 mb-3 font-medium">{{ $field->description }}</p>
                                               @else
                                                   <p class="text-sm text-gray-400 mb-3">Sin foto</p>
                                               @endif
                                                <button type="button" onclick="openPhotoModal({{ $field->id }}, '{{ addslashes($field->label) }}', '{{ addslashes($field->description ?? 'Tomar una foto clara.') }}')" 
                                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold shadow hover:bg-blue-700 focus:outline-none">
                                                    TOMAR FOTO
                                                </button>
                                           </div>
                                           <!-- Preview element for new uploads (hidden initially) -->
                                           <img id="img-preview-{{ $field->id }}" class="hidden mx-auto rounded-lg shadow-md max-h-48 object-cover mb-3">
                                       @endif
                                       
                                       <!-- Hidden Input -->
                                       <input type="file" 
                                              id="input-file-{{ $field->id }}"
                                              name="values[{{ $field->id }}]" 
                                              accept="image/*" 
                                              class="hidden" 
                                              onchange="handleFileSelect(this, {{ $field->id }})"
                                              {{ $field->required && !$currentValue ? 'required' : '' }}>
                                   </div>
                                    
                                @elseif($field->type === 'signature')
                                    <div class="border-2 border-solid border-gray-300 rounded-lg p-4 text-center bg-gray-50" id="signature-container-{{ $field->id }}">
                                        @if($currentValue)
                                            <div class="mb-3">
                                                <img src="{{ $currentValue }}" id="sig-preview-{{ $field->id }}" class="mx-auto rounded-lg shadow-md max-h-32 object-contain border border-gray-300 bg-white p-2">
                                                <input type="hidden" name="values[{{ $field->id }}]" value="{{ $currentValue }}" id="sig-data-{{ $field->id }}">
                                                <p class="text-xs text-green-600 mt-2 font-bold"><i class="fas fa-check-circle"></i> Firma Guardada</p>
                                            </div>
                                            <button type="button" onclick="openSignatureFieldModal({{ $field->id }}, '{{ addslashes($field->label) }}', '{{ addslashes($field->description ?? 'Por favor firme en el recuadro.') }}')"
                                                 class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold hover:bg-blue-200 focus:outline-none">
                                                <i class="fas fa-pen mr-1"></i> Cambiar Firma
                                            </button>
                                        @else
                                            <div id="no-signature-{{ $field->id }}">
                                                <i class="fas fa-pen-fancy text-gray-400 text-4xl mb-2"></i>
                                                @if($field->description)
                                                    <p class="text-sm text-gray-600 mb-3 font-medium">{{ $field->description }}</p>
                                                @else
                                                    <p class="text-sm text-gray-400 mb-3">Sin firma</p>
                                                @endif
                                                 <button type="button" onclick="openSignatureFieldModal({{ $field->id }}, '{{ addslashes($field->label) }}', '{{ addslashes($field->description ?? 'Por favor firme en el recuadro.') }}')"
                                                     class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold shadow hover:bg-blue-700 focus:outline-none">
                                                     <i class="fas fa-pen mr-1"></i> FIRMAR
                                                 </button>
                                            </div>
                                            <!-- Hidden input for signature data -->
                                            <input type="hidden" name="values[{{ $field->id }}]" id="sig-data-{{ $field->id }}" value="" {{ $field->required ? 'required' : '' }}>
                                            <!-- Preview element (hidden initially) -->
                                            <img id="sig-preview-{{ $field->id }}" class="hidden mx-auto rounded-lg shadow-md max-h-32 object-contain border border-gray-300 bg-white p-2 mb-3">
                                        @endif
                                    </div>
                                @endif
                           </div>
                       @endforeach
                   </div>
               </div>
               
               <!-- Navigation Buttons -->
               <div class="flex justify-between mt-4">
                   @if($index > 0)
                       <button type="button" onclick="switchStep({{ $index - 1 }})" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                           Anterior
                       </button>
                   @else
                       <div></div> <!-- Spacer -->
                   @endif

                   @if($index < count($phases) - 1)
                       <button type="button" onclick="switchStep({{ $index + 1 }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                           Siguiente
                       </button>
                   @else
                       <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded shadow-lg">
                                Guardar
                            </button>
                            <button type="button" onclick="openSignatureModal()" class="bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-4 rounded shadow-lg">
                                Finalizar
                            </button>
                       </div>
                   @endif
               </div>
           </div>
        @endforeach
    </form>
    @else
        <!-- Read Only View for Signed/Completed -->
        @foreach($phases as $phase)
           <div class="mb-6">
               <h3 class="font-bold border-b mb-2">{{ $phase->name }}</h3>
               <div class="grid grid-cols-2 gap-4">
                   @foreach($phase->fields as $field)
                       @php
                           $val = $inspection->values->where('field_id', $field->id)->first();
                       @endphp
                       <div>
                           <span class="text-gray-600 text-sm block">{{ $field->label }}:</span>
                           @if($field->type === 'photo' && $val && $val->value)
                                <img src="{{ asset('storage/' . $val->value) }}" class="max-w-[150px] rounded border mt-1">
                           @else
                                <span class="font-semibold">{{ $val ? $val->value : '-' }}</span>
                           @endif
                       </div>
                   @endforeach
               </div>
           </div>
        @endforeach
        
        <div class="mt-8">
            <h3 class="font-bold text-lg mb-4">Firmas</h3>
            <div class="flex gap-8">
                @foreach($inspection->signatures as $sig)
                    <div class="border p-4 rounded text-center">
                        <p class="font-bold">{{ ucfirst($sig->role) }}</p>
                        @if($sig->image_path)
                            <img src="{{ $sig->image_path }}" class="max-w-[150px] mx-auto my-2">
                        @endif
                        <p class="text-xs text-gray-500">{{ $sig->signed_at->format('d/m/Y H:i') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

</div>

<!-- Signature Modal -->
<div id="signatureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Firma del Inspector</h3>
            <div class="mt-2 text-center">
                <canvas id="sig-canvas" class="border border-gray-300 rounded" width="300" height="150"></canvas>
            </div>
            <div class="items-center px-4 py-3">
                <button id="clear-sig" class="px-4 py-2 bg-gray-300 text-black text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Limpiar
                </button>
                <button id="save-sig" class="mt-3 px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300">
                    Confirmar Firma
                </button>
                <button onclick="closeSignatureModal()" class="mt-3 px-4 py-2 bg-red-100 text-red-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Photo Instruction Modal -->
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-90 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-6 border w-full max-w-md shadow-lg rounded-lg bg-white m-4">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                <i class="fas fa-camera text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-2" id="photoModalTitle">Tomar Foto</h3>
            <div class="mt-2 px-2 py-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-lg text-gray-800 font-medium" id="photoModalDesc">
                    Instrucciones aquí...
                </p>
            </div>
            <p class="text-sm text-gray-500 mt-4">
                Asegúrese de que la foto sea clara y cumpla con las instrucciones anteriores.
            </p>
            <div class="mt-6">
                <button id="triggerCameraButton" class="w-full px-4 py-3 bg-blue-600 text-white text-lg font-bold rounded-lg shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transform transition active:scale-95">
                    <i class="fas fa-camera mr-2"></i> ABRIR CÁMARA
                </button>
                <button id="triggerGalleryButton" class="mt-3 w-full px-4 py-3 bg-green-600 text-white text-lg font-bold rounded-lg shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transform transition active:scale-95">
                    <i class="fas fa-images mr-2"></i> SUBIR DE GALERÍA
                </button>
                <button onclick="closePhotoModal()" class="mt-3 w-full px-4 py-2 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 focus:outline-none">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Signature Field Modal -->
<div id="signatureFieldModal" class="fixed inset-0 bg-black bg-opacity-90 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-6 border w-full max-w-lg shadow-lg rounded-lg bg-white m-4">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                <i class="fas fa-pen-fancy text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-2" id="signatureFieldModalTitle">Capturar Firma</h3>
            <div class="mt-2 px-2 py-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-lg text-gray-800 font-medium" id="signatureFieldModalDesc">
                    Instrucciones aquí...
                </p>
            </div>
            <div class="mt-4">
                <canvas id="signature-field-canvas" class="border-2 border-gray-400 rounded" width="450" height="200" style="touch-action: none; max-width:100%;"></canvas>
            </div>
            <div class="mt-6">
                <button id="clearSignatureFieldButton" class="w-full px-4 py-2 bg-gray-300 text-gray-800 font-medium rounded-lg hover:bg-gray-400 focus:outline-none">
                    <i class="fas fa-redo mr-1"></i> Limpiar
                </button>
                <button id="saveSignatureFieldButton" class="mt-3 w-full px-4 py-3 bg-green-600 text-white text-lg font-bold rounded-lg shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transform transition active:scale-95">
                    <i class="fas fa-check mr-2"></i> GUARDAR FIRMA
                </button>
                <button onclick="closeSignatureFieldModal()" class="mt-3 w-full px-4 py-2 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 focus:outline-none">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Signature Logic
    const canvas = document.getElementById('sig-canvas');
    if(canvas){
        const ctx = canvas.getContext('2d');
        let drawing = false;

        // Mouse events
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mouseup', endDraw);
        canvas.addEventListener('mousemove', draw);
        
        // Touch events
        canvas.addEventListener('touchstart', (e) => { e.preventDefault(); startDraw(e.touches[0]); });
        canvas.addEventListener('touchend', (e) => { e.preventDefault(); endDraw(); });
        canvas.addEventListener('touchmove', (e) => { e.preventDefault(); draw(e.touches[0]); });

        function startDraw(e) {
            drawing = true;
            ctx.beginPath();
            const rect = canvas.getBoundingClientRect();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        }

        function endDraw() {
            drawing = false;
        }

        function draw(e) {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.stroke();
        }

        document.getElementById('clear-sig').addEventListener('click', () => {
             ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        document.getElementById('save-sig').addEventListener('click', () => {
             const dataUrl = canvas.toDataURL();
             
             // Check if empty
             // ...
             
             // Submit Signature
             fetch('{{ route("inspector.inspections.sign", $inspection->id) }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                 },
                 body: JSON.stringify({ 
                     image_path: dataUrl,
                     role: 'inspector'
                 })
             })
             .then(response => response.json())
             .then(data => {
                 if(data.success || data.message){
                     // Also submit the form to save pending data
                     document.getElementById('inspectionForm').submit();
                 } else {
                     alert('Error saving signature');
                 }
             })
             .catch(console.error);
        });
    }

    function openSignatureModal() {
        // First, basic validation check?
        // ...
        document.getElementById('signatureModal').classList.remove('hidden');
    }

    function closeSignatureModal() {
        document.getElementById('signatureModal').classList.add('hidden');
    }

    // --- Photo Logic ---
    let currentFieldId = null;

    function openPhotoModal(fieldId, title, description) {
        currentFieldId = fieldId;
        document.getElementById('photoModalTitle').innerText = title;
        document.getElementById('photoModalDesc').innerText = description;
        document.getElementById('photoModal').classList.remove('hidden');
    }

    function closePhotoModal() {
        document.getElementById('photoModal').classList.add('hidden');
        currentFieldId = null;
    }

    document.getElementById('triggerCameraButton').addEventListener('click', function() {
        if (currentFieldId) {
            const input = document.getElementById('input-file-' + currentFieldId);
            if (input) {
                input.setAttribute('capture', 'environment'); // Force Camera
                input.click();
            }
            closePhotoModal();
        }
    });

    document.getElementById('triggerGalleryButton').addEventListener('click', function() {
        if (currentFieldId) {
            const input = document.getElementById('input-file-' + currentFieldId);
            if (input) {
                input.removeAttribute('capture'); // Allow Gallery/File Selection
                input.click();
            }
            closePhotoModal();
        }
    });

    function handleFileSelect(input, fieldId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.getElementById('img-preview-' + fieldId);
                const noPhotoDiv = document.getElementById('no-photo-' + fieldId);
                
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if (noPhotoDiv) {
                    noPhotoDiv.style.display = 'none'; // Hide the simplified initial view
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // --- Stepper Logic ---
    let currentStepIndex = 0;

    function switchStep(stepIndex) {
        // Prevent moving forward if current step is invalid
        if (stepIndex > currentStepIndex) {
            const currentContent = document.getElementById('step-content-' + currentStepIndex);
            if (currentContent) {
                // Find all required inputs/selects/textareas
                const inputs = currentContent.querySelectorAll('input[required], select[required], textarea[required]');
                let valid = true;
                
                for (let input of inputs) {
                    if (!input.value || (input.type === 'checkbox' && !input.checked)) {
                        // Special check for Hidden Photo inputs
                        if (input.type === 'file' || input.name.includes('values[')) { 
                             // Logic to check if value exists (hidden input populated)
                             // Actually, for photo fields, we use the hidden input with value=path
                             // Let's rely on reportValidity() if possible, but hidden inputs don't show bubbles.
                             // Check computed validation for our specific structure
                             const fieldId = input.name.match(/\d+/); // extract ID from values[123]
                             if(fieldId){
                                const hiddenVal = document.querySelector(`input[name="values[${fieldId}]"][type="hidden"]`);
                                if(hiddenVal && hiddenVal.value) continue; // valid
                             }
                        }
                        
                        // Use native validation reporting set custom error
                        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        alert('Por favor complete todos los campos obligatorios antes de continuar.');
                        // input.reportValidity(); // Often fails on hidden inputs
                        valid = false;
                        break;
                    }
                }
                
                if (!valid) return;

                // Auto-save when moving forward
                saveInspection();
            }
        }

        // Hide all steps
        const contents = document.querySelectorAll('.step-content');
        contents.forEach(content => content.classList.add('hidden'));

        // Show selected step
        const selectedContent = document.getElementById('step-content-' + stepIndex);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
            currentStepIndex = stepIndex;
        }

        // Update tabs styling
        const tabs = document.querySelectorAll('.step-tab');
        tabs.forEach((tab, index) => {
            // Enable clicking only if it's previous or current step? 
            // The prompt said "cannot advance unless completed". 
            // So we allow going back freely, but going forward is checked above.
            
            if (index === stepIndex) {
                tab.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
                tab.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            } else {
                tab.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600');
                tab.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            }
        });

        // Optional: Scroll to top of form
        document.getElementById('inspectionForm').scrollIntoView({ behavior: 'smooth' });
    }

    function saveInspection(redirectUrl = null) {
        const form = document.getElementById('inspectionForm');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Autoguardado exitoso');
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            } else {
                console.error('Error al guardar', data);
            }
        })
        .catch(error => {
            console.error('Error de red:', error);
        });
    }

    // --- Conditional Fields Logic ---
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing conditional fields logic...');
        
        // Run initial check
        checkConditionals();

        // Listen for changes on all inputs to re-evaluate
        const form = document.getElementById('inspectionForm');
        if (form) {
            // Add event listeners with logging
            form.addEventListener('change', function(e) {
                console.log('Change event detected on:', e.target.name || e.target.id, 'Value:', e.target.value);
                checkConditionals();
            });
            form.addEventListener('input', function(e) {
                console.log('Input event detected on:', e.target.name || e.target.id, 'Value:', e.target.value);
                checkConditionals();
            });
        }
        
        // Also add direct listeners to all parent fields for immediate response
        const dependentFields = document.querySelectorAll('[data-conditional-linked-to]');
        const parentIds = new Set();
        
        dependentFields.forEach(wrapper => {
            const parentId = wrapper.getAttribute('data-conditional-linked-to');
            if (parentId) {
                parentIds.add(parentId);
            }
        });
        
        console.log('Found parent field IDs:', Array.from(parentIds));
        
        // Add direct listeners to each parent field
        parentIds.forEach(parentId => {
            const parentInput = document.querySelector(`[name="values[${parentId}]"]`);
            if (parentInput) {
                console.log('Adding direct listener to parent field ID:', parentId, 'Type:', parentInput.type);
                parentInput.addEventListener('change', function() {
                    console.log('Direct change on parent field:', parentId, 'New value:', this.value);
                    checkConditionals();
                });
                if (parentInput.type !== 'select' && parentInput.type !== 'select-one') {
                    parentInput.addEventListener('input', function() {
                        console.log('Direct input on parent field:', parentId, 'New value:', this.value);
                        checkConditionals();
                    });
                }
            }
        });
    });

    function checkConditionals() {
        const dependentFields = document.querySelectorAll('[data-conditional-linked-to]');
        
        console.log('Checking conditionals for', dependentFields.length, 'fields');
        
        dependentFields.forEach(wrapper => {
            const parentId = wrapper.getAttribute('data-conditional-linked-to');
            const requiredValue = wrapper.getAttribute('data-conditional-value');
            if (!parentId) return;

            console.log('Checking field with parent ID:', parentId, 'required value:', requiredValue);

            // Find parent input - try to find by name attribute
            let parentInput = document.querySelector(`[name="values[${parentId}]"]`);
            
            // If not found, try finding by data-field-id on the wrapper
            if (!parentInput) {
                const parentWrapper = document.querySelector(`[data-field-id="${parentId}"]`);
                if (parentWrapper) {
                    parentInput = parentWrapper.querySelector('input, select, textarea');
                }
            }

            let isMatch = false;

            if (parentInput) {
                console.log('Found parent input:', parentInput.type, 'value:', parentInput.value);

                if (parentInput.type === 'checkbox') {
                     // Checkbox: value is usually '1' or 'on'
                     // If requiredValue is '1', 'on', 'true', check if checked
                     // If requiredValue is '0', 'off', 'false', check if !checked
                     const isChecked = parentInput.checked;
                     const trueValues = ['1', 'on', 'true', 'si', 'yes'];
                     const falseValues = ['0', 'off', 'false', 'no'];
                     
                     if (trueValues.includes(requiredValue.toLowerCase())) {
                         isMatch = isChecked;
                     } else if (falseValues.includes(requiredValue.toLowerCase())) {
                         isMatch = !isChecked;
                     } else {
                         // Exact match check (rare for checkbox)
                         isMatch = isChecked && parentInput.value == requiredValue;
                     }

                } else if (parentInput.type === 'radio') {
                    // For radio, we need the checked one
                    const radios = document.querySelectorAll(`[name="values[${parentId}]"]:checked`);
                    if (radios.length > 0) {
                        isMatch = radios[0].value == requiredValue;
                    }
                } else {
                    // Text, Select, Number, Date
                    // Trim whitespace and compare (can be case-insensitive for text)
                    const currentValue = String(parentInput.value).trim();
                    const expectedValue = String(requiredValue).trim();
                    
                    // Try exact match first
                    if (currentValue === expectedValue) {
                        isMatch = true;
                    } else {
                        // Try case-insensitive match for text/select
                        isMatch = currentValue.toLowerCase() === expectedValue.toLowerCase();
                    }
                }
                
                console.log('Match result:', isMatch);
            } else {
                console.warn('Parent input not found for ID:', parentId);
            }

            if (isMatch) {
                wrapper.style.display = 'block';
                // wrapper.classList.remove('hidden'); // If using classes
                
                // Re-enable inputs inside to allow submission/validation
                const inputs = wrapper.querySelectorAll('input, select, textarea');
                inputs.forEach(el => el.disabled = false);
                
                console.log('Field shown:', wrapper.querySelector('label')?.textContent);
            } else {
                wrapper.style.display = 'none';
                
                // Disable inputs inside to skip validation
                const inputs = wrapper.querySelectorAll('input, select, textarea');
                inputs.forEach(el => el.disabled = true);
                
                console.log('Field hidden:', wrapper.querySelector('label')?.textContent);
            }
        });
    }
</script>
@endsection
