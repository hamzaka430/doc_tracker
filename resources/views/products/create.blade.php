@extends('layouts.dashboard')

@section('title', 'Add New Document - Doc Tracker')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Add New Document</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('products.index') }}">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Add Document</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Document Information</div>
            </div>
            <div class="card-body">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    
                    <div id="documents-container">
                        <!-- First Document Row -->
                        <div class="document-row border rounded p-3 mb-3 position-relative bg-light">
                            <div class="row">
                                <!-- Product Name -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group mb-0">
                                        <label>Document Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name[]" 
                                               placeholder="Enter document name" value="{{ request('name') }}" required>
                                    </div>
                                </div>

                                <!-- Batch Number -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group mb-0">
                                        <label>Batch Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="batch_no[]" 
                                               placeholder="Enter batch number" value="{{ request('batch_no') }}" required>
                                    </div>
                                </div>

                                <!-- Stage -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group mb-0">
                                        <label>Stage <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="stage[]" 
                                               placeholder="Enter stage or select" 
                                               value="{{ request('stage') }}"
                                               required>
                                    </div>
                                </div>

                                <!-- Type -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group mb-0">
                                        <label>Type <span class="text-danger">*</span></label>
                                        <select class="form-select form-control" name="type[]" required>
                                            <option value="" disabled {{ !request('type') ? 'selected' : '' }}>Select Type</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($errors->any())
                        <div class="alert alert-danger mt-3">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif



                    <div class="mb-4 mt-2">
                        <button type="button" id="add-document-btn" class="btn btn-primary btn-border btn-round btn-sm">
                            <i class="fa fa-plus me-2"></i>Add Another Document
                        </button>
                        <button type="button" id="duplicate-document-btn" class="btn btn-dark btn-round btn-sm ms-2">
                            <i class="fa fa-copy me-2"></i>Duplicate Last Document
                        </button>
                    </div>

                    <div class="card-action">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check me-2"></i>Save Documents
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-danger">
                            <i class="fa fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .card-action {
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }
</style>
@endpush

@push('scripts')
<script>
const autocompleteData = {
    name: @json($names),
    batch_no: @json($batchNos),
    stage: @json($stages)
};

const userPreferences = @json($userPrefs);

function savePreference(key, value) {
    fetch('{{ route('preferences.update') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ key: key, value: value })
    });
}

function initAutocomplete(inputElement, dataList) {
    inputElement.setAttribute('autocomplete', 'off');
    
    let wrapper = inputElement.parentNode;
    if (!wrapper.classList.contains('dropdown')) {
        wrapper.classList.add('dropdown');
    }
    
    let dropdownMenu = wrapper.querySelector('.autocomplete-menu');
    if (!dropdownMenu) {
        dropdownMenu = document.createElement('ul');
        dropdownMenu.className = 'dropdown-menu w-100 shadow-sm autocomplete-menu';
        dropdownMenu.style.maxHeight = '200px';
        dropdownMenu.style.overflowY = 'auto';
        wrapper.appendChild(dropdownMenu);
    }

    if (inputElement.dataset.autocompleteInit) return;
    inputElement.dataset.autocompleteInit = '1';
    
    // Determine the type of autocomplete data this is (name, batch_no, stage)
    // We can infer this from the input's name attribute
    let storageKeySuffix = inputElement.name.replace('[]', '');

    inputElement.addEventListener('input', function() {
        const val = this.value.trim();
        const lowerVal = val.toLowerCase();
        dropdownMenu.innerHTML = '';
        
        if (!val) {
            dropdownMenu.classList.remove('show');
            return;
        }
        
        // Load hidden suggestions
        let hiddenSuggestions = userPreferences['hidden_suggestions'] || [];
        
        // Load custom added suggestions
        let customSuggestions = userPreferences['added_' + storageKeySuffix] || [];
        
        // Combine all available data and remove duplicates
        let combinedData = [...new Set([...dataList, ...customSuggestions])];
        
        let matches = combinedData.filter(item => 
            item.toLowerCase().includes(lowerVal) && !hiddenSuggestions.includes(item)
        );
        
        let exactMatchExists = combinedData.some(item => item.toLowerCase() === lowerVal && !hiddenSuggestions.includes(item));
        
        if (matches.length > 0 || !exactMatchExists) {
            matches.forEach(match => {
                let li = document.createElement('li');
                li.className = 'd-flex align-items-center justify-content-between pe-2';
                
                let a = document.createElement('a');
                a.className = 'dropdown-item py-2 flex-grow-1';
                a.href = '#';
                
                const matchIndex = match.toLowerCase().indexOf(lowerVal);
                if (matchIndex >= 0) {
                    const before = match.substring(0, matchIndex);
                    const matchedText = match.substring(matchIndex, matchIndex + lowerVal.length);
                    const after = match.substring(matchIndex + lowerVal.length);
                    a.innerHTML = `${before}<strong class="text-primary">${matchedText}</strong>${after}`;
                } else {
                    a.textContent = match;
                }
                
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    inputElement.value = match;
                    dropdownMenu.classList.remove('show');
                    inputElement.dispatchEvent(new Event('input', { bubbles: true }));
                });
                
                // Remove button
                let removeBtn = document.createElement('button');
                removeBtn.className = 'btn btn-link text-danger p-0 ms-2';
                removeBtn.innerHTML = '<i class="fa fa-times" title="Remove this suggestion"></i>';
                removeBtn.style.fontSize = '0.8rem';
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!hiddenSuggestions.includes(match)) {
                        hiddenSuggestions.push(match);
                        userPreferences['hidden_suggestions'] = hiddenSuggestions;
                        savePreference('hidden_suggestions', hiddenSuggestions);
                    }
                    li.remove();
                    if (dropdownMenu.children.length === 0) {
                        dropdownMenu.classList.remove('show');
                    }
                });
                
                li.appendChild(a);
                li.appendChild(removeBtn);
                dropdownMenu.appendChild(li);
            });
            
            // If what they typed doesn't exactly match any existing suggestion, show "Add new" option
            if (!exactMatchExists && val.length > 0) {
                if (matches.length > 0) {
                    let divider = document.createElement('li');
                    divider.innerHTML = '<hr class="dropdown-divider m-0">';
                    dropdownMenu.appendChild(divider);
                }
                
                let liAdd = document.createElement('li');
                let aAdd = document.createElement('a');
                aAdd.className = 'dropdown-item py-2 text-success fw-bold';
                aAdd.href = '#';
                aAdd.innerHTML = `<i class="fa fa-plus-circle me-1"></i> Add "${val}" to suggestions`;
                
                aAdd.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!customSuggestions.includes(val)) {
                        customSuggestions.push(val);
                        userPreferences['added_' + storageKeySuffix] = customSuggestions;
                        savePreference('added_' + storageKeySuffix, customSuggestions);
                    }
                    
                    // Remove from hidden if it was previously hidden
                    const hiddenIndex = hiddenSuggestions.indexOf(val);
                    if (hiddenIndex > -1) {
                        hiddenSuggestions.splice(hiddenIndex, 1);
                        userPreferences['hidden_suggestions'] = hiddenSuggestions;
                        savePreference('hidden_suggestions', hiddenSuggestions);
                    }
                    
                    inputElement.value = val;
                    dropdownMenu.classList.remove('show');
                    inputElement.dispatchEvent(new Event('input', { bubbles: true }));
                });
                
                liAdd.appendChild(aAdd);
                dropdownMenu.appendChild(liAdd);
            }
            
            dropdownMenu.classList.add('show');
        } else {
            dropdownMenu.classList.remove('show');
        }
    });

    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            dropdownMenu.classList.remove('show');
        }
    });
}

function bindAllAutocompletes() {
    document.querySelectorAll('input[name="name[]"]').forEach(el => initAutocomplete(el, autocompleteData.name));
    document.querySelectorAll('input[name="batch_no[]"]').forEach(el => initAutocomplete(el, autocompleteData.batch_no));
    document.querySelectorAll('input[name="stage[]"]').forEach(el => initAutocomplete(el, autocompleteData.stage));
}

document.addEventListener('DOMContentLoaded', function() {
    bindAllAutocompletes();

    const container = document.getElementById('documents-container');
    const addBtn = document.getElementById('add-document-btn');
    const form = container.closest('form');
    
    // === AUTO-SAVE DRAFTS LOGIC ===
    function saveDraft() {
        const formData = new FormData(form);
        const draft = {
            names: formData.getAll('name[]'),
            batch_nos: formData.getAll('batch_no[]'),
            stages: formData.getAll('stage[]'),
            types: formData.getAll('type[]')
        };
        // Only save if there's actually some data typed in
        if (draft.names.some(n => n) || draft.batch_nos.some(b => b)) {
            localStorage.setItem('doc_tracker_draft', JSON.stringify(draft));
        }
    }
    
    form.addEventListener('input', saveDraft);
    form.addEventListener('change', saveDraft);
    
    // Clear draft and prevent double submission when successfully submitting
    form.addEventListener('submit', function(e) {
        localStorage.removeItem('doc_tracker_draft');
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            // Disable button to prevent double clicks
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Saving...';
        }
    });

    // Restore draft on load (if not cloning from URL params)
    const urlParams = new URLSearchParams(window.location.search);
    const isCloning = urlParams.has('name');
    
    if (!isCloning && localStorage.getItem('doc_tracker_draft')) {
        try {
            const draft = JSON.parse(localStorage.getItem('doc_tracker_draft'));
            if (draft.names && draft.names.length > 0) {
                // We have multiple rows in draft
                for (let i = 1; i < draft.names.length; i++) {
                    addBtn.click(); // Create the extra rows
                }
                
                const nameInputs = container.querySelectorAll('input[name="name[]"]');
                const batchInputs = container.querySelectorAll('input[name="batch_no[]"]');
                const stageInputs = container.querySelectorAll('input[name="stage[]"]');
                const typeSelects = container.querySelectorAll('select[name="type[]"]');
                
                for (let i = 0; i < draft.names.length; i++) {
                    if (nameInputs[i]) nameInputs[i].value = draft.names[i] || '';
                    if (batchInputs[i]) batchInputs[i].value = draft.batch_nos[i] || '';
                    if (stageInputs[i]) stageInputs[i].value = draft.stages[i] || '';
                    if (typeSelects[i]) typeSelects[i].value = draft.types[i] || '';
                }
                
                if (typeof $ !== 'undefined' && $.notify) {
                    $.notify({
                        icon: 'fa fa-bell',
                        title: 'Draft Restored',
                        message: 'Your unsaved document draft has been recovered.',
                    },{
                        type: 'info',
                        placement: { from: "bottom", align: "right" },
                        time: 3000,
                    });
                }
            }
        } catch(e) {
            console.error('Failed to restore draft', e);
        }
    }

    // === DYNAMIC ROWS LOGIC ===
    addBtn.addEventListener('click', function() {
        const firstRow = container.querySelector('.document-row');
        const newRow = firstRow.cloneNode(true);
        
        // Clear inputs in cloned row
        const inputs = newRow.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
        
        const selects = newRow.querySelectorAll('select');
        selects.forEach(select => select.selectedIndex = 0);
        
        // Add a delete button to the new row
        if (!newRow.querySelector('.remove-row-btn')) {
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-icon btn-danger btn-sm remove-row-btn position-absolute';
            removeBtn.style.top = '-15px';
            removeBtn.style.right = '-10px';
            removeBtn.style.zIndex = '10';
            removeBtn.innerHTML = '<i class="fa fa-times"></i>';
            removeBtn.onclick = function() {
                newRow.remove();
                saveDraft(); // Update draft when a row is removed
            };
            newRow.appendChild(removeBtn);
        }
        
        // Clean up cloned row for autocomplete
        newRow.querySelectorAll('.autocomplete-menu').forEach(menu => menu.remove());
        newRow.querySelectorAll('input').forEach(input => {
            delete input.dataset.autocompleteInit;
        });
        
        container.appendChild(newRow);
        bindAllAutocompletes();
        saveDraft(); // Update draft when a row is added
    });

    const duplicateBtn = document.getElementById('duplicate-document-btn');
    if (duplicateBtn) {
        duplicateBtn.addEventListener('click', function() {
            const rows = container.querySelectorAll('.document-row');
            if (rows.length === 0) return;
            const lastRow = rows[rows.length - 1];
            const newRow = lastRow.cloneNode(true);
            
            // Copy values from the last row
            const oldInputs = lastRow.querySelectorAll('input, select');
            const newInputs = newRow.querySelectorAll('input, select');
            
            for (let i = 0; i < oldInputs.length; i++) {
                if (oldInputs[i].name === 'batch_no[]') {
                    newInputs[i].value = ''; // clear batch number
                } else {
                    newInputs[i].value = oldInputs[i].value;
                }
            }
            
            // Add a delete button to the new row
            if (!newRow.querySelector('.remove-row-btn')) {
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-icon btn-danger btn-sm remove-row-btn position-absolute';
                removeBtn.style.top = '-15px';
                removeBtn.style.right = '-10px';
                removeBtn.style.zIndex = '10';
                removeBtn.innerHTML = '<i class="fa fa-times"></i>';
                removeBtn.onclick = function() {
                    newRow.remove();
                    saveDraft();
                };
                newRow.appendChild(removeBtn);
            } else {
                // Re-bind the click event on the cloned remove button
                const removeBtn = newRow.querySelector('.remove-row-btn');
                removeBtn.onclick = function() {
                    newRow.remove();
                    saveDraft();
                };
            }
            
            // Clean up cloned row for autocomplete
            newRow.querySelectorAll('.autocomplete-menu').forEach(menu => menu.remove());
            newRow.querySelectorAll('input').forEach(input => {
                delete input.dataset.autocompleteInit;
            });
            
            container.appendChild(newRow);
            bindAllAutocompletes();
            saveDraft();
        });
    }
});
</script>
@endpush
