@extends('layouts.dashboard')

@section('title', 'Add Vials Data - Doc Tracker')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Add Vials Data</h3>
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
            <a href="{{ route('injection_details.index') }}">Vials Data</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Add Entry</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Vials Data Entry</div>
            </div>
            <div class="card-body">
                <form action="{{ route('injection_details.store') }}" method="POST">
                    @csrf

                    <div id="vials-container">
                        <!-- First Row -->
                        <div class="vials-row border rounded p-3 mb-3 position-relative bg-light">
                            <div class="row g-2">
                                <!-- Product Name -->
                                <div class="col-md-12 col-lg-3">
                                    <div class="form-group mb-0">
                                        <label>Product Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control autocomplete-input" name="product_name[]"
                                               placeholder="Abacus 40mg/5ml Injection" required>
                                    </div>
                                </div>

                                <!-- Batch No -->
                                <div class="col">
                                    <div class="form-group mb-0">
                                        <label>Batch No <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control px-2 autocomplete-input" name="batch_no[]"
                                               placeholder="013P056" maxlength="7" required>
                                    </div>
                                </div>

                                <!-- Batch Size -->
                                <div class="col">
                                    <div class="form-group mb-0">
                                        <label>Batch Size</label>
                                        <input type="text" class="form-control px-2" name="batch_size[]"
                                               placeholder="100000" maxlength="7">
                                    </div>
                                </div>

                                <!-- Optical Qty -->
                                <div class="col">
                                    <div class="form-group mb-0">
                                        <label>Optical Qty</label>
                                        <input type="text" class="form-control px-2" name="optical_qty[]"
                                               placeholder="100000" maxlength="7">
                                    </div>
                                </div>

                                <!-- Pkg Qty -->
                                <div class="col">
                                    <div class="form-group mb-0">
                                        <label>Pkg Qty</label>
                                        <input type="text" class="form-control px-2" name="pkg_qty[]"
                                               placeholder="100000" maxlength="7">
                                    </div>
                                </div>

                                <!-- Rejection -->
                                <div class="col-auto">
                                    <div class="form-group mb-0">
                                        <label>Rejection</label>
                                        <input type="text" class="form-control px-2 text-center" name="rejection[]"
                                               placeholder="10" maxlength="4" style="width: 60px;">
                                    </div>
                                </div>

                                <!-- Error -->
                                <div class="col-auto">
                                    <div class="form-group mb-0">
                                        <label>Error</label>
                                        <input type="text" class="form-control px-2 text-center" name="error[]"
                                               placeholder="05" maxlength="4" style="width: 60px;">
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
                        <button type="button" id="add-vials-btn" class="btn btn-primary btn-border btn-round btn-sm">
                            <i class="fa fa-plus me-2"></i>Add Another Entry
                        </button>
                        <button type="button" id="duplicate-vials-btn" class="btn btn-dark btn-round btn-sm ms-2">
                            <i class="fa fa-copy me-2"></i>Duplicate Last Entry
                        </button>
                    </div>

                    <div class="card-action">
                        <button type="submit" class="btn btn-success" id="save-btn">
                            <i class="fa fa-check me-2"></i>Save Vials Data
                        </button>
                        <a href="{{ route('injection_details.index') }}" class="btn btn-danger">
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
    .form-group { margin-bottom: 1.5rem; }
    .card-action { padding-top: 1rem; border-top: 1px solid #eee; }
    .vials-row { transition: box-shadow 0.2s; }
    .vials-row:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('vials-container');
    const addBtn    = document.getElementById('add-vials-btn');
    const dupBtn    = document.getElementById('duplicate-vials-btn');
    const saveBtn   = document.getElementById('save-btn');
    const form      = container.closest('form');

    function addRemoveButton(row) {
        if (row.querySelector('.remove-row-btn')) return;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-icon btn-danger btn-sm remove-row-btn position-absolute';
        btn.style.cssText = 'top:-15px;right:-10px;z-index:10;';
        btn.innerHTML = '<i class="fa fa-times"></i>';
        btn.onclick = () => row.remove();
        row.appendChild(btn);
    }

    addBtn.addEventListener('click', function () {
        const first  = container.querySelector('.vials-row');
        const newRow = first.cloneNode(true);
        newRow.querySelectorAll('input').forEach(i => i.value = '');
        addRemoveButton(newRow);
        container.appendChild(newRow);
        newRow.querySelector('input').focus();
    });

    dupBtn.addEventListener('click', function () {
        const rows   = container.querySelectorAll('.vials-row');
        const last   = rows[rows.length - 1];
        const newRow = last.cloneNode(true);

        // Copy values from last row but clear batch_no
        const oldInputs = last.querySelectorAll('input');
        const newInputs = newRow.querySelectorAll('input');
        oldInputs.forEach((inp, idx) => {
            if (inp.name.includes('batch_no')) {
                newInputs[idx].value = '';
            } else {
                newInputs[idx].value = inp.value;
            }
        });

        addRemoveButton(newRow);
        container.appendChild(newRow);
        newRow.querySelector('input[name="batch_no[]"]').focus();
    });

    // Prevent double submit
    form.addEventListener('submit', function () {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Saving...';
    });

    // AUTOCOMPLETE LOGIC
    const autocompleteData = {
        name: @json($names),
        batch_no: @json($batchNos)
    };

    const productBatchMap = {
    "2Sum 1g Injection": "015P",
    "2Sum 2g Injection": "016P",
    "2Sum 500mg Injection": "014P",
    "Abacus 100mg Suspension": "002P",
    "Abacus 100mg Tablet": "003P",
    "Abacus 200mg Tablet": "004P",
    "Abacus 40mg Suspension": "001P",
    "Caricef 100mg Suspension": "281P",
    "Caricef 100mg Suspension (60ml)": "282P",
    "Caricef 200mg Tablet": "284P",
    "Caricef 400mg Capsule": "280P",
    "Caricef DS Suspension": "283P",
    "Neucef 100mg/ml P.Drop": "288P",
    "Neucef 125mg Suspension": "286P",
    "Neucef 500mg Capsule": "285P",
    "Neucef DS Suspension": "287P",
    "Nivador 1g Injection": "291P",
    "Nivador 250mg Injection": "289P",
    "Nivador 500mg Injection": "290P",
    "Otid 125mg Suspension": "026P",
    "Otid 1g Injection": "032P",
    "Otid 250mg Capsule": "028P",
    "Otid 250mg Suspension": "027P",
    "Otid 500mg Capsule": "029P",
    "Otid 500mg Injection": "031P",
    "Oxidil 1g IV Injection": "296P",
    "Oxidil 250mg IM Injection": "292P",
    "Oxidil 250mg IV Injection": "293P",
    "Oxidil 2g IV Injection": "297P",
    "Oxidil 500mg IM Injection": "294P",
    "Oxidil 500mg IV Injection": "295P",
    "Slate 125mg Suspension": "005P",
    "Slate 187mg Suspension": "006P",
    "Slate 250mg Capsule": "009P001",
    "Slate 250mg Suspension": "007P",
    "Slate 500mg Capsule": "010P",
    "Slate 50mg/ml P.Drops": "008P",
    "Yorker 1g Injection": "013P",
    "Yorker 250mg Injection": "011P",
    "Yorker 500mg Injection": "012P"
};

    Object.keys(productBatchMap).forEach(name => {
        if(!autocompleteData.name.includes(name)) {
            autocompleteData.name.push(name);
        }
    });

    const userPreferences = @json($userPrefs);

    function savePreference(key, value) {
        fetch('{{ route("preferences.update") }}', {
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
        
        let storageKeySuffix = inputElement.name.replace('[]', '');
        if (storageKeySuffix === 'product_name') storageKeySuffix = 'name';

        inputElement.addEventListener('input', function() {
            const val = this.value.trim();
            const lowerVal = val.toLowerCase();
            dropdownMenu.innerHTML = '';
            
            if (!val) {
                dropdownMenu.classList.remove('show');
                return;
            }
            
            let hiddenSuggestions = userPreferences['hidden_suggestions'] || [];
            let customSuggestions = userPreferences['added_' + storageKeySuffix] || [];
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
                        
                        if (storageKeySuffix === 'name' && productBatchMap[match]) {
                            const row = inputElement.closest('.vials-row');
                            if (row) {
                                const batchInput = row.querySelector('input[name="batch_no[]"]');
                                if (batchInput && !batchInput.value) {
                                    batchInput.value = productBatchMap[match];
                                    batchInput.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                            }
                        }
                    });
                    
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
        document.querySelectorAll('input[name="product_name[]"]').forEach(el => {
            initAutocomplete(el, autocompleteData.name);
            el.removeEventListener('change', handleNameChange);
            el.addEventListener('change', handleNameChange);
        });
        document.querySelectorAll('input[name="batch_no[]"]').forEach(el => {
            initAutocomplete(el, autocompleteData.batch_no);
        });
    }

    function handleNameChange(e) {
        const val = e.target.value.trim();
        if (productBatchMap[val]) {
            const row = e.target.closest('.vials-row');
            if (row) {
                const batchInput = row.querySelector('input[name="batch_no[]"]');
                if (batchInput && !batchInput.value) {
                    batchInput.value = productBatchMap[val];
                    batchInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        }
    }

    bindAllAutocompletes();

    // Re-bind when a new row is added
    const originalAddBtnClick = addBtn.onclick;
    addBtn.addEventListener('click', function() {
        setTimeout(bindAllAutocompletes, 100);
    });
});
</script>
@endpush
