@props([
    'data' => [],
    'placeholder' => 'Select',
])

<div
    x-data="AlpineSelect({
        data: {{ json_encode($data) }},
        selected: @entangle($attributes->wire('model')),
        placeholder: '{{ $placeholder }}',
        multiple: {{ isset($attributes['multiple']) ? 'true':'false' }},
    })"
    x-init="init()"
    @click.away="closeSelect()"
    @keydown.escape="closeSelect()"
    @keydown.arrow-down="increaseIndex()"
    @keydown.arrow-up="decreaseIndex()"
    @keydown.enter="selectOption(Object.keys(options)[currentIndex])"
>

    <button
        class="rounded-md border content-center p-1 bg-white relative sm:text-sm sm:leading-5 w-full text-left"
        x-bind:class="{'border-blue-300 ring ring-blue-200 ring-opacity-50':open}"
        @click.prevent="toggleSelect()"
    >
        <div>
            <div class="m-1 inline-block" x-show="selected.length === 0" x-text="placeholder">&nbsp;</div>
        </div>
        @isset($attributes['multiple'])
            <div class="flex flex-wrap space-x-1">
                <template x-for="(key, index) in selected" :key="index">
                    <div class="text-gray-800 rounded-full bg-blue-300 px-2 py-0.5 my-0.5 flex flex-row items-center flex-shrink-0">
                        <div class="px-2" x-text="data[key]"></div>
                        <div class="w-4 cursor-pointer" @click.prevent.stop="deselectOption(index)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 16.538l-4.592-4.548 4.546-4.587-1.416-1.403-4.545 4.589-4.588-4.543-1.405 1.405 4.593 4.552-4.547 4.592 1.405 1.405 4.555-4.596 4.591 4.55 1.403-1.416z"/></svg></div>
                    </div>
                </template>
            </div>
        @else
            <div class="flex flex-wrap" x-cloak x-show="selected">
                <div class="text-gray-800 rounded-full bg-blue-300 px-2 py-0.5 my-0.5 flex flex-row items-center flex-shrink-0">
                    <div class="px-2" x-text="data[selected]"></div>
                    <div class="w-4 cursor-pointer" @click.prevent.stop="deselectOption()"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 16.538l-4.592-4.548 4.546-4.587-1.416-1.403-4.545 4.589-4.588-4.543-1.405 1.405 4.593 4.552-4.547 4.592 1.405 1.405 4.555-4.596 4.591 4.55 1.403-1.416z"/></svg></div>
                </div>
            </div>
        @endif

        <div
            class="mt-0.5 w-full bg-white border-gray-300 rounded-b-md border absolute top-full left-0 z-30"
            x-show="open"
            x-transition:enter="transition-transform transition-opacity linear duration-200"
            x-transition:enter-start="transform scale-y-0 origin-top"
            x-transition:enter-end="transform scale-y-1 origin-top"
            x-transition:leave="transition linear duration-200"
            x-transition:leave-end="transform scale-y-0 origin-top"
            x-cloak
        >

            <div class="bg-white p-2 w-full">
                <input class="block w-full p-2 border border-gray-300 rounded-md focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 sm:text-sm sm:leading-5" type="search" x-model="search" x-on:click.prevent.stop="open=true">
            </div>

            <div class="p-2 max-h-60 overflow-y-auto">
                <div x-cloak x-show="Object.keys(options).length === 0" x-text="emptyOptionsMessage">Gragr</div>
                <template x-for="(key, index) in Object.keys(options)" :key="index" >
                    @isset($attributes['multiple'])
                    <div
                        class="px-2 py-1"
                        x-bind:class="{'bg-gray-300 text-white hover:none':selected.includes(key), 'hover:bg-blue-500 hover:text-white cursor-pointer':!(selected.includes(key)), 'bg-blue-500 text-white':currentIndex==index}"
                        @click.prevent.stop="selectOption(key)"
                        x-text="Object.values(options)[index]">
                    </div>
                    @else
                    <div
                        class="px-2 py-1"
                        x-bind:class="{'bg-gray-300 text-white hover:none':selected==key, 'hover:bg-blue-500 hover:text-white cursor-pointer':!(selected==key), 'bg-blue-500 text-white':currentIndex==index}"
                        @click.prevent.stop="selectOption(key)"
                        x-text="Object.values(options)[index]">
                    </div>
                    @endisset
                </template>
            </div>
        </div>
    </button>
</div>

@once
    @push('body_scripts')
        <script>
            function AlpineSelect(config) {
                return {
                    data: config.data,
                    open: false,
                    search: '',
                    options: {},
                    selected: config.selected,
                    emptyOptionsMessage: 'No results match your search.',
                    placeholder: config.placeholder ?? "Select an option",
                    selected: config.selected ?? '',
                    multiple: config.multiple,
                    currentIndex: 0,

                    init: function() {
                        if(!this.selected) this.selected = '';
                        this.options=this.data

                        this.$watch('search', ((values) => {
                            if (!this.open || !values) return this.options = this.data

                            this.options = Object.keys(this.data)
                                .filter((key) => this.data[key].toLowerCase().includes(values.toLowerCase()))
                                .reduce((options, key) => {
                                    options[key] = this.data[key]
                                    return options
                                }, {})

                            this.currentIndex=0
                        }))
                    },

                    closeSelect: function() {
                        this.open = false
                        this.search = ''
                    },

                    toggleSelect: function() {
                        if (this.open) return this.closeSelect()

                        this.open = true
                    },

                    deselectOption: function(index) {
                        if(this.multiple) {
                            this.selected.splice(index, 1)
                        }
                        else {
                            this.selected = ''
                        }

                    },

                    selectOption: function(value) {

                        // If multiple push to the array, if not, keep that value and close menu
                        if(this.multiple){
                            // If it's not already in there
                            if (!this.selected.includes(value)) {
                                this.selected.push(value)
                            }
                        }
                        else {
                            this.selected=value
                            this.closeSelect()
                        }

                    },

                    increaseIndex: function() {
                        this.currentIndex++
                    },

                    decreaseIndex: function() {
                        this.currentIndex--
                    },
                }
            }
        </script>
    @endpush
@endonce
