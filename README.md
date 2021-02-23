# alpinejs-select

A reusable Laravel blade component for use with Laravel Livewire for select search functionality similar to Chosen, Select2, and others except built entirely with Alpine.js

## Usage
Place the blade file wherever you store your blade components (/resources/views/components)

Place it into your blade file and pass in the necesary props.
-data: Available options to be chosen from as an associative array e.g [1 => "First Option, 2 => "Second Option"]
-placeholder: The text that will appear when nothing is chosen

You may also pass in the following attributes:
-multiple: Allows users to select more than one element
-wire:model=: For binding the selected options back into a Livewire component. Should be a string if using single select or an array of ids if using multiple select.

```
<x-select-search :data="$departments" wire:model="values" placeholder="Select something!" multiple/>
```

## Todos
-Window detection/scroll up
-I'm sure it could be more dry
