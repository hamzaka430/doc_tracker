# Custom Select Dropdown - Documentation

## 📋 Overview

This custom select dropdown component is designed to match your luxury design system with:
- ✅ Rounded borders matching input fields
- ✅ Custom icons (left and right positioned)
- ✅ Hidden default browser arrow
- ✅ Fully responsive and mobile-friendly
- ✅ Modern UI with smooth animations
- ✅ Multiple variants and states

---

## 🎨 Features

### Core Features
- **Custom Icons**: Place Font Awesome icons on left and right
- **Hidden Default Arrow**: Browser's default dropdown arrow is completely hidden
- **Animated Dropdown Icon**: Right icon rotates on focus
- **Smooth Transitions**: All state changes are animated
- **Accessibility**: Maintains full keyboard navigation
- **Validation States**: Success and error states included

### Design Variants
1. **Basic Select** - Standard with icons
2. **Small Size** - Compact version
3. **Large Size** - Prominent version  
4. **Gradient Border** - Premium look with gradient
5. **Floating Label** - Material Design style
6. **Validation States** - Valid/Invalid styling

---

## 📦 Installation

### Step 1: Include CSS File
Add this to your `<head>` section:

```html
<link href="{{ asset('assets/custom-select.css') }}" rel="stylesheet">
```

### Step 2: Include Font Awesome
```html
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
```

---

## 🚀 Basic Usage

### HTML Structure

```html
<div class="custom-select-wrapper">
    <i class="fas fa-pills icon-left"></i>
    <select name="type" id="type">
        <option value="" disabled selected>Select Type</option>
        <option value="Injection">Injection</option>
        <option value="Suspension">Suspension</option>
        <option value="Tablet">Tablet</option>
        <option value="Capsule">Capsule</option>
    </select>
    <i class="fas fa-chevron-down icon-right"></i>
</div>
```

### Laravel Blade Example

```blade
<div class="custom-select-wrapper">
    <i class="fas fa-pills icon-left"></i>
    <select class="form-select" name="type" required>
        <option value="" disabled selected>Select Type</option>
        @foreach($types as $type)
            <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                {{ $type }}
            </option>
        @endforeach
    </select>
    <i class="fas fa-chevron-down icon-right"></i>
</div>
```

---

## 🎯 Size Variants

### Small Size
```html
<div class="custom-select-wrapper select-sm">
    <i class="fas fa-filter icon-left"></i>
    <select>
        <option value="" disabled selected>Filter...</option>
        <option value="all">All</option>
    </select>
    <i class="fas fa-chevron-down icon-right"></i>
</div>
```

### Large Size
```html
<div class="custom-select-wrapper select-lg">
    <i class="fas fa-star icon-left"></i>
    <select>
        <option value="" disabled selected>Choose Priority</option>
        <option value="high">High</option>
    </select>
    <i class="fas fa-chevron-down icon-right"></i>
</div>
```

---

## ✅ Validation States

### Success State
```html
<div class="custom-select-wrapper is-valid">
    <i class="fas fa-check-circle icon-left"></i>
    <select>
        <option value="selected" selected>Valid Option</option>
    </select>
    <i class="fas fa-chevron-down icon-right"></i>
</div>
```

### Error State
```html
<div class="custom-select-wrapper is-invalid">
    <i class="fas fa-exclamation-circle icon-left"></i>
    <select>
        <option value="" disabled selected>Please select</option>
    </select>
    <i class="fas fa-chevron-down icon-right"></i>
</div>
```

---

## 🌟 Special Effects

### Gradient Border
```html
<div class="custom-select-wrapper gradient-border">
    <i class="fas fa-crown icon-left"></i>
    <select>
        <option value="" disabled selected>Premium</option>
    </select>
    <i class="fas fa-chevron-down icon-right"></i>
</div>
```

### Floating Label (Material Design)
```html
<div class="custom-select-wrapper custom-select-floating">
    <i class="fas fa-building icon-left"></i>
    <select required>
        <option value=""></option>
        <option value="dept1">Marketing</option>
    </select>
    <label>Department</label>
    <i class="fas fa-chevron-down icon-right"></i>
</div>
```

---

## 🎨 Customization

### Change Colors
Edit CSS variables in `custom-select.css`:

```css
:root {
    --pure-white: #FFFFFF;
    --dark-blue: #1A1A2E;
    --pure-black: #000000;
}
```

### Custom Border Radius
```css
.custom-select-wrapper select {
    border-radius: 20px; /* Change this value */
}
```

### Custom Padding
```css
.custom-select-wrapper select {
    padding: 15px 50px 15px 50px; /* top right bottom left */
}
```

---

## 📱 Responsive Behavior

The component automatically adjusts for different screen sizes:

- **Desktop (>768px)**: Full size with standard padding
- **Tablet (768px)**: Slightly reduced padding and font size
- **Mobile (<576px)**: Compact version with smaller icons

---

## 🔧 JavaScript Integration

### Get Selected Value
```javascript
const selectElement = document.querySelector('#type');
selectElement.addEventListener('change', function() {
    const selectedValue = this.value;
    console.log('Selected:', selectedValue);
});
```

### Programmatically Set Value
```javascript
document.querySelector('#type').value = 'Injection';
```

### Validation Example
```javascript
const select = document.querySelector('#type');
if (select.value === '') {
    select.parentElement.classList.add('is-invalid');
} else {
    select.parentElement.classList.remove('is-invalid');
    select.parentElement.classList.add('is-valid');
}
```

---

## 🎭 Icon Options

You can use any Font Awesome icon. Popular choices:

```html
<!-- Pills/Medicine -->
<i class="fas fa-pills icon-left"></i>

<!-- Stages/Process -->
<i class="fas fa-diagram-project icon-left"></i>

<!-- Filter -->
<i class="fas fa-filter icon-left"></i>

<!-- User/Person -->
<i class="fas fa-user icon-left"></i>

<!-- Location -->
<i class="fas fa-location-dot icon-left"></i>

<!-- Calendar -->
<i class="fas fa-calendar icon-left"></i>

<!-- Building -->
<i class="fas fa-building icon-left"></i>

<!-- Star/Rating -->
<i class="fas fa-star icon-left"></i>
```

---

## 🐛 Troubleshooting

### Select is Too Wide
```css
.custom-select-wrapper {
    max-width: 400px; /* Limit width */
}
```

### Icons Not Showing
Ensure Font Awesome is loaded:
```html
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
```

### Border Not Visible
Check if parent has background:
```css
.custom-select-wrapper {
    background: white;
}
```

---

## 📊 Browser Support

- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

---

## 🎓 Best Practices

1. **Always include a placeholder**: Use `disabled selected` for the first option
2. **Use appropriate icons**: Match icons to the select purpose
3. **Add validation**: Use `required` attribute for mandatory fields
4. **Provide feedback**: Use validation states when form is submitted
5. **Label your selects**: Use `<label>` for accessibility
6. **Test on mobile**: Ensure touch targets are large enough

---

## 💡 Examples in Your App

### Product Type Selector
```blade
<div class="col-md-6">
    <label class="form-label">
        <i class="fas fa-pills me-2"></i>Type
    </label>
    <div class="custom-select-wrapper">
        <i class="fas fa-pills icon-left"></i>
        <select class="form-select" name="type" required>
            <option value="" disabled selected>Select Type</option>
            @foreach($types as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
        <i class="fas fa-chevron-down icon-right"></i>
    </div>
</div>
```

### Filter Dropdown
```blade
<div class="custom-select-wrapper">
    <i class="fas fa-pills icon-left"></i>
    <select class="form-select" id="typeFilter">
        <option value="">All Types</option>
        <option value="Injection">Injection</option>
        <option value="Suspension">Suspension</option>
        <option value="Tablet">Tablet</option>
        <option value="Capsule">Capsule</option>
    </select>
    <i class="fas fa-chevron-down icon-right"></i>
</div>
```

---

## 🔗 Related Files

- **CSS File**: `public/assets/custom-select.css`
- **Demo Page**: `resources/views/components/custom-select-demo.blade.php`
- **Implementation**: Used in `create.blade.php`, `edit.blade.php`, `index.blade.php`, `submitted.blade.php`

---

## 📄 License

This component is part of the Doc Tracker application and follows the same license.

---

## 🤝 Support

For issues or questions:
1. Check the demo page at `/components/custom-select-demo`
2. Review the troubleshooting section above
3. Inspect browser console for errors

---

**Version**: 1.0  
**Last Updated**: November 5, 2025  
**Compatible With**: Bootstrap 5.3+, Font Awesome 6.4+
